<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { Button } from '@/registry/new-york-v4/ui/button';

const props = defineProps({
    artigo: Object,
});
</script>

<template>
    <Head title="Detalhes do Artigo" />

    <AppLayout>
        <div class="mx-auto max-w-3xl space-y-6 rounded bg-white p-8 shadow">
            <h2 class="text-2xl font-bold text-[#CDAA62]">Informação do Artigo</h2>

            <div class="grid grid-cols-1 gap-4 text-gray-700 md:grid-cols-2">
                <div><strong class="text-[#CDAA62]">Referência:</strong> {{ artigo.referencia }}</div>
                <div><strong class="text-[#CDAA62]">Nome:</strong> {{ artigo.nome }}</div>
                <div><strong class="text-[#CDAA62]">Preço:</strong> € {{ parseFloat(artigo.preco).toFixed(2) }}</div>
                <div><strong class="text-[#CDAA62]">IVA:</strong> {{ artigo.iva?.percentagem }}%</div>
                <div><strong class="text-[#CDAA62]">Estado:</strong> {{ artigo.estado }}</div>
            </div>

            <div>
                <strong class="text-[#CDAA62]">Descrição:</strong>
                <p class="mt-1 whitespace-pre-line text-gray-600">{{ artigo.descricao || '—' }}</p>
            </div>

            <div>
                <strong class="text-[#CDAA62]">Observações:</strong>
                <p class="mt-1 whitespace-pre-line text-gray-600">{{ artigo.observacoes || '—' }}</p>
            </div>

            <div v-if="artigo.foto" class="pt-4">
                <strong class="text-[#CDAA62]">Foto:</strong>
                <div class="mt-2">
                    <img :src="route('ficheiro.privado', artigo.foto)" alt="Foto do artigo" class="max-w-xs rounded border" />
                </div>
            </div>

            <div class="pt-4">
                <Button variant="outline" @click="router.visit(route('artigos.index'))"> Voltar </Button>
            </div>
        </div>
    </AppLayout>
</template>
