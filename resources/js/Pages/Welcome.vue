<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/registry/new-york-v4/ui/card';
import { Badge } from '@/registry/new-york-v4/ui/badge';
import { Building2, FileText, ShieldCheck } from 'lucide-vue-next';
</script>

<template>
    <AppLayout>
        <Head title="Bem-vindo" />

        <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="mb-16 text-center">
                <h1 class="mb-4 text-5xl font-bold text-gray-900">Bem-vindo à plataforma</h1>
                <p class="mx-auto max-w-2xl text-lg text-gray-600">
                    <strong class="text-primary">CRCDecoradores</strong> – gere clientes, fornecedores, propostas e muito mais com eficiência e
                    segurança.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                <Card class="transition duration-500 hover:scale-[1.02] hover:shadow-lg">
                    <CardHeader class="flex flex-col items-center justify-center text-center">
                        <Building2 class="mb-2 h-10 w-10 text-primary" />
                        <CardTitle>Gestão de Entidades</CardTitle>
                    </CardHeader>
                    <CardContent class="text-center text-sm text-gray-500">
                        Organize <Badge variant="secondary">Clientes</Badge> e <Badge variant="secondary">Fornecedores</Badge> com estrutura e
                        facilidade.
                    </CardContent>
                </Card>

                <Card class="transition duration-500 hover:scale-[1.02] hover:shadow-lg">
                    <CardHeader class="flex flex-col items-center justify-center text-center">
                        <FileText class="mb-2 h-10 w-10 text-primary" />
                        <CardTitle>Propostas e Encomendas</CardTitle>
                    </CardHeader>
                    <CardContent class="text-center text-sm text-gray-500">
                        Crie propostas personalizadas e acompanhe o ciclo de encomendas de forma eficiente.
                    </CardContent>
                </Card>

                <Card class="transition duration-500 hover:scale-[1.02] hover:shadow-lg">
                    <CardHeader class="flex flex-col items-center justify-center text-center">
                        <ShieldCheck class="mb-2 h-10 w-10 text-primary" />
                        <CardTitle>Segurança e Registos</CardTitle>
                    </CardHeader>
                    <CardContent class="text-center text-sm text-gray-500">
                        Proteja os dados com 2FA, encriptação e <Badge>logs</Badge> detalhados.
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
