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
    ordemTrabalho: Object,
    entidades: Array,
});

const form = useForm({
    numero: props.ordemTrabalho?.numero ?? '',
    data_da_ordem: props.ordemTrabalho?.data_da_ordem ?? '',
    entidade_id: props.ordemTrabalho?.entidade_id ?? '',
    descricao: props.ordemTrabalho?.descricao ?? '',
    estado: props.ordemTrabalho?.estado ?? '',
});

const submit = () => {
    form.put(route('ordens-trabalho.update', props.ordemTrabalho.id), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Ordem de trabalho atualizada com sucesso!');
            router.visit(route('ordens-trabalho.index'));
        },
        onError: () => {
            toast.error('Erro ao atualizar a ordem de trabalho.');
        },
    });
};
</script>

<template>
    <Head :title="form.numero ? `Ordem #${form.numero} - Editar` : 'Editar Ordem de Trabalho'" />

    <AppLayout>
        <div class="mx-auto max-w-3xl space-y-6 rounded bg-white p-8 shadow">
            <h2 class="text-2xl font-bold text-[#CDAA62]">Editar Ordem de Trabalho</h2>

            <FormWithProvide :form="form" @submit.prevent="submit" class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <FormField name="numero">
                    <FormItem>
                        <FormLabel>Número</FormLabel>
                        <FormControl>
                            <Input v-model="form.numero" id="numero" readonly />
                        </FormControl>
                        <FormMessage>{{ form.errors.numero }}</FormMessage>
                    </FormItem>
                </FormField>

                <FormField name="data_da_ordem">
                    <FormItem>
                        <FormLabel>Data</FormLabel>
                        <FormControl>
                            <Input v-model="form.data_da_ordem" id="data_da_ordem" type="date" />
                        </FormControl>
                        <FormMessage>{{ form.errors.data_da_ordem }}</FormMessage>
                    </FormItem>
                </FormField>

                <FormField name="entidade_id" class="md:col-span-2">
                    <FormItem>
                        <FormLabel>Entidade</FormLabel>
                        <Select v-model="form.entidade_id">
                            <FormControl>
                                <SelectTrigger>
                                    <SelectValue placeholder="Selecionar entidade" />
                                </SelectTrigger>
                            </FormControl>
                            <SelectContent>
                                <SelectItem v-for="entidade in entidades" :key="entidade.id" :value="String(entidade.id)">
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
                            <Textarea v-model="form.descricao" id="descricao" rows="3" />
                        </FormControl>
                        <FormMessage>{{ form.errors.descricao }}</FormMessage>
                    </FormItem>
                </FormField>

                <div class="flex justify-end gap-2 pt-4 md:col-span-2">
                    <Button variant="outline" type="button" @click="router.visit(route('ordens-trabalho.index'))">Cancelar</Button>
                    <Button type="submit" :disabled="form.processing">Atualizar</Button>
                </div>
            </FormWithProvide>
        </div>
    </AppLayout>
</template>
