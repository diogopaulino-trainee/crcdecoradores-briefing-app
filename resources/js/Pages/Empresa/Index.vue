<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, computed } from 'vue';
import { Head, useForm, router, usePage } from '@inertiajs/vue3';
import { Button } from '@/registry/new-york-v4/ui/button';
import { Input } from '@/registry/new-york-v4/ui/input';
import { FormField, FormItem, FormLabel, FormControl, FormMessage } from '@/registry/new-york-v4/ui/form';
import FormWithProvide from '@/Components/FormWithProvide.vue';
import { useToast } from 'vue-toastification';

const page = usePage();
const empresa = computed(() => page.props.empresa);

const toast = useToast();
const editando = ref(false);
const previewLogotipo = ref(null);

const form = useForm({
    nome: empresa.value.nome,
    morada: empresa.value.morada,
    codigo_postal: empresa.value.codigo_postal,
    localidade: empresa.value.localidade,
    numero_contribuinte: empresa.value.numero_contribuinte,
    logotipo: null,
});

const logotipoAtual = computed(() => {
    return previewLogotipo.value ? previewLogotipo.value : route('empresa.logotipo') + '?' + new Date().getTime();
});

const submit = () => {
    form.post(route('empresa.update'), {
        forceFormData: true,
        onSuccess: () => {
            form.logotipo = null;
            previewLogotipo.value = null;
            editando.value = false;
            window.scrollTo(0, 0);
            window.location.reload();
        },
        onError: () => {
            toast.error('Erro ao atualizar os dados.');
        },
    });
};

const handleImagemSelecionada = (e) => {
    if (!editando.value) return;

    const file = e.target.files[0];
    form.logotipo = file;

    if (file) {
        const reader = new FileReader();
        reader.onload = () => {
            previewLogotipo.value = reader.result;
        };
        reader.readAsDataURL(file);
    }
};

const resetForm = () => {
    form.reset({
        nome: empresa.value.nome,
        morada: empresa.value.morada,
        codigo_postal: empresa.value.codigo_postal,
        localidade: empresa.value.localidade,
        numero_contribuinte: empresa.value.numero_contribuinte,
        logotipo: null,
    });

    previewLogotipo.value = null;
};

const cancelarEdicao = () => {
    router.visit(route('empresa.index'), { preserveScroll: true });
};
</script>

<template>
    <Head title="Empresa - Configurações" />

    <AppLayout>
        <div class="mx-auto max-w-3xl space-y-6 rounded bg-white p-8 shadow">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold text-[#CDAA62]">Dados da Empresa</h2>
                <Button v-if="!editando" @click="editando = true">Editar</Button>
            </div>

            <div class="flex justify-center">
                <img :src="logotipoAtual" alt="Logotipo da empresa" class="h-24 rounded object-contain shadow" />
            </div>

            <FormWithProvide :form="form" @submit.prevent="submit" class="space-y-4">
                <FormField name="nome">
                    <FormItem>
                        <FormLabel>Nome</FormLabel>
                        <FormControl>
                            <Input v-model="form.nome" :disabled="!editando" />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <FormField name="morada">
                    <FormItem>
                        <FormLabel>Morada</FormLabel>
                        <FormControl>
                            <Input v-model="form.morada" :disabled="!editando" />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <FormField name="codigo_postal">
                    <FormItem>
                        <FormLabel>Código Postal</FormLabel>
                        <FormControl>
                            <Input v-model="form.codigo_postal" :disabled="!editando" />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <FormField name="localidade">
                    <FormItem>
                        <FormLabel>Localidade</FormLabel>
                        <FormControl>
                            <Input v-model="form.localidade" :disabled="!editando" />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <FormField name="numero_contribuinte">
                    <FormItem>
                        <FormLabel>Número de Contribuinte</FormLabel>
                        <FormControl>
                            <Input v-model="form.numero_contribuinte" :disabled="!editando" />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <FormField name="logotipo">
                    <FormItem>
                        <FormLabel>Logotipo (opcional)</FormLabel>

                        <div class="flex items-center gap-4">
                            <div v-if="previewLogotipo" class="flex-shrink-0">
                                <img :src="previewLogotipo" alt="Preview" class="h-16 w-16 rounded object-contain shadow" />
                            </div>
                            <FormControl class="w-full">
                                <input type="file" class="w-full text-sm" accept="image/*" @change="handleImagemSelecionada" :disabled="!editando" />
                            </FormControl>
                        </div>

                        <FormMessage />
                    </FormItem>
                </FormField>

                <div class="flex justify-end gap-2 pt-4" v-if="editando">
                    <Button variant="outline" type="button" @click="cancelarEdicao"> Cancelar </Button>
                    <Button type="submit" :disabled="form.processing">Guardar</Button>
                </div>
            </FormWithProvide>
        </div>
    </AppLayout>
</template>
