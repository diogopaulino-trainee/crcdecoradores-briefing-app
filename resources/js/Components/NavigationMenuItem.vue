<script setup lang="ts">
import { NavigationMenuLink } from '@/registry/new-york-v4/ui/navigation-menu';

defineProps<{
    to: string;
    title: string;
}>();
</script>

<template>
    <li>
        <NavigationMenuLink as-child>
            <NuxtLink :to="to">
                <div class="text-sm font-medium leading-none">
                    {{ title }}
                </div>
                <p class="line-clamp-2 text-sm leading-snug text-muted-foreground">
                    <slot />
                </p>
            </NuxtLink>
        </NavigationMenuLink>
    </li>
</template>
