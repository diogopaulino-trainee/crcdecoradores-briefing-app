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
    entidade: Object,
    paises: Array,
});

const form = useForm({
    tipo: props.entidade.tipo,
    numero: props.entidade.numero,
    nif: props.entidade.nif,
    nome: props.entidade.nome,
    morada: props.entidade.morada,
    codigo_postal: props.entidade.codigo_postal,
    localidade: props.entidade.localidade,
    pais_id: props.entidade.pais_id,
    telefone: props.entidade.telefone,
    telemovel: props.entidade.telemovel,
    website: props.entidade.website,
    email: props.entidade.email,
    consentimento_rgpd: props.entidade.consentimento_rgpd,
    observacoes: props.entidade.observacoes,
    estado: props.entidade.estado,
});

const submit = () => {
    form.put(route('entidades.update', props.entidade.id), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Entidade atualizada com sucesso!');
            voltarParaLista();
        },
        onError: (errors) => {
            if (errors.nif) {
                form.setError('nif', errors.nif);
            }
            toast.error('Erro ao atualizar a entidade.');
        },
    });
};

const voltarParaLista = () => {
    if (form.tipo === 'cliente') {
        router.visit(route('clientes.index'));
    } else if (form.tipo === 'fornecedor') {
        router.visit(route('fornecedores.index'));
    } else {
        router.visit(route('entidades.index'));
    }
};
</script>

<template>
    <Head title="Editar Entidade" />

    <AppLayout>
        <div class="mx-auto max-w-3xl space-y-6 rounded bg-white p-8 shadow">
            <h2 class="text-2xl font-bold text-[#CDAA62]">Editar Entidade ({{ form.tipo }})</h2>

            <FormWithProvide :form="form" @submit.prevent="submit" class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <FormField name="nif">
                    <FormItem>
                        <FormLabel>NIF</FormLabel>
                        <FormControl>
                            <Input v-model="form.nif" id="nif" />
                        </FormControl>
                        <FormMessage>{{ form.errors.nif }}</FormMessage>
                    </FormItem>
                </FormField>

                <FormField name="nome" class="md:col-span-2">
                    <FormItem>
                        <FormLabel>Nome</FormLabel>
                        <FormControl>
                            <Input v-model="form.nome" id="nome" />
                        </FormControl>
                        <FormMessage>{{ form.errors.nome }}</FormMessage>
                    </FormItem>
                </FormField>

                <FormField name="morada" class="md:col-span-2">
                    <FormItem>
                        <FormLabel>Morada</FormLabel>
                        <FormControl>
                            <Input v-model="form.morada" id="morada" />
                        </FormControl>
                        <FormMessage>{{ form.errors.morada }}</FormMessage>
                    </FormItem>
                </FormField>

                <FormField name="codigo_postal">
                    <FormItem>
                        <FormLabel>Código Postal</FormLabel>
                        <FormControl>
                            <Input v-model="form.codigo_postal" id="codigo_postal" />
                        </FormControl>
                        <FormMessage>{{ form.errors.codigo_postal }}</FormMessage>
                    </FormItem>
                </FormField>

                <FormField name="localidade">
                    <FormItem>
                        <FormLabel>Localidade</FormLabel>
                        <FormControl>
                            <Input v-model="form.localidade" id="localidade" />
                        </FormControl>
                        <FormMessage>{{ form.errors.localidade }}</FormMessage>
                    </FormItem>
                </FormField>

                <FormField name="pais_id" class="md:col-span-2">
                    <FormItem>
                        <FormLabel>País</FormLabel>
                        <Select v-model="form.pais_id">
                            <FormControl>
                                <SelectTrigger>
                                    <SelectValue placeholder="Selecionar país" />
                                </SelectTrigger>
                            </FormControl>
                            <SelectContent>
                                <SelectItem v-for="pais in paises" :key="pais.id" :value="pais.id">
                                    {{ pais.nome }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <FormMessage>{{ form.errors.pais_id }}</FormMessage>
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

                <FormField name="website">
                    <FormItem>
                        <FormLabel>Website</FormLabel>
                        <FormControl>
                            <Input v-model="form.website" id="website" />
                        </FormControl>
                        <FormMessage>{{ form.errors.website }}</FormMessage>
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
                                <SelectItem value="sim">Sim</SelectItem>
                                <SelectItem value="nao">Não</SelectItem>
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
                                <SelectItem value="ativo">Ativo</SelectItem>
                                <SelectItem value="inativo">Inativo</SelectItem>
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
                    <Button variant="outline" type="button" @click="voltarParaLista">Cancelar</Button>
                    <Button type="submit" :disabled="form.processing">Atualizar</Button>
                </div>
            </FormWithProvide>
        </div>
    </AppLayout>
</template>
