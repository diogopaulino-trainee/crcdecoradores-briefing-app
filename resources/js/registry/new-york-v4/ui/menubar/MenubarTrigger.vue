<script setup lang="ts">
import { cn } from '@/lib/utils';
import { MenubarTrigger, type MenubarTriggerProps, useForwardProps } from 'reka-ui';
import { computed, type HTMLAttributes } from 'vue';

const props = defineProps<MenubarTriggerProps & { class?: HTMLAttributes['class'] }>();

const delegatedProps = computed(() => {
    const { class: _, ...delegated } = props;

    return delegated;
});

const forwardedProps = useForwardProps(delegatedProps);
</script>

<template>
    <MenubarTrigger
        data-slot="menubar-trigger"
        v-bind="forwardedProps"
        :class="
            cn(
                'outline-hidden flex select-none items-center rounded-sm px-2 py-1 text-sm font-medium focus:bg-accent focus:text-accent-foreground data-[state=open]:bg-accent data-[state=open]:text-accent-foreground',
                props.class,
            )
        "
    >
        <slot />
    </MenubarTrigger>
</template>
