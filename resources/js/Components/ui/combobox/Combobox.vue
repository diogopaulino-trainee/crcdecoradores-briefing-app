<script setup>
import { ref, computed, watch } from 'vue';
import { Input } from '@/registry/new-york-v4/ui/input';
import { Popover, PopoverContent, PopoverTrigger } from '@/registry/new-york-v4/ui/popover';

const props = defineProps({
    modelValue: [String, Number],
    items: Array,
    placeholder: String,
    searchFields: Array,
    itemLabel: String,
    itemValue: String,
    itemText: Function,
});

const emit = defineEmits(['update:modelValue']);

const open = ref(false);
const search = ref('');

const filteredItems = computed(() => {
    if (!search.value) return props.items;
    const term = search.value.toLowerCase();
    return props.items.filter((item) => props.searchFields.some((field) => String(item[field]).toLowerCase().includes(term)));
});

const selectItem = (item) => {
    emit('update:modelValue', item[props.itemValue]);
    open.value = false;
};

const selectedText = computed(() => {
    const item = props.items.find((i) => i[props.itemValue] === props.modelValue);
    return item ? props.itemText(item) : '';
});
</script>

<template>
    <Popover v-model:open="open">
        <PopoverTrigger as-child>
            <Input :value="selectedText" :placeholder="placeholder" readonly @click="open = true" />
        </PopoverTrigger>
        <PopoverContent class="w-[--radix-popover-trigger-width] p-2" style="max-height: 300px; overflow-y: auto">
            <Input v-model="search" placeholder="Procurar..." class="mb-2" />
            <div class="pr-1">
                <button
                    v-for="item in filteredItems"
                    :key="item[itemValue]"
                    @click="selectItem(item)"
                    class="block w-full rounded px-2 py-1 text-left hover:bg-[#CDAA62]/10"
                    :class="{ 'bg-[#CDAA62]/10 font-semibold': modelValue === item[itemValue] }"
                >
                    {{ itemText(item) }}
                </button>
            </div>
        </PopoverContent>
    </Popover>
</template>
