<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import {
    ArrowLeft,
    ArrowRight,
    BookOpen,
    Check,
    Lock,
    MapPin,
    ShoppingCart,
    Ticket,
} from '@lucide/vue';
import InputError from '@/components/InputError.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import ProductCard from '@/components/public/ProductCard.vue';
import { store as cartStore } from '@/actions/App/Http/Controllers/CartController';
import { index as catalogIndex } from '@/actions/App/Http/Controllers/CatalogController';
import { store as reservationStore } from '@/actions/App/Http/Controllers/ReservationController';
import { contact, login } from '@/routes';
import { formatPrice } from '@/lib/formatPrice';
import type { CatalogProduct } from '@/types';

const props = defineProps<{
    product: CatalogProduct;
    relatedProducts: CatalogProduct[];
}>();

const user = usePage().props.auth.user;
const cartForm = useForm({ quantity: 1, product: '' });
const reservationForm = useForm({ quantity: 1, product: '' });

const addToCart = (): void => {
    cartForm.post(cartStore(props.product.slug).url, {
        preserveScroll: true,
        onSuccess: () => cartForm.reset(),
    });
};

const reserve = (): void => {
    reservationForm.post(reservationStore(props.product.slug).url, {
        preserveScroll: true,
        onSuccess: () => reservationForm.reset(),
    });
};

const metadata = (product: CatalogProduct) =>
    [
        ['Autor', product.author],
        ['Editorial', product.publisher],
        ['ISBN', product.isbn],
        ['Formato', product.formatLabel],
        ['Idioma', product.language],
        ['Páginas', product.pages],
        ['Año de edición', product.editionYear],
        ['Categoría', product.category?.name],
    ].filter(
        ([, value]) => value !== null && value !== undefined && value !== '',
    ) as [string, string | number][];
</script>

