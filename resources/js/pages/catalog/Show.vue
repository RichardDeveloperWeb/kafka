<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, ArrowRight, BookOpen, Check, MapPin } from '@lucide/vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import ProductCard from '@/components/public/ProductCard.vue';
import { index as catalogIndex } from '@/actions/App/Http/Controllers/CatalogController';
import { contact } from '@/routes';
import { formatPrice } from '@/lib/formatPrice';
import type { CatalogProduct } from '@/types';

defineProps<{
    product: CatalogProduct;
    relatedProducts: CatalogProduct[];
}>();

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

                    <div class="mt-8 flex flex-wrap gap-3">
                        <Button as-child size="lg">
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
