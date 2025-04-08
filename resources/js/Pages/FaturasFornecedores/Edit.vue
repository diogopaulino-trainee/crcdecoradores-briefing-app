<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { Button } from '@/registry/new-york-v4/ui/button';
import { Input } from '@/registry/new-york-v4/ui/input';
import { Select, SelectTrigger, SelectValue, SelectContent, SelectItem } from '@/registry/new-york-v4/ui/select';
import { FormField, FormItem, FormLabel, FormControl, FormMessage } from '@/registry/new-york-v4/ui/form';
import FormWithProvide from '@/Components/FormWithProvide.vue';
import DatePicker from '@/Components/ui/date-picker/date-picker.vue';
import { ref, watch, computed } from 'vue';
import { useToast } from 'vue-toastification';

const toast = useToast();

const props = defineProps({
    fatura: Object,
    fornecedores: Array,
    encomendasFornecedor: {
        type: Array,
        default: () => [],
    },
});

const form = useForm({
    numero: props.fatura.numero ?? '',
    data_da_fatura: props.fatura.data_da_fatura ?? '',
    data_de_vencimento: props.fatura.data_de_vencimento ?? '',
    fornecedor_id: props.fatura.fornecedor_id ?? '',
    encomenda_fornecedor_id: props.fatura.encomenda_fornecedor_id ?? '',
    valor_total: props.fatura.valor_total ?? '',
    estado: props.fatura.estado ?? '',
    documento: null,
    comprovativo_pagamento: null,
    remover_documentos: [],
    remover_comprovativos: [],
});

const showUploadComprovativo = ref(form.estado === 'Paga');
const showModal = ref(false);

let estadoAnterior = props.fatura.estado ?? 'Pendente';

watch(
    () => form.estado,
    (novoEstado) => {
        if (estadoAnterior === 'Pendente' && novoEstado === 'Paga') {
            showModal.value = true;
        }

        if (novoEstado !== 'Paga') {
            showUploadComprovativo.value = false;
            form.comprovativo_pagamento = null;
        }

        estadoAnterior = novoEstado;
    },
);

watch(
    () => form.encomenda_fornecedor_id,
    (id) => {
        const encomenda = props.encomendasFornecedor.find((e) => String(e.id) === String(id));
        form.valor_total = encomenda?.valor_total ?? '';
    },
);

const confirmarEnvio = () => {
    showUploadComprovativo.value = true;
    showModal.value = false;
};

const cancelarEnvio = () => {
    showModal.value = false;
    form.estado = 'Pendente';
};

const maxFileSizeMB = 10;

const handleMultipleFiles = (e, key) => {
    const files = Array.from(e.target.files);
    const maxFileSize = maxFileSizeMB * 1024 * 1024;

    const validFiles = [];
    const invalidFiles = [];

    files.forEach((file) => {
        if (file.size > maxFileSize) {
            invalidFiles.push(file.name);
        } else {
            validFiles.push(file);
        }
    });

    if (invalidFiles.length) {
        toast.error(`Os seguintes ficheiros excedem o limite de ${maxFileSizeMB}MB e **não serão enviados**:\n• ${invalidFiles.join('\n• ')}`, {
            timeout: 7000,
            closeOnClick: true,
            pauseOnHover: true,
        });
    }

    if (validFiles.length === 0 && invalidFiles.length > 0) {
        toast.warning('Nenhum dos ficheiros selecionados é válido para envio.');
    }

    form[key] = validFiles;
};

const encomendasFiltradas = computed(() => {
    if (!form.fornecedor_id) return [];
    return props.encomendasFornecedor.filter((e) => String(e.fornecedor_id) === String(form.fornecedor_id));
});

const submit = () => {
    const fd = new FormData();

    fd.append('_method', 'PUT');

    fd.append('numero', form.numero ?? '');
    fd.append('data_da_fatura', formatDate(form.data_da_fatura));
    fd.append('data_de_vencimento', formatDate(form.data_de_vencimento));
    fd.append('fornecedor_id', form.fornecedor_id ?? '');
    fd.append('encomenda_fornecedor_id', form.encomenda_fornecedor_id ?? '');
    fd.append('valor_total', form.valor_total ?? '');
    fd.append('estado', form.estado ?? '');

    (form.documento ?? []).forEach((file) => fd.append('documento[]', file));
    (form.comprovativo_pagamento ?? []).forEach((file) => fd.append('comprovativo_pagamento[]', file));
    (form.remover_documentos ?? []).forEach((file) => fd.append('remover_documentos[]', file));
    (form.remover_comprovativos ?? []).forEach((file) => fd.append('remover_comprovativos[]', file));

    router.post(route('faturas.update', props.fatura.id), fd, {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Fatura atualizada com sucesso!');
            router.visit(route('faturas.index'));
        },
        onError: (errors) => {
            console.error('Erros de validação:', errors);
            toast.error('Erro ao atualizar a fatura.');
        },
    });
};

const formatDate = (d) => {
    if (!d) return '';
    return typeof d === 'string' ? d : new Date(d).toISOString().split('T')[0];
};

const removerDocumento = (index) => {
    const fileToRemove = props.fatura.documento[index];
    if (fileToRemove) {
        form.remover_documentos.push(fileToRemove);
        props.fatura.documento.splice(index, 1);
    }
};

const removerComprovativo = (index) => {
    const fileToRemove = props.fatura.comprovativo_pagamento[index];
    if (fileToRemove) {
        form.remover_comprovativos.push(fileToRemove);
        props.fatura.comprovativo_pagamento.splice(index, 1);
    }
};
</script>