<template>
    <div>
        <Head :title="product.title" />

        <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <Link
                :href="catalogIndex()"
                class="inline-flex items-center gap-2 text-sm text-muted-foreground transition-colors hover:text-foreground"
            >
                <ArrowLeft class="size-4" />
                Volver al catálogo
            </Link>

            <div class="mt-8 grid gap-10 lg:grid-cols-[0.9fr_1.1fr]">
                <div
                    class="relative flex min-h-100 items-center justify-center overflow-hidden rounded-3xl border border-border bg-secondary/60 p-10"
                >
                    <div
                        class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(255,255,255,0.65),transparent_55%)] dark:bg-[radial-gradient(circle_at_top_left,rgba(255,255,255,0.08),transparent_55%)]"
                    ></div>
                    <BookOpen class="relative size-24 text-foreground/15" />
                    <Badge
                        :variant="product.isAvailable ? 'secondary' : 'outline'"
                        class="absolute top-5 right-5 bg-background/90"
                    >
                        {{ product.availabilityLabel ?? 'Sin información' }}
                    </Badge>
                </div>

                <div>
                    <p
                        class="text-xs font-semibold tracking-[0.2em] text-amber-700 uppercase dark:text-amber-400"
                    >
                        {{
                            product.category?.name ??
                            product.formatLabel ??
                            'Producto'
                        }}
                    </p>
                    <h1
                        class="mt-3 font-serif text-4xl leading-tight font-semibold tracking-tight sm:text-5xl"
                    >
                        {{ product.title }}
                    </h1>
                    <p
                        v-if="product.author"
                        class="mt-3 text-lg text-muted-foreground"
                    >
                        {{ product.author }}
                    </p>

                    <div class="mt-7 flex flex-wrap items-center gap-4">
                        <span class="text-3xl font-semibold tabular-nums">{{
                            formatPrice(product.price)
                        }}</span>
                        <span
                            v-if="product.isAvailable"
                            class="flex items-center gap-1.5 text-sm text-muted-foreground"
                        >
                            <Check class="size-4 text-emerald-600" />
                            {{ product.stock }} disponibles
                        </span>
                    </div>

                    <p
                        v-if="product.synopsis"
                        class="mt-7 max-w-2xl text-sm leading-7 text-muted-foreground"
                    >
                        {{ product.synopsis }}
                    </p>

                    <div v-if="user" class="mt-8 space-y-4">
                        <div
                            v-if="product.isAvailable"
                            class="grid gap-4 rounded-2xl border border-border bg-card p-5 sm:grid-cols-2"
                        >
                            <form class="space-y-3" @submit.prevent="addToCart">
                                <div class="grid gap-2">
                                    <label
                                        :for="`cart-quantity-${product.id}`"
                                        class="text-xs font-medium tracking-wide uppercase"
                                    >
                                        Cantidad
                                    </label>
                                    <Input
                                        :id="`cart-quantity-${product.id}`"
                                        v-model.number="cartForm.quantity"
                                        type="number"
                                        min="1"
                                        :max="Math.max(product.stock, 1)"
                                    />
                                    <InputError
                                        :message="cartForm.errors.quantity"
                                    />
                                    <InputError
                                        :message="cartForm.errors.product"
                                    />
                                </div>
                                <Button
                                    type="submit"
                                    size="lg"
                                    class="w-full"
                                    :disabled="cartForm.processing"
                                >
                                    <ShoppingCart class="size-4" />
                                    Agregar al carrito
                                </Button>
                            </form>

                            <form class="space-y-3" @submit.prevent="reserve">
                                <div class="grid gap-2">
                                    <label
                                        :for="`reservation-quantity-${product.id}`"
                                        class="text-xs font-medium tracking-wide uppercase"
                                    >
                                        Apartar 15 minutos
                                    </label>
                                    <Input
                                        :id="`reservation-quantity-${product.id}`"
                                        v-model.number="
                                            reservationForm.quantity
                                        "
                                        type="number"
                                        min="1"
                                        :max="Math.max(product.stock, 1)"
                                    />
                                    <InputError
                                        :message="
                                            reservationForm.errors.quantity
                                        "
                                    />
                                    <InputError
                                        :message="
                                            reservationForm.errors.product
                                        "
                                    />
                                </div>
                                <Button
                                    type="submit"
                                    size="lg"
                                    variant="outline"
                                    class="w-full"
                                    :disabled="reservationForm.processing"
                                >
                                    <Ticket class="size-4" />
                                    Apartar producto
                                </Button>
                            </form>
                        </div>

                        <div class="flex flex-wrap gap-3">
                            <Button as-child size="lg" variant="secondary">
                                <Link :href="contact()">
                                    Preguntar por este ejemplar
                                    <ArrowRight />
                                </Link>
                            </Button>
                            <Button as-child size="lg" variant="outline">
                                <Link :href="catalogIndex()"
                                    >Seguir explorando</Link
                                >
                            </Button>
                        </div>
                    </div>

                    <div v-else class="mt-8 space-y-4">
                        <div
                            class="flex flex-wrap items-center gap-3 rounded-2xl border border-border bg-card p-5"
                        >
                            <Lock class="size-5 text-muted-foreground" />
                            <p class="flex-1 text-sm text-muted-foreground">
                                Inicia sesión para agregar productos al carrito,
                                apartarlos o finalizar una compra.
                            </p>
                            <div class="flex gap-2">
                                <Button as-child>
                                    <Link :href="login()">Ingresar</Link>
                                </Button>
                                <Button as-child variant="outline">
                                    <Link :href="catalogIndex()">Volver</Link>
                                </Button>
                            </div>
                        </div>
                        <Button as-child size="lg" variant="secondary">
                            <Link :href="contact()">
                                Preguntar por este ejemplar
                                <ArrowRight />
                            </Link>
                        </Button>
                    </div>

                    <p
                        class="mt-4 flex items-center gap-2 text-xs text-muted-foreground"
                    >
                        <MapPin class="size-3.5" />
                        También puedes vernos en calle Oriente 4, frente al
                        parque principal.
                    </p>

                    <dl
                        class="mt-10 grid gap-x-8 gap-y-4 border-t border-border pt-8 sm:grid-cols-2"
                    >
                        <div
                            v-for="[label, value] in metadata(product)"
                            :key="label"
                        >
                            <dt
                                class="text-xs tracking-[0.14em] text-muted-foreground uppercase"
                            >
                                {{ label }}
                            </dt>
                            <dd class="mt-1 text-sm font-medium">
                                {{ value }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <section v-if="relatedProducts.length" class="mt-20">
                <h2 class="font-serif text-2xl font-semibold">
                    También te puede interesar
                </h2>
                <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    <ProductCard
                        v-for="related in relatedProducts"
                        :key="related.id"
                        :product="related"
                    />
                </div>
            </section>
        </section>
    </div>
</template>
