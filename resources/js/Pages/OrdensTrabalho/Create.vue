<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { Button } from '@/registry/new-york-v4/ui/button';
import { Input } from '@/registry/new-york-v4/ui/input';
import { Textarea } from '@/registry/new-york-v4/ui/textarea';
import { Select, SelectTrigger, SelectValue, SelectContent, SelectItem } from '@/registry/new-york-v4/ui/select';
import { FormField, FormItem, FormLabel, FormControl, FormMessage } from '@/registry/new-york-v4/ui/form';
import FormWithProvide from '@/Components/FormWithProvide.vue';
import { useToast } from 'vue-toastification';

const toast = useToast();

const props = defineProps({
    entidades: Array,
    proximoNumero: Number,
});

const form = useForm({
    data_da_ordem: new Date().toISOString().substr(0, 10),
    entidade_id: '',
    descricao: '',
    estado: 'Pendente',
});

const submit = () => {
    form.post(route('ordens-trabalho.store'), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Ordem de trabalho criada com sucesso!');
            router.visit(route('ordens-trabalho.index'));
        },
        onError: () => {
            toast.error('Erro ao criar a ordem de trabalho.');
        },
    });
};
</script>

<template>
    <Head title="Nova Ordem de Trabalho" />

    <AppLayout>
        <div class="mx-auto max-w-3xl space-y-6 rounded bg-white p-8 shadow">
            <h2 class="text-2xl font-bold text-[#CDAA62]">Criar Nova Ordem de Trabalho</h2>

            <div>
                <label class="block text-sm font-medium text-gray-700">Número</label>
                <input
                    type="text"
                    :value="props.proximoNumero"
                    class="mt-1 w-full cursor-not-allowed rounded border bg-gray-100 px-3 py-2"
                    readonly
                />
            </div>

            <FormWithProvide :form="form" @submit.prevent="submit" class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <FormField name="data_da_ordem">
                    <FormItem>
                        <FormLabel>Data da Ordem</FormLabel>
                        <FormControl>
                            <Input v-model="form.data_da_ordem" id="data_da_ordem" type="date" />
                        </FormControl>
                        <FormMessage>{{ form.errors.data_da_ordem }}</FormMessage>
                    </FormItem>
                </FormField>

                <FormField name="entidade_id">
                    <FormItem>
                        <FormLabel>Entidade</FormLabel>
                        <Select v-model="form.entidade_id">
                            <FormControl>
                                <SelectTrigger>
                                    <SelectValue placeholder="Selecionar entidade" />
                                </SelectTrigger>
                            </FormControl>
                            <SelectContent>
                                <SelectItem v-for="entidade in entidades" :key="entidade.id" :value="entidade.id">
                                    {{ entidade.nome }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <FormMessage>{{ form.errors.entidade_id }}</FormMessage>
                    </FormItem>
                </FormField>

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
                                <SelectItem value="Pendente">Pendente</SelectItem>
                                <SelectItem value="Em Execução">Em Execução</SelectItem>
                                <SelectItem value="Concluída">Concluída</SelectItem>
                                <SelectItem value="Cancelada">Cancelada</SelectItem>
                            </SelectContent>
                        </Select>
                        <FormMessage>{{ form.errors.estado }}</FormMessage>
                    </FormItem>
                </FormField>

                <FormField name="descricao" class="md:col-span-2">
                    <FormItem>
                        <FormLabel>Descrição</FormLabel>
                        <FormControl>
                            <Textarea v-model="form.descricao" id="descricao" rows="4" />
                        </FormControl>
                        <FormMessage>{{ form.errors.descricao }}</FormMessage>
                    </FormItem>
                </FormField>

                <div class="flex justify-end gap-2 pt-4 md:col-span-2">
                    <Button variant="outline" type="button" @click="router.visit(route('ordens-trabalho.index'))">Cancelar</Button>
                    <Button type="submit" :disabled="form.processing">Guardar</Button>
                </div>
            </FormWithProvide>
        </div>
    </AppLayout>
</template>
