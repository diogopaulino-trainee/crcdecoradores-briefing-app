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
});

const form = useForm({
    entidade_id: '',
    numero: '',
    primeiro_nome: '',
    apelido: '',
    funcao: '',
    telefone: '',
    telemovel: '',
    email: '',
    consentimento_rgpd: '',
    observacoes: '',
    estado: 'ativo',
});

const submit = () => {
    form.post(route('contactos.store'), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Contacto criado com sucesso!');
            router.visit(route('contactos.index'));
        },
        onError: () => {
            toast.error('Erro ao criar o contacto.');
        },
    });
};
</script>

<template>
    <Head title="Novo Contacto" />

    <AppLayout>
        <div class="mx-auto max-w-3xl space-y-6 rounded bg-white p-8 shadow">
            <h2 class="text-2xl font-bold text-[#CDAA62]">Criar Novo Contacto</h2>

            <FormWithProvide :form="form" @submit.prevent="submit" class="grid grid-cols-1 gap-4 md:grid-cols-2">
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
                                <SelectItem v-for="entidade in entidades" :key="entidade.id" :value="entidade.id">
                                    {{ entidade.nome }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <FormMessage>{{ form.errors.entidade_id }}</FormMessage>
                    </FormItem>
                </FormField>

                <FormField name="primeiro_nome">
                    <FormItem>
                        <FormLabel>Primeiro Nome</FormLabel>
                        <FormControl>
                            <Input v-model="form.primeiro_nome" id="primeiro_nome" />
                        </FormControl>
                        <FormMessage>{{ form.errors.primeiro_nome }}</FormMessage>
                    </FormItem>
                </FormField>

                <FormField name="apelido">
                    <FormItem>
                        <FormLabel>Apelido</FormLabel>
                        <FormControl>
                            <Input v-model="form.apelido" id="apelido" />
                        </FormControl>
                        <FormMessage>{{ form.errors.apelido }}</FormMessage>
                    </FormItem>
                </FormField>

                <FormField name="funcao">
                    <FormItem>
                        <FormLabel>Função</FormLabel>
                        <FormControl>
                            <Input v-model="form.funcao" id="funcao" />
                        </FormControl>
                        <FormMessage>{{ form.errors.funcao }}</FormMessage>
                    </FormItem>
                </FormField>

                <FormField name="telefone">
                    <FormItem>
                        <FormLabel>Telefone</FormLabel>
                        <FormControl>
                            <Input v-model="form.telefone" id="telefone" />
                        </FormControl>
                        <FormMessage>{{ form.errors.telefone }}</FormMessage>
                    </FormItem>
                </FormField>

                <FormField name="telemovel">
                    <FormItem>
                        <FormLabel>Telemóvel</FormLabel>
                        <FormControl>
                            <Input v-model="form.telemovel" id="telemovel" />
                        </FormControl>
                        <FormMessage>{{ form.errors.telemovel }}</FormMessage>
                    </FormItem>
                </FormField>

                <FormField name="email">
                    <FormItem>
                        <FormLabel>Email</FormLabel>
                        <FormControl>
                            <Input v-model="form.email" id="email" />
                        </FormControl>
                        <FormMessage>{{ form.errors.email }}</FormMessage>
                    </FormItem>
                </FormField>

                <FormField name="consentimento_rgpd">
                    <FormItem>
                        <FormLabel>Consentimento RGPD</FormLabel>
                        <Select v-model="form.consentimento_rgpd">
                            <FormControl>
                                <SelectTrigger>
                                    <SelectValue placeholder="Selecionar" />
                                </SelectTrigger>
                            </FormControl>
                            <SelectContent>
                                <SelectItem value="Sim">Sim</SelectItem>
                                <SelectItem value="Não">Não</SelectItem>
                            </SelectContent>
                        </Select>
                        <FormMessage>{{ form.errors.consentimento_rgpd }}</FormMessage>
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

                <FormField name="observacoes" class="md:col-span-2">
                    <FormItem>
                        <FormLabel>Observações</FormLabel>
                        <FormControl>
                            <Textarea v-model="form.observacoes" id="observacoes" rows="3" />
                        </FormControl>
                        <FormMessage>{{ form.errors.observacoes }}</FormMessage>
                    </FormItem>
                </FormField>

                <div class="flex justify-end gap-2 pt-4 md:col-span-2">
                    <Button variant="outline" type="button" @click="router.visit(route('contactos.index'))">Cancelar</Button>
                    <Button type="submit" :disabled="form.processing">Guardar</Button>
                </div>
            </FormWithProvide>
        </div>
    </AppLayout>
</template>
