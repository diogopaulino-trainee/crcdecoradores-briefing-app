<script setup>
import { ref, watch } from 'vue';

const model = defineModel();

const internalValue = ref(model.value);

watch(model, (val) => {
    internalValue.value = val;
});

watch(internalValue, (val) => {
    model.value = val;
});
</script>

<template>
    <input
        type="date"
        v-model="internalValue"
        class="w-full rounded border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[#CDAA62] focus:ring-[#CDAA62]"
    />
</template>
