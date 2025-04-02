<script setup>
import { ref, computed, onMounted } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';

import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';

const page = usePage();
const user = computed(() => page.props.auth.user);

const qrCode = ref(null);
const recoveryCodes = ref([]);
const enabling = ref(false);
const confirming = ref(false);

const confirmForm = useForm({ code: '' });
const form = useForm({});

const isConfirmed = computed(() => user.value?.two_factor_confirmed_at !== null);

const enableTwoFactorAuthentication = () => {
    enabling.value = true;

    form.post(route('two-factor.enable'), {
        preserveScroll: true,
        onSuccess: () => {
            showQrCode();
            showRecoveryCodes();
            confirming.value = true;
        },
        onFinish: () => {
            enabling.value = false;
        },
    });
};

const confirmTwoFactorAuthentication = () => {
    confirmForm.post(route('two-factor.confirm'), {
        preserveScroll: true,
        onSuccess: () => {
            confirming.value = false;
            confirmForm.reset();
        },
    });
};

const showQrCode = () => {
    axios.get(route('two-factor.qr-code')).then((response) => {
        qrCode.value = response.data.svg;
    });
};

const showRecoveryCodes = () => {
    axios
        .get(route('two-factor.recovery-codes'))
        .then((response) => {
            recoveryCodes.value = response.data;
        })
        .catch((error) => {
            if (error.response?.status === 423) {
                window.location.href = route('password.confirm');
            }
        });
};

const regenerateRecoveryCodes = () => {
    form.post(route('two-factor.recovery-codes'), {
        preserveScroll: true,
        onSuccess: () => showRecoveryCodes(),
    });
};

const disableTwoFactorAuthentication = () => {
    form.delete(route('two-factor.disable'), {
        preserveScroll: true,
        onSuccess: () => {
            qrCode.value = null;
            recoveryCodes.value = [];
            confirming.value = false;
        },
    });
};

onMounted(() => {
    if (isConfirmed.value) {
        showRecoveryCodes();
    }
});
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900">Autenticação 2FA</h2>
            <p class="mt-1 text-sm text-gray-600">Ativa a autenticação de dois fatores para aumentar a segurança da tua conta.</p>
        </header>

        <div class="mt-4 space-y-4">
            <!-- QR Code -->
            <div v-if="qrCode && !isConfirmed" v-html="qrCode" class="mx-auto max-w-xs" />

            <!-- Formulário de confirmação -->
            <form v-if="confirming && !isConfirmed" @submit.prevent="confirmTwoFactorAuthentication" class="space-y-3">
                <InputLabel for="code" value="Código de verificação" />
                <TextInput id="code" v-model="confirmForm.code" required autofocus autocomplete="one-time-code" />
                <InputError :message="confirmForm.errors.code" class="mt-1" />
                <PrimaryButton :disabled="confirmForm.processing"> Confirmar 2FA </PrimaryButton>
            </form>

            <!-- Códigos de recuperação -->
            <div v-if="recoveryCodes.length">
                <h3 class="text-sm font-medium text-gray-900">Códigos de recuperação</h3>
                <ul class="mt-2 space-y-1 text-sm text-gray-700">
                    <li v-for="code in recoveryCodes" :key="code">{{ code }}</li>
                </ul>
            </div>

            <!-- Botões -->
            <div class="flex flex-wrap items-center gap-4">
                <PrimaryButton v-if="!isConfirmed" @click="enableTwoFactorAuthentication" :disabled="enabling"> Ativar 2FA </PrimaryButton>

                <PrimaryButton v-if="isConfirmed" @click="disableTwoFactorAuthentication"> Desativar 2FA </PrimaryButton>

                <PrimaryButton v-if="recoveryCodes.length && isConfirmed" @click="regenerateRecoveryCodes"> Regenerar Códigos </PrimaryButton>
            </div>
        </div>
    </section>
</template>
