<script setup lang="ts">
import { cn } from '@/lib/utils';
import { MenubarContent, type MenubarContentProps, MenubarPortal, useForwardProps } from 'reka-ui';
import { computed, type HTMLAttributes } from 'vue';

const props = withDefaults(defineProps<MenubarContentProps & { class?: HTMLAttributes['class'] }>(), {
    align: 'start',
    alignOffset: -4,
    sideOffset: 8,
});

const delegatedProps = computed(() => {
    const { class: _, ...delegated } = props;

    return delegated;
});

const forwardedProps = useForwardProps(delegatedProps);
</script>

<template>
    <MenubarPortal>
        <MenubarContent
            data-slot="menubar-content"
            v-bind="forwardedProps"
            :class="
                cn(
                    'origin-(--reka-menubar-content-transform-origin) z-50 min-w-[12rem] overflow-hidden rounded-md border bg-popover p-1 text-popover-foreground shadow-md data-[state=open]:animate-in data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95 data-[side=bottom]:slide-in-from-top-2 data-[side=left]:slide-in-from-right-2 data-[side=right]:slide-in-from-left-2 data-[side=top]:slide-in-from-bottom-2',
                    props.class,
                )
            "
        >
            <slot />
        </MenubarContent>
    </MenubarPortal>
</template>
