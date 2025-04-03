<script setup lang="ts">
import { cn } from '@/lib/utils';
import { useFormField } from './useFormField';
import { computed, inject } from 'vue';
import type { InertiaForm } from '@inertiajs/vue3';

const props = defineProps<{
    class?: string;
}>();

const { name, formMessageId } = useFormField();
const form = inject<InertiaForm<any>>('form', {} as InertiaForm<any>);

const error = computed(() => {
    const fieldName = name.value;
    return form?.errors?.[fieldName] || '';
});
</script>

<template>
    <p v-if="error" :id="formMessageId" data-slot="form-message" class="text-sm text-red-500" :class="props.class">
        {{ error }}
    </p>
</template>
