<script setup lang="ts">
import { Button } from '@/registry/new-york-v4/ui/button';
import { Input } from '@/registry/new-york-v4/ui/input';
import { Label } from '@/registry/new-york-v4/ui/label';
import { Popover, PopoverContent, PopoverTrigger } from '@/registry/new-york-v4/ui/popover';
</script>

<template>
    <Popover>
        <PopoverTrigger as-child>
            <Button variant="outline"> Open popover </Button>
        </PopoverTrigger>
        <PopoverContent class="w-80" align="start">
            <div class="grid gap-4">
                <div class="grid gap-1.5">
                    <h4 class="font-medium leading-none">Dimensions</h4>
                    <p class="text-sm text-muted-foreground">Set the dimensions for the layer.</p>
                </div>
                <div class="grid gap-2">
                    <div class="grid grid-cols-3 items-center gap-4">
                        <Label for="width">Width</Label>
                        <Input id="width" default-value="100%" class="col-span-2 h-8" />
                    </div>
                    <div class="grid grid-cols-3 items-center gap-4">
                        <Label for="maxWidth">Max. width</Label>
                        <Input id="maxWidth" default-value="300px" class="col-span-2 h-8" />
                    </div>
                    <div class="grid grid-cols-3 items-center gap-4">
                        <Label for="height">Height</Label>
                        <Input id="height" default-value="25px" class="col-span-2 h-8" />
                    </div>
                    <div class="grid grid-cols-3 items-center gap-4">
                        <Label for="maxHeight">Max. height</Label>
                        <Input id="maxHeight" default-value="none" class="col-span-2 h-8" />
                    </div>
                </div>
            </div>
        </PopoverContent>
    </Popover>
</template>
