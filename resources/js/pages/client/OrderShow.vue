<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, PackageCheck } from '@lucide/vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { index as ordersIndex } from '@/actions/App/Http/Controllers/OrderController';
import { dashboard } from '@/routes';
import { formatPrice } from '@/lib/formatPrice';
import type { OrderData, ShippingAddressSnapshot } from '@/types';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Mi cuenta', href: dashboard() },
            { title: 'Pedidos', href: ordersIndex() },
            { title: 'Detalle', href: '#' },
        ],
    },
});

const props = defineProps<{
    order: OrderData;
}>();

const addressParts = (address: ShippingAddressSnapshot): string =>
    [
        address.recipient_name,
        `${address.street ?? ''} ${address.exterior_number ?? ''}`.trim(),
        address.interior_number ? `Int. ${address.interior_number}` : null,
        address.neighborhood,
        address.city,
        address.state,
        address.postal_code,
        address.country,
    ]
        .filter(Boolean)
        .join(', ');
</script>

<template>
    <div>
        <Head :title="`Pedido #${props.order.id}`" />

        <div class="flex flex-1 flex-col gap-6 p-4">
            <section>
                <Link
                    :href="ordersIndex()"
                    class="inline-flex items-center gap-2 text-sm text-muted-foreground transition-colors hover:text-foreground"
                >
                    <ArrowLeft class="size-4" />
                    Volver a mis pedidos
                </Link>
                <div class="mt-6 flex flex-wrap items-center gap-3">
                    <p
                        class="text-xs font-semibold tracking-[0.18em] text-amber-700 uppercase dark:text-amber-400"
                    >
                        Pedido #{{ props.order.id }}
                    </p>
                    <Badge variant="secondary">{{
                        props.order.statusLabel
                    }}</Badge>
                </div>
                <p class="mt-2 text-sm text-muted-foreground">
                    Confirmado el
                    {{
                        props.order.placedAt
                            ? new Date(props.order.placedAt).toLocaleString(
                                  'es-MX',
                              )
                            : '—'
                    }}
                </p>
            </section>

            <div class="grid gap-6 lg:grid-cols-[1.5fr_0.8fr]">
                <Card>
                    <CardHeader>
                        <CardTitle class="font-serif text-xl"
                            >Productos</CardTitle
                        >
                    </CardHeader>
                    <CardContent class="space-y-3">
                        <div
                            v-for="item in props.order.items"
                            :key="item.id"
                            class="flex items-start justify-between gap-4 rounded-xl border border-border p-4"
                        >
                            <div class="min-w-0">
                                <p class="font-medium">{{ item.title }}</p>
                                <p class="text-xs text-muted-foreground">
                                    {{ item.author ?? 'Los Cuentos de Kafka' }}
                                    ·
                                    {{ item.formatLabel ?? 'Producto' }}
                                </p>
                                <p class="mt-1 text-xs text-muted-foreground">
                                    {{ item.quantity }} ×
                                    {{ formatPrice(item.unitPrice) }}
                                </p>
                            </div>
                            <span class="shrink-0 font-semibold tabular-nums">
                                {{ formatPrice(item.lineTotal) }}
                            </span>
                        </div>
                    </CardContent>
                </Card>

                <div class="space-y-6">
                    <Card>
                        <CardHeader>
                            <CardTitle class="font-serif text-xl"
                                >Entrega</CardTitle
                            >
                        </CardHeader>
                        <CardContent class="space-y-3 text-sm">
                            <p class="leading-6 text-muted-foreground">
                                {{ addressParts(props.order.shippingAddress) }}
                            </p>
                            <p
                                v-if="props.order.shippingAddress.phone"
                                class="text-xs text-muted-foreground"
                            >
                                Tel. {{ props.order.shippingAddress.phone }}
                            </p>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle class="font-serif text-xl"
                                >Totales</CardTitle
                            >
                        </CardHeader>
                        <CardContent class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-muted-foreground"
                                    >Subtotal</span
                                >
                                <span class="tabular-nums">{{
                                    formatPrice(props.order.subtotal)
                                }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-muted-foreground">Envío</span>
                                <span class="tabular-nums">{{
                                    formatPrice(props.order.shippingTotal)
                                }}</span>
                            </div>
                            <div
                                v-if="props.order.depositAmount > 0"
                                class="flex justify-between text-muted-foreground"
                            >
                                <span>Depósito de preventa</span>
                                <span class="tabular-nums">
                                    −
                                    {{ formatPrice(props.order.depositAmount) }}
                                </span>
                            </div>
                            <div
                                v-if="props.order.depositAmount > 0"
                                class="flex justify-between text-muted-foreground"
                            >
                                <span>Restante cobrado</span>
                                <span class="tabular-nums">
                                    {{ formatPrice(props.order.balanceAmount) }}
                                </span>
                            </div>
                            <div
                                class="flex justify-between border-t border-border pt-3 text-base font-semibold"
                            >
                                <span>Total</span>
                                <span class="tabular-nums">{{
                                    formatPrice(props.order.total)
                                }}</span>
                            </div>
                            <p
                                class="flex items-center gap-2 pt-2 text-xs text-muted-foreground"
                            >
                                <PackageCheck class="size-3.5" />
                                Pago simulado · listo para preparación
                            </p>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </div>
</template>
