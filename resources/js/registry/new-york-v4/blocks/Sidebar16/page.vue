<script lang="ts">
export const iframeHeight = '800px';
export const description = 'A sidebar with a header and a search form.';
</script>

<script setup lang="ts">
import AppSidebar from '@/registry/new-york-v4/blocks/Sidebar16/components/AppSidebar.vue';
import SiteHeader from '@/registry/new-york-v4/blocks/Sidebar16/components/SiteHeader.vue';
import { SidebarInset, SidebarProvider } from '@/registry/new-york-v4/ui/sidebar';
</script>

<template>
    <div class="[--header-height:calc(--spacing(14))]">
        <SidebarProvider class="flex flex-col">
            <SiteHeader />
            <div class="flex flex-1">
                <AppSidebar />
                <SidebarInset>
                    <div class="flex flex-1 flex-col gap-4 p-4">
                        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                            <div class="aspect-video rounded-xl bg-muted/50" />
                            <div class="aspect-video rounded-xl bg-muted/50" />
                            <div class="aspect-video rounded-xl bg-muted/50" />
                        </div>
                        <div class="min-h-[100vh] flex-1 rounded-xl bg-muted/50 md:min-h-min" />
                    </div>
                </SidebarInset>
            </div>
        </SidebarProvider>
    </div>
</template>
