<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Save } from '@lucide/vue';
import { computed } from 'vue';
import InputError from '@/components/InputError.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { index as adminDashboard } from '@/actions/App/Http/Controllers/Admin/DashboardController';
import {
    index as ordersIndex,
    update as updateOrder,
} from '@/actions/App/Http/Controllers/Admin/OrderController';
import { formatPrice } from '@/lib/formatPrice';
import type { AdminOption, AdminOrderDetail } from '@/types';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Administración', href: adminDashboard() },
            { title: 'Pedidos', href: ordersIndex() },
            { title: 'Detalle', href: '#' },
        ],
    },
});

const props = defineProps<{
    order: AdminOrderDetail;
    statuses: AdminOption[];
}>();

const form = useForm({
    status: props.order.status,
});

const statusOptions = computed<AdminOption[]>(() => {
    const options = [...props.statuses];

    if (
        props.order.status &&
        !options.some((option) => option.value === props.order.status)
    ) {
        options.unshift({
            value: props.order.status,
            label: props.order.statusLabel ?? props.order.status,
        });
    }

    return options;
});

const addressLabel = (address: Record<string, unknown> | null): string => {
    if (!address) {
        return '—';
    }

    return (
        [
            'recipient_name',
            'street',
            'exterior_number',
            'interior_number',
            'neighborhood',
            'city',
            'state',
            'postal_code',
            'country',
        ]
            .map((key) => address[key])
            .filter(
                (value): value is string =>
                    typeof value === 'string' && value.trim() !== '',
            )
            .join(', ') || '—'
    );
};

const submit = (): void => {
    form.patch(updateOrder(props.order.id).url, { preserveScroll: true });
};
</script>

<template>
    <div>
        <Head :title="`Pedido #${props.order.id}`" />

        <div class="flex flex-col gap-6">
            <section>
                <Link
                    :href="ordersIndex()"
                    class="inline-flex items-center gap-2 text-sm text-muted-foreground transition-colors hover:text-foreground"
                >
                    <ArrowLeft class="size-4" />
                    Volver a pedidos
                </Link>
                <div class="mt-6 flex flex-wrap items-center gap-3">
                    <h1 class="font-serif text-2xl font-semibold">
                        Pedido #{{ props.order.id }}
                    </h1>
                    <Badge variant="secondary">{{
                        props.order.statusLabel ?? props.order.status
                    }}</Badge>
                </div>
                <p class="mt-1 text-sm text-muted-foreground">
                    {{ props.order.user?.name ?? 'Cliente no disponible' }} ·
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
                            >Artículos</CardTitle
                        >
                    </CardHeader>
                    <CardContent class="grid gap-3">
                        <div
                            v-for="item in props.order.items"
                            :key="item.id"
                            class="flex flex-wrap items-start justify-between gap-4 rounded-xl border border-border p-4"
                        >
                            <div class="min-w-0">
                                <p class="truncate font-medium">
                                    {{ item.title }}
                                </p>
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
                            <span class="font-medium tabular-nums">
                                {{ formatPrice(item.lineTotal) }}
                            </span>
                        </div>
                        <p
                            v-if="props.order.items.length === 0"
                            class="rounded-xl border border-dashed border-border p-6 text-center text-sm text-muted-foreground"
                        >
                            Este pedido no tiene artículos.
                        </p>
                    </CardContent>
                </Card>

                <div class="grid gap-6">
                    <Card>
                        <CardHeader>
                            <CardTitle class="font-serif text-xl"
                                >Estado</CardTitle
                            >
                        </CardHeader>
                        <CardContent>
                            <form class="grid gap-3" @submit.prevent="submit">
                                <div class="grid gap-2">
                                    <Label for="status"
                                        >Estatus del pedido</Label
                                    >
                                    <Select v-model="form.status">
                                        <SelectTrigger
                                            id="status"
                                            class="w-full"
                                        >
                                            <SelectValue
                                                placeholder="Selecciona un estatus"
                                            />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectGroup>
                                                <SelectItem
                                                    v-for="option in statusOptions"
                                                    :key="option.value"
                                                    :value="option.value"
                                                >
                                                    {{ option.label }}
                                                </SelectItem>
                                            </SelectGroup>
                                        </SelectContent>
                                    </Select>
                                    <InputError :message="form.errors.status" />
                                </div>
                                <Button
                                    type="submit"
                                    class="justify-self-start"
                                    :disabled="form.processing"
                                >
                                    <Save />
                                    Actualizar estatus
                                </Button>
                            </form>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle class="font-serif text-xl"
                                >Entrega y totales</CardTitle
                            >
                        </CardHeader>
                        <CardContent class="grid gap-4 text-sm">
                            <p class="leading-6 text-muted-foreground">
                                {{ addressLabel(props.order.shippingAddress) }}
                            </p>
                            <div class="grid gap-2">
                                <div class="flex justify-between">
                                    <span class="text-muted-foreground"
                                        >Subtotal</span
                                    >
                                    <span class="tabular-nums">{{
                                        formatPrice(props.order.subtotal)
                                    }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-muted-foreground"
                                        >Envío</span
                                    >
                                    <span class="tabular-nums">{{
                                        formatPrice(props.order.shippingTotal)
                                    }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-muted-foreground"
                                        >Depósito</span
                                    >
                                    <span class="tabular-nums">{{
                                        formatPrice(props.order.depositAmount)
                                    }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-muted-foreground"
                                        >Restante</span
                                    >
                                    <span class="tabular-nums">{{
                                        formatPrice(props.order.balanceAmount)
                                    }}</span>
                                </div>
                                <div
                                    class="flex justify-between border-t border-border pt-3 text-base font-semibold"
                                >
                                    <span>Total</span>
                                    <span class="tabular-nums">{{
                                        formatPrice(props.order.total)
                                    }}</span>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </div>
</template>
