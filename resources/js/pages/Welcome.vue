<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import {
    ArrowRight,
    BookOpen,
    MapPin,
    MessageCircle,
    Sparkles,
} from '@lucide/vue';
import { Button } from '@/components/ui/button';
import ProductCard from '@/components/public/ProductCard.vue';
import { index as catalogIndex } from '@/actions/App/Http/Controllers/CatalogController';
import { about, contact } from '@/routes';
import type { CatalogCategory, CatalogProduct, SiteInfo } from '@/types';

defineProps<{
    featuredProducts: CatalogProduct[];
    categories: CatalogCategory[];
}>();

const site = usePage<{ site: SiteInfo }>().props.site;
</script>

<template>
    <div>
        <Head title="Inicio" />

        <section class="relative overflow-hidden bg-foreground text-background">
            <div
                class="pointer-events-none absolute -top-40 -right-40 size-120 rounded-full bg-amber-500/20 blur-3xl"
            ></div>
            <div
                class="pointer-events-none absolute -bottom-52 -left-36 size-110 rounded-full bg-orange-700/20 blur-3xl"
            ></div>

            <div
                class="relative mx-auto grid max-w-7xl gap-14 px-4 py-20 sm:px-6 lg:grid-cols-[1.1fr_0.9fr] lg:items-center lg:px-8 lg:py-28"
            >
                <div class="max-w-2xl">
                    <span
                        class="inline-flex items-center gap-2 rounded-full border border-background/20 px-3 py-1 text-xs tracking-[0.16em] uppercase"
                    >
                        <Sparkles class="size-3.5" />
                        Librería en Orizaba · desde 2026
                    </span>

                    <h1
                        class="mt-8 font-serif text-5xl leading-[1.05] font-semibold tracking-tight sm:text-6xl lg:text-7xl"
                    >
                        Libros que abren
                        <span class="block text-amber-300 italic"
                            >conversaciones.</span
                        >
                    </h1>

                    <p
                        class="mt-7 max-w-xl text-base leading-7 text-background/70 sm:text-lg"
                    >
                        {{ site.tagline }}. En {{ site.name }} reunimos lecturas
                        para todos los gustos y un espacio cálido donde leer,
                        descubrir y debatir en el corazón de Orizaba.
                    </p>

                    <div class="mt-9 flex flex-wrap gap-3">
                        <Button as-child size="lg">
                            <Link :href="catalogIndex()">
                                Explorar catálogo
                                <ArrowRight />
                            </Link>
                        </Button>
                        <Button
                            as-child
                            size="lg"
                            variant="outline"
                            class="border-background/25 bg-transparent text-background hover:bg-background/10 hover:text-background"
                        >
                            <Link :href="about()"
                                >Conocer nuestra historia</Link
                            >
                        </Button>
                    </div>

                    <dl
                        class="mt-14 grid max-w-lg grid-cols-3 gap-6 border-t border-background/15 pt-7"
                    >
                        <div>
                            <dt class="text-2xl font-semibold tabular-nums">
                                9
                            </dt>
                            <dd class="mt-1 text-xs text-background/60">
                                categorías
                            </dd>
                        </div>
                        <div>
                            <dt class="text-2xl font-semibold tabular-nums">
                                10
                            </dt>
                            <dd class="mt-1 text-xs text-background/60">
                                títulos iniciales
                            </dd>
                        </div>
                        <div>
                            <dt class="text-2xl font-semibold">1</dt>
                            <dd class="mt-1 text-xs text-background/60">
                                espacio de lectura
                            </dd>
                        </div>
                    </dl>
                </div>

                <div class="relative mx-auto w-full max-w-md">
                    <div
                        class="absolute inset-8 rotate-6 rounded-3xl border border-background/10 bg-background/5"
                    ></div>
                    <div
                        class="absolute inset-4 -rotate-3 rounded-3xl bg-amber-200/90"
                    ></div>
                    <div
                        class="relative rounded-3xl border border-background/10 bg-card p-8 text-foreground shadow-2xl"
                    >
                        <div class="flex items-center justify-between">
                            <BookOpen class="size-6" />
                            <span
                                class="text-[11px] tracking-[0.2em] text-muted-foreground uppercase"
                                >Lectura recomendada</span
                            >
                        </div>
                        <div class="mt-10 space-y-2">
                            <p
                                class="font-serif text-3xl leading-tight font-semibold"
                            >
                                Cien años de soledad
                            </p>
                            <p class="text-sm text-muted-foreground">
                                Gabriel García Márquez
                            </p>
                        </div>
                        <div class="mt-10 h-px bg-border"></div>
                        <p
                            class="mt-6 font-serif text-lg text-muted-foreground italic"
                        >
                            “Un lugar para quedarse un poco más entre páginas.”
                        </p>
                        <div
                            class="mt-8 flex items-center gap-2 text-xs text-muted-foreground"
                        >
                            <MapPin class="size-3.5" />
                            Frente al parque principal
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
            <div
                class="flex flex-col justify-between gap-5 md:flex-row md:items-end"
            >
                <div>
                    <p
                        class="text-xs font-semibold tracking-[0.2em] text-amber-700 uppercase dark:text-amber-400"
                    >
                        Nuestra selección
                    </p>
                    <h2
                        class="mt-3 font-serif text-3xl font-semibold tracking-tight sm:text-4xl"
                    >
                        Para empezar una buena lectura
                    </h2>
                    <p
                        class="mt-3 max-w-xl text-sm leading-6 text-muted-foreground"
                    >
                        Una muestra de lo que puedes encontrar en nuestros
                        estantes y en nuestro catálogo digital.
                    </p>
                </div>
                <Button as-child variant="ghost">
                    <Link :href="catalogIndex()">
                        Ver todo el catálogo
                        <ArrowRight />
                    </Link>
                </Button>
            </div>

            <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <ProductCard
                    v-for="product in featuredProducts"
                    :key="product.id"
                    :product="product"
                />
            </div>
        </section>

        <section class="border-y border-border bg-card/50">
            <div class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
                <div class="max-w-2xl">
                    <p
                        class="text-xs font-semibold tracking-[0.2em] text-amber-700 uppercase dark:text-amber-400"
                    >
                        Explora por categoría
                    </p>
                    <h2
                        class="mt-3 font-serif text-3xl font-semibold tracking-tight sm:text-4xl"
                    >
                        Hay un libro para cada curiosidad
                    </h2>
                </div>

                <div class="mt-10 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    <Link
                        v-for="category in categories"
                        :key="category.id"
                        :href="
                            catalogIndex({ query: { category: category.slug } })
                        "
                        class="group rounded-2xl border border-border bg-background p-5 transition-all hover:-translate-y-0.5 hover:border-foreground/40 hover:shadow-md"
                    >
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h3 class="font-serif text-lg font-semibold">
                                    {{ category.name }}
                                </h3>
                                <p
                                    class="mt-1 text-xs leading-5 text-muted-foreground"
                                >
                                    {{
                                        category.description ??
                                        'Descubre nuestras novedades.'
                                    }}
                                </p>
                            </div>
                            <ArrowRight
                                class="mt-1 size-4 shrink-0 text-muted-foreground transition-transform group-hover:translate-x-1"
                            />
                        </div>
                    </Link>
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
            <div
                class="grid gap-8 rounded-3xl bg-secondary/70 p-8 sm:p-12 lg:grid-cols-[1fr_auto] lg:items-center"
            >
                <div class="max-w-2xl">
                    <MessageCircle
                        class="size-6 text-amber-700 dark:text-amber-400"
                    />
                    <h2
                        class="mt-5 font-serif text-3xl leading-tight font-semibold sm:text-4xl"
                    >
                        Tu próxima conversación empieza en una página.
                    </h2>
                    <p class="mt-4 text-sm leading-6 text-muted-foreground">
                        Ven a leer, únete a nuestros clubes o escríbenos para
                        encontrar ese libro que aún no sabes que buscas.
                    </p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <Button as-child>
                        <Link :href="contact()">
                            Escríbenos
                            <ArrowRight />
                        </Link>
                    </Button>
                    <Button as-child variant="outline">
                        <Link :href="about()">Sobre nosotros</Link>
                    </Button>
                </div>
            </div>
        </section>
    </div>
</template>
