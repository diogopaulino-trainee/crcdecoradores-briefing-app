<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { cn } from '@/lib/utils';
import { reactiveOmit } from '@vueuse/core';
import { ChevronRight } from 'lucide-vue-next';
import { DropdownMenuSubTrigger, type DropdownMenuSubTriggerProps, useForwardProps } from 'reka-ui';

const props = defineProps<DropdownMenuSubTriggerProps & { class?: HTMLAttributes['class']; inset?: boolean }>();

const delegatedProps = reactiveOmit(props, 'class', 'inset');
const forwardedProps = useForwardProps(delegatedProps);
</script>

<template>
    <DropdownMenuSubTrigger
        data-slot="dropdown-menu-sub-trigger"
        v-bind="forwardedProps"
        :class="
            cn(
                'outline-hidden flex cursor-default select-none items-center rounded-sm px-2 py-1.5 text-sm focus:bg-accent focus:text-accent-foreground data-[state=open]:bg-accent data-[inset]:pl-8 data-[state=open]:text-accent-foreground',
                props.class,
            )
        "
    >
        <slot />
        <ChevronRight class="ml-auto size-4" />
    </DropdownMenuSubTrigger>
</template>
