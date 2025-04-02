<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { cn } from '@/lib/utils';
import { reactiveOmit } from '@vueuse/core';
import { DropdownMenuItem, type DropdownMenuItemProps, useForwardProps } from 'reka-ui';

const props = withDefaults(
    defineProps<
        DropdownMenuItemProps & {
            class?: HTMLAttributes['class'];
            inset?: boolean;
            variant?: 'default' | 'destructive';
        }
    >(),
    {
        variant: 'default',
    },
);

const delegatedProps = reactiveOmit(props, 'inset', 'variant');

const forwardedProps = useForwardProps(delegatedProps);
</script>

<template>
    <DropdownMenuItem
        data-slot="dropdown-menu-item"
        :data-inset="inset ? '' : undefined"
        :data-variant="variant"
        v-bind="forwardedProps"
        :class="
            cn(
                `data-[variant=destructive]:*:[svg]:!text-destructive-foreground outline-hidden relative flex cursor-default select-none items-center gap-2 rounded-sm px-2 py-1.5 text-sm focus:bg-accent focus:text-accent-foreground data-[disabled]:pointer-events-none data-[inset]:pl-8 data-[variant=destructive]:text-destructive-foreground data-[disabled]:opacity-50 data-[variant=destructive]:focus:bg-destructive/10 data-[variant=destructive]:focus:text-destructive-foreground dark:data-[variant=destructive]:focus:bg-destructive/40 [&_svg:not([class*='size-'])]:size-4 [&_svg:not([class*='text-'])]:text-muted-foreground [&_svg]:pointer-events-none [&_svg]:shrink-0`,
                props.class,
            )
        "
    >
        <slot />
    </DropdownMenuItem>
</template>
