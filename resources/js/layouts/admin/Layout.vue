<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { index as adminDashboard } from '@/actions/App/Http/Controllers/Admin/DashboardController';
import { index as adminCategoriesIndex } from '@/actions/App/Http/Controllers/Admin/CategoryController';
import { index as adminMessagesIndex } from '@/actions/App/Http/Controllers/Admin/MessageController';
import { index as adminOrdersIndex } from '@/actions/App/Http/Controllers/Admin/OrderController';
import { index as adminProductsIndex } from '@/actions/App/Http/Controllers/Admin/ProductController';
import { index as adminReservationsIndex } from '@/actions/App/Http/Controllers/Admin/ReservationController';
import { index as adminUsersIndex } from '@/actions/App/Http/Controllers/Admin/UserController';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import { cn } from '@/lib/utils';
import type { NavItem } from '@/types';

type AdminNavItem = NavItem & {
    component: string;
    exact?: boolean;
};

const page = usePage();
const { isCurrentUrl, isCurrentOrParentUrl } = useCurrentUrl();
const permissions = computed(() => page.props.auth.permissions ?? []);

const adminNavItems: AdminNavItem[] = [
    {
        title: 'Dashboard',
        href: adminDashboard(),
        component: 'admin/Dashboard',
        exact: true,
        permission: 'admin.index',
    },
    {
        title: 'Productos',
        href: adminProductsIndex(),
        component: 'admin/products',
        permission: 'products.index',
    },
    {
        title: 'Categorías',
        href: adminCategoriesIndex(),
        component: 'admin/categories',
        permission: 'categories.index',
    },
    {
        title: 'Preventas',
        href: adminReservationsIndex(),
        component: 'admin/reservations',
        permission: 'reservations.index',
    },
    {
        title: 'Pedidos',
        href: adminOrdersIndex(),
        component: 'admin/orders',
        permission: 'orders.index',
    },
    {
        title: 'Mensajes',
        href: adminMessagesIndex(),
        component: 'admin/messages',
        permission: 'messages.index',
    },
    {
        title: 'Usuarios',
        href: adminUsersIndex(),
        component: 'admin/users',
        permission: 'users.index',
    },
];

const visibleAdminNavItems = computed(() =>
    adminNavItems.filter(
        (item) =>
            !item.permission || permissions.value.includes(item.permission),
    ),
);

const currentTitle = computed(() => {
    const current = adminNavItems.find((item) =>
        page.component.startsWith(item.component),
    );

    return current?.title ?? 'Administración';
});

const isActive = (item: AdminNavItem): boolean =>
    item.exact ? isCurrentUrl(item.href) : isCurrentOrParentUrl(item.href);
</script>

<template>
    <div class="flex flex-1 flex-col gap-6 p-4">
        <header class="flex flex-col gap-3">
            <Heading :title="currentTitle" variant="small" />
            <nav class="flex flex-wrap gap-2" aria-label="Administración">
                <Button
                    v-for="item in visibleAdminNavItems"
                    :key="item.title"
                    as-child
                    variant="ghost"
                    size="sm"
                    :class="cn('justify-start', isActive(item) && 'bg-muted')"
                >
                    <Link :href="item.href">{{ item.title }}</Link>
                </Button>
            </nav>
        </header>

        <section class="flex flex-1 flex-col gap-6">
            <slot />
        </section>
    </div>
</template>
