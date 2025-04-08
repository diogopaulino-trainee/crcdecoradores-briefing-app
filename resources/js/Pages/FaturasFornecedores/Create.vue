<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { Button } from '@/registry/new-york-v4/ui/button';
import { Input } from '@/registry/new-york-v4/ui/input';
import { Select, SelectTrigger, SelectValue, SelectContent, SelectItem } from '@/registry/new-york-v4/ui/select';
import { FormField, FormItem, FormLabel, FormControl, FormMessage } from '@/registry/new-york-v4/ui/form';
import FormWithProvide from '@/Components/FormWithProvide.vue';
import DatePicker from '@/Components/ui/date-picker/date-picker.vue';
import { ref, watch, computed, onMounted } from 'vue';
import { useToast } from 'vue-toastification';

const toast = useToast();

const props = defineProps({
    fornecedores: Array,
    encomendasFornecedor: {
        type: Array,
        default: () => [],
    },
    proximoNumero: Number,
});

const today = new Date();
const vencimento = new Date();
vencimento.setDate(today.getDate() + 30);

const form = useForm({
    numero: '',
    data_da_fatura: today.toISOString().substring(0, 10),
    data_de_vencimento: vencimento.toISOString().substring(0, 10),
    fornecedor_id: '',
    encomenda_fornecedor_id: '',
    valor_total: '',
    documento: null,
    comprovativo_pagamento: null,
    estado: 'Pendente',
});

onMounted(() => {
    form.numero = props.proximoNumero;
});

const showUploadComprovativo = ref(false);
const showModal = ref(false);

watch(
    () => form.estado,
    (val) => {
        if (val === 'Paga') {
            showModal.value = true;
        } else {
            showUploadComprovativo.value = false;
            form.comprovativo_pagamento = null;
        }
    },
    { immediate: true },
);

watch(
    () => form.encomenda_fornecedor_id,
    (id) => {
        const encomenda = props.encomendasFornecedor.find((e) => String(e.id) === String(id));
        if (encomenda) {
            form.valor_total = encomenda.valor_total;
        } else {
            form.valor_total = '';
        }
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

const handleFileChange = (e, key) => {
    form[key] = Array.from(e.target.files);
};

const encomendasFiltradas = computed(() => {
    if (!form.fornecedor_id) return [];
    return props.encomendasFornecedor.filter((e) => String(e.fornecedor_id) === String(form.fornecedor_id));
});

const fornecedorSelecionado = computed(() => {
    if (!form.fornecedor_id) return null;
    return props.fornecedores.find((f) => String(f.id) === String(form.fornecedor_id));
});

const submit = () => {
    form.post(route('faturas.store'), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            toast.success('Fatura criada com sucesso!');
            router.visit(route('faturas.index'));
        },
        onError: () => {
            toast.error('Erro ao criar a fatura.');
        },
    });
};

const handleMultipleFiles = (e, key) => {
    form[key] = Array.from(e.target.files);
};
</script>

<template>
    <Head title="Nova Fatura de Fornecedor" />
    <AppLayout>
        <div class="mx-auto max-w-4xl space-y-6 rounded bg-white p-8 shadow">
            <h2 class="text-2xl font-bold text-[#CDAA62]">Criar Nova Fatura de Fornecedor</h2>

            <FormWithProvide :form="form" @submit.prevent="submit" class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <FormField name="numero">
                    <FormItem>
                        <FormLabel>Número</FormLabel>
                        <FormControl>
                            <Input v-model="form.numero" readonly />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <FormField name="data_da_fatura">
                    <FormItem>
                        <FormLabel>Data da Fatura</FormLabel>
                        <FormControl>
                            <DatePicker v-model="form.data_da_fatura" />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <FormField name="fornecedor_id">
                    <FormItem>
                        <FormLabel>Fornecedor</FormLabel>
                        <FormControl>
                            <select v-model="form.fornecedor_id" class="w-full rounded border px-3 py-2 text-sm">
                                <option value="" disabled>Selecionar fornecedor</option>
                                <option
                                    v-for="f in fornecedores"
                                    :key="f.id"
                                    :value="f.id"
                                    :class="{
                                        'text-green-600': encomendasFornecedor.some((e) => String(e.fornecedor_id) === String(f.id)),
                                        'text-red-600': !encomendasFornecedor.some((e) => String(e.fornecedor_id) === String(f.id)),
                                    }"
                                >
                                    {{ f.nome }}
                                    {{
                                        encomendasFornecedor.some((e) => String(e.fornecedor_id) === String(f.id))
                                            ? ` (${encomendasFornecedor.filter((e) => String(e.fornecedor_id) === String(f.id)).length} encomendas)`
                                            : ' (sem encomendas)'
                                    }}
                                </option>
                            </select>
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <FormField name="data_de_vencimento">
                    <FormItem>
                        <FormLabel>Data de Vencimento</FormLabel>
                        <FormControl>
                            <DatePicker v-model="form.data_de_vencimento" />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <FormField name="encomenda_fornecedor_id">
                    <FormItem>
                        <FormLabel>Encomenda de Fornecedor</FormLabel>
                        <FormControl>
                            <select v-model="form.encomenda_fornecedor_id" class="w-full rounded border px-3 py-2 text-sm" required>
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
                        <FormControl>
                            <Input v-model="form.valor_total" type="number" step="0.01" readonly />
                        </FormControl>
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
                </FormField>

                <FormField v-if="showUploadComprovativo" name="comprovativo_pagamento">
                    <FormItem>
                        <FormLabel>Comprovativo de Pagamento (PDF, JPG, PNG)</FormLabel>
                        <FormControl>
                            <Input type="file" multiple @change="(e) => handleMultipleFiles(e, 'comprovativo_pagamento')" />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <div class="flex justify-end gap-2 pt-4 md:col-span-2">
                    <Button variant="outline" type="button" @click="router.visit(route('faturas.index'))">Cancelar</Button>
                    <Button type="submit" :disabled="form.processing">Guardar</Button>
                </div>
            </FormWithProvide>
        </div>

        <!-- Modal de confirmação de envio -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-xl">
                <h2 class="mb-4 text-lg font-semibold">Confirmar Envio de Comprovativos</h2>
                <p class="mb-6">Pretende enviar os <strong>comprovativos de pagamento</strong> ao fornecedor por email?</p>
                <div class="flex justify-end gap-2">
                    <Button variant="outline" @click="cancelarEnvio">Não</Button>
                    <Button variant="default" class="bg-[#CDAA62] text-white hover:bg-[#b38f52]" @click="confirmarEnvio"> Sim, Enviar </Button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
