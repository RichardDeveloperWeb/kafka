<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Package } from '@lucide/vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { index as catalogIndex } from '@/actions/App/Http/Controllers/CatalogController';
import { show as orderShow } from '@/actions/App/Http/Controllers/OrderController';
import { dashboard } from '@/routes';
import { formatPrice } from '@/lib/formatPrice';
import type { OrderData } from '@/types';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Mi cuenta', href: dashboard() },
            { title: 'Pedidos', href: '/pedidos' },
        ],
    },
});

defineProps<{
    orders: OrderData[];
}>();
</script>

<template>
    <div>
        <Head title="Mis pedidos" />

        <div class="flex flex-1 flex-col gap-6 p-4">
            <section>
                <p
                    class="text-xs font-semibold tracking-[0.18em] text-amber-700 uppercase dark:text-amber-400"
                >
                    Historial
                </p>
                <h1 class="mt-2 font-serif text-3xl font-semibold">
                    Mis pedidos
                </h1>
                <p class="mt-2 text-sm text-muted-foreground">
                    Consulta el estado y el detalle de tus compras.
                </p>
            </section>

            <div
                v-if="orders.length === 0"
                class="flex flex-col items-center rounded-2xl border border-dashed border-border px-6 py-16 text-center"
            >
                <Package class="size-10 text-muted-foreground/60" />
                <h2 class="mt-4 font-serif text-xl font-semibold">
                    Todavía no hay pedidos
                </h2>
                <p class="mt-2 max-w-sm text-sm text-muted-foreground">
                    Cuando confirmes una compra aparecerá aquí.
                </p>
                <Button as-child class="mt-6">
                    <Link :href="catalogIndex()">Explorar catálogo</Link>
                </Button>
            </div>

            <div v-else class="space-y-3">
                <Card v-for="order in orders" :key="order.id">
                    <CardContent
                        class="flex flex-col gap-4 p-5 sm:flex-row sm:items-center sm:justify-between"
                    >
                        <div>
                            <div class="flex flex-wrap items-center gap-3">
                                <h2 class="font-serif text-lg font-semibold">
                                    Pedido #{{ order.id }}
                                </h2>
                                <Badge variant="secondary">{{
                                    order.statusLabel
                                }}</Badge>
                            </div>
                            <p class="mt-1 text-xs text-muted-foreground">
                                {{
                                    order.placedAt
                                        ? new Date(
                                              order.placedAt,
                                          ).toLocaleString('es-MX')
                                        : '—'
                                }}
                                · {{ order.items.length }} producto(s)
                            </p>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="text-right">
                                <span class="font-semibold tabular-nums">
                                    {{ formatPrice(order.total) }}
                                </span>
                                <p
                                    v-if="order.depositAmount > 0"
                                    class="text-xs text-muted-foreground"
                                >
                                    Depósito
                                    {{ formatPrice(order.depositAmount) }} ·
                                    Restante
                                    {{ formatPrice(order.balanceAmount) }}
                                </p>
                            </div>
                            <Button as-child size="sm" variant="outline">
                                <Link :href="orderShow(order.id)"
                                    >Ver detalle</Link
                                >
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </div>
</template>
