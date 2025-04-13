<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, computed, h } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { Button } from '@/registry/new-york-v4/ui/button';
import DataTable from '@/Components/ui/data-table/DataTable.vue';
import { ArrowUp, ArrowDown, Trash2 } from 'lucide-vue-next';

const props = defineProps({
    logs: Object,
    filtros: {
        type: Object,
        default: () => ({}),
    },
});

const filtrosLocais = ref({
    termo: props.filtros.termo || '',
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

const currentSort = computed(() => props.filtros?.sort || 'created_at');
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
        { preserveState: true, replace: true },
    );
};

const showModalEliminar = ref(false);
const showModalEliminarTodos = ref(false);
const logAEliminar = ref(null);

const confirmarEliminar = (id) => {
    const log = props.logs.data.find((l) => l.id === id);
    if (log) {
        logAEliminar.value = log;
        showModalEliminar.value = true;
    }
};

const eliminar = () => {
    if (logAEliminar.value) {
        router.delete(route('logs.destroy', logAEliminar.value.id), {
            onSuccess: () => {
                showModalEliminar.value = false;
                logAEliminar.value = null;
            },
        });
    }
};

const confirmarEliminarTodos = () => {
    showModalEliminarTodos.value = true;
};

const eliminarTodos = () => {
    router.post(
        route('logs.clearAll'),
        {},
        {
            onSuccess: () => {
                showModalEliminarTodos.value = false;
            },
        },
    );
};

const columns = [
    {
        accessorKey: 'data',
        header: () =>
            h('div', { class: 'flex items-center gap-1 cursor-pointer', onClick: () => sortBy('created_at') }, [
                'Data',
                currentSort.value === 'created_at' ? h(currentDirection.value === 'asc' ? ArrowUp : ArrowDown, { class: 'w-4 h-4' }) : null,
            ]),
        cell: ({ row }) => h('span', new Date(row.created_at).toLocaleDateString('pt-PT')),
    },
    {
        accessorKey: 'hora',
        header: 'Hora',
        cell: ({ row }) => h('span', new Date(row.created_at).toLocaleTimeString('pt-PT', { hour: '2-digit', minute: '2-digit' })),
    },
    {
        accessorKey: 'user',
        header: 'Utilizador',
        cell: ({ row }) => h('span', row.causer?.name || '—'),
    },
    {
        accessorKey: 'log_name',
        header: 'Menu',
        cell: ({ row }) => h('span', row.log_name || '—'),
    },
    {
        accessorKey: 'description',
        header: 'Ação',
        cell: ({ row }) => h('span', row.description || '—'),
    },
    {
        accessorKey: 'properties.user_agent',
        header: 'Dispositivo',
        cell: ({ row }) => h('span', row.properties?.user_agent?.slice(0, 30) || '—'),
    },
    {
        accessorKey: 'properties.ip',
        header: 'IP',
        cell: ({ row }) => h('span', row.properties?.ip || '—'),
    },
    {
        id: 'acoes',
        header: 'Ações',
        cell: ({ row }) =>
            h(
                Button,
                {
                    variant: 'ghost',
                    size: 'icon',
                    title: 'Eliminar',
                    onClick: () => confirmarEliminar(row.id),
                    class: 'text-red-500 hover:text-red-700',
                },
                () => h(Trash2, { class: 'w-5 h-5' }),
            ),
    },
];
</script>

<template>
    <Head title="Logs - CRCDecoradores" />

    <AppLayout>
        <div class="mb-4 flex flex-col gap-4">
            <h1 class="text-2xl font-bold">Logs</h1>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <input
                    v-model="filtrosLocais.termo"
                    @input="aplicarFiltros"
                    placeholder="Pesquisar utilizador, menu ou ação"
                    class="col-span-3 rounded border px-3 py-2 text-sm shadow-sm"
                />
            </div>

            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-600">{{ logs.total }} resultado(s)</div>
                <div class="flex gap-2">
                    <Button variant="outline" @click="limparFiltros">Limpar tudo</Button>
                    <Button variant="destructive" class="bg-[#CDAA62] text-white hover:bg-[#b38f52]" @click="confirmarEliminarTodos">
                        Eliminar Todos
                    </Button>
                </div>
            </div>
        </div>

        <DataTable :columns="columns" :data="logs.data" class="rounded border shadow" />

        <div class="mt-4 flex justify-center">
            <ul class="flex gap-1">
                <li v-for="link in logs.links" :key="link.label">
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

        <!-- Modal Eliminar Individual -->
        <div v-if="showModalEliminar" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div class="animate-fade-in w-[90%] max-w-md rounded-xl bg-white p-6 shadow-xl">
                <h2 class="mb-4 text-lg font-semibold text-gray-800">Confirmar Eliminação</h2>
                <p class="mb-6 text-sm text-gray-700">
                    Queres eliminar o log de
                    <strong class="text-[#CDAA62]">{{ logAEliminar?.causer?.name || '—' }}</strong>
                    com ação
                    <strong class="text-[#CDAA62]">{{ logAEliminar?.description || '—' }}</strong
                    >?
                </p>
                <div class="flex justify-end gap-2">
                    <Button variant="outline" @click="showModalEliminar = false">Cancelar</Button>
                    <Button variant="destructive" class="bg-[#CDAA62] text-white hover:bg-[#b38f52]" @click="eliminar"> Eliminar </Button>
                </div>
            </div>
        </div>

        <!-- Modal Eliminar Todos -->
        <div v-if="showModalEliminarTodos" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div class="animate-fade-in w-[90%] max-w-md rounded-xl bg-white p-6 shadow-xl">
                <h2 class="mb-4 text-lg font-semibold text-gray-800">Eliminar Todos os Logs</h2>
                <p class="mb-6 text-sm text-gray-700">
                    Esta ação vai eliminar <strong class="text-[#CDAA62]">todos os logs</strong> do sistema. Tens a certeza?
                </p>
                <div class="flex justify-end gap-2">
                    <Button variant="outline" @click="showModalEliminarTodos = false">Cancelar</Button>
                    <Button variant="destructive" class="bg-[#CDAA62] text-white hover:bg-[#b38f52]" @click="eliminarTodos"> Eliminar Todos </Button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
