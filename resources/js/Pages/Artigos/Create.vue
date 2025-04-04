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
    ivas: Array,
});

const form = useForm({
    referencia: '',
    nome: '',
    descricao: '',
    preco: '',
    iva_id: '',
    foto: null,
    observacoes: '',
    estado: 'Ativo',
});

const submit = () => {
    form.post(route('artigos.store'), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            toast.success('Artigo criado com sucesso!');
            router.visit(route('artigos.index'));
        },
        onError: () => {
            toast.error('Erro ao criar o artigo.');
        },
    });
};
</script>

<template>
    <Head title="Novo Artigo" />

    <AppLayout>
        <div class="mx-auto max-w-3xl space-y-6 rounded bg-white p-8 shadow">
            <h2 class="text-2xl font-bold text-[#CDAA62]">Criar Novo Artigo</h2>

            <FormWithProvide :form="form" @submit.prevent="submit" class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <FormField name="referencia">
                    <FormItem>
                        <FormLabel>Referência</FormLabel>
                        <FormControl>
                            <Input v-model="form.referencia" id="referencia" />
                        </FormControl>
                        <FormMessage>{{ form.errors.referencia }}</FormMessage>
                    </FormItem>
                </FormField>

                <FormField name="nome">
                    <FormItem>
                        <FormLabel>Nome</FormLabel>
                        <FormControl>
                            <Input v-model="form.nome" id="nome" />
                        </FormControl>
                        <FormMessage>{{ form.errors.nome }}</FormMessage>
                    </FormItem>
                </FormField>

                <FormField name="preco">
                    <FormItem>
                        <FormLabel>Preço</FormLabel>
                        <FormControl>
                            <Input v-model="form.preco" id="preco" type="number" step="0.01" />
                        </FormControl>
                        <FormMessage>{{ form.errors.preco }}</FormMessage>
                    </FormItem>
                </FormField>

                <FormField name="iva">
                    <FormItem>
                        <FormLabel>IVA</FormLabel>
                        <Select v-model="form.iva_id">
                            <FormControl>
                                <SelectTrigger>
                                    <SelectValue placeholder="Selecionar IVA" />
                                </SelectTrigger>
                            </FormControl>
                            <SelectContent>
                                <SelectItem v-for="iva in props.ivas" :key="iva.id" :value="iva.id"> {{ iva.percentagem }}% </SelectItem>
                            </SelectContent>
                        </Select>
                        <FormMessage>{{ form.errors.iva }}</FormMessage>
                    </FormItem>
                </FormField>

                <FormField name="estado">
                    <FormItem>
                        <FormLabel>Estado</FormLabel>
                        <Select v-model="form.estado">
                            <FormControl>
                                <SelectTrigger>
                                    <SelectValue placeholder="Selecionar" />
                                </SelectTrigger>
                            </FormControl>
                            <SelectContent>
                                <SelectItem value="Ativo">Ativo</SelectItem>
                                <SelectItem value="Inativo">Inativo</SelectItem>
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

                <FormField name="observacoes" class="md:col-span-2">
                    <FormItem>
                        <FormLabel>Observações</FormLabel>
                        <FormControl>
                            <Textarea v-model="form.observacoes" id="observacoes" rows="2" />
                        </FormControl>
                        <FormMessage>{{ form.errors.observacoes }}</FormMessage>
                    </FormItem>
                </FormField>

                <FormField name="foto" class="md:col-span-2">
                    <FormItem>
                        <FormLabel>Foto</FormLabel>
                        <FormControl>
                            <Input type="file" @change="(e) => (form.foto = e.target.files[0])" />
                        </FormControl>
                        <FormMessage>{{ form.errors.foto }}</FormMessage>
                    </FormItem>
                </FormField>

                <div class="flex justify-end gap-2 pt-4 md:col-span-2">
                    <Button variant="outline" type="button" @click="router.visit(route('artigos.index'))">Cancelar</Button>
                    <Button type="submit" :disabled="form.processing">Guardar</Button>
                </div>
            </FormWithProvide>
        </div>
    </AppLayout>
</template>
