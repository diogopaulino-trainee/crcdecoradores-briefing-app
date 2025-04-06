<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, h, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { Button } from '@/registry/new-york-v4/ui/button';
import { DropdownMenu, DropdownMenuTrigger, DropdownMenuContent, DropdownMenuItem } from '@/registry/new-york-v4/ui/dropdown-menu';
import DataTable from '@/Components/ui/data-table/DataTable.vue';
import { Settings, ArrowDown, ArrowUp } from 'lucide-vue-next';

const props = defineProps({
    encomendas: Object,
    filtro: String,
    filtros: {
        type: Object,
        default: () => ({}),
    },
});

const currentSort = computed(() => props.filtros?.sort || 'data_da_proposta');
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

const filtrosLocais = ref({
    termo: props.filtros.termo || '',
    estado: props.filtros.estado || '',
});
const debounceTimer = ref(null);
const showModal = ref(false);
const showConverterModal = ref(false);
const encomendaAEliminar = ref(null);
const encomendaAConverter = ref(null);
const rotatingId = ref(null);

const aplicarFiltros = () => {
    clearTimeout(debounceTimer.value);
    debounceTimer.value = setTimeout(() => {
        router.get(route(route().current()), filtrosLocais.value, {
            preserveState: true,
            replace: true,
        });
    }, 200);
};

const limparFiltros = () => {
    filtrosLocais.value = { termo: '', estado: '' };
    aplicarFiltros();
};

// Redireciona conforme o filtro atual
const irParaIndex = () => {
    switch (props.filtro) {
        case 'clientes':
            router.get(route('encomendas.clientes'));
            break;
        case 'fornecedores':
            router.get(route('encomendas.fornecedores'));
            break;
        default:
            router.get(route('encomendas.index'));
    }
};

const nova = () => router.get(route('encomendas.create', { tipo: props.filtro }));
const ver = (id) => router.get(route('encomendas.show', id));
const editar = (id) => router.get(route('encomendas.edit', id));

const confirmarEliminar = (id) => {
    encomendaAEliminar.value = props.encomendas.data.find((e) => e.id === id);
    showModal.value = true;
};

const eliminar = () => {
    if (encomendaAEliminar.value) {
        router.delete(route('encomendas.destroy', encomendaAEliminar.value.id), {
            onSuccess: () => irParaIndex(),
        });
    }
};

const confirmarConversao = (id) => {
    encomendaAConverter.value = props.encomendas.data.find((e) => e.id === id);
    showConverterModal.value = true;
};

const converter = () => {
    if (encomendaAConverter.value) {
        router.post(
            route('encomendas.converter', encomendaAConverter.value.id),
            {},
            {
                onSuccess: () => {
                    showConverterModal.value = false;
                    irParaIndex();
                },
            },
        );
    }
};

const tipoLabel = computed(() => {
    if (props.filtro === 'clientes') return 'Clientes';
    if (props.filtro === 'fornecedores') return 'Fornecedores';
    return 'Todas';
});

