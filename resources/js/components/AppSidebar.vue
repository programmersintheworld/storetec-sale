<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { LayoutGrid, Warehouse, ShoppingBasket, SquareUserRound, SquarePercent, BadgeDollarSign, ArchiveRestore } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';
import NavUser from '@/components/NavUser.vue';
import NavWarehouses from '@/components/NavWarehouses.vue';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'Almacenes',
        href: '/warehouses',
        icon: Warehouse,
    },
    {
        title: 'Clientes',
        href: '/customers',
        icon: SquareUserRound,
    },
    {
        title: 'Productos',
        href: '/warehouses/products',
        icon: ShoppingBasket,
    },
    {
        title: 'Entradas',
        href: '/entries',
        icon: ArchiveRestore,
    },
    {
        title: 'Ventas',
        href: '/sales',
        icon: SquarePercent,
    },
];

// const footerNavItems: NavItem[] = [
//     // {
//     //     title: 'Github Repo',
//     //     href: 'https://github.com/laravel/vue-starter-kit',
//     //     icon: Folder,
//     // },
//     // {
//     //     title: 'Documentation',
//     //     href: 'https://laravel.com/docs/starter-kits#vue',
//     //     icon: BookOpen,
//     // },
// ];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <!-- <NavFooter :items="footerNavItems" /> -->
            <NavWarehouses />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
