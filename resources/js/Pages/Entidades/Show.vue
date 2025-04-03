<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, usePage, router } from '@inertiajs/vue3';
import { Button } from '@/registry/new-york-v4/ui/button';

const props = defineProps({
    entidade: Object,
});
</script>

<template>
    <Head title="Detalhes da Entidade" />

    <AppLayout title="Detalhes da Entidade">
        <div class="mx-auto max-w-3xl space-y-6 rounded bg-white p-8 shadow">
            <h2 class="text-2xl font-bold text-[#CDAA62]">Informação da Entidade</h2>

            <div class="grid grid-cols-1 gap-4 text-gray-700 md:grid-cols-2">
                <div><strong class="text-[#CDAA62]">Tipo:</strong> {{ entidade.tipo }}</div>
                <div><strong class="text-[#CDAA62]">Nº:</strong> {{ entidade.numero }}</div>
                <div><strong class="text-[#CDAA62]">NIF:</strong> {{ entidade.nif }}</div>
                <div><strong class="text-[#CDAA62]">Nome:</strong> {{ entidade.nome }}</div>
                <div><strong class="text-[#CDAA62]">Morada:</strong> {{ entidade.morada }}</div>
                <div><strong class="text-[#CDAA62]">Código Postal:</strong> {{ entidade.codigo_postal }}</div>
                <div><strong class="text-[#CDAA62]">Localidade:</strong> {{ entidade.localidade }}</div>
                <div><strong class="text-[#CDAA62]">País:</strong> {{ entidade.pais?.nome || '—' }}</div>
                <div><strong class="text-[#CDAA62]">Telefone:</strong> {{ entidade.telefone }}</div>
                <div><strong class="text-[#CDAA62]">Telemóvel:</strong> {{ entidade.telemovel }}</div>
                <div><strong class="text-[#CDAA62]">Website:</strong> {{ entidade.website }}</div>
                <div><strong class="text-[#CDAA62]">Email:</strong> {{ entidade.email }}</div>
                <div><strong class="text-[#CDAA62]">Consentimento RGPD:</strong> {{ entidade.consentimento_rgpd }}</div>
                <div><strong class="text-[#CDAA62]">Estado:</strong> {{ entidade.estado }}</div>
            </div>

            <div>
                <strong class="text-[#CDAA62]">Observações:</strong>
                <p class="mt-1 whitespace-pre-line text-gray-600">{{ entidade.observacoes || '—' }}</p>
            </div>

            <div class="pt-4">
                <Button variant="outline" @click="router.visit(entidade.tipo === 'cliente' ? route('clientes.index') : route('fornecedores.index'))">
                    Voltar
                </Button>
            </div>
        </div>
    </AppLayout>
</template>
