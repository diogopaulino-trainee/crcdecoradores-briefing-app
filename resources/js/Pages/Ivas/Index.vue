<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, computed, h } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { Button } from '@/registry/new-york-v4/ui/button';
import { DropdownMenu, DropdownMenuTrigger, DropdownMenuContent, DropdownMenuItem } from '@/registry/new-york-v4/ui/dropdown-menu';
import DataTable from '@/Components/ui/data-table/DataTable.vue';
import { Settings, ArrowUp, ArrowDown } from 'lucide-vue-next';
import { usePage } from '@inertiajs/vue3';

const erro = computed(() => usePage().props.flash?.error);

const props = defineProps({
    ivas: Object,
    filtros: Object,
});

const filtrosLocais = ref({
    termo: props.filtros?.termo || '',
});

const debounceTimer = ref(null);

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
    filtrosLocais.value.termo = '';
    aplicarFiltros();
};

const showModal = ref(false);
const ivaAEliminar = ref(null);
const rotatingId = ref(null);

const novo = () => router.get(route('ivas.create'));
const editar = (id) => router.get(route('ivas.edit', id));
const confirmarEliminar = (id) => {
    const iva = props.ivas.data.find((i) => i.id === id);
    if (iva) {
        ivaAEliminar.value = iva;
        showModal.value = true;
    }
};
const eliminar = () => {
    if (ivaAEliminar.value) {
        router.delete(route('ivas.destroy', ivaAEliminar.value.id), {
            onSuccess: () => router.get(route('ivas.index')),
        });
    }
};

const currentSort = computed(() => props.filtros?.sort || 'nome');
const currentDirection = computed(() => props.filtros?.direction || 'asc');

const sortBy = (column) => {
    const isAsc = currentSort.value === column && currentDirection.value === 'asc';
    router.get(
        route(route().current()),
        {
            ...filtrosLocais.value,
            sort: column,
            direction: isAsc ? 'desc' : 'asc',
        },
        { preserveState: true, replace: true },
    );
};

const columns = [
    {
        accessorKey: 'nome',
        header: () =>
            h('div', { class: 'flex items-center gap-1 cursor-pointer', onClick: () => sortBy('nome') }, [
                'Nome',
                currentSort.value === 'nome' ? h(currentDirection.value === 'asc' ? ArrowUp : ArrowDown, { class: 'w-4 h-4' }) : null,
            ]),
        cell: ({ row }) => h('span', row?.nome || '—'),
    },
    {
        accessorKey: 'percentagem',
        header: () =>
            h('div', { class: 'flex items-center gap-1 cursor-pointer', onClick: () => sortBy('percentagem') }, [
                'Percentagem',
                currentSort.value === 'percentagem' ? h(currentDirection.value === 'asc' ? ArrowUp : ArrowDown, { class: 'w-4 h-4' }) : null,
            ]),
        cell: ({ row }) => h('span', `${Number(row?.percentagem).toFixed(2)}%`),
    },
    {
        id: 'acoes',
        header: 'Ações',
        cell: ({ row }) =>
            h(DropdownMenu, null, {
                default: () => [
                    h(DropdownMenuTrigger, { asChild: true }, () =>
                        h(
                            Button,
                            {
                                size: 'icon',
                                variant: 'ghost',
                                class: 'h-8 w-8',
                                onClick: () => {
                                    rotatingId.value = row.id;
                                    setTimeout(() => (rotatingId.value = null), 500);
                                },
                            },
                            () =>
                                h(Settings, {
                                    class: [
                                        '!w-6 !h-6 text-gray-500 transition-transform duration-500',
                                        rotatingId.value === row.id ? 'animate-spin' : '',
                                    ],
                                    'stroke-width': 2,
                                }),
                        ),
                    ),
                    h(DropdownMenuContent, { align: 'end' }, () => [
                        h(DropdownMenuItem, { onClick: () => editar(row.id), class: 'cursor-pointer' }, () => 'Editar'),
                        h(
                            DropdownMenuItem,
                            {
                                onClick: () => confirmarEliminar(row.id),
                                class: 'cursor-pointer text-red-600 hover:text-red-700',
                            },
                            () => 'Eliminar',
                        ),
                    ]),
                ],
            }),
    },
];
</script>

<template>
    <Head title="IVAs - CRCDecoradores" />

    <AppLayout>
        <div class="mb-4 flex flex-col gap-4">
            <h1 class="text-2xl font-bold">IVAs</h1>

            <div v-if="erro" class="mb-4 rounded border border-red-300 bg-red-100 p-3 text-sm text-red-700 shadow-sm">
                {{ erro }}
            </div>

            <!-- Filtros -->
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <input
                    v-model="filtrosLocais.termo"
                    @input="aplicarFiltros"
                    placeholder="Pesquisar por nome ou percentagem"
                    class="rounded border px-3 py-2 text-sm shadow-sm"
                />
            </div>

            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-600">{{ ivas.total }} resultado(s)</div>
                <div class="flex gap-2">
                    <Button variant="outline" @click="limparFiltros">Limpar</Button>
                    <Button @click="novo">Novo IVA</Button>
                </div>
            </div>
        </div>

        <DataTable :columns="columns" :data="ivas.data" class="rounded border shadow" />

        <!-- Paginação -->
        <div class="mt-4 flex justify-center">
            <ul class="flex gap-1">
                <li v-for="link in ivas.links" :key="link.label">
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

        <!-- Modal de Confirmação -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div class="rounded-lg bg-white p-6 shadow-xl">
                <h2 class="mb-4 text-lg font-semibold">Confirmar Eliminação</h2>
                <p class="mb-6">
                    Tens a certeza que queres eliminar o IVA
                    <strong class="text-[#CDAA62]">{{ ivaAEliminar?.nome }}</strong
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
