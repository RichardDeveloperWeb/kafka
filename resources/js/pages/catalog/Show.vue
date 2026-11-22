<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import {
    ArrowLeft,
    ArrowRight,
    BookOpen,
    CalendarDays,
    Check,
    Lock,
    MapPin,
    ShoppingCart,
    Ticket,
} from '@lucide/vue';
import { computed, watch } from 'vue';
import InputError from '@/components/InputError.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import ProductCard from '@/components/public/ProductCard.vue';
import { create as addressCreate } from '@/actions/App/Http/Controllers/AddressController';
import { store as cartStore } from '@/actions/App/Http/Controllers/CartController';
import { index as catalogIndex } from '@/actions/App/Http/Controllers/CatalogController';
import { store as reservationStore } from '@/actions/App/Http/Controllers/ReservationController';
import { contact, login } from '@/routes';
import { formatPrice } from '@/lib/formatPrice';
import type { AddressData, CatalogProduct } from '@/types';

const props = defineProps<{
    product: CatalogProduct;
    addresses: AddressData[];
    relatedProducts: CatalogProduct[];
}>();

const user = usePage().props.auth.user;
const cartForm = useForm({ quantity: 1, product: '' });
const reservationForm = useForm({
    quantity: 1,
    deposit_amount: props.product.depositMinimum ?? 0,
    product: '',
    address_id:
        props.addresses.find((address) => address.isDefault)?.id ??
        props.addresses[0]?.id ??
        0,
});

const minimumDeposit = computed(
    () =>
        Math.round(
            (props.product.depositMinimum ?? 0) *
                reservationForm.quantity *
                100,
        ) / 100,
);
const maximumDeposit = computed(
    () =>
        Math.round(props.product.price * reservationForm.quantity * 100) / 100,
);
const releaseDate = computed(() =>
    props.product.releasesAt
        ? new Date(props.product.releasesAt).toLocaleDateString('es-MX', {
              day: 'numeric',
              month: 'long',
              year: 'numeric',
          })
        : null,
);

watch(
    () => reservationForm.quantity,
    (quantity) => {
        if (quantity < 1) {
            return;
        }

        reservationForm.deposit_amount = Math.min(
            Math.max(reservationForm.deposit_amount, minimumDeposit.value),
            maximumDeposit.value,
        );
    },
);

const addToCart = (): void => {
    cartForm.post(cartStore(props.product.slug).url, {
        preserveScroll: true,
        onSuccess: () => cartForm.reset(),
    });
};

const reserve = (): void => {
    reservationForm.post(reservationStore(props.product.slug).url, {
        preserveScroll: true,
        onSuccess: () => {
            reservationForm.quantity = 1;
            reservationForm.deposit_amount = props.product.depositMinimum ?? 0;
        },
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
                        :variant="
                            product.isPreorder
                                ? 'default'
                                : product.isAvailable
                                  ? 'secondary'
                                  : 'outline'
                        "
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
                            v-if="product.isPreorder && releaseDate"
                            class="flex items-center gap-1.5 text-sm text-muted-foreground"
                        >
                            <CalendarDays class="size-4" />
                            Sale el {{ releaseDate }}
                        </span>
                        <span
                            v-else-if="product.isAvailable"
                            class="flex items-center gap-1.5 text-sm text-muted-foreground"
                        >
                            <Check class="size-4 text-emerald-600" />
                            {{ product.stock }} disponibles
                        </span>
                    </div>

                    <div
                        v-if="product.isPreorder"
                        class="mt-6 rounded-2xl border border-amber-200 bg-amber-50/70 p-4 text-sm leading-6 text-amber-900 dark:border-amber-900/50 dark:bg-amber-950/20 dark:text-amber-200"
                    >
                        <p class="font-medium">Preventa con depósito</p>
                        <p class="mt-1">
                            Apartalo desde {{ formatPrice(minimumDeposit) }}. El
                            resto se cobrará automáticamente en la fecha de
                            salida y prepararemos tu envío.
                        </p>
                    </div>

                    <p
                        v-if="product.synopsis"
                        class="mt-7 max-w-2xl text-sm leading-7 text-muted-foreground"
                    >
                        {{ product.synopsis }}
                    </p>

                    <div v-if="user" class="mt-8 space-y-4">
                        <form
                            v-if="product.isPreorder"
                            class="grid gap-4 rounded-2xl border border-border bg-card p-5 sm:grid-cols-2"
                            @submit.prevent="reserve"
                        >
                            <div class="space-y-3">
                                <div class="grid gap-2">
                                    <label
                                        :for="`preorder-quantity-${product.id}`"
                                        class="text-xs font-medium tracking-wide uppercase"
                                    >
                                        Cantidad
                                    </label>
                                    <Input
                                        :id="`preorder-quantity-${product.id}`"
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
                                </div>
                                <div class="grid gap-2">
                                    <label
                                        :for="`preorder-deposit-${product.id}`"
                                        class="text-xs font-medium tracking-wide uppercase"
                                    >
                                        Depósito
                                    </label>
                                    <Input
                                        :id="`preorder-deposit-${product.id}`"
                                        v-model.number="
                                            reservationForm.deposit_amount
                                        "
                                        type="number"
                                        step="0.01"
                                        :min="minimumDeposit"
                                        :max="maximumDeposit"
                                    />
                                    <p class="text-xs text-muted-foreground">
                                        Entre
                                        {{ formatPrice(minimumDeposit) }} y
                                        {{ formatPrice(maximumDeposit) }}
                                    </p>
                                    <InputError
                                        :message="
                                            reservationForm.errors
                                                .deposit_amount
                                        "
                                    />
                                </div>
                            </div>

                            <div class="space-y-3">
                                <div class="grid gap-2">
                                    <label
                                        :for="`preorder-address-${product.id}`"
                                        class="text-xs font-medium tracking-wide uppercase"
                                    >
                                        Dirección de envío
                                    </label>
                                    <select
                                        :id="`preorder-address-${product.id}`"
                                        v-model.number="
                                            reservationForm.address_id
                                        "
                                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                                    >
                                        <option :value="0" disabled>
                                            Selecciona una dirección
                                        </option>
                                        <option
                                            v-for="address in addresses"
                                            :key="address.id"
                                            :value="address.id"
                                        >
                                            {{ address.label ?? address.city }}
                                            ·
                                            {{ address.recipientName }}
                                        </option>
                                    </select>
                                    <InputError
                                        :message="
                                            reservationForm.errors.address_id
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
                                    class="w-full"
                                    :disabled="
                                        reservationForm.processing ||
                                        addresses.length === 0
                                    "
                                >
                                    <Ticket class="size-4" />
                                    Apartar preventa
                                </Button>
                                <Link
                                    v-if="addresses.length === 0"
                                    :href="addressCreate()"
                                    class="block text-center text-xs text-muted-foreground underline underline-offset-4 hover:text-foreground"
                                >
                                    Agrega una dirección para apartar
                                </Link>
                            </div>
                        </form>

                        <form
                            v-else-if="product.isAvailable"
                            class="grid gap-4 rounded-2xl border border-border bg-card p-5 sm:grid-cols-2"
                            @submit.prevent="addToCart"
                        >
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
                                class="w-full self-end"
                                :disabled="cartForm.processing"
                            >
                                <ShoppingCart class="size-4" />
                                Agregar al carrito
                            </Button>
                        </form>

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
                                apartar preventas o finalizar una compra.
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
