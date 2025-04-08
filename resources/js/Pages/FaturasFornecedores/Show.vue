<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { Button } from '@/registry/new-york-v4/ui/button';
import { onMounted } from 'vue';

const props = defineProps({
    fatura: Object,
});

onMounted(() => {
    console.log('Fatura recebida:', props.fatura);
});
</script>

<template>
    <Head :title="`Fatura #${fatura.numero} - CRCDecoradores`" />

    <AppLayout>
        <div class="mx-auto max-w-3xl space-y-6 rounded bg-white p-8 shadow">
            <h2 class="text-2xl font-bold text-[#CDAA62]">Informação da Fatura de Fornecedor</h2>

            <div class="grid grid-cols-1 gap-4 text-gray-700 md:grid-cols-2">
                <div><strong class="text-[#CDAA62]">Nº:</strong> {{ fatura.numero }}</div>
                <div><strong class="text-[#CDAA62]">Data da Fatura:</strong> {{ fatura.data_da_fatura }}</div>
                <div><strong class="text-[#CDAA62]">Data de Vencimento:</strong> {{ fatura.data_de_vencimento }}</div>
                <div><strong class="text-[#CDAA62]">Fornecedor (da Fatura):</strong> {{ fatura.fornecedor?.nome || '—' }}</div>
                <div><strong class="text-[#CDAA62]">Encomenda:</strong> Nº {{ fatura.encomenda_fornecedor?.numero || '—' }}</div>
                <div>
                    <strong class="text-[#CDAA62]">Fornecedor (da Encomenda):</strong>
                    {{ fatura.encomenda_fornecedor?.fornecedor_encomenda?.nome || '—' }}
                </div>
                <div><strong class="text-[#CDAA62]">Estado:</strong> {{ fatura.estado }}</div>
                <div>
                    <strong class="text-[#CDAA62]">Valor Total:</strong>
                    {{ fatura.valor_total ? Number(fatura.valor_total).toFixed(2) + ' €' : '—' }}
                </div>
                <div>
                    <strong class="text-[#CDAA62]">Documento:</strong>
                    <div class="mt-1">
                        <template v-if="Array.isArray(fatura.documento) && fatura.documento.length">
                            <ul class="space-y-1">
                                <li v-for="(ficheiro, index) in fatura.documento" :key="index">
                                    <a
                                        :href="route('ficheiro.privado', { caminho: ficheiro })"
                                        target="_blank"
                                        class="text-blue-600 underline hover:text-blue-800"
                                    >
                                        Ver documento {{ fatura.documento.length > 1 ? `(${index + 1})` : '' }}
                                    </a>
                                </li>
                            </ul>
                        </template>
                        <template v-else>—</template>
                    </div>
                </div>
                <div>
                    <strong class="text-[#CDAA62]">Comprovativo Pagamento:</strong>
                    <div class="mt-1">
                        <template v-if="Array.isArray(fatura.comprovativo_pagamento) && fatura.comprovativo_pagamento.length">
                            <ul class="space-y-1">
                                <li v-for="(ficheiro, index) in fatura.comprovativo_pagamento" :key="index">
                                    <a
                                        :href="route('ficheiro.privado', { caminho: ficheiro })"
                                        target="_blank"
                                        class="text-blue-600 underline hover:text-blue-800"
                                    >
                                        Ver comprovativo {{ fatura.comprovativo_pagamento.length > 1 ? `(${index + 1})` : '' }}
                                    </a>
                                </li>
                            </ul>
                        </template>
                        <template v-else>—</template>
                    </div>
                </div>
            </div>

            <div class="pt-4">
                <Button variant="outline" @click="router.visit(route('faturas.index'))"> Voltar </Button>
            </div>
        </div>
    </AppLayout>
</template>
