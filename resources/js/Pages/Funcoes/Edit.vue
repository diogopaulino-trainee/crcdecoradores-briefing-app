<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { Button } from '@/registry/new-york-v4/ui/button';
import { Input } from '@/registry/new-york-v4/ui/input';
import { FormField, FormItem, FormLabel, FormControl, FormMessage } from '@/registry/new-york-v4/ui/form';
import FormWithProvide from '@/Components/FormWithProvide.vue';
import { useToast } from 'vue-toastification';

const toast = useToast();

const props = defineProps({
    funcao: Object,
});

const form = useForm({
    _method: 'put',
    nome: props.funcao.nome,
    descricao: props.funcao.descricao,
});

const submit = () => {
    form.post(route('funcoes.update', props.funcao.id), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Função atualizada com sucesso!');
            router.visit(route('funcoes.index'));
        },
        onError: () => {
            toast.error('Erro ao atualizar a função.');
        },
    });
};
</script>

<template>
    <Head title="Editar Função" />

    <AppLayout>
        <div class="mx-auto max-w-xl space-y-6 rounded bg-white p-8 shadow">
            <h2 class="text-2xl font-bold text-[#CDAA62]">Editar Função</h2>

            <FormWithProvide :form="form" @submit.prevent="submit" class="space-y-4">
                <FormField name="nome">
                    <FormItem>
                        <FormLabel>Nome</FormLabel>
                        <FormControl>
                            <Input v-model="form.nome" id="nome" maxlength="255" />
                        </FormControl>
                        <FormMessage>{{ form.errors.nome }}</FormMessage>
                    </FormItem>
                </FormField>

                <FormField name="descricao">
                    <FormItem>
                        <FormLabel>Descrição</FormLabel>
                        <FormControl>
                            <Input v-model="form.descricao" id="descricao" maxlength="1000" />
                        </FormControl>
                        <FormMessage>{{ form.errors.descricao }}</FormMessage>
                    </FormItem>
                </FormField>

                <div class="flex justify-end gap-2 pt-4">
                    <Button variant="outline" type="button" @click="router.visit(route('funcoes.index'))">Cancelar</Button>
                    <Button type="submit" :disabled="form.processing">Atualizar</Button>
                </div>
            </FormWithProvide>
        </div>
    </AppLayout>
</template>
