<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { Lock } from 'lucide-vue-next';

const props = defineProps({
    mustVerifyEmail: Boolean,
    status: String,
    userData: Object,
});

const user = usePage().props.auth.user;

const form = useForm({
    name: props.userData?.name ?? '',
    email: props.userData?.email ?? '',
    telemovel: props.userData?.telemovel ?? '',
    estado: props.userData?.estado ?? 'Ativo',
    role: props.userData?.role ?? '—',
});
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-[#CAAC5F]">Informação do Perfil</h2>

            <p class="mt-1 text-sm text-gray-600">Atualize as informações do seu perfil e o seu endereço de email.</p>
        </header>

        <form @submit.prevent="form.patch(route('profile.update'))" class="mt-6 space-y-6">
            <div>
                <InputLabel for="name" value="Nome" />

                <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required autofocus autocomplete="name" />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="email" value="Email" />

                <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" required autocomplete="username" />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div>
                <InputLabel for="telemovel" value="Telemóvel" />
                <TextInput id="telemovel" type="text" class="mt-1 block w-full" v-model="form.telemovel" />
                <InputError class="mt-2" :message="form.errors.telemovel" />
            </div>

            <div>
                <InputLabel for="estado" value="Estado" />
                <div class="relative">
                    <TextInput
                        id="estado"
                        type="text"
                        class="mt-1 block w-full cursor-not-allowed bg-gray-100 pr-10 text-gray-700"
                        v-model="form.estado"
                        disabled
                        readonly
                    />
                    <Lock class="absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                </div>
            </div>

            <div>
                <InputLabel for="role" value="Grupo de Permissões" />
                <div class="relative">
                    <TextInput
                        id="role"
                        type="text"
                        class="mt-1 block w-full cursor-not-allowed bg-gray-100 pr-10 text-gray-700"
                        v-model="form.role"
                        disabled
                        readonly
                    />
                    <Lock class="absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                </div>
            </div>

            <div v-if="mustVerifyEmail && user.email_verified_at === null">
                <p class="mt-2 text-sm text-gray-800">
                    O seu endereço de email não está verificado.
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        Clique aqui para reenviar o email de verificação.
                    </Link>
                </p>

                <div v-show="status === 'verification-link-sent'" class="mt-2 text-sm font-medium text-green-600">
                    Foi enviado um novo link de verificação para o seu endereço de email.
                </div>
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing" class="bg-[#CAAC5F] text-white hover:bg-[#bca248]">Guardar</PrimaryButton>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p v-if="form.recentlySuccessful" class="text-sm text-gray-600">Guardado.</p>
                </Transition>
            </div>
        </form>
    </section>
</template>
