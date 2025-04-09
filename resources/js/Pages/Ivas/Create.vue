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
    percentagem: '',
});

const submit = () => {
    form.post(route('ivas.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.clearErrors();
            toast.success('IVA criado com sucesso!');
            router.visit(route('ivas.index'));
        },
        onError: () => {
            toast.error('Erro ao criar o IVA.');
        },
    });
};
</script>

<template>
    <Head title="Novo IVA" />

    <AppLayout>
        <div class="mx-auto max-w-xl space-y-6 rounded bg-white p-8 shadow">
            <h2 class="text-2xl font-bold text-[#CDAA62]">Criar Novo IVA</h2>

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

                <FormField name="percentagem">
                    <FormItem>
                        <FormLabel>Percentagem</FormLabel>
                        <FormControl>
                            <Input v-model="form.percentagem" id="percentagem" type="number" step="0.01" min="0" />
                        </FormControl>
                        <FormMessage>{{ form.errors.percentagem }}</FormMessage>
                    </FormItem>
                </FormField>

                <div class="flex justify-end gap-2 pt-4">
                    <Button variant="outline" type="button" @click="router.visit(route('ivas.index'))">Cancelar</Button>
                    <Button type="submit" :disabled="form.processing">Guardar</Button>
                </div>
            </FormWithProvide>
        </div>
    </AppLayout>
</template>
