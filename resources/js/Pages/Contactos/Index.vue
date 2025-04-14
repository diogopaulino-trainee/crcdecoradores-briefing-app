<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, computed, h } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { Button } from '@/registry/new-york-v4/ui/button';
import { DropdownMenu, DropdownMenuTrigger, DropdownMenuContent, DropdownMenuItem } from '@/registry/new-york-v4/ui/dropdown-menu';
import DataTable from '@/Components/ui/data-table/DataTable.vue';
import { Settings, ArrowUp, ArrowDown } from 'lucide-vue-next';

const props = defineProps({
    contactos: Object,
    filtros: Object,
    entidades: Array,
});

const filtrosLocais = ref({
    nome: props.filtros.nome || '',
    estado: props.filtros.estado || '',
    consentimento_rgpd: props.filtros.consentimento_rgpd || '',
    entidade_id: props.filtros.entidade_id || '',
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
    filtrosLocais.value = {
        nome: '',
        estado: '',
        consentimento_rgpd: '',
        entidade_id: '',
    };
    aplicarFiltros();
};

const showModal = ref(false);
const contactoAEliminar = ref(null);
const rotatingId = ref(null);

const nova = () => router.get(route('contactos.create'));
const editar = (id) => router.get(route('contactos.edit', id));
const ver = (id) => router.get(route('contactos.show', id));
const confirmarEliminar = (id) => {
    const contacto = props.contactos.data.find((c) => c.id === id);
    if (contacto) {
        contactoAEliminar.value = contacto;
        showModal.value = true;
    }
};
const eliminar = () => {
    if (contactoAEliminar.value) {
        router.delete(route('contactos.destroy', contactoAEliminar.value.id), {
            onSuccess: () => {
                router.get(route('contactos.index'));
            },
        });
    }
};

const currentSort = computed(() => props.filtros.sort || 'primeiro_nome');
const currentDirection = computed(() => props.filtros.direction || 'asc');

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

const columns = [
    {
        accessorKey: 'primeiro_nome',
        header: () =>
            h('div', { class: 'flex items-center gap-1 cursor-pointer', onClick: () => sortBy('primeiro_nome') }, [
                'Primeiro Nome',
                currentSort.value === 'primeiro_nome' ? h(currentDirection.value === 'asc' ? ArrowUp : ArrowDown, { class: 'w-4 h-4' }) : null,
            ]),
        cell: ({ row }) => h('span', row?.primeiro_nome || '—'),
    },
    {
        accessorKey: 'apelido',
        header: () =>
            h('div', { class: 'flex items-center gap-1 cursor-pointer', onClick: () => sortBy('apelido') }, [
                'Apelido',
                currentSort.value === 'apelido' ? h(currentDirection.value === 'asc' ? ArrowUp : ArrowDown, { class: 'w-4 h-4' }) : null,
            ]),
        cell: ({ row }) => h('span', row?.apelido || '—'),
    },
    {
        accessorKey: 'funcao.nome',
        header: () =>
            h('div', { class: 'flex items-center gap-1 cursor-pointer', onClick: () => sortBy('funcao') }, [
                'Função',
                currentSort.value === 'funcao' ? h(currentDirection.value === 'asc' ? ArrowUp : ArrowDown, { class: 'w-4 h-4' }) : null,
            ]),
        cell: ({ row }) => h('span', row?.funcao?.nome || '—'),
    },
    {
        header: () =>
            h('div', { class: 'flex items-center gap-1 cursor-pointer', onClick: () => sortBy('entidade_nome') }, [
                'Entidade',
                currentSort.value === 'entidade_nome' ? h(currentDirection.value === 'asc' ? ArrowUp : ArrowDown, { class: 'w-4 h-4' }) : null,
            ]),
        accessorFn: (row) => row.entidade?.nome,
        cell: ({ row }) => h('span', row?.entidade?.nome || '—'),
    },
    {
        accessorKey: 'telefone',
        header: 'Telefone',
        cell: ({ row }) => h('span', row?.telefone || ''),
    },
    {
        accessorKey: 'telemovel',
        header: 'Telemóvel',
        cell: ({ row }) => h('span', row?.telemovel || ''),
    },
    {
        accessorKey: 'email',
        header: 'Email',
        cell: ({ row }) => h('span', row?.email || ''),
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
    <Head :title="`Contactos - CRCDecoradores`" />

    <AppLayout>
        <div class="mb-4 flex flex-col gap-4">
            <h1 class="text-2xl font-bold">Contactos</h1>

            <!-- Filtros Avançados -->
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <input
                    v-model="filtrosLocais.nome"
                    @input="aplicarFiltros"
                    placeholder="Nome"
                    class="col-span-3 rounded border px-3 py-2 text-sm shadow-sm"
                />
                <select v-model="filtrosLocais.entidade_id" @change="aplicarFiltros" class="rounded border px-3 py-2 text-sm shadow-sm">
                    <option value="">Selecionar Entidade</option>
                    <option v-for="entidade in props.entidades" :key="entidade.id" :value="entidade.id">{{ entidade.nome }}</option>
                </select>
                <select v-model="filtrosLocais.estado" @change="aplicarFiltros" class="rounded border px-3 py-2 text-sm shadow-sm">
                    <option value="">Todos os estados</option>
                    <option value="Ativo">Ativo</option>
                    <option value="Inativo">Inativo</option>
                </select>
                <select v-model="filtrosLocais.consentimento_rgpd" @change="aplicarFiltros" class="rounded border px-3 py-2 text-sm shadow-sm">
                    <option value="">Todos os consentimentos RGPD</option>
                    <option value="Sim">Sim</option>
                    <option value="Não">Não</option>
                </select>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-2">
                <div class="text-sm text-gray-600">{{ contactos.total }} resultado(s) encontrados</div>
                <div class="flex gap-2">
                    <Button @click="limparFiltros" variant="outline">Limpar tudo</Button>
                    <Button @click="nova">Novo Contacto</Button>
                </div>
            </div>
        </div>

        <DataTable :columns="columns" :data="contactos.data" class="rounded border shadow" />

        <!-- Paginação -->
        <div class="mt-4 flex justify-center">
            <ul class="flex gap-1">
                <li v-for="link in contactos.links" :key="link.label">
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
                    Tens a certeza que queres eliminar o contacto
                    <strong class="text-[#CDAA62]">{{ contactoAEliminar?.primeiro_nome }} {{ contactoAEliminar?.apelido }}</strong
                    >?
                </p>
                <div class="flex justify-end gap-2">
                    <Button variant="outline" @click="showModal = false">Cancelar</Button>
                    <Button variant="destructive" class="bg-[#CDAA62] text-white hover:bg-[#b38f52]" @click="eliminar"> Eliminar </Button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
