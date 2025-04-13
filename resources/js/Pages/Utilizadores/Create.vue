<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { Button } from '@/registry/new-york-v4/ui/button';
import { Input } from '@/registry/new-york-v4/ui/input';
import { Select, SelectTrigger, SelectValue, SelectContent, SelectItem } from '@/registry/new-york-v4/ui/select';
import { FormField, FormItem, FormLabel, FormControl, FormMessage } from '@/registry/new-york-v4/ui/form';
import FormWithProvide from '@/Components/FormWithProvide.vue';
import { useToast } from 'vue-toastification';

const props = defineProps({
    roles: Array,
});

const toast = useToast();

const form = useForm({
    name: '',
    email: '',
    telemovel: '',
    estado: 'Ativo',
    role: '',
    password: '',
    password_confirmation: '',
});

const voltar = () => router.visit(route('utilizadores.index'));

const submit = () => {
    form.post(route('utilizadores.store'), {
        onSuccess: () => toast.success('Utilizador criado com sucesso!'),
        onError: () => toast.error('Erro ao criar o utilizador.'),
    });
};
</script>

<template>
    <Head title="Novo Utilizador" />
    <AppLayout>
        <div class="mx-auto max-w-3xl space-y-6 rounded bg-white p-8 shadow">
            <h2 class="text-2xl font-bold text-[#CDAA62]">Criar Novo Utilizador</h2>

            <FormWithProvide :form="form" @submit.prevent="submit" class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <!-- Nome -->
                <FormField name="name">
                    <FormItem>
                        <FormLabel>Nome</FormLabel>
                        <FormControl>
                            <Input v-model="form.name" />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <!-- Email -->
                <FormField name="email">
                    <FormItem>
                        <FormLabel>Email</FormLabel>
                        <FormControl>
                            <Input v-model="form.email" type="email" />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <FormField name="password">
                    <FormItem>
                        <FormLabel>Password</FormLabel>
                        <FormControl>
                            <Input type="password" v-model="form.password" />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <FormField name="password_confirmation">
                    <FormItem>
                        <FormLabel>Confirmar Password</FormLabel>
                        <FormControl>
                            <Input type="password" v-model="form.password_confirmation" />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <!-- Telemóvel -->
                <FormField name="telemovel">
                    <FormItem>
                        <FormLabel>Telemóvel</FormLabel>
                        <FormControl>
                            <Input v-model="form.telemovel" />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <!-- Grupo de Permissões -->
                <FormField name="role">
                    <FormItem>
                        <FormLabel>Grupo de Permissões</FormLabel>
                        <Select v-model="form.role">
                            <FormControl>
                                <SelectTrigger>
                                    <SelectValue placeholder="Selecionar grupo" />
                                </SelectTrigger>
                            </FormControl>
                            <SelectContent>
                                <SelectItem v-for="role in roles" :key="role.id" :value="role.name">
                                    {{ role.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <!-- Estado -->
                <FormField name="estado">
                    <FormItem>
                        <FormLabel>Estado</FormLabel>
                        <Select v-model="form.estado">
                            <FormControl>
                                <SelectTrigger>
                                    <SelectValue placeholder="Selecionar estado" />
                                </SelectTrigger>
                            </FormControl>
                            <SelectContent>
                                <SelectItem value="Ativo">Ativo</SelectItem>
                                <SelectItem value="Inativo">Inativo</SelectItem>
                            </SelectContent>
                        </Select>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <!-- Ações -->
                <div class="flex justify-end gap-2 pt-4 md:col-span-2">
                    <Button variant="outline" type="button" @click="voltar">Cancelar</Button>
                    <Button type="submit" :disabled="form.processing">Guardar</Button>
                </div>
            </FormWithProvide>
        </div>
    </AppLayout>
</template>
