<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, computed, h } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { Button } from '@/registry/new-york-v4/ui/button';
import { DropdownMenu, DropdownMenuTrigger, DropdownMenuContent, DropdownMenuItem } from '@/registry/new-york-v4/ui/dropdown-menu';
import DataTable from '@/Components/ui/data-table/DataTable.vue';
import { Settings, ArrowUp, ArrowDown } from 'lucide-vue-next';

const props = defineProps({
    artigos: Object,
    filtros: Object,
    ivas: Array,
});

const showImageModal = ref(false);
const imagemSelecionada = ref('');

const filtrosLocais = ref({
    termo: props.filtros.termo || '',
    preco: props.filtros.preco || '',
    estado: props.filtros.estado || '',
    iva_id: props.filtros.iva_id || '',
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
        termo: '',
        preco: '',
        estado: '',
        iva_id: '',
    };
    aplicarFiltros();
};

const showModal = ref(false);
const artigoAEliminar = ref(null);
const rotatingId = ref(null);

const nova = () => router.get(route('artigos.create'));
const ver = (id) => router.get(route('artigos.show', id));
const editar = (id) => router.get(route('artigos.edit', id));
const confirmarEliminar = (id) => {
    const artigo = props.artigos.data.find((a) => a.id === id);
    if (artigo) {
        artigoAEliminar.value = artigo;
        showModal.value = true;
    }
};
const eliminar = () => {
    if (artigoAEliminar.value) {
        router.delete(route('artigos.destroy', artigoAEliminar.value.id), {
            onSuccess: () => router.get(route('artigos.index')),
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
        {
            preserveState: true,
            replace: true,
        },
    );
};

const columns = [
    {
        accessorKey: 'referencia',
        header: () =>
            h('div', { class: 'flex items-center gap-1 cursor-pointer', onClick: () => sortBy('referencia') }, [
                'Referência',
                currentSort.value === 'referencia' ? h(currentDirection.value === 'asc' ? ArrowUp : ArrowDown, { class: 'w-4 h-4' }) : null,
            ]),
        cell: ({ row }) => h('span', row?.referencia || '—'),
    },
    {
        accessorKey: 'foto',
        header: 'Foto',
        cell: ({ row }) =>
            h('img', {
                src: row?.foto ? route('ficheiro.privado', row.foto) : '/images/logo_crc.png',
                alt: row?.nome,
                class: 'w-12 h-12 object-cover rounded border cursor-pointer transition-transform hover:scale-105',
                onClick: () => {
                    imagemSelecionada.value = row?.foto ? route('ficheiro.privado', row.foto) : '';
                    showImageModal.value = true;
                },
            }),
    },
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
        accessorKey: 'descricao',
        header: 'Descrição',
        cell: ({ row }) => h('span', row?.descricao || '—'),
    },
    {
        accessorKey: 'preco',
        header: () =>
            h('div', { class: 'flex items-center gap-1 cursor-pointer', onClick: () => sortBy('preco') }, [
                'Preço',
                currentSort.value === 'preco' ? h(currentDirection.value === 'asc' ? ArrowUp : ArrowDown, { class: 'w-4 h-4' }) : null,
            ]),
        cell: ({ row }) => h('span', `€ ${parseFloat(row?.preco).toFixed(2)}`),
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
    <Head title="Artigos - CRCDecoradores" />

    <AppLayout>
        <div class="mb-4 flex flex-col gap-4">
            <h1 class="text-2xl font-bold">Artigos</h1>

            <!-- Filtros -->
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <input
                    v-model="filtrosLocais.termo"
                    @input="aplicarFiltros"
                    placeholder="Pesquisar referência, nome ou descrição"
                    class="col-span-3 rounded border px-3 py-2 text-sm shadow-sm"
                />

                <input
                    v-model="filtrosLocais.preco"
                    @input="aplicarFiltros"
                    type="number"
                    step="0.01"
                    placeholder="Preço"
                    class="rounded border px-3 py-2 text-sm shadow-sm"
                />

                <select v-model="filtrosLocais.estado" @change="aplicarFiltros" class="rounded border px-3 py-2 text-sm shadow-sm">
                    <option value="">Todos os estados</option>
                    <option value="Ativo">Ativo</option>
                    <option value="Inativo">Inativo</option>
                </select>

                <select v-model="filtrosLocais.iva_id" @change="aplicarFiltros" class="rounded border px-3 py-2 text-sm shadow-sm">
                    <option value="">Todos os IVAs</option>
                    <option v-for="iva in props.ivas" :key="iva.id" :value="iva.id">{{ iva.percentagem }}%</option>
                </select>
            </div>

            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-600">{{ artigos.total }} resultado(s)</div>
                <div class="flex gap-2">
                    <Button variant="outline" @click="limparFiltros">Limpar tudo</Button>
                    <Button @click="nova">Novo Artigo</Button>
                </div>
            </div>
        </div>

        <DataTable :columns="columns" :data="artigos.data" class="rounded border shadow" />

        <!-- Paginação -->
        <div class="mt-4 flex justify-center">
            <ul class="flex gap-1">
                <li v-for="link in artigos.links" :key="link.label">
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
                    Tens a certeza que queres eliminar o artigo
                    <strong class="text-[#CDAA62]">{{ artigoAEliminar?.nome }}</strong
                    >?
                </p>
                <div class="flex justify-end gap-2">
                    <Button variant="outline" @click="showModal = false">Cancelar</Button>
                    <Button variant="destructive" class="bg-[#CDAA62] text-white hover:bg-[#b38f52]" @click="eliminar"> Eliminar </Button>
                </div>
            </div>
        </div>

        <!-- Modal de Imagem com Transição -->
        <transition name="fade">
            <div
                v-if="showImageModal"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm"
                @click.self="showImageModal = false"
            >
                <transition name="zoom">
                    <div class="relative max-w-3xl scale-100 p-4">
                        <img :src="imagemSelecionada" alt="Imagem ampliada" class="max-h-[80vh] rounded border border-white shadow-2xl" />
                        <button
                            @click="showImageModal = false"
                            class="absolute right-2 top-2 rounded-full bg-white p-1 shadow transition hover:bg-gray-100"
                            aria-label="Fechar imagem"
                        >
                            ✕
                        </button>
                    </div>
                </transition>
            </div>
        </transition>
    </AppLayout>
</template>
