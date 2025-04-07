<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { Button } from '@/registry/new-york-v4/ui/button';

const props = defineProps({
    ordemTrabalho: Object,
});
</script>

<template>
    <Head :title="`Ordem #${ordemTrabalho.numero} - CRCDecoradores`" />

    <AppLayout>
        <div class="mx-auto max-w-3xl space-y-6 rounded bg-white p-8 shadow">
            <h2 class="text-2xl font-bold text-[#CDAA62]">Informação da Ordem de Trabalho</h2>

            <div class="grid grid-cols-1 gap-4 text-gray-700 md:grid-cols-2">
                <div><strong class="text-[#CDAA62]">Nº:</strong> {{ ordemTrabalho.numero }}</div>
                <div><strong class="text-[#CDAA62]">Data:</strong> {{ ordemTrabalho.data_da_ordem }}</div>
                <div><strong class="text-[#CDAA62]">Estado:</strong> {{ ordemTrabalho.estado }}</div>
                <div><strong class="text-[#CDAA62]">Entidade:</strong> {{ ordemTrabalho.entidade?.nome || '—' }}</div>
            </div>

            <div>
                <strong class="text-[#CDAA62]">Descrição:</strong>
                <p class="mt-1 whitespace-pre-line text-gray-600">{{ ordemTrabalho.descricao || '—' }}</p>
            </div>

            <div class="pt-4">
                <Button variant="outline" @click="router.visit(route('ordens-trabalho.index'))"> Voltar </Button>
            </div>
        </div>
    </AppLayout>
</template>
