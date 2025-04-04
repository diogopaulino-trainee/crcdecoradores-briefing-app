<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { Button } from '@/registry/new-york-v4/ui/button';
import { Input } from '@/registry/new-york-v4/ui/input';
import { Select, SelectTrigger, SelectValue, SelectContent, SelectItem } from '@/registry/new-york-v4/ui/select';
import { FormField, FormItem, FormLabel, FormControl, FormMessage } from '@/registry/new-york-v4/ui/form';
import FormWithProvide from '@/Components/FormWithProvide.vue';
import DatePicker from '@/Components/ui/date-picker/date-picker.vue';
import Combobox from '@/Components/ui/combobox/Combobox.vue';
import { ref, watch } from 'vue';
import { useToast } from 'vue-toastification';

const toast = useToast();

const props = defineProps({
    clientes: Array,
    artigos: Array,
    fornecedores: Array,
    proximoNumero: Number,
});

const today = new Date();
const form = useForm({
    numero: props.proximoNumero,
    data_da_proposta: today.toISOString().substring(0, 10),
    validade: '',
    cliente_id: '',
    estado: 'Rascunho',
    linhas: [],
});

watch(
    () => form.data_da_proposta,
    (val) => {
        const data = new Date(val);
        data.setDate(data.getDate() + 30);
        form.validade = data.toISOString().substring(0, 10);
    },
    { immediate: true },
);

const adicionarLinha = () => {
    form.linhas.push({ artigo_id: null, fornecedor_id: null, quantidade: 1, preco_unitario: 0 });
};

const removerLinha = (index) => {
    form.linhas.splice(index, 1);
};

const setPrecoUnitario = (linha, artigoId) => {
    const artigo = props.artigos.find((a) => a.id === artigoId);
    if (artigo) {
        linha.preco_unitario = artigo.preco;
    }
};

const submit = () => {
    form.post(route('propostas.store'), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Proposta criada com sucesso!');
            router.visit(route('propostas.index'));
        },
        onError: () => {
            toast.error('Erro ao criar a proposta.');
        },
    });
};
</script>

<template>
    <Head title="Nova Proposta" />
    <AppLayout>
        <div class="mx-auto max-w-4xl space-y-6 rounded bg-white p-8 shadow">
            <h2 class="text-2xl font-bold text-[#CDAA62]">Criar Nova Proposta</h2>

            <FormWithProvide :form="form" @submit.prevent="submit" class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <FormField name="numero">
                    <FormItem>
                        <FormLabel>Número</FormLabel>
                        <FormControl>
                            <Input v-model="form.numero" id="numero" readonly />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <FormField name="data_da_proposta">
                    <FormItem>
                        <FormLabel>Data da Proposta</FormLabel>
                        <FormControl>
                            <DatePicker v-model="form.data_da_proposta" />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <FormField name="validade">
                    <FormItem>
                        <FormLabel>Validade</FormLabel>
                        <FormControl>
                            <Input type="date" v-model="form.validade" />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <FormField name="cliente_id">
                    <FormItem>
                        <FormLabel>Cliente</FormLabel>
                        <Select v-model="form.cliente_id">
                            <FormControl>
                                <SelectTrigger>
                                    <SelectValue placeholder="Selecionar cliente" />
                                </SelectTrigger>
                            </FormControl>
                            <SelectContent>
                                <SelectItem v-for="cliente in clientes" :key="cliente.id" :value="cliente.id">
                                    {{ cliente.nome }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <FormMessage />
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
                                <SelectItem value="Rascunho">Rascunho</SelectItem>
                                <SelectItem value="Fechado">Fechado</SelectItem>
                            </SelectContent>
                        </Select>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <div class="md:col-span-2">
                    <h3 class="font-semibold text-[#CDAA62]">Linhas de Artigos</h3>
                    <div
                        v-for="(linha, index) in form.linhas"
                        :key="index"
                        class="mb-4 grid grid-cols-1 items-end gap-4 border-b pb-4 md:grid-cols-4"
                    >
                        <FormField :name="`linhas.${index}.artigo_id`">
                            <FormItem>
                                <FormLabel>Artigo</FormLabel>
                                <FormControl>
                                    <Combobox
                                        v-model="linha.artigo_id"
                                        :items="artigos"
                                        placeholder="Pesquisar artigo por referência ou nome"
                                        :search-fields="['referencia', 'nome']"
                                        item-label="nome"
                                        item-value="id"
                                        :item-text="(a) => `${a.referencia} - ${a.nome}`"
                                        @update:modelValue="(id) => setPrecoUnitario(linha, id)"
                                    />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <FormField :name="`linhas.${index}.fornecedor_id`">
                            <FormItem>
                                <FormLabel>Fornecedor</FormLabel>
                                <FormControl>
                                    <Select v-model="linha.fornecedor_id">
                                        <SelectTrigger>
                                            <SelectValue placeholder="Selecionar fornecedor" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem v-for="f in fornecedores" :key="f.id" :value="f.id">{{ f.nome }}</SelectItem>
                                        </SelectContent>
                                    </Select>
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <FormField :name="`linhas.${index}.preco_unitario`">
                            <FormItem>
                                <FormLabel>Preço Custo</FormLabel>
                                <FormControl>
                                    <Input v-model="linha.preco_unitario" type="number" step="0.01" />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <FormField :name="`linhas.${index}.quantidade`">
                            <FormItem>
                                <FormLabel>Quantidade</FormLabel>
                                <FormControl>
                                    <div class="flex gap-2">
                                        <Input v-model="linha.quantidade" type="number" min="1" />
                                        <button type="button" @click="removerLinha(index)" class="text-red-600 hover:underline">Remover</button>
                                    </div>
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>
                    </div>

                    <Button type="button" @click="adicionarLinha" variant="outline">+ Adicionar Artigo</Button>
                </div>

                <div class="flex justify-end gap-2 pt-4 md:col-span-2">
                    <Button variant="outline" type="button" @click="router.visit(route('propostas.index'))">Cancelar</Button>
                    <Button type="submit" :disabled="form.processing">Guardar</Button>
                </div>
            </FormWithProvide>
        </div>
    </AppLayout>
</template>
