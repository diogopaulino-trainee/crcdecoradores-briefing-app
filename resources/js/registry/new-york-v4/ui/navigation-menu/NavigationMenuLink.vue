<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { cn } from '@/lib/utils';
import { reactiveOmit } from '@vueuse/core';
import { NavigationMenuLink, type NavigationMenuLinkEmits, type NavigationMenuLinkProps, useForwardPropsEmits } from 'reka-ui';

const props = defineProps<NavigationMenuLinkProps & { class?: HTMLAttributes['class'] }>();
const emits = defineEmits<NavigationMenuLinkEmits>();

const delegatedProps = reactiveOmit(props, 'class');
const forwarded = useForwardPropsEmits(delegatedProps, emits);
</script>

<template>
    <NavigationMenuLink
        data-slot="navigation-menu-link"
        v-bind="forwarded"
        :class="
            cn(
                `'data-[active=true]:focus:bg-accent [&_svg:not([class*='size-'])]:size-4' flex flex-col gap-1 rounded-sm p-2 text-sm outline-ring/50 ring-ring/10 transition-[color,box-shadow] hover:bg-accent hover:text-accent-foreground focus:bg-accent focus:text-accent-foreground focus-visible:outline-1 focus-visible:ring-4 data-[active=true]:bg-accent/50 data-[active=true]:text-accent-foreground data-[active=true]:hover:bg-accent dark:outline-ring/40 dark:ring-ring/20 [&_svg:not([class*='text-'])]:text-muted-foreground`,
                props.class,
            )
        "
    >
        <slot />
    </NavigationMenuLink>
</template>
