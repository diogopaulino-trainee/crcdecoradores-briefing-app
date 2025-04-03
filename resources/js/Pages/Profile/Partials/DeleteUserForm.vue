<script setup>
import DangerButton from '@/Components/DangerButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;

    nextTick(() => passwordInput.value.focus());
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;

    form.clearErrors();
    form.reset();
};
</script>

<template>
    <section class="space-y-6">
        <header>
            <h2 class="text-lg font-medium text-[#CAAC5F]">Eliminar Conta</h2>

            <p class="mt-1 text-sm text-gray-600">
                Uma vez que a sua conta seja eliminada, todos os seus recursos e dados serão eliminados permanentemente. Antes de eliminar a sua
                conta, por favor, descarregue qualquer informação ou dados que pretenda conservar.
            </p>
        </header>

        <DangerButton @click="confirmUserDeletion">Eliminar Conta</DangerButton>

        <Modal :show="confirmingUserDeletion" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-[#CAAC5F]">Tem certeza que deseja eliminar a sua conta?</h2>

                <p class="mt-1 text-sm text-gray-600">
                    Uma vez que a sua conta seja eliminada, todos os seus recursos e dados serão eliminados permanentemente. Por favor, introduza a
                    sua password para confirmar que deseja eliminar permanentemente a sua conta.
                </p>

                <div class="mt-6">
                    <InputLabel for="password" value="Password" class="sr-only" />

                    <TextInput
                        id="password"
                        ref="passwordInput"
                        v-model="form.password"
                        type="password"
                        class="mt-1 block w-3/4"
                        placeholder="Password"
                        @keyup.enter="deleteUser"
                    />

                    <InputError :message="form.errors.password" class="mt-2" />
                </div>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeModal"> Cancelar </SecondaryButton>

                    <DangerButton class="ms-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing" @click="deleteUser">
                        Eliminar Conta
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </section>
</template>
