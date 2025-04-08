<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, computed, h } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { Button } from '@/registry/new-york-v4/ui/button';
import { DropdownMenu, DropdownMenuTrigger, DropdownMenuContent, DropdownMenuItem } from '@/registry/new-york-v4/ui/dropdown-menu';
import DataTable from '@/Components/ui/data-table/DataTable.vue';
import { Settings, ArrowUp, ArrowDown } from 'lucide-vue-next';

const props = defineProps({
    faturas: Object,
    filtros: {
        type: Object,
        default: () => ({}),
    },
});

const filtrosLocais = ref({
    termo: props.filtros.termo || '',
    estado: props.filtros.estado || '',
});

const debounceTimer = ref(null);

const aplicarFiltros = () => {
    clearTimeout(debounceTimer.value);
    debounceTimer.value = setTimeout(() => {
        router.get(route(route().current()), filtrosLocais.value, {
            preserveState: true,
            replace: true,
        });
    }, 100);
};

const limparFiltros = () => {
    filtrosLocais.value = { termo: '', estado: '' };
    aplicarFiltros();
};

const showModal = ref(false);
const faturaAEliminar = ref(null);

const nova = () => router.get(route('faturas.create'));
const ver = (id) => router.get(route('faturas.show', id));
const editar = (id) => router.get(route('faturas.edit', id));
const confirmarEliminar = (id) => {
    const fatura = props.faturas.data.find((f) => f.id === id);
    if (fatura) {
        faturaAEliminar.value = fatura;
        showModal.value = true;
    }
};
const eliminar = () => {
    if (faturaAEliminar.value) {
        router.delete(route('faturas.destroy', { faturaFornecedor: faturaAEliminar.value.id }), {
            onSuccess: () => router.get(route('faturas.index')),
        });
    }
};

const currentSort = computed(() => props.filtros?.sort || 'data_da_fatura');
const currentDirection = computed(() => props.filtros?.direction || 'desc');

const sortBy = (column) => {
    const isAsc = currentSort.value === column && currentDirection.value === 'asc';
    router.get(
        route(route().current()),
        {
            ...filtrosLocais.value,
            sort: column,
            direction: isAsc ? 'desc' : 'asc',
        },
        {
            preserveState: true,
            replace: true,
        },
    );
};

const rotatingId = ref(null);

const columns = [
    {
        accessorKey: 'data_da_fatura',
        header: () =>
            h('div', { class: 'flex items-center gap-1 cursor-pointer', onClick: () => sortBy('data_da_fatura') }, [
                'Data',
                currentSort.value === 'data_da_fatura' ? h(currentDirection.value === 'asc' ? ArrowUp : ArrowDown, { class: 'w-4 h-4' }) : null,
            ]),
        cell: ({ row }) => h('span', new Date(row?.data_da_fatura).toLocaleDateString()),
    },
    {
        accessorKey: 'numero',
        header: () =>
            h('div', { class: 'flex items-center gap-1 cursor-pointer', onClick: () => sortBy('numero') }, [
                'Número',
                currentSort.value === 'numero' ? h(currentDirection.value === 'asc' ? ArrowUp : ArrowDown, { class: 'w-4 h-4' }) : null,
            ]),
        cell: ({ row }) => h('span', row?.numero || '—'),
    },
    {
        accessorFn: (row) => row.fornecedor?.nome,
        header: () =>
            h('div', { class: 'flex items-center gap-1 cursor-pointer', onClick: () => sortBy('fornecedor_id') }, [
                'Fornecedor',
                currentSort.value === 'fornecedor_id' ? h(currentDirection.value === 'asc' ? ArrowUp : ArrowDown, { class: 'w-4 h-4' }) : null,
            ]),
        cell: ({ row }) => h('span', row?.fornecedor?.nome || '—'),
    },
    {
        accessorKey: 'valor_total',
        header: () =>
            h('div', { class: 'flex items-center gap-1 cursor-pointer', onClick: () => sortBy('valor_total') }, [
                'Valor Total',
                currentSort.value === 'valor_total' ? h(currentDirection.value === 'asc' ? ArrowUp : ArrowDown, { class: 'w-4 h-4' }) : null,
            ]),
        cell: ({ row }) => h('span', `€ ${parseFloat(row?.valor_total || 0).toFixed(2)}`),
    },
    {
        accessorKey: 'estado',
        header: 'Estado',
        cell: ({ row }) => h('span', row?.estado || '—'),
    },
    {
        id: 'acoes',
        header: 'Ações',
        cell: ({ row }) => {
            const id = row?.id;
            return h(DropdownMenu, null, {
                default: () => [
                    h(DropdownMenuTrigger, { asChild: true }, () =>
                        h(
                            Button,
                            {
                                size: 'icon',
                                variant: 'ghost',
                                class: 'h-8 w-8',
                                onClick: () => {
                                    rotatingId.value = id;
                                    setTimeout(() => (rotatingId.value = null), 500);
                                },
                            },
                            () =>
                                h(Settings, {
                                    class: [
                                        '!w-6 !h-6 text-gray-500 transition-transform duration-500',
                                        rotatingId.value === id ? 'animate-spin' : '',
                                    ],
                                    'stroke-width': 2,
                                }),
                        ),
                    ),
                    h(DropdownMenuContent, { align: 'end' }, () => [
                        h(DropdownMenuItem, { onClick: () => ver(id), class: 'cursor-pointer' }, () => 'Ver'),
                        h(DropdownMenuItem, { onClick: () => editar(id), class: 'cursor-pointer' }, () => 'Editar'),
                        h(
                            DropdownMenuItem,
                            { onClick: () => confirmarEliminar(id), class: 'cursor-pointer text-red-600 hover:text-red-700' },
                            () => 'Eliminar',
                        ),
                    ]),
                ],
            });
        },
    },
];
</script>

