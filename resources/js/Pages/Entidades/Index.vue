<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, h, computed, watch } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { Button } from '@/registry/new-york-v4/ui/button';
import { DropdownMenu, DropdownMenuTrigger, DropdownMenuContent, DropdownMenuItem } from '@/registry/new-york-v4/ui/dropdown-menu';
import DataTable from '@/Components/ui/data-table/DataTable.vue';
import { Settings, ArrowUp, ArrowDown } from 'lucide-vue-next';

const props = defineProps({
    entidades: Object,
    filtro: String,
    filtros: Object,
    paises: Array,
});

const filtrosLocais = ref({
    nome: props.filtros.nome || '',
    nif: props.filtros.nif || '',
    email: props.filtros.email || '',
    estado: props.filtros.estado || '',
    consentimento_rgpd: props.filtros.consentimento_rgpd || '',
    pais_id: props.filtros.pais_id || '',
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
        nif: '',
        email: '',
        estado: '',
        consentimento_rgpd: '',
        pais_id: '',
    };
    aplicarFiltros();
};

const showModal = ref(false);
const entidadeAEliminar = ref(null);
const rotatingId = ref(null);

const nova = () => router.get(route('entidades.create'), { tipo: props.filtro });
const editar = (id) => router.get(route('entidades.edit', id));
const confirmarEliminar = (id) => {
    const entidade = props.entidades.data.find((e) => e.id === id);
    if (entidade) {
        entidadeAEliminar.value = entidade;
        showModal.value = true;
    }
};
const eliminar = () => {
    if (entidadeAEliminar.value) {
        router.delete(route('entidades.destroy', entidadeAEliminar.value.id), {
            onSuccess: () => {
                router.get(
                    props.filtro === 'cliente'
                        ? route('clientes.index')
                        : props.filtro === 'fornecedor'
                          ? route('fornecedores.index')
                          : route('entidades.index'),
                );
            },
        });
    }
};

const currentSort = computed(() => props.filtros.sort || 'nome');
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
        accessorKey: 'nif',
        header: () =>
            h('div', { class: 'flex items-center gap-1 cursor-pointer', onClick: () => sortBy('nif') }, [
                'NIF',
                currentSort.value === 'nif' ? h(currentDirection.value === 'asc' ? ArrowUp : ArrowDown, { class: 'w-4 h-4' }) : null,
            ]),
    },
    {
        accessorKey: 'nome',
        header: () =>
            h('div', { class: 'flex items-center gap-1 cursor-pointer', onClick: () => sortBy('nome') }, [
                'Nome',
                currentSort.value === 'nome' ? h(currentDirection.value === 'asc' ? ArrowUp : ArrowDown, { class: 'w-4 h-4' }) : null,
            ]),
    },
    { accessorKey: 'telefone', header: 'Telefone' },
    { accessorKey: 'telemovel', header: 'Telemóvel' },
    { accessorKey: 'website', header: 'Website' },
    { accessorKey: 'email', header: 'Email' },
    {
        id: 'acoes',
        header: 'Ações',
        cell: ({ row }) => {
            const id = row?.original?.id ?? row?.id;
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
                        h(DropdownMenuItem, { onClick: () => router.get(route('entidades.show', id)), class: 'cursor-pointer' }, () => 'Ver'),
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
    <Head :title="`Entidades (${filtro}) - CRCDecoradores`" />

    <AppLayout>
        <div class="mb-4 flex flex-col gap-4">
            <h1 class="text-2xl font-bold">Entidades ({{ filtro }})</h1>

            <!-- Filtros Avançados -->
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <input v-model="filtrosLocais.nome" @input="aplicarFiltros" placeholder="Nome" class="rounded border px-3 py-2 text-sm shadow-sm" />
                <input
                    v-model="filtrosLocais.nif"
                    @input="aplicarFiltros"
                    placeholder="NIF com prefixo (ex: PT123456789)"
                    class="rounded border px-3 py-2 text-sm shadow-sm"
                />
                <input v-model="filtrosLocais.email" @input="aplicarFiltros" placeholder="Email" class="rounded border px-3 py-2 text-sm shadow-sm" />
                <select v-model="filtrosLocais.estado" @change="aplicarFiltros" class="rounded border px-3 py-2 text-sm shadow-sm">
                    <option value="">Todos os estados</option>
                    <option value="ativo">Ativo</option>
                    <option value="inativo">Inativo</option>
                </select>
                <select v-model="filtrosLocais.consentimento_rgpd" @change="aplicarFiltros" class="rounded border px-3 py-2 text-sm shadow-sm">
                    <option value="">Todos os consentimentos RGPD</option>
                    <option value="sim">Sim</option>
                    <option value="nao">Não</option>
                </select>
                <select v-model="filtrosLocais.pais_id" @change="aplicarFiltros" class="rounded border px-3 py-2 text-sm shadow-sm">
                    <option value="">Todos os países</option>
                    <option v-for="pais in paises" :key="pais.id" :value="pais.id">{{ pais.nome }}</option>
                </select>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-2">
                <div class="text-sm text-gray-600">{{ entidades.total }} resultado(s) encontrados</div>
                <div class="flex gap-2">
                    <Button @click="limparFiltros" variant="outline">Limpar tudo</Button>
                    <Button @click="nova">Nova Entidade</Button>
                </div>
            </div>
        </div>

        <DataTable :columns="columns" :data="entidades.data" class="rounded border shadow" />

        <!-- Paginação -->
        <div class="mt-4 flex justify-center">
            <ul class="flex gap-1">
                <li v-for="link in entidades.links" :key="link.label">
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
                    Tens a certeza que queres eliminar a entidade
                    <strong class="text-[#CDAA62]">{{ entidadeAEliminar?.nome }}</strong
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
