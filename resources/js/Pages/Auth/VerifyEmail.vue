<script setup>
import { computed } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

import { Card, CardContent, CardHeader, CardTitle } from '@/registry/new-york-v4/ui/card';
import { Button } from '@/registry/new-york-v4/ui/button';

const props = defineProps({
    status: {
        type: String,
    },
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(() => props.status === 'verification-link-sent');
</script>

<template>
    <GuestLayout>
        <Head title="Verificação de Email" />

        <div class="flex items-center justify-center bg-[#fdfcfb] px-4 py-4">
            <Card class="w-full max-w-md border border-[#CDAA62]/30 shadow-xl">
                <CardHeader>
                    <CardTitle class="text-center text-2xl font-bold text-[#CDAA62]">Verifique o seu email</CardTitle>
                </CardHeader>

                <CardContent class="space-y-4 text-sm text-gray-700">
                    <p>
                        Obrigado por se registar! Antes de continuar, confirme o seu endereço de email clicando no link que acabámos de enviar.
                        <br />
                        Se não recebeu o email, podemos enviar outro.
                    </p>

                    <div v-if="verificationLinkSent" class="rounded-md bg-green-100 p-3 text-center text-sm font-medium text-green-700">
                        Um novo link de verificação foi enviado para o seu email.
                    </div>

                    <form @submit.prevent="submit" class="mt-4 flex flex-col items-center gap-4 sm:flex-row sm:justify-between">
                        <Button type="submit" :disabled="form.processing" class="bg-[#CDAA62] text-white hover:bg-[#b89450]">
                            Reenviar Email de Verificação
                        </Button>

                        <Link
                            :href="route('logout')"
                            method="post"
                            as="button"
                            class="text-sm text-[#CDAA62] underline transition-colors hover:text-[#a5823e]"
                        >
                            Terminar sessão
                        </Link>
                    </form>
                </CardContent>
            </Card>
        </div>
    </GuestLayout>
</template>
