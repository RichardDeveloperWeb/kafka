<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { CalendarDays, Ticket } from '@lucide/vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
    index as catalogIndex,
    show as catalogShow,
} from '@/actions/App/Http/Controllers/CatalogController';
import { show as orderShow } from '@/actions/App/Http/Controllers/OrderController';
import { destroy as reservationDestroy } from '@/actions/App/Http/Controllers/ReservationController';
import { dashboard } from '@/routes';
import { formatPrice } from '@/lib/formatPrice';
import type { ReservationData } from '@/types';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Mi cuenta', href: dashboard() },
            { title: 'Preventas', href: '/apartados' },
        ],
    },
});

defineProps<{
    reservations: ReservationData[];
}>();

const cancel = (id: number): void => {
    router.delete(reservationDestroy(id).url, { preserveScroll: true });
};

const formatDate = (value: string | null): string =>
    value
        ? new Date(value).toLocaleString('es-MX', {
              day: 'numeric',
              month: 'long',
              year: 'numeric',
          })
        : '—';
</script>

<template>
    <div>
        <Head title="Mis preventas" />

        <div class="flex flex-1 flex-col gap-6 p-4">
            <section>
                <p
                    class="text-xs font-semibold tracking-[0.18em] text-amber-700 uppercase dark:text-amber-400"
                >
                    Reservas con depósito
                </p>
                <h1 class="mt-2 font-serif text-3xl font-semibold">
                    Mis preventas
                </h1>
                <p
                    class="mt-2 flex items-center gap-2 text-sm text-muted-foreground"
                >
                    <CalendarDays class="size-4" />
                    El faltante se cobra automáticamente en la fecha de salida.
                    Puedes cancelar antes de esa fecha y recuperar tu depósito.
                </p>
            </section>

            <div
                v-if="reservations.length === 0"
                class="flex flex-col items-center rounded-2xl border border-dashed border-border px-6 py-16 text-center"
            >
                <Ticket class="size-10 text-muted-foreground/60" />
                <h2 class="mt-4 font-serif text-xl font-semibold">
                    No tienes preventas
                </h2>
                <p class="mt-2 max-w-sm text-sm text-muted-foreground">
                    Apartalos con un depósito desde la página de un producto y
                    recíbelos cuando se lancen.
                </p>
                <Button as-child class="mt-6">
                    <Link :href="catalogIndex()">Explorar catálogo</Link>
                </Button>
            </div>

            <div v-else class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                <Card v-for="reservation in reservations" :key="reservation.id">
                    <CardHeader
                        class="flex-row items-start justify-between gap-3"
                    >
                        <div class="min-w-0">
                            <CardTitle class="font-serif text-lg leading-snug">
                                {{ reservation.product.title }}
                            </CardTitle>
                            <p class="mt-1 text-xs text-muted-foreground">
                                {{
                                    reservation.product.author ??
                                    'Los Cuentos de Kafka'
                                }}
                            </p>
                        </div>
                        <Badge
                            :variant="
                                reservation.isCancellable ||
                                reservation.status === 'depositado'
                                    ? 'secondary'
                                    : 'outline'
                            "
                        >
                            {{ reservation.statusLabel }}
                        </Badge>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="flex justify-between text-sm">
                            <span class="text-muted-foreground">Cantidad</span>
                            <span class="tabular-nums">{{
                                reservation.quantity
                            }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-muted-foreground">Depósito</span>
                            <span class="font-medium tabular-nums">
                                {{ formatPrice(reservation.depositAmount) }}
                            </span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-muted-foreground"
                                >Restante estimado</span
                            >
                            <span class="tabular-nums">
                                {{ formatPrice(reservation.balanceAmount) }}
                            </span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-muted-foreground">Salida</span>
                            <span class="tabular-nums">
                                {{ formatDate(reservation.releasesAt) }}
                            </span>
                        </div>
                        <div class="flex flex-wrap gap-2 pt-2">
                            <Button
                                as-child
                                variant="outline"
                                size="sm"
                                class="flex-1"
                            >
                                <Link
                                    :href="
                                        catalogShow(reservation.product.slug)
                                    "
                                >
                                    Ver producto
                                </Link>
                            </Button>
                            <Button
                                v-if="reservation.orderId"
                                as-child
                                size="sm"
                                variant="secondary"
                                class="flex-1"
                            >
                                <Link :href="orderShow(reservation.orderId)">
                                    Ver pedido
                                </Link>
                            </Button>
                            <Button
                                v-if="reservation.isCancellable"
                                type="button"
                                size="sm"
                                variant="ghost"
                                @click="cancel(reservation.id)"
                            >
                                Cancelar
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </div>
</template>