<template>
    <Head title="Faturas de Fornecedor - CRCDecoradores" />

    <AppLayout>
        <div class="mb-4 flex flex-col gap-4">
            <h1 class="text-2xl font-bold">Faturas de Fornecedor</h1>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <input
                    v-model="filtrosLocais.termo"
                    @input="aplicarFiltros"
                    placeholder="Pesquisar número ou fornecedor"
                    class="col-span-3 rounded border px-3 py-2 text-sm shadow-sm"
                />

                <select v-model="filtrosLocais.estado" @change="aplicarFiltros" class="rounded border px-3 py-2 text-sm shadow-sm">
                    <option value="">Todos os estados</option>
                    <option value="Pendente">Pendente</option>
                    <option value="Paga">Paga</option>
                </select>
            </div>

            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-600">{{ faturas.total }} resultado(s)</div>
                <div class="flex gap-2">
                    <Button variant="outline" @click="limparFiltros">Limpar tudo</Button>
                    <Button @click="nova">Nova Fatura</Button>
                </div>
            </div>
        </div>

        <DataTable :columns="columns" :data="faturas.data" class="rounded border shadow" />

        <div class="mt-4 flex justify-center">
            <ul class="flex gap-1">
                <li v-for="link in faturas.links" :key="link.label">
                    <button
                        v-if="link.url"
                        @click="router.get(link.url)"
                        class="px-3 py-1 text-sm"
                        :class="{
                            'font-bold text-[#CDAA62] underline': link.active,
                            'text-gray-500 hover:text-[#CDAA62]': !link.active,
                        }"
                        v-html="link.label"
                    />
                    <span v-else class="px-3 py-1 text-sm text-gray-400" v-html="link.label" />
                </li>
            </ul>
        </div>

        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div class="rounded-lg bg-white p-6 shadow-xl">
                <h2 class="mb-4 text-lg font-semibold">Confirmar Eliminação</h2>
                <p class="mb-6">
                    Tens a certeza que queres eliminar a fatura
                    <strong class="text-[#CDAA62]">{{ faturaAEliminar?.numero }}</strong
                    >?
                </p>
                <div class="flex justify-end gap-2">
                    <Button variant="outline" @click="showModal = false">Cancelar</Button>
                    <Button variant="destructive" class="bg-[#CDAA62] text-white hover:bg-[#b38f52]" @click="eliminar">Eliminar</Button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
