<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import { Button } from '@/registry/new-york-v4/ui/button';
import { Accordion, AccordionContent, AccordionItem, AccordionTrigger } from '@/registry/new-york-v4/ui/accordion';
import {
    Home,
    LayoutDashboard,
    Users,
    Truck,
    Contact,
    FileText,
    ClipboardList,
    Package,
    ClipboardCheck,
    Globe,
    Briefcase,
    Boxes,
    Percent,
    ScrollText,
    Building,
    UserCog,
    LogOut,
} from 'lucide-vue-next';
import {
    DropdownMenu,
    DropdownMenuTrigger,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
} from '@/registry/new-york-v4/ui/dropdown-menu';
import { onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';

const showingNavigationDropdown = ref(false);

const page = usePage();

onMounted(() => {
    const flash = page.props.flash;

    if (flash?.success) {
        toast.success(flash.success);
    }

    if (flash?.error) {
        toast.error(flash.error);
    }

    if (flash?.warning) {
        toast.warning(flash.warning);
    }
});
</script>

<template>
    <div class="flex min-h-screen bg-gray-50 text-gray-800">
        <!-- Sidebar -->
        <aside
            class="fixed left-0 top-0 hidden h-screen w-64 overflow-y-auto border-r bg-white shadow-sm scrollbar-thin scrollbar-track-transparent scrollbar-thumb-[#CDAA62]/60 hover:scrollbar-thumb-[#CDAA62] md:block"
        >
            <div class="border-b p-6">
                <Link :href="route('welcome')">
                    <img src="/images/logo_crc.png" alt="Logo CRC" class="mx-auto h-20" />
                </Link>
            </div>
            <nav class="space-y-2 p-4">
                <template v-if="$page.props.auth?.user">
                    <div>
                        <Link
                            :href="route('welcome')"
                            class="mt-1 flex items-center gap-2 rounded px-4 py-2 text-[18px] font-semibold hover:bg-gray-100"
                            :class="{ 'bg-gray-200 text-[18px] font-semibold': route().current('welcome') }"
                        >
                            <Home class="h-5 w-5" /> Início
                        </Link>
                    </div>

                    <Accordion type="multiple" class="w-full">
                        <AccordionItem value="entidades">
                            <AccordionTrigger class="px-4 text-[18px]">Entidades</AccordionTrigger>
                            <AccordionContent class="space-y-1">
                                <Link
                                    :href="route('clientes.index')"
                                    class="flex items-center gap-2 rounded px-4 py-2 text-[15px] hover:bg-gray-100"
                                    :class="{ 'bg-gray-200 font-semibold': route().current('clientes.index') }"
                                >
                                    <Users class="h-5 w-5" /> Clientes
                                </Link>
                                <Link
                                    :href="route('fornecedores.index')"
                                    class="flex items-center gap-2 rounded px-4 py-2 text-[15px] hover:bg-gray-100"
                                    :class="{ 'bg-gray-200 font-semibold': route().current('fornecedores.index') }"
                                >
                                    <Truck class="h-5 w-5" /> Fornecedores
                                </Link>
                                <Link
                                    :href="route('contactos.index')"
                                    class="flex items-center gap-2 rounded px-4 py-2 text-[15px] hover:bg-gray-100"
                                    :class="{ 'bg-gray-200 font-semibold': route().current('contactos.index') }"
                                >
                                    <Contact class="h-5 w-5" /> Contactos
                                </Link>
                            </AccordionContent>
                        </AccordionItem>

                        <AccordionItem value="propostas">
                            <AccordionTrigger class="px-4 text-[18px]">Propostas & Encomendas</AccordionTrigger>
                            <AccordionContent class="space-y-1">
                                <Link
                                    :href="route('propostas.index')"
                                    class="flex items-center gap-2 rounded px-4 py-2 text-[15px] hover:bg-gray-100"
                                    :class="{ 'bg-gray-200 font-semibold': route().current('propostas.index') }"
                                >
                                    <FileText class="h-5 w-5" /> Propostas
                                </Link>
                                <Link
                                    :href="route('encomendas.clientes')"
                                    class="flex items-center gap-2 rounded px-4 py-2 text-[15px] hover:bg-gray-100"
                                    :class="{ 'bg-gray-200 font-semibold': route().current('encomendas.clientes') }"
                                >
                                    <ClipboardList class="h-5 w-5" /> Encomendas - Clientes
                                </Link>
                                <Link
                                    :href="route('encomendas.fornecedores')"
                                    class="flex items-center gap-2 rounded px-4 py-2 text-[15px] hover:bg-gray-100"
                                    :class="{ 'bg-gray-200 font-semibold': route().current('encomendas.fornecedores') }"
                                >
                                    <ClipboardCheck class="h-5 w-5" /> Encomendas - Fornecedores
                                </Link>
                                <Link
                                    :href="route('ordens-trabalho.index')"
                                    class="flex items-center gap-2 rounded px-4 py-2 text-[15px] hover:bg-gray-100"
                                    :class="{ 'bg-gray-200 font-semibold': route().current('ordens-trabalho.index') }"
                                >
                                    <Package class="h-5 w-5" /> Ordens de Trabalho
                                </Link>
                            </AccordionContent>
                        </AccordionItem>

                        <AccordionItem value="financeiro">
                            <AccordionTrigger class="px-4 text-[18px]">Financeiro</AccordionTrigger>
                            <AccordionContent class="space-y-1">
                                <Link
                                    :href="route('faturas.index')"
                                    class="flex items-center gap-2 rounded px-4 py-2 text-[15px] hover:bg-gray-100"
                                    :class="{ 'bg-gray-200 font-semibold': route().current('faturas.index') }"
                                >
                                    <FileText class="h-5 w-5" /> Faturas Fornecedores
                                </Link>
                                <Link
                                    :href="route('ivas.index')"
                                    class="flex items-center gap-2 rounded px-4 py-2 text-[15px] hover:bg-gray-100"
                                    :class="{ 'bg-gray-200 font-semibold': route().current('ivas.index') }"
                                >
                                    <Percent class="h-5 w-5" /> IVA
                                </Link>
                            </AccordionContent>
                        </AccordionItem>

                        <AccordionItem value="gestao-acessos">
                            <AccordionTrigger class="px-4 text-[18px]">Gestão de Acessos</AccordionTrigger>
                            <AccordionContent class="space-y-1">
                                <Link
                                    :href="route('utilizadores.index')"
                                    class="flex items-center gap-2 rounded px-4 py-2 text-[15px] hover:bg-gray-100"
                                    :class="{ 'bg-gray-200 font-semibold': route().current('utilizadores.index') }"
                                >
                                    <Users class="h-5 w-5" /> Utilizadores
                                </Link>
                                <Link
                                    :href="route('permissoes.index')"
                                    class="flex items-center gap-2 rounded px-4 py-2 text-[15px] hover:bg-gray-100"
                                    :class="{ 'bg-gray-200 font-semibold': route().current('permissoes.index') }"
                                >
                                    <UserCog class="h-5 w-5" /> Permissões
                                </Link>
                            </AccordionContent>
                        </AccordionItem>

                        <AccordionItem value="configuracoes">
                            <AccordionTrigger class="px-4 text-[18px]">Configurações</AccordionTrigger>
                            <AccordionContent class="space-y-1">
                                <Link
                                    :href="route('paises.index')"
                                    class="flex items-center gap-2 rounded px-4 py-2 text-[15px] hover:bg-gray-100"
                                    :class="{ 'bg-gray-200 font-semibold': route().current('paises.index') }"
                                >
                                    <Globe class="h-5 w-5" /> Entidades - Países
                                </Link>
                                <Link
                                    :href="route('funcoes.index')"
                                    class="flex items-center gap-2 rounded px-4 py-2 text-[15px] hover:bg-gray-100"
                                    :class="{ 'bg-gray-200 font-semibold': route().current('funcoes.index') }"
                                >
                                    <Briefcase class="h-5 w-5" /> Contactos - Funções
                                </Link>
                                <Link
                                    :href="route('artigos.index')"
                                    class="flex items-center gap-2 rounded px-4 py-2 text-[15px] hover:bg-gray-100"
                                    :class="{ 'bg-gray-200 font-semibold': route().current('artigos.index') }"
                                >
                                    <Boxes class="h-5 w-5" /> Artigos
                                </Link>
                                <Link
                                    :href="route('logs.index')"
                                    class="flex items-center gap-2 rounded px-4 py-2 text-[15px] hover:bg-gray-100"
                                    :class="{ 'bg-gray-200 font-semibold': route().current('logs.index') }"
                                >
                                    <ScrollText class="h-5 w-5" /> Logs
                                </Link>
                                <Link
                                    :href="route('empresas.index')"
                                    class="flex items-center gap-2 rounded px-4 py-2 text-[15px] hover:bg-gray-100"
                                    :class="{ 'bg-gray-200 font-semibold': route().current('empresas.index') }"
                                >
                                    <Building class="h-5 w-5" /> Empresa
                                </Link>
                            </AccordionContent>
                        </AccordionItem>
                    </Accordion>
                </template>

                <template v-else>
                    <!-- Se não estiver autenticado -->
                    <p class="text-center text-sm text-gray-600">
                        Para aceder ao conteúdo, por favor <Link :href="route('login')" class="text-blue-600 underline">faça login</Link> ou
                        <Link :href="route('register')" class="text-blue-600 underline">registe-se</Link>.
                    </p>
                </template>
            </nav>
        </aside>

        <!-- Main -->
        <div class="flex min-h-screen flex-1 flex-col md:ml-64">
            <!-- Top Nav -->
            <header class="flex items-center justify-between border-b bg-white px-4 py-3 shadow-sm">
                <div class="md:hidden">
                    <button @click="showingNavigationDropdown = !showingNavigationDropdown" class="text-gray-600 hover:text-gray-900">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                :class="{ hidden: showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"
                            />
                            <path
                                :class="{ hidden: !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </div>

                <!-- Botões à direita -->
                <div class="ml-auto flex items-center gap-4">
                    <template v-if="$page.props.auth?.user">
                        <DropdownMenu>
                            <DropdownMenuTrigger as-child>
                                <Button variant="ghost" class="flex items-center gap-2 text-base font-semibold text-[#CDAA62] hover:bg-[#CDAA62]/10">
                                    <UserCog class="h-5 w-5 text-[#CDAA62]" />
                                    {{ $page.props.auth.user.name }}
                                </Button>
                            </DropdownMenuTrigger>

                            <DropdownMenuContent align="end" class="w-56 border border-[#CDAA62]/30 shadow-lg">
                                <DropdownMenuLabel class="text-sm font-bold uppercase tracking-wider text-[#CDAA62]">Conta</DropdownMenuLabel>
                                <DropdownMenuSeparator />

                                <DropdownMenuItem as-child>
                                    <Link
                                        :href="route('dashboard')"
                                        class="flex cursor-pointer items-center gap-2 px-2 py-2 text-base font-medium text-gray-700 hover:bg-[#CDAA62]/10 hover:text-[#CDAA62]"
                                    >
                                        <LayoutDashboard class="h-5 w-5" />
                                        Dashboard
                                    </Link>
                                </DropdownMenuItem>

                                <DropdownMenuItem as-child>
                                    <Link
                                        :href="route('profile.edit')"
                                        class="flex cursor-pointer items-center gap-2 px-2 py-2 text-base font-medium text-gray-700 hover:bg-[#CDAA62]/10 hover:text-[#CDAA62]"
                                    >
                                        <UserCog class="h-5 w-5" />
                                        Perfil
                                    </Link>
                                </DropdownMenuItem>

                                <DropdownMenuSeparator />

                                <DropdownMenuItem as-child>
                                    <form :action="route('logout')" method="post" class="w-full">
                                        <input type="hidden" name="_token" :value="$page.props.csrf_token" />
                                        <button
                                            type="submit"
                                            class="flex w-full items-center gap-2 px-2 py-2 text-base font-medium text-red-600 hover:bg-[#CDAA62]/10 hover:text-red-700"
                                        >
                                            <LogOut class="h-5 w-5" />
                                            Sair
                                        </button>
                                    </form>
                                </DropdownMenuItem>
                            </DropdownMenuContent>
                        </DropdownMenu>
                    </template>

                    <template v-else>
                        <Button as-child variant="outline" class="text-sm">
                            <Link :href="route('login')">Login</Link>
                        </Button>
                        <Button as-child variant="default" class="text-sm">
                            <Link :href="route('register')">Registar</Link>
                        </Button>
                    </template>
                </div>
            </header>

            <aside v-if="showingNavigationDropdown" class="fixed inset-0 z-50 flex md:hidden">
                <!-- Overlay -->
                <div
                    class="fixed inset-0 bg-black/30 backdrop-blur-sm transition-opacity duration-300"
                    @click="showingNavigationDropdown = false"
                ></div>

                <!-- Sidebar content com scroll -->
                <div
                    class="animate-slide-in relative z-50 h-full max-h-screen w-64 overflow-y-auto border-r bg-white shadow-lg scrollbar-thin scrollbar-track-transparent scrollbar-thumb-[#CDAA62]/60 hover:scrollbar-thumb-[#CDAA62]"
                >
                    <nav class="space-y-4 p-4 text-[15px]">
                        <template v-if="$page.props.auth?.user">
                            <!-- Botão fechar -->
                            <div class="mb-2 flex justify-end">
                                <button @click="showingNavigationDropdown = false" class="text-gray-500 hover:text-gray-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <div>
                                <Link
                                    :href="route('welcome')"
                                    class="mt-1 flex items-center gap-2 rounded px-4 py-2 hover:bg-gray-100"
                                    :class="{ 'bg-gray-200 font-semibold': route().current('welcome') }"
                                >
                                    <Home class="h-4 w-4" /> Início
                                </Link>
                            </div>

                            <Accordion type="multiple" class="w-full">
                                <!-- Entidades -->
                                <AccordionItem value="entidades">
                                    <AccordionTrigger class="flex items-center gap-2 px-4 text-[15px]"> Entidades </AccordionTrigger>
                                    <AccordionContent class="space-y-1">
                                        <Link
                                            :href="route('clientes.index')"
                                            class="flex items-center gap-2 rounded px-4 py-2 hover:bg-gray-100"
                                            :class="{ 'bg-gray-200 font-semibold': route().current('clientes.index') }"
                                        >
                                            <Users class="h-4 w-4" /> Clientes
                                        </Link>
                                        <Link
                                            :href="route('fornecedores.index')"
                                            class="flex items-center gap-2 rounded px-4 py-2 hover:bg-gray-100"
                                            :class="{ 'bg-gray-200 font-semibold': route().current('fornecedores.index') }"
                                        >
                                            <Truck class="h-4 w-4" /> Fornecedores
                                        </Link>
                                        <Link
                                            :href="route('contactos.index')"
                                            class="flex items-center gap-2 rounded px-4 py-2 hover:bg-gray-100"
                                            :class="{ 'bg-gray-200 font-semibold': route().current('contactos.index') }"
                                        >
                                            <Contact class="h-4 w-4" /> Contactos
                                        </Link>
                                    </AccordionContent>
                                </AccordionItem>

                                <!-- Propostas -->
                                <AccordionItem value="propostas">
                                    <AccordionTrigger class="flex items-center gap-2 px-4 text-[15px]"> Propostas & Encomendas </AccordionTrigger>
                                    <AccordionContent class="space-y-1">
                                        <Link
                                            :href="route('propostas.index')"
                                            class="flex items-center gap-2 rounded px-4 py-2 hover:bg-gray-100"
                                            :class="{ 'bg-gray-200 font-semibold': route().current('propostas.index') }"
                                        >
                                            <FileText class="h-4 w-4" /> Propostas
                                        </Link>
                                        <Link
                                            :href="route('encomendas.clientes')"
                                            class="flex items-center gap-2 rounded px-4 py-2 hover:bg-gray-100"
                                            :class="{ 'bg-gray-200 font-semibold': route().current('encomendas.clientes') }"
                                        >
                                            <ClipboardList class="h-4 w-4" /> Encomendas - Clientes
                                        </Link>
                                        <Link
                                            :href="route('encomendas.fornecedores')"
                                            class="flex items-center gap-2 rounded px-4 py-2 hover:bg-gray-100"
                                            :class="{ 'bg-gray-200 font-semibold': route().current('encomendas.fornecedores') }"
                                        >
                                            <ClipboardCheck class="h-4 w-4" /> Encomendas - Fornecedores
                                        </Link>
                                        <Link
                                            :href="route('ordens-trabalho.index')"
                                            class="flex items-center gap-2 rounded px-4 py-2 hover:bg-gray-100"
                                            :class="{ 'bg-gray-200 font-semibold': route().current('ordens-trabalho.index') }"
                                        >
                                            <Package class="h-4 w-4" /> Ordens de Trabalho
                                        </Link>
                                    </AccordionContent>
                                </AccordionItem>

                                <!-- Financeiro -->
                                <AccordionItem value="financeiro">
                                    <AccordionTrigger class="flex items-center gap-2 px-4 text-[15px]"> Financeiro </AccordionTrigger>
                                    <AccordionContent class="space-y-1">
                                        <Link
                                            :href="route('faturas.index')"
                                            class="flex items-center gap-2 rounded px-4 py-2 hover:bg-gray-100"
                                            :class="{ 'bg-gray-200 font-semibold': route().current('faturas.index') }"
                                        >
                                            <FileText class="h-4 w-4" /> Faturas Fornecedores
                                        </Link>
                                        <Link
                                            :href="route('ivas.index')"
                                            class="flex items-center gap-2 rounded px-4 py-2 hover:bg-gray-100"
                                            :class="{ 'bg-gray-200 font-semibold': route().current('ivas.index') }"
                                        >
                                            <Percent class="h-4 w-4" /> IVA
                                        </Link>
                                    </AccordionContent>
                                </AccordionItem>

                                <!-- Gestão de Acessos -->
                                <AccordionItem value="gestao-acessos">
                                    <AccordionTrigger class="flex items-center gap-2 px-4 text-[15px]"> Gestão de Acessos </AccordionTrigger>
                                    <AccordionContent class="space-y-1">
                                        <Link
                                            :href="route('utilizadores.index')"
                                            class="flex items-center gap-2 rounded px-4 py-2 hover:bg-gray-100"
                                            :class="{ 'bg-gray-200 font-semibold': route().current('utilizadores.index') }"
                                        >
                                            <Users class="h-4 w-4" /> Utilizadores
                                        </Link>
                                        <Link
                                            :href="route('permissoes.index')"
                                            class="flex items-center gap-2 rounded px-4 py-2 hover:bg-gray-100"
                                            :class="{ 'bg-gray-200 font-semibold': route().current('permissoes.index') }"
                                        >
                                            <UserCog class="h-4 w-4" /> Permissões
                                        </Link>
                                    </AccordionContent>
                                </AccordionItem>

                                <!-- Configurações -->
                                <AccordionItem value="configuracoes">
                                    <AccordionTrigger class="flex items-center gap-2 px-4 text-[15px]"> Configurações </AccordionTrigger>
                                    <AccordionContent class="space-y-1">
                                        <Link
                                            :href="route('paises.index')"
                                            class="flex items-center gap-2 rounded px-4 py-2 hover:bg-gray-100"
                                            :class="{ 'bg-gray-200 font-semibold': route().current('paises.index') }"
                                        >
                                            <Globe class="h-4 w-4" /> Entidades - Países
                                        </Link>
                                        <Link
                                            :href="route('funcoes.index')"
                                            class="flex items-center gap-2 rounded px-4 py-2 hover:bg-gray-100"
                                            :class="{ 'bg-gray-200 font-semibold': route().current('funcoes.index') }"
                                        >
                                            <Briefcase class="h-4 w-4" /> Contactos - Funções
                                        </Link>
                                        <Link
                                            :href="route('artigos.index')"
                                            class="flex items-center gap-2 rounded px-4 py-2 hover:bg-gray-100"
                                            :class="{ 'bg-gray-200 font-semibold': route().current('artigos.index') }"
                                        >
                                            <Boxes class="h-4 w-4" /> Artigos
                                        </Link>
                                        <Link
                                            :href="route('logs.index')"
                                            class="flex items-center gap-2 rounded px-4 py-2 hover:bg-gray-100"
                                            :class="{ 'bg-gray-200 font-semibold': route().current('logs.index') }"
                                        >
                                            <ScrollText class="h-4 w-4" /> Logs
                                        </Link>
                                        <Link
                                            :href="route('empresas.index')"
                                            class="flex items-center gap-2 rounded px-4 py-2 hover:bg-gray-100"
                                            :class="{ 'bg-gray-200 font-semibold': route().current('empresas.index') }"
                                        >
                                            <Building class="h-4 w-4" /> Empresa
                                        </Link>
                                    </AccordionContent>
                                </AccordionItem>
                            </Accordion>

                            <form :action="route('logout')" method="post">
                                <input type="hidden" name="_token" :value="$page.props.csrf_token" />
                                <button type="submit" class="block w-full rounded px-4 py-2 text-left text-sm text-red-600 hover:bg-red-100">
                                    Sair
                                </button>
                            </form>
                        </template>

                        <template v-else>
                            <p class="text-center text-sm text-gray-600">
                                Para aceder ao conteúdo, por favor
                                <Link :href="route('login')" class="text-blue-600 underline">faça login</Link> ou
                                <Link :href="route('register')" class="text-blue-600 underline">registe-se</Link>.
                            </p>
                        </template>
                    </nav>
                </div>
            </aside>

            <!-- Page Content -->
            <main class="flex-1 p-6">
                <slot />
            </main>

            <!-- Footer -->
            <footer class="border-t bg-white py-4 text-center text-sm text-gray-500">
                &copy; {{ new Date().getFullYear() }} CRCDecoradores. Todos os direitos reservados.
            </footer>
        </div>
    </div>
</template>
