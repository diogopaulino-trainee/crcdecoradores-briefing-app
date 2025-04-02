<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { cn } from '@/lib/utils';
import { reactiveOmit } from '@vueuse/core';
import { ChevronRight } from 'lucide-vue-next';
import { MenubarSubTrigger, type MenubarSubTriggerProps, useForwardProps } from 'reka-ui';

const props = defineProps<MenubarSubTriggerProps & { class?: HTMLAttributes['class']; inset?: boolean }>();

const delegatedProps = reactiveOmit(props, 'class', 'inset');
const forwardedProps = useForwardProps(delegatedProps);
</script>

<template>
    <MenubarSubTrigger
        data-slot="menubar-sub-trigger"
        :data-inset="inset ? '' : undefined"
        v-bind="forwardedProps"
        :class="
            cn(
                'flex cursor-default select-none items-center rounded-sm px-2 py-1.5 text-sm outline-none focus:bg-accent focus:text-accent-foreground data-[state=open]:bg-accent data-[inset]:pl-8 data-[state=open]:text-accent-foreground',
                props.class,
            )
        "
    >
        <slot />
        <ChevronRight class="ml-auto size-4" />
    </MenubarSubTrigger>
</template>
