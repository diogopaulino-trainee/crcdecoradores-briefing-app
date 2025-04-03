<script setup>
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/registry/new-york-v4/ui/table';

const props = defineProps({
    columns: Array,
    data: Array,
});
</script>

<template>
    <Table>
        <TableHeader>
            <TableRow>
                <TableHead v-for="column in columns" :key="column.accessorKey || column.id" class="py-2 text-lg font-bold text-[#CDAA62]">
                    <component :is="typeof column.header === 'function' ? column.header : 'span'">
                        <template v-if="typeof column.header !== 'function'">
                            {{ column.header }}
                        </template>
                    </component>
                </TableHead>
            </TableRow>
        </TableHeader>

        <TableBody>
            <TableRow v-for="row in data" :key="row.id">
                <TableCell v-for="column in columns" :key="column.accessorKey || column.id" class="py-3 text-base">
                    <template v-if="typeof column.cell === 'function'">
                        <component :is="column.cell({ row })" />
                    </template>
                    <template v-else>
                        {{ row[column.accessorKey] }}
                    </template>
                </TableCell>
            </TableRow>
        </TableBody>
    </Table>
</template>