<template>
    <Head :title="`Editar Fatura #${form.numero}`" />
    <AppLayout>
        <div class="mx-auto max-w-4xl space-y-6 rounded bg-white p-8 shadow">
            <h2 class="text-2xl font-bold text-[#CDAA62]">Editar Fatura de Fornecedor</h2>

            <FormWithProvide :form="form" @submit.prevent="submit" class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <FormField name="numero">
                    <FormItem>
                        <FormLabel>Número</FormLabel>
                        <FormControl><Input v-model="form.numero" readonly /></FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <FormField name="data_da_fatura">
                    <FormItem>
                        <FormLabel>Data da Fatura</FormLabel>
                        <FormControl><DatePicker v-model="form.data_da_fatura" /></FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <FormField name="data_de_vencimento">
                    <FormItem>
                        <FormLabel>Data de Vencimento</FormLabel>
                        <FormControl><DatePicker v-model="form.data_de_vencimento" /></FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <FormField name="fornecedor_id">
                    <FormItem>
                        <FormLabel>Fornecedor</FormLabel>
                        <FormControl>
                            <select v-model="form.fornecedor_id" class="w-full rounded border px-3 py-2 text-sm">
                                <option value="" disabled>Selecionar fornecedor</option>
                                <option v-for="f in fornecedores" :key="f.id" :value="f.id">{{ f.nome }}</option>
                            </select>
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <FormField name="encomenda_fornecedor_id">
                    <FormItem>
                        <FormLabel>Encomenda de Fornecedor</FormLabel>
                        <FormControl>
                            <select v-model="form.encomenda_fornecedor_id" class="w-full rounded border px-3 py-2 text-sm">
                                <option disabled value="">Selecionar encomenda</option>
                                <option v-for="e in encomendasFiltradas" :key="e.id" :value="e.id">
                                    Nº {{ e.numero }} — {{ new Date(e.data_da_proposta).toLocaleDateString() }}
                                </option>
                            </select>
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <FormField name="valor_total">
                    <FormItem>
                        <FormLabel>Valor Total</FormLabel>
                        <FormControl><Input v-model="form.valor_total" type="number" step="0.01" readonly /></FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <FormField name="estado">
                    <FormItem>
                        <FormLabel>Estado</FormLabel>
                        <Select v-model="form.estado">
                            <FormControl>
                                <SelectTrigger><SelectValue placeholder="Selecionar estado" /></SelectTrigger>
                            </FormControl>
                            <SelectContent>
                                <SelectItem value="Pendente">Pendente</SelectItem>
                                <SelectItem value="Paga">Paga</SelectItem>
                            </SelectContent>
                        </Select>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <FormField name="documento">
                    <FormItem>
                        <FormLabel>Documento (PDF, JPG, PNG)</FormLabel>
                        <FormControl>
                            <Input type="file" multiple @change="(e) => handleMultipleFiles(e, 'documento')" />
                        </FormControl>
                        <FormMessage />
                    </FormItem>

                    <!-- Documentos já existentes -->
                    <div v-if="props.fatura.documento?.length" class="mt-2 flex flex-wrap gap-2">
                        <span
                            v-for="(file, i) in props.fatura.documento"
                            :key="'doc-' + i"
                            class="flex items-center gap-2 rounded bg-gray-100 px-3 py-1 text-sm text-gray-700"
                        >
                            Documento {{ i + 1 }}
                            <button type="button" class="text-red-500 hover:text-red-700" @click="removerDocumento(i)">✕</button>
                        </span>
                    </div>
                </FormField>

                <FormField v-if="showUploadComprovativo" name="comprovativo_pagamento">
                    <FormItem>
                        <FormLabel>Comprovativo de Pagamento (PDF, JPG, PNG)</FormLabel>
                        <FormControl>
                            <Input type="file" multiple @change="(e) => handleMultipleFiles(e, 'comprovativo_pagamento')" />
                        </FormControl>
                        <FormMessage />
                    </FormItem>

                    <!-- Comprovativos já existentes -->
                    <div v-if="props.fatura.comprovativo_pagamento?.length" class="mt-2 flex flex-wrap gap-2">
                        <span
                            v-for="(file, i) in props.fatura.comprovativo_pagamento"
                            :key="'comprovativo-' + i"
                            class="flex items-center gap-2 rounded bg-gray-100 px-3 py-1 text-sm text-gray-700"
                        >
                            Comprovativo {{ i + 1 }}
                            <button type="button" class="text-red-500 hover:text-red-700" @click="removerComprovativo(i)">✕</button>
                        </span>
                    </div>
                </FormField>

                <div class="flex justify-end gap-2 pt-4 md:col-span-2">
                    <Button variant="outline" type="button" @click="router.visit(route('faturas.index'))">Cancelar</Button>
                    <Button type="submit" :disabled="form.processing">Atualizar</Button>
                </div>
            </FormWithProvide>
        </div>

        <!-- Modal de Confirmação -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-xl">
                <h2 class="mb-4 text-lg font-semibold">Confirmar Envio de Comprovativos</h2>
                <p class="mb-6">Pretende enviar os <strong>comprovativos de pagamento</strong> ao fornecedor por email?</p>
                <div class="flex justify-end gap-2">
                    <Button variant="outline" @click="cancelarEnvio">Não</Button>
                    <Button class="bg-[#CDAA62] text-white hover:bg-[#b38f52]" @click="confirmarEnvio">Sim, Enviar</Button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
