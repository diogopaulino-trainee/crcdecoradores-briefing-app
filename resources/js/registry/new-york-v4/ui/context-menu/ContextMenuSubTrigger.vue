<script setup lang="ts">
import { cn } from '@/lib/utils';
import { ChevronRight } from 'lucide-vue-next';
import { ContextMenuSubTrigger, type ContextMenuSubTriggerProps, useForwardProps } from 'reka-ui';
import { computed, type HTMLAttributes } from 'vue';

const props = defineProps<ContextMenuSubTriggerProps & { class?: HTMLAttributes['class']; inset?: boolean }>();

const delegatedProps = computed(() => {
    const { class: _, ...delegated } = props;

    return delegated;
});

const forwardedProps = useForwardProps(delegatedProps);
</script>

<template>
    <ContextMenuSubTrigger
        data-slot="context-menu-sub-trigger"
        :data-inset="inset ? '' : undefined"
        v-bind="forwardedProps"
        :class="
            cn(
                `outline-hidden flex cursor-default select-none items-center rounded-sm px-2 py-1.5 text-sm focus:bg-accent focus:text-accent-foreground data-[state=open]:bg-accent data-[inset]:pl-8 data-[state=open]:text-accent-foreground [&_svg:not([class*='size-'])]:size-4 [&_svg]:pointer-events-none [&_svg]:shrink-0`,
                props.class,
            )
        "
    >
        <slot />
        <ChevronRight class="ml-auto" />
    </ContextMenuSubTrigger>
</template>
