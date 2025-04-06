<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { Button } from '@/registry/new-york-v4/ui/button';

const props = defineProps({
    encomenda: Object,
});
</script>

<template>
    <Head :title="`Encomenda ${encomenda.numero}`" />

    <AppLayout>
        <div class="mx-auto max-w-4xl space-y-6 rounded bg-white p-8 shadow">
            <h2 class="text-2xl font-bold text-[#CDAA62]">Detalhes da Encomenda</h2>

            <div class="grid grid-cols-1 gap-4 text-gray-700 md:grid-cols-2">
                <div>
                    <strong class="text-[#CDAA62]">Nº Encomenda:</strong>
                    {{ encomenda.numero }}
                </div>
                <div>
                    <strong class="text-[#CDAA62]">Data da Proposta:</strong>
                    {{ encomenda.data_da_proposta }}
                </div>
                <div>
                    <strong class="text-[#CDAA62]">Validade:</strong>
                    {{ encomenda.validade }}
                </div>
                <div>
                    <strong class="text-[#CDAA62]">Estado:</strong>
                    {{ encomenda.estado }}
                </div>
                <div>
                    <strong class="text-[#CDAA62]">Valor Total:</strong>
                    € {{ parseFloat(encomenda.valor_total).toFixed(2) }}
                </div>
            </div>

            <div>
                <strong class="text-[#CDAA62]">
                    {{ encomenda.tipo === 'fornecedor' ? 'Fornecedor:' : 'Cliente:' }}
                </strong>
                <p class="mt-1 text-gray-600">{{ encomenda.cliente?.nome }}</p>
            </div>

            <div class="pt-4">
                <strong class="text-[#CDAA62]">Linhas da Encomenda:</strong>
                <table class="mt-2 w-full border text-left text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border px-4 py-2">Referência</th>
                            <th class="border px-4 py-2">Artigo</th>
                            <th class="border px-4 py-2">Descrição</th>
                            <th class="border px-4 py-2">Qtd</th>
                            <th class="border px-4 py-2">IVA</th>
                            <th class="border px-4 py-2">Preço Unitário</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="linha in encomenda.linhas" :key="linha.id">
                            <td class="border px-4 py-2">{{ linha.artigo?.referencia }}</td>
                            <td class="border px-4 py-2">{{ linha.artigo?.nome }}</td>
                            <td class="border px-4 py-2">{{ linha.artigo?.descricao }}</td>
                            <td class="border px-4 py-2 text-center">{{ linha.quantidade }}</td>
                            <td class="border px-4 py-2 text-center">{{ linha.artigo?.iva?.percentagem }}%</td>
                            <td class="border px-4 py-2 text-right">€ {{ parseFloat(linha.preco_unitario).toFixed(2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="pt-4">
                <Button
                    variant="outline"
                    @click="router.visit(route('encomendas.' + (encomenda.tipo === 'fornecedor' ? 'fornecedores' : 'clientes')))"
                >
                    Voltar
                </Button>
            </div>
        </div>
    </AppLayout>
</template>
