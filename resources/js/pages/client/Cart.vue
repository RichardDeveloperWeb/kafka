<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowRight, ShoppingCart, Trash2 } from '@lucide/vue';
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import {
    destroy as cartDestroy,
    index as cartIndex,
    update as cartUpdate,
} from '@/actions/App/Http/Controllers/CartController';
import { index as catalogIndex } from '@/actions/App/Http/Controllers/CatalogController';
import { create as checkoutCreate } from '@/actions/App/Http/Controllers/CheckoutController';
import { dashboard } from '@/routes';
import { formatPrice } from '@/lib/formatPrice';
import type { CartSummary } from '@/types';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Mi cuenta', href: dashboard() },
            { title: 'Carrito', href: cartIndex() },
        ],
    },
});

const props = defineProps<{
    cart: CartSummary;
}>();

const quantities = ref<Record<number, number>>(
    Object.fromEntries(
        props.cart.items.map((item) => [item.id, item.quantity]),
    ),
);

const updateItem = (id: number): void => {
    router.patch(
        cartUpdate(id).url,
        {
            quantity: quantities.value[id] ?? 1,
        },
        {
            preserveScroll: true,
        },
    );
};

const removeItem = (id: number): void => {
    router.delete(cartDestroy(id).url, {
        preserveScroll: true,
    });
};
</script>

<template>
    <div>
        <Head title="Mi carrito" />

        <div class="flex flex-1 flex-col gap-6 p-4">
            <section class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p
                        class="text-xs font-semibold tracking-[0.18em] text-amber-700 uppercase dark:text-amber-400"
                    >
                        Tu selección
                    </p>
                    <h1 class="mt-2 font-serif text-3xl font-semibold">
                        Mi carrito
                    </h1>
                    <p class="mt-2 text-sm text-muted-foreground">
                        {{ props.cart.count }} producto(s) esperando tu
                        confirmación.
                    </p>
                </div>
                <Button as-child variant="outline">
                    <Link :href="catalogIndex()">Seguir comprando</Link>
                </Button>
            </section>

            <div
                v-if="props.cart.items.length === 0"
                class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-border px-6 py-16 text-center"
            >
                <ShoppingCart class="size-10 text-muted-foreground/60" />
                <h2 class="mt-4 font-serif text-xl font-semibold">
                    Tu carrito está vacío
                </h2>
                <p class="mt-2 max-w-sm text-sm text-muted-foreground">
                    Explora el catálogo y agrega tus próximas lecturas
                    favoritas.
                </p>
                <Button as-child class="mt-6">
                    <Link :href="catalogIndex()">
                        Explorar catálogo
                        <ArrowRight />
                    </Link>
                </Button>
            </div>

            <div v-else class="grid gap-6 lg:grid-cols-[1.6fr_0.9fr]">
                <div class="space-y-3">
                    <Card
                        v-for="item in props.cart.items"
                        :key="item.id"
                        class="overflow-hidden"
                    >
                        <CardContent
                            class="flex flex-col gap-4 p-5 sm:flex-row sm:items-center"
                        >
                            <div class="min-w-0 flex-1">
                                <p
                                    class="text-xs tracking-[0.14em] text-muted-foreground uppercase"
                                >
                                    {{ item.formatLabel ?? 'Producto' }}
                                </p>
                                <h2
                                    class="mt-1 font-serif text-lg font-semibold"
                                >
                                    {{ item.title }}
                                </h2>
                                <p class="text-sm text-muted-foreground">
                                    {{ item.author ?? 'Los Cuentos de Kafka' }}
                                </p>
                                <p
                                    class="mt-2 text-sm font-medium tabular-nums"
                                >
                                    {{ formatPrice(item.price) }} c/u
                                </p>
                            </div>

                            <form
                                class="flex items-center gap-2"
                                @submit.prevent="updateItem(item.id)"
                            >
                                <Input
                                    v-model.number="quantities[item.id]"
                                    type="number"
                                    min="1"
                                    :max="Math.max(item.stock, 1)"
                                    class="w-20"
                                    aria-label="Cantidad"
                                />
                                <Button
                                    type="submit"
                                    size="sm"
                                    variant="outline"
                                >
                                    Actualizar
                                </Button>
                            </form>

                            <div
                                class="flex items-center justify-between gap-4 sm:flex-col sm:items-end"
                            >
                                <span class="font-semibold tabular-nums">
                                    {{ formatPrice(item.lineTotal) }}
                                </span>
                                <Button
                                    type="button"
                                    size="icon"
                                    variant="ghost"
                                    :aria-label="`Quitar ${item.title}`"
                                    @click="removeItem(item.id)"
                                >
                                    <Trash2
                                        class="size-4 text-muted-foreground"
                                    />
                                </Button>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <Card class="h-fit">
                    <CardContent class="space-y-4 p-6">
                        <h2 class="font-serif text-xl font-semibold">
                            Resumen
                        </h2>
                        <div class="flex justify-between text-sm">
                            <span class="text-muted-foreground">Subtotal</span>
                            <span class="tabular-nums">{{
                                formatPrice(props.cart.subtotal)
                            }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-muted-foreground"
                                >Envío estimado</span
                            >
                            <span class="tabular-nums">
                                {{
                                    props.cart.shippingTotal > 0
                                        ? formatPrice(props.cart.shippingTotal)
                                        : 'Gratis'
                                }}
                            </span>
                        </div>
                        <div
                            class="flex justify-between border-t border-border pt-4 text-base font-semibold"
                        >
                            <span>Total</span>
                            <span class="tabular-nums">{{
                                formatPrice(props.cart.total)
                            }}</span>
                        </div>
                        <Button as-child class="w-full">
                            <Link :href="checkoutCreate()">
                                Finalizar compra
                                <ArrowRight />
                            </Link>
                        </Button>
                        <p class="text-center text-xs text-muted-foreground">
                            Pago simulado · sin cargos reales
                        </p>
                    </CardContent>
                </Card>
            </div>
        </div>
    </div>
</template>
