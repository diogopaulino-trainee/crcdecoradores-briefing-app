<script setup lang="ts">
import { provide } from 'vue';
import { type InertiaForm } from '@inertiajs/vue3';

const props = defineProps<{
    form: InertiaForm<any>;
}>();

provide('form', props.form);
</script>

<template>
    <form :class="$attrs.class" @submit="$emit('submit', $event)">
        <slot />
    </form>
</template>
