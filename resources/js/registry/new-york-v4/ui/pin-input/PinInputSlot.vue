<script setup lang="ts">
import { cn } from '@/lib/utils';
import { PinInputInput, type PinInputInputProps, useForwardProps } from 'reka-ui';
import { computed, type HTMLAttributes } from 'vue';

const props = defineProps<PinInputInputProps & { class?: HTMLAttributes['class'] }>();

const delegatedProps = computed(() => {
    const { class: _, ...delegated } = props;
    return delegated;
});

const forwardedProps = useForwardProps(delegatedProps);
</script>

<template>
    <PinInputInput
        data-slot="pin-input-slot"
        v-bind="forwardedProps"
        :class="
            cn(
                'focus:aria-invalid:ring-destructive/20 dark:focus:aria-invalid:ring-destructive/40 aria-invalid:border-destructive focus:aria-invalid:border-destructive shadow-xs relative flex h-9 w-9 items-center justify-center border-y border-r border-input text-center text-sm outline-none transition-all first:rounded-l-md first:border-l last:rounded-r-md focus:z-10 focus:border-ring focus:ring-[3px] focus:ring-ring/50 dark:bg-input/30',
                props.class,
            )
        "
    />
</template>
