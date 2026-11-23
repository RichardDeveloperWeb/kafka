<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { index as adminDashboard } from '@/actions/App/Http/Controllers/Admin/DashboardController';
import {
    index as ordersIndex,
    show as orderShow,
} from '@/actions/App/Http/Controllers/Admin/OrderController';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { formatPrice } from '@/lib/formatPrice';
import type { AdminOrder } from '@/types';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Administración', href: adminDashboard() },
            { title: 'Pedidos', href: ordersIndex() },
        ],
    },
});

const props = defineProps<{
    orders: AdminOrder[];
}>();

const formatDate = (value: string | null): string =>
    value ? new Date(value).toLocaleString('es-MX') : '—';
</script>

<template>
    <div>
        <Head title="Pedidos" />

        <div class="flex flex-col gap-6">
            <section>
                <h1 class="font-serif text-2xl font-semibold">Pedidos</h1>
                <p class="mt-1 text-sm text-muted-foreground">
                    Consulta y actualiza el estado de las compras.
                </p>
            </section>

            <div class="overflow-x-auto rounded-xl border border-border">
                <table class="w-full min-w-[880px] text-left text-sm">
                    <thead
                        class="bg-muted/50 text-xs text-muted-foreground uppercase"
                    >
                        <tr>
                            <th class="px-4 py-3 font-medium">Pedido</th>
                            <th class="px-4 py-3 font-medium">Cliente</th>
                            <th class="px-4 py-3 font-medium">Estado</th>
                            <th class="px-4 py-3 font-medium">Artículos</th>
                            <th class="px-4 py-3 font-medium">Total</th>
                            <th class="px-4 py-3 font-medium">Depósito</th>
                            <th class="px-4 py-3 text-right font-medium">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="order in props.orders"
                            :key="order.id"
                            class="border-t border-border"
                        >
                            <td class="px-4 py-3">
                                <p class="font-medium tabular-nums">
                                    #{{ order.id }}
                                </p>
                                <p class="text-xs text-muted-foreground">
                                    {{ formatDate(order.placedAt) }}
                                </p>
                            </td>
                            <td class="px-4 py-3">
                                <p class="font-medium">
                                    {{ order.user?.name ?? '—' }}
                                </p>
                                <p
                                    class="truncate text-xs text-muted-foreground"
                                >
                                    {{ order.user?.email ?? '' }}
                                </p>
                            </td>
                            <td class="px-4 py-3">
                                <Badge variant="secondary">{{
                                    order.statusLabel ?? order.status
                                }}</Badge>
                            </td>
                            <td class="px-4 py-3 tabular-nums">
                                {{ order.itemsCount }}
                            </td>
                            <td class="px-4 py-3 font-medium tabular-nums">
                                {{ formatPrice(order.total) }}
                            </td>
                            <td class="px-4 py-3 tabular-nums">
                                {{ formatPrice(order.depositAmount) }}
                                <p class="text-xs text-muted-foreground">
                                    Restante
                                    {{ formatPrice(order.balanceAmount) }}
                                </p>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <Button as-child size="sm" variant="outline">
                                    <Link :href="orderShow(order.id)">
                                        Ver detalle
                                    </Link>
                                </Button>
                            </td>
                        </tr>
                        <tr v-if="props.orders.length === 0">
                            <td colspan="7" class="px-4 py-12 text-center">
                                <p class="text-sm text-muted-foreground">
                                    No hay pedidos registrados.
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
