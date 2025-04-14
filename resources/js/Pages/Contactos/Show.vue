<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { Button } from '@/registry/new-york-v4/ui/button';

const props = defineProps({
    contacto: Object,
});
</script>

<template>
    <Head :title="`Contacto #${contacto.numero} - CRCDecoradores`" />

    <AppLayout>
        <div class="mx-auto max-w-3xl space-y-6 rounded bg-white p-8 shadow">
            <h2 class="text-2xl font-bold text-[#CDAA62]">Informação do Contacto</h2>

            <div class="grid grid-cols-1 gap-4 text-gray-700 md:grid-cols-2">
                <div><strong class="text-[#CDAA62]">Nº:</strong> {{ contacto.numero }}</div>
                <div><strong class="text-[#CDAA62]">Primeiro Nome:</strong> {{ contacto.primeiro_nome }}</div>
                <div><strong class="text-[#CDAA62]">Apelido:</strong> {{ contacto.apelido }}</div>
                <div><strong class="text-[#CDAA62]">Função:</strong> {{ contacto.funcao?.nome || '—' }}</div>
                <div><strong class="text-[#CDAA62]">Telefone:</strong> {{ contacto.telefone || '—' }}</div>
                <div><strong class="text-[#CDAA62]">Telemóvel:</strong> {{ contacto.telemovel || '—' }}</div>
                <div><strong class="text-[#CDAA62]">Email:</strong> {{ contacto.email || '—' }}</div>
                <div><strong class="text-[#CDAA62]">Consentimento RGPD:</strong> {{ contacto.consentimento_rgpd }}</div>
                <div><strong class="text-[#CDAA62]">Estado:</strong> {{ contacto.estado }}</div>
                <div><strong class="text-[#CDAA62]">Entidade:</strong> {{ contacto.entidade?.nome || '—' }}</div>
            </div>

            <div>
                <strong class="text-[#CDAA62]">Observações:</strong>
                <p class="mt-1 whitespace-pre-line text-gray-600">{{ contacto.observacoes || '—' }}</p>
            </div>

            <div class="pt-4">
                <Button variant="outline" @click="router.visit(route('contactos.index'))"> Voltar </Button>
            </div>
        </div>
    </AppLayout>
</template>
