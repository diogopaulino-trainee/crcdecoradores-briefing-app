<script setup lang="ts">
import { cn } from '@/lib/utils';
import { Check } from 'lucide-vue-next';
import {
    MenubarCheckboxItem,
    type MenubarCheckboxItemEmits,
    type MenubarCheckboxItemProps,
    MenubarItemIndicator,
    useForwardPropsEmits,
} from 'reka-ui';
import { computed, type HTMLAttributes } from 'vue';

const props = defineProps<MenubarCheckboxItemProps & { class?: HTMLAttributes['class'] }>();
const emits = defineEmits<MenubarCheckboxItemEmits>();

const delegatedProps = computed(() => {
    const { class: _, ...delegated } = props;

    return delegated;
});

const forwarded = useForwardPropsEmits(delegatedProps, emits);
</script>

<template>
    <MenubarCheckboxItem
        data-slot="menubar-checkbox-item"
        v-bind="forwarded"
        :class="
            cn(
                `rounded-xs outline-hidden relative flex cursor-default select-none items-center gap-2 py-1.5 pl-8 pr-2 text-sm focus:bg-accent focus:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50 [&_svg:not([class*='size-'])]:size-4 [&_svg]:pointer-events-none [&_svg]:shrink-0`,
                props.class,
            )
        "
    >
        <span class="pointer-events-none absolute left-2 flex size-3.5 items-center justify-center">
            <MenubarItemIndicator>
                <Check class="size-4" />
            </MenubarItemIndicator>
        </span>
        <slot />
    </MenubarCheckboxItem>
</template>
