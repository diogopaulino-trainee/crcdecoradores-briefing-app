<script lang="ts">
export const iframeHeight = '800px';
export const description = 'A sidebar with collapsible sections.';
</script>

<script setup lang="ts">
import AppSidebar from '@/registry/new-york-v4/blocks/Sidebar02/components/AppSidebar.vue';
import {
    Breadcrumb,
    BreadcrumbItem,
    BreadcrumbLink,
    BreadcrumbList,
    BreadcrumbPage,
    BreadcrumbSeparator,
} from '@/registry/new-york-v4/ui/breadcrumb';
import { Separator } from '@/registry/new-york-v4/ui/separator';
import { SidebarInset, SidebarProvider, SidebarTrigger } from '@/registry/new-york-v4/ui/sidebar';
</script>

<template>
    <SidebarProvider>
        <AppSidebar />
        <SidebarInset>
            <header class="sticky top-0 flex h-16 shrink-0 items-center gap-2 border-b bg-background px-4">
                <SidebarTrigger class="-ml-1" />
                <Separator orientation="vertical" class="mr-2 h-4" />
                <Breadcrumb>
                    <BreadcrumbList>
                        <BreadcrumbItem class="hidden md:block">
                            <BreadcrumbLink href="#"> Building Your Application </BreadcrumbLink>
                        </BreadcrumbItem>
                        <BreadcrumbSeparator class="hidden md:block" />
                        <BreadcrumbItem>
                            <BreadcrumbPage>Data Fetching</BreadcrumbPage>
                        </BreadcrumbItem>
                    </BreadcrumbList>
                </Breadcrumb>
            </header>
            <div class="flex flex-1 flex-col gap-4 p-4">
                <div v-for="i in 24" :key="i" class="aspect-video h-12 w-full rounded-lg bg-muted/50" />
            </div>
        </SidebarInset>
    </SidebarProvider>
</template>
