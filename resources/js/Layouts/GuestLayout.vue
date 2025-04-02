<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="flex min-h-screen flex-col items-center justify-center bg-gradient-to-br from-[#fefefe] to-[#f9f6f1] px-4 py-8">
        <!-- Logotipo -->
        <div class="mb-6">
            <Link href="/">
                <ApplicationLogo class="h-20 transition duration-300 hover:opacity-80" />
            </Link>
        </div>

        <!-- Cartão de autenticação -->
        <div class="w-full rounded-xl border border-[#CDAA62]/40 bg-white p-6 shadow-2xl sm:max-w-md">
            <slot />
        </div>

        <!-- Rodapé -->
        <div class="mt-8 text-center text-xs font-medium text-[#CDAA62]">
            &copy; {{ new Date().getFullYear() }} CRCDecoradores. Todos os direitos reservados.
        </div>
    </div>
</template>
