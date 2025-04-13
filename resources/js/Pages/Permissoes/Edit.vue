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
    grupo: Object,
    permissoes: Object,
});

const toast = useToast();

const form = useForm({
    name: props.grupo.name || '',
    estado: props.grupo.estado || 'Ativo',
    permissions: (props.grupo.permissions ?? []).map((p) => p.name),
});

const voltar = () => router.visit(route('permissoes.index'));

const submit = () => {
    form.permissions = [...new Set(form.permissions)];

    form.put(route('permissoes.update', props.grupo.id), {
        onSuccess: () => toast.success('Grupo atualizado com sucesso!'),
        onError: () => toast.error('Erro ao atualizar o grupo.'),
    });
};
</script>

<template>
    <Head title="Editar Grupo de Permissões" />

    <AppLayout>
        <div class="mx-auto max-w-3xl space-y-6 rounded bg-white p-8 shadow">
            <h2 class="text-2xl font-bold text-[#CDAA62]">Editar Grupo de Permissões</h2>

            <FormWithProvide :form="form" @submit.prevent="submit" class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <!-- Nome do Grupo -->
                <FormField name="name">
                    <FormItem>
                        <FormLabel>Nome do Grupo</FormLabel>
                        <FormControl>
                            <Input v-model="form.name" />
                        </FormControl>
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

                <!-- Permissões -->
                <div class="md:col-span-2">
                    <h3 class="mb-2 font-semibold text-[#CDAA62]">Permissões por Menu</h3>
                    <div v-for="(grupo, menu) in permissoes" :key="menu" class="mb-4 border-t pt-4">
                        <h4 class="mb-2 text-sm font-bold uppercase text-gray-700">{{ menu }}</h4>
                        <div class="grid grid-cols-2 gap-2 md:grid-cols-4">
                            <label v-for="permissao in grupo" :key="permissao.id" class="flex items-center space-x-2 text-sm">
                                <input
                                    type="checkbox"
                                    :value="permissao.name"
                                    v-model="form.permissions"
                                    class="rounded border-gray-300 text-[#CDAA62] focus:ring-[#CDAA62]"
                                />
                                <span>
                                    {{ permissao.name.split('.').pop().charAt(0).toUpperCase() + permissao.name.split('.').pop().slice(1) }}
                                </span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Ações -->
                <div class="flex justify-end gap-2 pt-4 md:col-span-2">
                    <Button variant="outline" type="button" @click="voltar">Cancelar</Button>
                    <Button type="submit" :disabled="form.processing">Guardar</Button>
                </div>
            </FormWithProvide>
        </div>
    </AppLayout>
</template>
