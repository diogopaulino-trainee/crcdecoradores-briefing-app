<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, h, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { Button } from '@/registry/new-york-v4/ui/button';
import { DropdownMenu, DropdownMenuTrigger, DropdownMenuContent, DropdownMenuItem } from '@/registry/new-york-v4/ui/dropdown-menu';
import DataTable from '@/Components/ui/data-table/DataTable.vue';
import { Settings, ArrowDown, ArrowUp } from 'lucide-vue-next';

const props = defineProps({
    utilizadores: Object,
    filtros: Object,
});

const filtrosLocais = ref({
    termo: props.filtros.termo || '',
    estado: props.filtros.estado || '',
});

const currentSort = computed(() => props.filtros?.sort || 'name');
const currentDirection = computed(() => props.filtros?.direction || 'asc');

const debounceTimer = ref(null);
const showModal = ref(false);
const utilizadorAEliminar = ref(null);

const aplicarFiltros = () => {
    clearTimeout(debounceTimer.value);
    debounceTimer.value = setTimeout(() => {
        router.get(route('utilizadores.index'), filtrosLocais.value, {
            preserveState: true,
            replace: true,
        });
    }, 200);
};

const limparFiltros = () => {
    filtrosLocais.value = { termo: '', estado: '' };
    aplicarFiltros();
};

const sortBy = (campo) => {
    const isAsc = currentSort.value === campo && currentDirection.value === 'asc';
    router.get(
        route('utilizadores.index'),
        {
            ...filtrosLocais.value,
            sort: campo,
            direction: isAsc ? 'desc' : 'asc',
        },
        {
            preserveState: true,
            replace: true,
        },
    );
};

const editar = (id) => router.get(route('utilizadores.edit', id));
const ver = (id) => router.get(route('utilizadores.show', id));
const confirmarEliminar = (id) => {
    utilizadorAEliminar.value = props.utilizadores.data.find((u) => u.id === id);
    showModal.value = true;
};
const eliminar = () => {
    if (utilizadorAEliminar.value) {
        router.delete(route('utilizadores.destroy', utilizadorAEliminar.value.id));
        showModal.value = false;
    }
};

const columns = [
    {
        accessorKey: 'name',
        header: () =>
            h('div', { class: 'flex items-center gap-1 cursor-pointer', onClick: () => sortBy('name') }, [
                'Nome',
                currentSort.value === 'name' ? h(currentDirection.value === 'asc' ? ArrowUp : ArrowDown, { class: 'w-4 h-4 text-[#CDAA62]' }) : null,
            ]),
        cell: ({ row }) => h('span', row?.name || '—'),
    },
    { accessorKey: 'email', header: 'Email' },
    { accessorKey: 'telemovel', header: 'Telemóvel' },
    {
        accessorKey: 'roles',
        header: 'Grupo de Permissões',
        cell: ({ row }) => h('span', row.roles?.map((r) => r.name).join(', ') || '—'),
    },
    { accessorKey: 'estado', header: 'Estado' },
    {
        id: 'acoes',
        header: 'Ações',
        cell: ({ row }) =>
            h(DropdownMenu, null, {
                default: () => [
                    h(DropdownMenuTrigger, { asChild: true }, () =>
                        h(Button, { size: 'icon', variant: 'ghost', class: 'h-8 w-8' }, () => h(Settings, { class: 'w-5 h-5 text-gray-500' })),
                    ),
                    h(DropdownMenuContent, { align: 'end' }, () => [
                        h(DropdownMenuItem, { onClick: () => ver(row.id), class: 'cursor-pointer' }, () => 'Ver'),
                        h(DropdownMenuItem, { onClick: () => editar(row.id), class: 'cursor-pointer' }, () => 'Editar'),
                        h(DropdownMenuItem, { onClick: () => confirmarEliminar(row.id), class: 'cursor-pointer text-red-600' }, () => 'Eliminar'),
                    ]),
                ],
            }),
    },
];
</script>

<template>
    <Head title="Utilizadores - CRCDecoradores" />
    <AppLayout>
        <div class="mb-4">
            <h1 class="mb-4 text-2xl font-bold">Gestão de Acessos - Utilizadores</h1>

            <div class="mb-4 grid grid-cols-1 gap-4 md:grid-cols-3">
                <input
                    v-model="filtrosLocais.termo"
                    @input="aplicarFiltros"
                    placeholder="Pesquisar nome ou email"
                    class="col-span-3 rounded border px-3 py-2 text-sm shadow-sm"
                />
                <select v-model="filtrosLocais.estado" @change="aplicarFiltros" class="rounded border px-3 py-2 text-sm shadow-sm">
                    <option value="">Todos os estados</option>
                    <option value="Ativo">Ativo</option>
                    <option value="Inativo">Inativo</option>
                </select>
            </div>

            <div class="mb-2 flex items-center justify-between">
                <span class="text-sm text-gray-600">{{ utilizadores.total }} resultado(s)</span>
                <div class="flex gap-2">
                    <Button variant="outline" @click="limparFiltros">Limpar tudo</Button>
                    <Button @click="router.get(route('utilizadores.create'))">Novo Utilizador</Button>
                </div>
            </div>

            <DataTable :columns="columns" :data="utilizadores.data" />
        </div>

        <!-- Modal Eliminar -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div class="rounded-lg bg-white p-6 shadow-lg">
                <h2 class="mb-4 text-lg font-semibold">Eliminar Utilizador</h2>
                <p class="mb-6">
                    Tens a certeza que queres eliminar <strong>{{ utilizadorAEliminar?.name }}</strong
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
