<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

import { Card, CardContent, CardHeader, CardTitle } from '@/registry/new-york-v4/ui/card';
import { Label } from '@/registry/new-york-v4/ui/label';
import { Input } from '@/registry/new-york-v4/ui/input';
import { Button } from '@/registry/new-york-v4/ui/button';

import { Mail } from 'lucide-vue-next';
import { ref } from 'vue';

defineProps({
    status: String,
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <GuestLayout>
        <Head title="Recuperar Password" />

        <div class="flex items-center justify-center bg-[#fdfcfb] px-4 py-4">
            <Card class="w-full max-w-md border border-[#CDAA62]/30 shadow-xl">
                <CardHeader>
                    <CardTitle class="text-center text-2xl font-bold text-[#CDAA62]">Recuperar Password</CardTitle>
                </CardHeader>

                <CardContent>
                    <div class="mb-4 text-sm text-gray-600">
                        Esqueceu-se da sua password? Sem problema. Insira o seu email e enviaremos um link para que possa definir uma nova.
                    </div>

                    <div v-if="status" class="mb-4 text-center text-sm font-medium text-green-600">
                        {{ status }}
                    </div>

                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <Label for="email" class="text-[#CDAA62]">Email</Label>
                            <div class="relative mt-1">
                                <Mail class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-[#CDAA62]" />
                                <Input id="email" v-model="form.email" type="email" required autocomplete="username" class="pl-10" />
                            </div>
                            <p v-if="form.errors.email" class="mt-1 text-sm text-red-500">{{ form.errors.email }}</p>
                        </div>

                        <div class="flex justify-end">
                            <Button type="submit" :disabled="form.processing" class="bg-[#CDAA62] text-sm text-white hover:bg-[#b89450]">
                                Enviar link de recuperação
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </GuestLayout>
</template>
