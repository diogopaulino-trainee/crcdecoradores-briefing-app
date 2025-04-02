<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

import { Card, CardContent, CardHeader, CardTitle } from '@/registry/new-york-v4/ui/card';
import { Label } from '@/registry/new-york-v4/ui/label';
import { Input } from '@/registry/new-york-v4/ui/input';
import { Button } from '@/registry/new-york-v4/ui/button';
import { Checkbox } from '@/registry/new-york-v4/ui/checkbox';

import { Mail, Lock, Eye, EyeOff } from 'lucide-vue-next';
import { ref } from 'vue';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const showPassword = ref(false);

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Login" />

        <div class="flex items-center justify-center bg-[#fdfcfb] px-4 py-4">
            <Card class="w-full max-w-md border border-[#CDAA62]/30 shadow-xl">
                <CardHeader>
                    <CardTitle class="text-center text-2xl font-bold text-[#CDAA62]">Aceder à plataforma</CardTitle>
                </CardHeader>

                <CardContent>
                    <form @submit.prevent="submit" class="space-y-4">
                        <div v-if="status" class="text-center text-sm font-medium text-green-600">
                            {{ status }}
                        </div>

                        <!-- Email -->
                        <div>
                            <Label for="email" class="mb-2 text-[#CDAA62]">Email</Label>
                            <div class="relative">
                                <Mail class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-[#CDAA62]" />
                                <Input id="email" v-model="form.email" type="email" required autocomplete="username" class="pl-10" />
                            </div>
                            <p v-if="form.errors.email" class="mt-1 text-sm text-red-500">{{ form.errors.email }}</p>
                        </div>

                        <!-- Password -->
                        <div>
                            <Label for="password" class="mb-2 text-[#CDAA62]">Password</Label>
                            <div class="relative">
                                <Lock class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-[#CDAA62]" />
                                <Input
                                    id="password"
                                    :type="showPassword ? 'text' : 'password'"
                                    v-model="form.password"
                                    required
                                    autocomplete="current-password"
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

                        <!-- Remember Me -->
                        <div class="flex items-center space-x-2">
                            <Checkbox id="remember" v-model:checked="form.remember" />
                            <Label for="remember" class="cursor-pointer text-sm text-gray-700">Lembrar-me</Label>
                        </div>

                        <!-- Footer -->
                        <div class="flex items-center justify-between">
                            <Link v-if="canResetPassword" :href="route('password.request')" class="text-sm text-[#CDAA62] hover:underline">
                                Esqueceu a password?
                            </Link>

                            <Button type="submit" :disabled="form.processing" class="ml-auto bg-[#CDAA62] text-sm text-white hover:bg-[#b89450]">
                                Entrar
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </GuestLayout>
</template>
