<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { Button } from '@/registry/new-york-v4/ui/button';

const props = defineProps({
    utilizador: Object,
});
</script>

<template>
    <Head :title="`Utilizador ${utilizador.name}`" />

    <AppLayout>
        <div class="mx-auto max-w-3xl space-y-6 rounded bg-white p-8 shadow">
            <h2 class="text-2xl font-bold text-[#CDAA62]">Detalhes do Utilizador</h2>

            <div class="grid grid-cols-1 gap-4 text-gray-700 md:grid-cols-2">
                <div>
                    <strong class="text-[#CDAA62]">Nome:</strong>
                    {{ utilizador.name }}
                </div>
                <div>
                    <strong class="text-[#CDAA62]">Email:</strong>
                    {{ utilizador.email }}
                </div>
                <div>
                    <strong class="text-[#CDAA62]">Telemóvel:</strong>
                    {{ utilizador.telemovel || '—' }}
                </div>
                <div>
                    <strong class="text-[#CDAA62]">Estado:</strong>
                    {{ utilizador.estado }}
                </div>
                <div class="md:col-span-2">
                    <strong class="text-[#CDAA62]">Grupo de Permissões:</strong>
                    <ul class="mt-1 list-disc pl-5 text-gray-600">
                        <li v-for="role in utilizador.roles" :key="role.id">
                            {{ role.name }}
                        </li>
                        <li v-if="!utilizador.roles?.length">—</li>
                    </ul>
                </div>
            </div>

            <div class="pt-4">
                <Button variant="outline" @click="router.visit(route('utilizadores.index'))"> Voltar </Button>
            </div>
        </div>
    </AppLayout>
</template>
