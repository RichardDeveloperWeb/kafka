<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import {
    BookOpen,
    ClipboardList,
    LayoutDashboard,
    LayoutGrid,
    MapPin,
    MessageSquare,
    Package,
    ShoppingCart,
    Tags,
    Ticket,
    Users,
} from '@lucide/vue';
import { computed } from 'vue';
import AppLogo from '@/components/AppLogo.vue';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarGroup,
    SidebarGroupLabel,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { index as adminDashboard } from '@/actions/App/Http/Controllers/Admin/DashboardController';
import { index as adminCategoriesIndex } from '@/actions/App/Http/Controllers/Admin/CategoryController';
import { index as adminMessagesIndex } from '@/actions/App/Http/Controllers/Admin/MessageController';
import { index as adminOrdersIndex } from '@/actions/App/Http/Controllers/Admin/OrderController';
import { index as adminProductsIndex } from '@/actions/App/Http/Controllers/Admin/ProductController';
import { index as adminReservationsIndex } from '@/actions/App/Http/Controllers/Admin/ReservationController';
import { index as adminUsersIndex } from '@/actions/App/Http/Controllers/Admin/UserController';
import { index as addressesIndex } from '@/actions/App/Http/Controllers/AddressController';
import { index as cartIndex } from '@/actions/App/Http/Controllers/CartController';
import { index as catalogIndex } from '@/actions/App/Http/Controllers/CatalogController';
import { index as ordersIndex } from '@/actions/App/Http/Controllers/OrderController';
import { index as reservationsIndex } from '@/actions/App/Http/Controllers/ReservationController';
import { about, dashboard } from '@/routes';
import type { NavItem } from '@/types';

const page = usePage();
const permissions = computed(() => page.props.auth.permissions ?? []);

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

const adminNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: adminDashboard(),
        icon: LayoutDashboard,
        permission: 'admin.index',
    },
    {
        title: 'Productos',
        href: adminProductsIndex(),
        icon: Package,
        permission: 'products.index',
    },
    {
        title: 'Categorías',
        href: adminCategoriesIndex(),
        icon: Tags,
        permission: 'categories.index',
    },
    {
        title: 'Preventas',
        href: adminReservationsIndex(),
        icon: Ticket,
        permission: 'reservations.index',
    },
    {
        title: 'Pedidos',
        href: adminOrdersIndex(),
        icon: ClipboardList,
        permission: 'orders.index',
    },
    {
        title: 'Mensajes',
        href: adminMessagesIndex(),
        icon: MessageSquare,
        permission: 'messages.index',
    },
    {
        title: 'Usuarios',
        href: adminUsersIndex(),
        icon: Users,
        permission: 'users.index',
    },
];

const visibleAdminNavItems = computed(() =>
    adminNavItems.filter(
        (item) =>
            !item.permission || permissions.value.includes(item.permission),
    ),
);

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
            <SidebarGroup
                v-if="visibleAdminNavItems.length > 0"
                class="px-2 py-0"
            >
                <SidebarGroupLabel>Administración</SidebarGroupLabel>
                <SidebarMenu>
                    <SidebarMenuItem
                        v-for="item in visibleAdminNavItems"
                        :key="item.title"
                    >
                        <SidebarMenuButton as-child :tooltip="item.title">
                            <Link :href="item.href">
                                <component :is="item.icon" />
                                <span>{{ item.title }}</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarGroup>
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
</template>
