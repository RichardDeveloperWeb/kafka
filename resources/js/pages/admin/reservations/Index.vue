<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { index as adminDashboard } from '@/actions/App/Http/Controllers/Admin/DashboardController';
import {
    destroy as destroyReservation,
    index as reservationsIndex,
} from '@/actions/App/Http/Controllers/Admin/ReservationController';
import { formatPrice } from '@/lib/formatPrice';
import type { AdminPreorder } from '@/types';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Administración', href: adminDashboard() },
            { title: 'Preventas', href: reservationsIndex() },
        ],
    },
});

const props = defineProps<{
    reservations: AdminPreorder[];
}>();

const formatDate = (value: string | null): string =>
    value ? new Date(value).toLocaleString('es-MX') : '—';

const addressLabel = (address: Record<string, unknown> | null): string => {
    if (!address) {
        return '—';
    }

    return (
        ['recipient_name', 'street', 'exterior_number', 'city', 'state']
            .map((key) => address[key])
            .filter(
                (value): value is string =>
                    typeof value === 'string' && value.trim() !== '',
            )
            .join(', ') || '—'
    );
};

const cancel = (reservation: AdminPreorder): void => {
    if (
        !window.confirm(
            `¿Cancelar y reembolsar la preventa #${reservation.id}?`,
        )
    ) {
        return;
    }

    router.delete(destroyReservation(reservation.id).url, {
        preserveScroll: true,
    });
};
</script>

<template>
    <div>
        <Head title="Preventas" />

        <div class="flex flex-col gap-6">
            <section>
                <h1 class="font-serif text-2xl font-semibold">Preventas</h1>
                <p class="mt-1 text-sm text-muted-foreground">
                    Revisa los depósitos, las fechas de salida y los reembolsos.
                </p>
            </section>

            <div class="overflow-x-auto rounded-xl border border-border">
                <table class="w-full min-w-[960px] text-left text-sm">
                    <thead
                        class="bg-muted/50 text-xs text-muted-foreground uppercase"
                    >
                        <tr>
                            <th class="px-4 py-3 font-medium">Preventa</th>
                            <th class="px-4 py-3 font-medium">Producto</th>
                            <th class="px-4 py-3 font-medium">Cliente</th>
                            <th class="px-4 py-3 font-medium">Entrega</th>
                            <th class="px-4 py-3 font-medium">Estado</th>
                            <th class="px-4 py-3 font-medium">Montos</th>
                            <th class="px-4 py-3 text-right font-medium">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="reservation in props.reservations"
                            :key="reservation.id"
                            class="border-t border-border"
                        >
                            <td class="px-4 py-3 tabular-nums">
                                #{{ reservation.id }}
                                <p class="text-xs text-muted-foreground">
                                    {{ reservation.quantity }} pieza(s)
                                </p>
                            </td>
                            <td class="px-4 py-3">
                                <p class="max-w-xs truncate font-medium">
                                    {{ reservation.product?.title ?? '—' }}
                                </p>
                            </td>
                            <td class="px-4 py-3">
                                <p class="font-medium">
                                    {{ reservation.user?.name ?? '—' }}
                                </p>
                                <p
                                    class="truncate text-xs text-muted-foreground"
                                >
                                    {{ reservation.user?.email ?? '' }}
                                </p>
                            </td>
                            <td class="px-4 py-3">
                                <p class="text-xs text-muted-foreground">
                                    {{
                                        addressLabel(
                                            reservation.shippingAddress,
                                        )
                                    }}
                                </p>
                                <p class="text-xs tabular-nums">
                                    {{ formatDate(reservation.releasesAt) }}
                                </p>
                            </td>
                            <td class="px-4 py-3">
                                <Badge
                                    :variant="
                                        reservation.status === 'depositado'
                                            ? 'secondary'
                                            : 'outline'
                                    "
                                >
                                    {{
                                        reservation.statusLabel ??
                                        reservation.status
                                    }}
                                </Badge>
                                <p
                                    v-if="reservation.chargedAt"
                                    class="mt-1 text-xs text-muted-foreground"
                                >
                                    Cobrado
                                    {{ formatDate(reservation.chargedAt) }}
                                </p>
                                <p
                                    v-if="reservation.refundedAt"
                                    class="mt-1 text-xs text-muted-foreground"
                                >
                                    Reembolsado
                                    {{ formatDate(reservation.refundedAt) }}
                                </p>
                            </td>
                            <td class="px-4 py-3">
                                <p class="tabular-nums">
                                    {{ formatPrice(reservation.depositAmount) }}
                                </p>
                                <p
                                    class="text-xs text-muted-foreground tabular-nums"
                                >
                                    Restante
                                    {{ formatPrice(reservation.balanceAmount) }}
                                </p>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <Button
                                    v-if="reservation.status === 'depositado'"
                                    type="button"
                                    size="sm"
                                    variant="ghost"
                                    @click="cancel(reservation)"
                                >
                                    Cancelar y reembolsar
                                </Button>
                                <span
                                    v-else
                                    class="text-xs text-muted-foreground"
                                >
                                    —
                                </span>
                            </td>
                        </tr>
                        <tr v-if="props.reservations.length === 0">
                            <td colspan="7" class="px-4 py-12 text-center">
                                <p class="text-sm text-muted-foreground">
                                    No hay preventas registradas.
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
