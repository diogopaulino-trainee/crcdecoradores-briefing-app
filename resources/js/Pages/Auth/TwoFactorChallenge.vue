<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';

import { Card, CardContent, CardHeader, CardTitle } from '@/registry/new-york-v4/ui/card';
import { Label } from '@/registry/new-york-v4/ui/label';
import { Input } from '@/registry/new-york-v4/ui/input';
import { Button } from '@/registry/new-york-v4/ui/button';
import { Eye, EyeOff, ShieldCheck } from 'lucide-vue-next';

const form = useForm({
    code: '',
    recovery_code: '',
});

const showCode = ref(false);

const submit = () => {
    form.post(route('two-factor.login'), {
        onFinish: () => form.reset('code', 'recovery_code'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Autenticação 2FA" />

        <div class="flex items-center justify-center bg-[#fdfcfb] px-4 py-24">
            <Card class="w-full max-w-md border border-[#CDAA62]/30 shadow-xl">
                <CardHeader>
                    <CardTitle class="flex items-center justify-center gap-2 text-center text-2xl font-bold text-[#CDAA62]">
                        <ShieldCheck class="h-6 w-6" />
                        Autenticação 2FA
                    </CardTitle>
                </CardHeader>

                <CardContent>
                    <form @submit.prevent="submit" class="space-y-4">
                        <p class="text-center text-sm text-gray-600">Introduz o código de autenticação fornecido pela tua aplicação autenticadora.</p>

                        <div>
                            <Label for="code" class="mb-2 text-[#CDAA62]">Código</Label>
                            <div class="relative">
                                <Input
                                    id="code"
                                    :type="showCode ? 'text' : 'password'"
                                    inputmode="numeric"
                                    v-model="form.code"
                                    autofocus
                                    class="pr-10 text-center font-mono uppercase tracking-[.3em]"
                                    placeholder="●●●●●●"
                                />
                                <button
                                    type="button"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-[#CDAA62] hover:text-[#a5823e]"
                                    @click="showCode = !showCode"
                                >
                                    <component :is="showCode ? EyeOff : Eye" class="h-4 w-4" />
                                </button>
                            </div>
                            <p v-if="form.errors.code" class="mt-1 text-sm text-red-500">{{ form.errors.code }}</p>
                        </div>

                        <div>
                            <Label for="recovery_code" class="mb-2 text-[#CDAA62]">Código de Recuperação (opcional)</Label>
                            <Input
                                id="recovery_code"
                                type="text"
                                class="text-center font-mono uppercase tracking-wide"
                                v-model="form.recovery_code"
                                placeholder="ABCD-EFGH-IJKL"
                            />
                            <p v-if="form.errors.recovery_code" class="mt-1 text-sm text-red-500">{{ form.errors.recovery_code }}</p>
                        </div>

                        <Button type="submit" :disabled="form.processing" class="w-full bg-[#CDAA62] text-white hover:bg-[#b89450]">
                            Verificar
                        </Button>
                    </form>
                </CardContent>
            </Card>
        </div>
    </GuestLayout>
</template>
