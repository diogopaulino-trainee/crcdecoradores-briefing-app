<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

import { Card, CardContent, CardHeader, CardTitle } from '@/registry/new-york-v4/ui/card';
import { Label } from '@/registry/new-york-v4/ui/label';
import { Input } from '@/registry/new-york-v4/ui/input';
import { Button } from '@/registry/new-york-v4/ui/button';

import { Mail, Lock, Eye, EyeOff } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps({
    email: {
        type: String,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
});

const showPassword = ref(false);
const showPasswordConfirmation = ref(false);

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Redefinir Password" />

        <div class="flex items-center justify-center bg-[#fdfcfb] px-4 py-4">
            <Card class="w-full max-w-md border border-[#CDAA62]/30 shadow-xl">
                <CardHeader>
                    <CardTitle class="text-center text-2xl font-bold text-[#CDAA62]">Redefinir Password</CardTitle>
                </CardHeader>

                <CardContent>
                    <form @submit.prevent="submit" class="space-y-4">
                        <!-- Email -->
                        <div>
                            <Label for="email" class="text-[#CDAA62]">Email</Label>
                            <div class="relative">
                                <Mail class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-[#CDAA62]" />
                                <Input id="email" v-model="form.email" type="email" required autocomplete="username" class="pl-10" />
                            </div>
                            <p v-if="form.errors.email" class="mt-1 text-sm text-red-500">{{ form.errors.email }}</p>
                        </div>

                        <!-- Password -->
                        <div>
                            <Label for="password" class="text-[#CDAA62]">Nova Password</Label>
                            <div class="relative">
                                <Lock class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-[#CDAA62]" />
                                <Input
                                    id="password"
                                    :type="showPassword ? 'text' : 'password'"
                                    v-model="form.password"
                                    required
                                    autocomplete="new-password"
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

                        <!-- Confirm Password -->
                        <div>
                            <Label for="password_confirmation" class="text-[#CDAA62]">Confirmar Password</Label>
                            <div class="relative">
                                <Lock class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-[#CDAA62]" />
                                <Input
                                    id="password_confirmation"
                                    :type="showPasswordConfirmation ? 'text' : 'password'"
                                    v-model="form.password_confirmation"
                                    required
                                    autocomplete="new-password"
                                    class="pl-10 pr-10"
                                />
                                <button
                                    type="button"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-[#CDAA62] hover:text-[#a5823e]"
                                    @click="showPasswordConfirmation = !showPasswordConfirmation"
                                >
                                    <component :is="showPasswordConfirmation ? EyeOff : Eye" class="h-4 w-4" />
                                </button>
                            </div>
                            <p v-if="form.errors.password_confirmation" class="mt-1 text-sm text-red-500">{{ form.errors.password_confirmation }}</p>
                        </div>

                        <!-- Submit -->
                        <div class="flex justify-end">
                            <Button type="submit" :disabled="form.processing" class="bg-[#CDAA62] text-sm text-white hover:bg-[#b89450]">
                                Redefinir Password
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </GuestLayout>
</template>
