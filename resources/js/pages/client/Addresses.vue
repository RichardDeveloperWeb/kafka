<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { MapPin, Pencil, Trash2 } from '@lucide/vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
    create as addressCreate,
    destroy as addressDestroy,
    edit as addressEdit,
} from '@/actions/App/Http/Controllers/AddressController';
import { dashboard } from '@/routes';
import type { AddressData } from '@/types';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Mi cuenta', href: dashboard() },
            { title: 'Direcciones', href: '/direcciones' },
        ],
    },
});

const props = defineProps<{
    addresses: AddressData[];
}>();

const fullAddress = (address: AddressData): string =>
    [
        `${address.street} ${address.exteriorNumber}`,
        address.interiorNumber ? `Int. ${address.interiorNumber}` : null,
        address.neighborhood,
        address.city,
        address.state,
        address.postalCode,
        address.country,
    ]
        .filter(Boolean)
        .join(', ');

const remove = (id: number): void => {
    router.delete(addressDestroy(id).url, { preserveScroll: true });
};
</script>

<template>
    <div>
        <Head title="Mis direcciones" />

        <div class="flex flex-1 flex-col gap-6 p-4">
            <section class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p
                        class="text-xs font-semibold tracking-[0.18em] text-amber-700 uppercase dark:text-amber-400"
                    >
                        Entregas
                    </p>
                    <h1 class="mt-2 font-serif text-3xl font-semibold">
                        Mis direcciones
                    </h1>
                    <p class="mt-2 text-sm text-muted-foreground">
                        Guarda tus direcciones para acelerar cada compra.
                    </p>
                </div>
                <Button as-child>
                    <Link :href="addressCreate()">Nueva dirección</Link>
                </Button>
            </section>

            <div
                v-if="props.addresses.length === 0"
                class="flex flex-col items-center rounded-2xl border border-dashed border-border px-6 py-16 text-center"
            >
                <MapPin class="size-10 text-muted-foreground/60" />
                <h2 class="mt-4 font-serif text-xl font-semibold">
                    Aún no guardas direcciones
                </h2>
                <p class="mt-2 max-w-sm text-sm text-muted-foreground">
                    Agrégala antes de finalizar tu primera compra.
                </p>
                <Button as-child class="mt-6">
                    <Link :href="addressCreate()">Agregar dirección</Link>
                </Button>
            </div>

            <div v-else class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                <Card v-for="address in props.addresses" :key="address.id">
                    <CardHeader
                        class="flex-row items-start justify-between gap-3"
                    >
                        <div>
                            <CardTitle class="font-serif text-lg">
                                {{ address.label ?? address.typeLabel }}
                            </CardTitle>
                            <p class="mt-1 text-sm text-muted-foreground">
                                {{ address.recipientName }}
                            </p>
                        </div>
                        <Badge v-if="address.isDefault">Predeterminada</Badge>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <p
                            class="min-h-10 text-sm leading-6 text-muted-foreground"
                        >
                            {{ fullAddress(address) }}
                        </p>
                        <p
                            v-if="address.phone"
                            class="text-xs text-muted-foreground"
                        >
                            Tel. {{ address.phone }}
                        </p>
                        <div class="flex gap-2">
                            <Button
                                as-child
                                variant="outline"
                                size="sm"
                                class="flex-1"
                            >
                                <Link :href="addressEdit(address.id)">
                                    <Pencil class="size-3.5" />
                                    Editar
                                </Link>
                            </Button>
                            <Button
                                type="button"
                                size="icon"
                                variant="ghost"
                                :aria-label="`Eliminar dirección ${address.id}`"
                                @click="remove(address.id)"
                            >
                                <Trash2 class="size-4 text-muted-foreground" />
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </div>
</template>
