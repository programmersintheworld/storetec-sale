<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

const props = defineProps({
    totalSales: String,
    totalEntries: String,
    totalProducts: String,
    totalClients: String,
    totalProfit: String,
    totalLoss: String,
});

console.log('Total Sales:', props);
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <div
                    class="border-sidebar-border/70 dark:border-sidebar-border relative aspect-video overflow-hidden rounded-xl border bg-gradient-to-br from-indigo-500 to-indigo-700 p-6 text-white shadow-lg"
                >
                    <h1 class="text-lg font-semibold">Ventas</h1>
                    <div class="flex h-full flex-col items-center justify-center gap-2">
                        <p class="text-4xl font-bold">${{ props.totalSales }}</p>
                        <p class="text-sm text-white/80">Total de ventas registradas</p>
                    </div>
                </div>

                <div
                    class="border-sidebar-border/70 dark:border-sidebar-border relative aspect-video overflow-hidden rounded-xl border bg-gradient-to-br from-emerald-500 to-emerald-700 p-6 text-white shadow-lg"
                >
                    <h1 class="text-lg font-semibold">Ingresos</h1>
                    <div class="flex h-full flex-col items-center justify-center gap-2">
                        <p class="text-4xl font-bold">${{ props.totalEntries }}</p>
                        <p class="text-sm text-white/80">Total invertido en productos</p>
                    </div>
                </div>

                <div
                    class="border-sidebar-border/70 dark:border-sidebar-border relative aspect-video overflow-hidden rounded-xl border bg-gradient-to-br from-cyan-500 to-cyan-700 p-6 text-white shadow-lg"
                >
                    <h1 class="text-lg font-semibold">Total de productos</h1>
                    <div class="flex h-full flex-col items-center justify-center gap-2">
                        <p class="text-4xl font-bold">{{ props.totalProducts }}</p>
                        <p class="text-sm text-white/80">Productos en el sistema</p>
                    </div>
                </div>
            </div>

            <div class="mt-4 grid auto-rows-min gap-4 md:grid-cols-3">
                <div
                    class="border-sidebar-border/70 dark:border-sidebar-border relative aspect-video overflow-hidden rounded-xl border bg-gradient-to-br from-fuchsia-500 to-fuchsia-700 p-6 text-white shadow-lg"
                >
                    <h1 class="text-lg font-semibold">Clientes</h1>
                    <div class="flex h-full flex-col items-center justify-center gap-2">
                        <p class="text-4xl font-bold">{{ props.totalClients }}</p>
                        <p class="text-sm text-white/80">Clientes únicos registrados</p>
                    </div>
                </div>

                <div
                    class="border-sidebar-border/70 dark:border-sidebar-border relative aspect-video overflow-hidden rounded-xl border bg-gradient-to-br from-yellow-500 to-yellow-600 p-6 text-white shadow-lg"
                >
                    <h1 class="text-lg font-semibold">Ganancias totales</h1>
                    <div class="flex h-full flex-col items-center justify-center gap-2">
                        <p class="text-4xl font-bold">${{ props.totalProfit }}</p>
                        <p class="text-sm text-white/80">Beneficio neto</p>
                    </div>
                </div>

                <div
                    class="border-sidebar-border/70 dark:border-sidebar-border relative aspect-video overflow-hidden rounded-xl border bg-gradient-to-br from-red-500 to-red-700 p-6 text-white shadow-lg"
                >
                    <h1 class="text-lg font-semibold">Pérdidas totales</h1>
                    <div class="flex h-full flex-col items-center justify-center gap-2">
                        <p class="text-4xl font-bold">${{ props.totalLoss }}</p>
                        <p class="text-sm text-white/80">Diferencia negativa</p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
