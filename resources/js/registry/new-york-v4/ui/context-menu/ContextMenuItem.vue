<script setup lang="ts">
import { cn } from '@/lib/utils';
import { ContextMenuItem, type ContextMenuItemEmits, type ContextMenuItemProps, useForwardPropsEmits } from 'reka-ui';
import { computed, type HTMLAttributes } from 'vue';

const props = withDefaults(
    defineProps<
        ContextMenuItemProps & {
            class?: HTMLAttributes['class'];
            inset?: boolean;
            variant?: 'default' | 'destructive';
        }
    >(),
    {
        variant: 'default',
    },
);
const emits = defineEmits<ContextMenuItemEmits>();

const delegatedProps = computed(() => {
    const { class: _, ...delegated } = props;

    return delegated;
});

const forwarded = useForwardPropsEmits(delegatedProps, emits);
</script>

<template>
    <ContextMenuItem
        data-slot="context-menu-item"
        :data-inset="inset ? '' : undefined"
        :data-variant="variant"
        v-bind="forwarded"
        :class="
            cn(
                `data-[variant=destructive]:*:[svg]:!text-destructive-foreground outline-hidden relative flex cursor-default select-none items-center gap-2 rounded-sm px-2 py-1.5 text-sm focus:bg-accent focus:text-accent-foreground data-[disabled]:pointer-events-none data-[inset]:pl-8 data-[variant=destructive]:text-destructive-foreground data-[disabled]:opacity-50 data-[variant=destructive]:focus:bg-destructive/10 data-[variant=destructive]:focus:text-destructive-foreground dark:data-[variant=destructive]:focus:bg-destructive/40 [&_svg:not([class*='size-'])]:size-4 [&_svg:not([class*='text-'])]:text-muted-foreground [&_svg]:pointer-events-none [&_svg]:shrink-0`,
                props.class,
            )
        "
    >
        <slot />
    </ContextMenuItem>
</template>
