<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    BookOpen,
    MessageSquare,
    Package,
    Tags,
    Ticket,
    Users,
} from '@lucide/vue';
import { computed } from 'vue';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { index as adminDashboard } from '@/actions/App/Http/Controllers/Admin/DashboardController';
import { index as categoriesIndex } from '@/actions/App/Http/Controllers/Admin/CategoryController';
import {
    index as messagesIndex,
    show as messageShow,
} from '@/actions/App/Http/Controllers/Admin/MessageController';
import { index as ordersIndex } from '@/actions/App/Http/Controllers/Admin/OrderController';
import { index as productsIndex } from '@/actions/App/Http/Controllers/Admin/ProductController';
import { index as reservationsIndex } from '@/actions/App/Http/Controllers/Admin/ReservationController';
import { index as usersIndex } from '@/actions/App/Http/Controllers/Admin/UserController';
import { formatPrice } from '@/lib/formatPrice';
import type { AdminCounts, AdminMessage, AdminPreorder } from '@/types';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Administración', href: adminDashboard() },
            { title: 'Dashboard', href: adminDashboard() },
        ],
    },
});

const props = defineProps<{
    counts: AdminCounts;
    recentPreorders: AdminPreorder[];
    recentMessages: AdminMessage[];
}>();

const metricCards = computed(() => [
    {
        title: 'Productos',
        value: props.counts.products,
        href: productsIndex(),
        icon: BookOpen,
    },
    {
        title: 'Categorías',
        value: props.counts.categories,
        href: categoriesIndex(),
        icon: Tags,
    },
    {
        title: 'Preventas',
        value: props.counts.preorders,
        href: reservationsIndex(),
        icon: Ticket,
    },
    {
        title: 'Pedidos',
        value: props.counts.orders,
        href: ordersIndex(),
        icon: Package,
    },
    {
        title: 'Mensajes',
        value: props.counts.messages,
        href: messagesIndex(),
        icon: MessageSquare,
    },
    {
        title: 'Usuarios',
        value: props.counts.users,
        href: usersIndex(),
        icon: Users,
    },
]);

const formatDate = (value: string | null): string =>
    value ? new Date(value).toLocaleString('es-MX') : '—';
</script>

<template>
    <div>
        <Head title="Panel de administración" />

        <div class="flex flex-col gap-6">
            <section class="rounded-2xl border border-border bg-card p-6">
                <p
                    class="text-xs font-semibold tracking-[0.18em] text-muted-foreground uppercase"
                >
                    Panel
                </p>
                <h1 class="mt-2 font-serif text-3xl font-semibold">
                    Administración
                </h1>
                <p class="mt-2 text-sm text-muted-foreground">
                    Un vistazo rápido a la tienda, las preventas y la actividad
                    reciente.
                </p>
            </section>

            <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                <Link
                    v-for="card in metricCards"
                    :key="card.title"
                    :href="card.href"
                    class="transition-colors hover:opacity-80"
                >
                    <Card>
                        <CardHeader
                            class="flex-row items-center justify-between"
                        >
                            <CardTitle class="text-sm font-medium">{{
                                card.title
                            }}</CardTitle>
                            <component
                                :is="card.icon"
                                class="size-4 text-muted-foreground"
                            />
                        </CardHeader>
                        <CardContent>
                            <p class="text-2xl font-semibold tabular-nums">
                                {{ card.value }}
                            </p>
                        </CardContent>
                    </Card>
                </Link>
            </section>

            <section class="grid gap-6 lg:grid-cols-2">
                <Card>
                    <CardHeader class="flex-row items-center justify-between">
                        <CardTitle class="font-serif text-xl"
                            >Preventas recientes</CardTitle
                        >
                        <Link
                            :href="reservationsIndex()"
                            class="text-xs text-muted-foreground hover:text-foreground"
                        >
                            Ver todas
                        </Link>
                    </CardHeader>
                    <CardContent class="grid gap-3">
                        <p
                            v-if="props.recentPreorders.length === 0"
                            class="rounded-xl border border-dashed border-border p-6 text-center text-sm text-muted-foreground"
                        >
                            No hay preventas recientes.
                        </p>
                        <div
                            v-for="preorder in props.recentPreorders"
                            :key="preorder.id"
                            class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-border p-4"
                        >
                            <div class="min-w-0">
                                <p class="truncate text-sm font-medium">
                                    {{
                                        preorder.product?.title ??
                                        `Preventa #${preorder.id}`
                                    }}
                                </p>
                                <p class="text-xs text-muted-foreground">
                                    {{
                                        preorder.user?.name ??
                                        'Cliente no disponible'
                                    }}
                                    · {{ formatDate(preorder.releasesAt) }}
                                </p>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-sm font-medium tabular-nums">
                                    {{ formatPrice(preorder.depositAmount) }}
                                </span>
                                <Badge variant="secondary">{{
                                    preorder.statusLabel ?? preorder.status
                                }}</Badge>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex-row items-center justify-between">
                        <CardTitle class="font-serif text-xl"
                            >Mensajes recientes</CardTitle
                        >
                        <Link
                            :href="messagesIndex()"
                            class="text-xs text-muted-foreground hover:text-foreground"
                        >
                            Ver todos
                        </Link>
                    </CardHeader>
                    <CardContent class="grid gap-3">
                        <p
                            v-if="props.recentMessages.length === 0"
                            class="rounded-xl border border-dashed border-border p-6 text-center text-sm text-muted-foreground"
                        >
                            No hay mensajes recientes.
                        </p>
                        <Link
                            v-for="message in props.recentMessages"
                            :key="message.id"
                            :href="messageShow(message.id)"
                            class="rounded-xl border border-border p-4 transition-colors hover:bg-accent/50"
                        >
                            <div
                                class="flex items-center justify-between gap-3"
                            >
                                <p class="truncate text-sm font-medium">
                                    {{ message.name }}
                                </p>
                                <Badge
                                    :variant="
                                        message.readAt ? 'outline' : 'secondary'
                                    "
                                >
                                    {{ message.readAt ? 'Leído' : 'Nuevo' }}
                                </Badge>
                            </div>
                            <p
                                class="mt-1 truncate text-xs text-muted-foreground"
                            >
                                {{ message.message }}
                            </p>
                            <p class="mt-1 text-xs text-muted-foreground">
                                {{ formatDate(message.createdAt) }}
                            </p>
                        </Link>
                    </CardContent>
                </Card>
            </section>
        </div>
    </div>
</template>