const columns = [
    {
        accessorKey: 'data_da_proposta',
        header: () =>
            h('div', { class: 'flex items-center gap-1 cursor-pointer', onClick: () => sortBy('data_da_proposta') }, [
                'Data',
                currentSort.value === 'data_da_proposta'
                    ? h(currentDirection.value === 'asc' ? ArrowUp : ArrowDown, { class: 'w-4 h-4 text-[#CDAA62]' })
                    : null,
            ]),
        cell: ({ row }) => h('span', row?.data_da_proposta ? new Date(row.data_da_proposta).toLocaleDateString() : '—'),
    },
    {
        accessorKey: 'numero',
        header: 'Número',
        cell: ({ row }) => h('span', row?.numero || '—'),
    },
    {
        accessorKey: 'validade',
        header: 'Validade',
        cell: ({ row }) => h('span', row?.validade ? new Date(row?.validade).toLocaleDateString() : '—'),
    },
    {
        accessorFn: (row) => row.cliente?.nome,
        header: 'Cliente',
        cell: ({ row }) => h('span', row?.cliente?.nome || '—'),
    },
    {
        accessorKey: 'valor_total',
        header: () =>
            h('div', { class: 'flex items-center gap-1 cursor-pointer', onClick: () => sortBy('valor_total') }, [
                'Valor Total',
                currentSort.value === 'valor_total'
                    ? h(currentDirection.value === 'asc' ? ArrowUp : ArrowDown, { class: 'w-4 h-4 text-[#CDAA62]' })
                    : null,
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
                            {
                                onClick: () => confirmarEliminar(id),
                                class: 'cursor-pointer text-red-600 hover:text-red-700',
                            },
                            () => 'Eliminar',
                        ),
                        h(
                            DropdownMenuItem,
                            {
                                class: 'cursor-pointer text-[#CDAA62] hover:text-[#a17d3b]',
                                onClick: () => window.open(route('encomendas.download', id), '_blank'),
                            },
                            () => 'Download PDF',
                        ),
                        row?.estado === 'Fechado' &&
                            props.filtro !== 'fornecedores' &&
                            h(
                                DropdownMenuItem,
                                {
                                    class: 'cursor-pointer text-[#CDAA62] hover:text-[#a17d3b]',
                                    onClick: () => confirmarConversao(id),
                                },
                                () => 'Converter para Encomenda de Fornecedor',
                            ),
                    ]),
                ],
            });
        },
    },
];
</script>

<template>
    <Head :title="`Encomendas (${tipoLabel}) - CRCDecoradores`" />
    <AppLayout>
        <div class="mb-4 flex flex-col gap-4">
            <h1 class="text-2xl font-bold">Encomendas ({{ tipoLabel }})</h1>

            <!-- Filtros -->
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <input
                    v-model="filtrosLocais.termo"
                    @input="aplicarFiltros"
                    placeholder="Pesquisar número ou cliente"
                    class="col-span-3 rounded border px-3 py-2 text-sm shadow-sm"
                />
                <select v-model="filtrosLocais.estado" @change="aplicarFiltros" class="rounded border px-3 py-2 text-sm shadow-sm">
                    <option value="">Todos os estados</option>
                    <option value="Rascunho">Rascunho</option>
                    <option value="Fechado">Fechado</option>
                </select>
            </div>

            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-600">{{ encomendas.total }} resultado(s)</div>
                <div class="flex gap-2">
                    <Button variant="outline" @click="limparFiltros">Limpar tudo</Button>
                    <Button @click="nova">Nova Encomenda</Button>
                </div>
            </div>
        </div>

        <DataTable :columns="columns" :data="encomendas.data" class="rounded border shadow" />

        <!-- Modal Eliminar -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div class="rounded-lg bg-white p-6 shadow-xl">
                <h2 class="mb-4 text-lg font-semibold">Confirmar Eliminação</h2>
                <p class="mb-6">
                    Tens a certeza que queres eliminar a encomenda
                    <strong class="text-[#CDAA62]">{{ encomendaAEliminar?.numero }}</strong
                    >?
                </p>
                <div class="flex justify-end gap-2">
                    <Button variant="outline" @click="showModal = false">Cancelar</Button>
                    <Button variant="destructive" class="bg-[#CDAA62] text-white hover:bg-[#b38f52]" @click="eliminar">Eliminar</Button>
                </div>
            </div>
        </div>

        <!-- Modal Converter -->
        <div v-if="showConverterModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div class="rounded-lg bg-white p-6 shadow-xl">
                <h2 class="mb-4 text-lg font-semibold">Converter Encomenda</h2>
                <p class="mb-6">
                    Queres mesmo converter a encomenda
                    <strong class="text-[#CDAA62]">{{ encomendaAConverter?.numero }}</strong>
                    para Encomenda de Fornecedor?
                </p>
                <div class="flex justify-end gap-2">
                    <Button variant="outline" @click="showConverterModal = false">Cancelar</Button>
                    <Button class="bg-[#CDAA62] text-white hover:bg-[#b38f52]" @click="converter">Converter</Button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
