<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/registry/new-york-v4/ui/card';
import { Users, FileText, Package, ShieldCheck } from 'lucide-vue-next';
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout>
        <div class="mx-auto max-w-7xl space-y-8 px-4 py-12 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold text-gray-800">Dashboard</h2>

            <!-- Resumo -->
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <Card class="transition hover:shadow-md">
                    <CardHeader class="flex items-center gap-4">
                        <Users class="h-6 w-6 text-primary" />
                        <CardTitle class="text-sm font-medium text-gray-500">Clientes</CardTitle>
                    </CardHeader>
                    <CardContent class="text-2xl font-bold text-gray-800">34</CardContent>
                </Card>

                <Card class="transition hover:shadow-md">
                    <CardHeader class="flex items-center gap-4">
                        <FileText class="h-6 w-6 text-primary" />
                        <CardTitle class="text-sm font-medium text-gray-500">Propostas</CardTitle>
                    </CardHeader>
                    <CardContent class="text-2xl font-bold text-gray-800">12</CardContent>
                </Card>

                <Card class="transition hover:shadow-md">
                    <CardHeader class="flex items-center gap-4">
                        <Package class="h-6 w-6 text-primary" />
                        <CardTitle class="text-sm font-medium text-gray-500">Encomendas</CardTitle>
                    </CardHeader>
                    <CardContent class="text-2xl font-bold text-gray-800">8</CardContent>
                </Card>

                <Card class="transition hover:shadow-md">
                    <CardHeader class="flex items-center gap-4">
                        <ShieldCheck class="h-6 w-6 text-primary" />
                        <CardTitle class="text-sm font-medium text-gray-500">2FA Ativo</CardTitle>
                    </CardHeader>
                    <CardContent class="text-2xl font-bold text-gray-800">Sim</CardContent>
                </Card>
            </div>

            <!-- Placeholder para gráfico ou actividade -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <Card>
                    <CardHeader>
                        <CardTitle class="text-gray-800">Actividade recente</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-3 text-sm text-gray-600">
                        <p>• Proposta #1024 criada por João</p>
                        <p>• Cliente "ABC Decorações" adicionado</p>
                        <p>• Encomenda #203 marcada como entregue</p>
                        <p class="italic text-gray-400">Mais eventos brevemente...</p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle class="text-gray-800">Resumo semanal (mock)</CardTitle>
                    </CardHeader>
                    <CardContent class="italic text-gray-500"> [ Placeholder para gráfico / estatísticas ] </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
