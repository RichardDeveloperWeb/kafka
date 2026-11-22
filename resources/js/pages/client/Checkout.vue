<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, MapPin, PackageCheck } from '@lucide/vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { create as addressCreate } from '@/actions/App/Http/Controllers/AddressController';
import { index as cartIndex } from '@/actions/App/Http/Controllers/CartController';
import { store as checkoutStore } from '@/actions/App/Http/Controllers/CheckoutController';
import { dashboard } from '@/routes';
import { formatPrice } from '@/lib/formatPrice';
import type { AddressData, CartSummary } from '@/types';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Mi cuenta', href: dashboard() },
            { title: 'Carrito', href: cartIndex() },
            { title: 'Finalizar compra', href: '#' },
        ],
    },
});

defineProps<{
    cart: CartSummary;
    addresses: AddressData[];
}>();

const addressLine = (address: AddressData): string =>
    [
        `${address.street} ${address.exteriorNumber}`,
        address.interiorNumber ? `Int. ${address.interiorNumber}` : null,
        address.city,
        address.state,
        address.postalCode,
    ]
        .filter(Boolean)
        .join(', ');
</script>

<template>
    <div>
        <Head title="Finalizar compra" />

        <div class="flex flex-1 flex-col gap-6 p-4">
            <section>
                <Link
                    :href="cartIndex()"
                    class="inline-flex items-center gap-2 text-sm text-muted-foreground transition-colors hover:text-foreground"
                >
                    <ArrowLeft class="size-4" />
                    Volver al carrito
                </Link>
                <p
                    class="mt-6 text-xs font-semibold tracking-[0.18em] text-amber-700 uppercase dark:text-amber-400"
                >
                    Último paso
                </p>
                <h1 class="mt-2 font-serif text-3xl font-semibold">
                    Finalizar compra
                </h1>
                <p class="mt-2 text-sm text-muted-foreground">
                    Confirma la dirección de entrega y revisa tu pedido. El pago
                    es simulado.
                </p>
            </section>

            <div
                v-if="cart.items.length === 0"
                class="rounded-2xl border border-dashed border-border px-6 py-12 text-center"
            >
                <p class="font-serif text-xl font-semibold">
                    Tu carrito está vacío
                </p>
                <p class="mt-2 text-sm text-muted-foreground">
                    Agrega productos antes de finalizar la compra.
                </p>
                <Button as-child class="mt-5">
                    <Link :href="cartIndex()">Ir al carrito</Link>
                </Button>
            </div>

            <Form
                v-else-if="addresses.length > 0"
                v-bind="checkoutStore.form()"
                class="grid gap-6 lg:grid-cols-[1.3fr_0.7fr]"
                v-slot="{ errors, processing }"
            >
                <Card>
                    <CardHeader>
                        <CardTitle class="font-serif text-xl">
                            Dirección de entrega
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-3">
                        <label
                            v-for="address in addresses"
                            :key="address.id"
                            class="flex cursor-pointer items-start gap-3 rounded-xl border border-border p-4 transition-colors has-checked:border-foreground has-checked:bg-accent/40"
                        >
                            <input
                                type="radio"
                                name="address_id"
                                :value="address.id"
                                :default-checked="address.isDefault"
                                class="mt-1 accent-foreground"
                            />
                            <span class="min-w-0">
                                <span class="flex flex-wrap items-center gap-2">
                                    <span class="text-sm font-medium">
                                        {{ address.label ?? address.typeLabel }}
                                    </span>
                                    <span
                                        v-if="address.isDefault"
                                        class="rounded-full bg-secondary px-2 py-0.5 text-[11px] text-secondary-foreground"
                                    >
                                        Predeterminada
                                    </span>
                                </span>
                                <span
                                    class="mt-1 block text-sm text-muted-foreground"
                                >
                                    {{ address.recipientName }}
                                </span>
                                <span
                                    class="mt-1 block text-xs text-muted-foreground"
                                >
                                    {{ addressLine(address) }}
                                </span>
                            </span>
                        </label>
                        <InputError :message="errors.address_id" />
                        <Button as-child variant="link" class="px-0">
                            <Link :href="addressCreate()"
                                >Agregar otra dirección</Link
                            >
                        </Button>
                    </CardContent>
                </Card>

                <Card class="h-fit">
                    <CardHeader>
                        <CardTitle class="font-serif text-xl"
                            >Tu pedido</CardTitle
                        >
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div
                            v-for="item in cart.items"
                            :key="item.id"
                            class="flex justify-between gap-4 text-sm"
                        >
                            <span class="min-w-0">
                                <span class="block truncate font-medium">{{
                                    item.title
                                }}</span>
                                <span class="text-xs text-muted-foreground">
                                    {{ item.quantity }} ×
                                    {{ formatPrice(item.price) }}
                                </span>
                            </span>
                            <span class="shrink-0 tabular-nums">
                                {{ formatPrice(item.lineTotal) }}
                            </span>
                        </div>
                        <div
                            class="space-y-2 border-t border-border pt-4 text-sm"
                        >
                            <div class="flex justify-between">
                                <span class="text-muted-foreground"
                                    >Subtotal</span
                                >
                                <span class="tabular-nums">{{
                                    formatPrice(cart.subtotal)
                                }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-muted-foreground">Envío</span>
                                <span class="tabular-nums">
                                    {{
                                        cart.shippingTotal > 0
                                            ? formatPrice(cart.shippingTotal)
                                            : 'Gratis'
                                    }}
                                </span>
                            </div>
                            <div
                                class="flex justify-between text-base font-semibold"
                            >
                                <span>Total</span>
                                <span class="tabular-nums">{{
                                    formatPrice(cart.total)
                                }}</span>
                            </div>
                        </div>
                        <Button
                            type="submit"
                            class="w-full"
                            :disabled="processing"
                        >
                            <PackageCheck class="size-4" />
                            Confirmar pedido
                        </Button>
                        <p class="text-center text-xs text-muted-foreground">
                            No se realizará ningún cargo real.
                        </p>
                    </CardContent>
                </Card>
            </Form>

            <div
                v-else
                class="rounded-2xl border border-dashed border-border px-6 py-12 text-center"
            >
                <MapPin class="mx-auto size-10 text-muted-foreground/60" />
                <p class="mt-4 font-serif text-xl font-semibold">
                    Necesitas una dirección
                </p>
                <p class="mt-2 text-sm text-muted-foreground">
                    Agrega una dirección de entrega para continuar.
                </p>
                <Button as-child class="mt-5">
                    <Link :href="addressCreate()">Agregar dirección</Link>
                </Button>
            </div>
        </div>
    </div>
</template>
