<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import {
    ArrowRight,
    BookOpen,
    MapPin,
    Package,
    ShoppingCart,
    Ticket,
} from '@lucide/vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { index as addressesIndex } from '@/actions/App/Http/Controllers/AddressController';
import { index as cartIndex } from '@/actions/App/Http/Controllers/CartController';
import { index as catalogIndex } from '@/actions/App/Http/Controllers/CatalogController';
import {
    index as ordersIndex,
    show as orderShow,
} from '@/actions/App/Http/Controllers/OrderController';
import { index as reservationsIndex } from '@/actions/App/Http/Controllers/ReservationController';
import { dashboard } from '@/routes';
import { formatPrice } from '@/lib/formatPrice';
import type { DashboardOverview, OrderData } from '@/types';

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Mi cuenta', href: dashboard() }],
    },
});

defineProps<{
    overview: DashboardOverview;
    recentOrders: OrderData[];
}>();

const user = usePage().props.auth.user;

const quickLinks = [
    {
        title: 'Explorar catálogo',
        description: 'Descubre nuevas lecturas y ediciones especiales.',
        href: catalogIndex(),
        icon: BookOpen,
    },
    {
        title: 'Mi carrito',
        description: 'Revisa los productos que elegiste.',
        href: cartIndex(),
        icon: ShoppingCart,
    },
    {
        title: 'Mis preventas',
        description: 'Consulta tus depósitos y fechas de salida.',
        href: reservationsIndex(),
        icon: Ticket,
    },
    {
        title: 'Direcciones',
        description: 'Administra tus direcciones de entrega.',
        href: addressesIndex(),
        icon: MapPin,
    },
];
</script>

<template>
    <div>
        <Head title="Mi cuenta" />

        <div class="flex flex-1 flex-col gap-6 p-4">
            <section
                class="relative overflow-hidden rounded-2xl border border-border bg-card p-6 sm:p-8"
            >
                <div
                    class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(180,83,9,0.12),transparent_55%)] dark:bg-[radial-gradient(circle_at_top_right,rgba(251,191,36,0.08),transparent_55%)]"
                ></div>
                <div class="relative max-w-2xl">
                    <p
                        class="text-xs font-semibold tracking-[0.18em] text-amber-700 uppercase dark:text-amber-400"
                    >
                        Mi cuenta
                    </p>
                    <h1 class="mt-3 font-serif text-3xl font-semibold">
                        Hola, {{ user?.name ?? 'lector' }}
                    </h1>
                    <p class="mt-3 text-sm leading-7 text-muted-foreground">
                        Desde aquí puedes seguir tus pedidos, administrar tus
                        preventas y preparar tu próxima compra.
                    </p>
                    <div class="mt-6 flex flex-wrap gap-3">
                        <Button as-child>
                            <Link :href="cartIndex()">
                                Ir al carrito
                                <ArrowRight />
                            </Link>
                        </Button>
                        <Button as-child variant="outline">
                            <Link :href="catalogIndex()">Ver catálogo</Link>
                        </Button>
                    </div>
                </div>
            </section>

            <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <Card>
                    <CardHeader class="flex-row items-center justify-between">
                        <CardTitle class="text-sm font-medium"
                            >Carrito</CardTitle
                        >
                        <ShoppingCart class="size-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <p class="text-2xl font-semibold tabular-nums">
                            {{ overview.cartCount }}
                        </p>
                        <p class="text-xs text-muted-foreground">
                            productos listos
                        </p>
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader class="flex-row items-center justify-between">
                        <CardTitle class="text-sm font-medium"
                            >Preventas</CardTitle
                        >
                        <Ticket class="size-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <p class="text-2xl font-semibold tabular-nums">
                            {{ overview.reservations }}
                        </p>
                        <p class="text-xs text-muted-foreground">
                            depósitos activos
                        </p>
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader class="flex-row items-center justify-between">
                        <CardTitle class="text-sm font-medium"
                            >Pedidos</CardTitle
                        >
                        <Package class="size-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <p class="text-2xl font-semibold tabular-nums">
                            {{ overview.orders }}
                        </p>
                        <p class="text-xs text-muted-foreground">
                            compras realizadas
                        </p>
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader class="flex-row items-center justify-between">
                        <CardTitle class="text-sm font-medium"
                            >Direcciones</CardTitle
                        >
                        <MapPin class="size-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <p class="text-2xl font-semibold tabular-nums">
                            {{ overview.addresses }}
                        </p>
                        <p class="text-xs text-muted-foreground">guardadas</p>
                    </CardContent>
                </Card>
            </section>

            <section class="grid gap-6 lg:grid-cols-[1.3fr_0.7fr]">
                <Card>
                    <CardHeader class="flex-row items-center justify-between">
                        <CardTitle class="font-serif text-xl"
                            >Pedidos recientes</CardTitle
                        >
                        <Link
                            :href="ordersIndex()"
                            class="text-xs font-medium text-muted-foreground hover:text-foreground"
                        >
                            Ver todos
                        </Link>
                    </CardHeader>
                    <CardContent class="space-y-3">
                        <div
                            v-if="recentOrders.length === 0"
                            class="rounded-xl border border-dashed border-border p-6 text-center text-sm text-muted-foreground"
                        >
                            Aún no tienes pedidos. ¡Tu próxima lectura te
                            espera!
                        </div>
                        <Link
                            v-for="order in recentOrders"
                            :key="order.id"
                            :href="orderShow(order.id)"
                            class="flex items-center justify-between rounded-xl border border-border p-4 transition-colors hover:bg-accent/50"
                        >
                            <div>
                                <p class="text-sm font-medium">
                                    Pedido #{{ order.id }}
                                </p>
                                <p class="text-xs text-muted-foreground">
                                    {{ order.items.length }} producto(s) ·
                                    {{ formatPrice(order.total) }}
                                </p>
                            </div>
                            <Badge variant="secondary">{{
                                order.statusLabel
                            }}</Badge>
                        </Link>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle class="font-serif text-xl"
                            >Accesos rápidos</CardTitle
                        >
                    </CardHeader>
                    <CardContent class="grid gap-2">
                        <Link
                            v-for="link in quickLinks"
                            :key="link.title"
                            :href="link.href"
                            class="group flex items-start gap-3 rounded-xl border border-transparent p-3 transition-colors hover:border-border hover:bg-accent/50"
                        >
                            <component
                                :is="link.icon"
                                class="mt-0.5 size-4 shrink-0 text-muted-foreground group-hover:text-foreground"
                            />
                            <span>
                                <span class="block text-sm font-medium">{{
                                    link.title
                                }}</span>
                                <span
                                    class="block text-xs text-muted-foreground"
                                >
                                    {{ link.description }}
                                </span>
                            </span>
                        </Link>
                    </CardContent>
                </Card>
            </section>
        </div>
    </div>
</template>
