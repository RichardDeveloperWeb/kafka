<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    BookOpen,
    LayoutGrid,
    MapPin,
    Package,
    ShoppingCart,
    Ticket,
} from '@lucide/vue';
import AppLogo from '@/components/AppLogo.vue';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { index as addressesIndex } from '@/actions/App/Http/Controllers/AddressController';
import { index as cartIndex } from '@/actions/App/Http/Controllers/CartController';
import { index as catalogIndex } from '@/actions/App/Http/Controllers/CatalogController';
import { index as ordersIndex } from '@/actions/App/Http/Controllers/OrderController';
import { index as reservationsIndex } from '@/actions/App/Http/Controllers/ReservationController';
import { about, dashboard } from '@/routes';
import type { NavItem } from '@/types';

const mainNavItems: NavItem[] = [
    {
        title: 'Mi cuenta',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Catálogo',
        href: catalogIndex(),
        icon: BookOpen,
    },
    {
        title: 'Carrito',
        href: cartIndex(),
        icon: ShoppingCart,
    },
    {
        title: 'Preventas',
        href: reservationsIndex(),
        icon: Ticket,
    },
    {
        title: 'Pedidos',
        href: ordersIndex(),
        icon: Package,
    },
    {
        title: 'Direcciones',
        href: addressesIndex(),
        icon: MapPin,
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Nosotros',
        href: about(),
        icon: BookOpen,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
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
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
