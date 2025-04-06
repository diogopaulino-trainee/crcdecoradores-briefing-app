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

const clearSelection = () => {
    emit('update:modelValue', null);
};

const selectedText = computed(() => {
    const item = props.items.find((i) => String(i[props.itemValue]) === String(props.modelValue));
    return item ? props.itemText(item) : '';
});

watch(
    () => [props.items, props.modelValue],
    () => {
        search.value = '';
    },
    { immediate: true },
);
</script>

<template>
    <Popover v-model:open="open">
        <PopoverTrigger as-child>
            <button type="button" class="relative w-full min-w-[200px] rounded border bg-white px-3 py-2 text-left text-sm">
                <span :class="[selectedText ? 'text-black' : 'text-gray-400', 'block truncate pr-10']" :title="selectedText || placeholder">
                    {{ selectedText || placeholder }}
                </span>

                <button
                    v-if="selectedText"
                    type="button"
                    @click.stop="clearSelection"
                    class="absolute right-7 top-1/2 -translate-y-1/2 text-gray-400 hover:text-black"
                    title="Limpar seleção"
                >
                    ❌
                </button>

                <span class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400">▼</span>
            </button>
        </PopoverTrigger>

        <PopoverContent class="w-[--radix-popover-trigger-width] p-2" style="max-height: 300px; overflow-y: auto">
            <Input v-model="search" placeholder="Procurar..." class="mb-2" />
            <div class="pr-1">
                <button
                    v-for="item in filteredItems"
                    :key="item[itemValue]"
                    @click="selectItem(item)"
                    class="block w-full rounded px-2 py-1 text-left hover:bg-[#CDAA62]/10"
                    :class="{
                        'bg-[#CDAA62]/10 font-semibold': String(modelValue) === String(item[itemValue]),
                    }"
                    tabindex="-1"
                >
                    {{ itemText(item) }}
                </button>
            </div>
        </PopoverContent>
    </Popover>
</template>
