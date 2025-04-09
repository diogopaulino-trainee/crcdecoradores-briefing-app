<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { Button } from '@/registry/new-york-v4/ui/button';
import { Input } from '@/registry/new-york-v4/ui/input';
import { FormField, FormItem, FormLabel, FormControl, FormMessage } from '@/registry/new-york-v4/ui/form';
import FormWithProvide from '@/Components/FormWithProvide.vue';
import { useToast } from 'vue-toastification';

const toast = useToast();

const form = useForm({
    nome: '',
    codigo: '',
});

const submit = () => {
    form.post(route('paises.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.clearErrors();
            toast.success('País criado com sucesso!');
            router.visit(route('paises.index'));
        },
        onError: () => {
            toast.error('Erro ao criar o país.');
        },
    });
};
</script>

<template>
    <Head title="Novo País" />

    <AppLayout>
        <div class="mx-auto max-w-xl space-y-6 rounded bg-white p-8 shadow">
            <h2 class="text-2xl font-bold text-[#CDAA62]">Criar Novo País</h2>

            <FormWithProvide :form="form" @submit.prevent="submit" class="space-y-4">
                <FormField name="nome">
                    <FormItem>
                        <FormLabel>Nome</FormLabel>
                        <FormControl>
                            <Input v-model="form.nome" id="nome" />
                        </FormControl>
                        <FormMessage>{{ form.errors.nome }}</FormMessage>
                    </FormItem>
                </FormField>

                <FormField name="codigo">
                    <FormItem>
                        <FormLabel>Código (ex: PT)</FormLabel>
                        <FormControl>
                            <Input v-model="form.codigo" id="codigo" maxlength="5" />
                        </FormControl>
                        <FormMessage>{{ form.errors.codigo }}</FormMessage>
                    </FormItem>
                </FormField>

                <div class="flex justify-end gap-2 pt-4">
                    <Button variant="outline" type="button" @click="router.visit(route('paises.index'))">Cancelar</Button>
                    <Button type="submit" :disabled="form.processing">Guardar</Button>
                </div>
            </FormWithProvide>
        </div>
    </AppLayout>
</template>
