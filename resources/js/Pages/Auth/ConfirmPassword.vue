<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

import { Card, CardContent, CardHeader, CardTitle } from '@/registry/new-york-v4/ui/card';
import { Label } from '@/registry/new-york-v4/ui/label';
import { Input } from '@/registry/new-york-v4/ui/input';
import { Button } from '@/registry/new-york-v4/ui/button';

import { Lock, Eye, EyeOff } from 'lucide-vue-next';
import { ref } from 'vue';

const form = useForm({
    password: '',
});

const showPassword = ref(false);

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Confirmar Password" />

        <div class="flex items-center justify-center bg-[#fdfcfb] px-4 py-4">
            <Card class="w-full max-w-md border border-[#CDAA62]/30 shadow-xl">
                <CardHeader>
                    <CardTitle class="text-center text-2xl font-bold text-[#CDAA62]">Confirmar Password</CardTitle>
                </CardHeader>

                <CardContent>
                    <p class="mb-4 text-center text-sm text-gray-600">
                        Esta é uma área segura da aplicação. Por favor confirma a tua password antes de continuar.
                    </p>

                    <form @submit.prevent="submit" class="space-y-4">
                        <!-- Password -->
                        <div>
                            <Label for="password" class="text-[#CDAA62]">Password</Label>
                            <div class="relative mt-1">
                                <Lock class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-[#CDAA62]" />
                                <Input
                                    id="password"
                                    :type="showPassword ? 'text' : 'password'"
                                    v-model="form.password"
                                    required
                                    autocomplete="current-password"
                                    autofocus
                                    class="pl-10 pr-10"
                                />
                                <button
                                    type="button"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-[#CDAA62] hover:text-[#a5823e]"
                                    @click="showPassword = !showPassword"
                                >
                                    <component :is="showPassword ? EyeOff : Eye" class="h-4 w-4" />
                                </button>
                            </div>
                            <p v-if="form.errors.password" class="mt-1 text-sm text-red-500">{{ form.errors.password }}</p>
                        </div>

                        <!-- Submit -->
                        <div class="flex justify-end">
                            <Button type="submit" :disabled="form.processing" class="bg-[#CDAA62] text-sm text-white hover:bg-[#b89450]">
                                Confirmar
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </GuestLayout>
</template>
