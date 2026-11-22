<script setup lang="ts">
import { reactive } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { Search, SlidersHorizontal } from '@lucide/vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import ProductCard from '@/components/public/ProductCard.vue';
import { index as catalogIndex } from '@/actions/App/Http/Controllers/CatalogController';
import type {
    CatalogCategory,
    CatalogFilters,
    FormatOption,
    PaginatedProducts,
} from '@/types';

const props = defineProps<{
    products: PaginatedProducts;
    categories: CatalogCategory[];
    formats: FormatOption[];
    filters: CatalogFilters;
}>();

const filters = reactive<CatalogFilters>({ ...props.filters });

function applyFilters(): void {
    router.get(
        catalogIndex().url,
        { ...filters },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        },
    );
}

function clearFilters(): void {
    Object.assign(filters, { category: '', q: '', format: '' });
    applyFilters();
}
</script>

<template>
    <div>
        <Head title="Catálogo" />

        <section class="border-b border-border bg-card/50">
            <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
                <p
                    class="text-xs font-semibold tracking-[0.2em] text-amber-700 uppercase dark:text-amber-400"
                >
                    Catálogo
                </p>
                <h1
                    class="mt-3 font-serif text-4xl font-semibold tracking-tight sm:text-5xl"
                >
                    Encuentra tu próxima lectura
                </h1>
                <p
                    class="mt-4 max-w-2xl text-sm leading-6 text-muted-foreground"
                >
                    Explora libros, revistas, material didáctico, audiolibros y
                    detalles para lectores. Filtra por categoría, formato o
                    busca por título, autor e ISBN.
                </p>
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <form
                class="rounded-2xl border border-border bg-background p-5"
                @submit.prevent="applyFilters"
            >
                <div class="flex items-center gap-2 text-sm font-semibold">
                    <SlidersHorizontal class="size-4" />
                    Filtros
                </div>

                <div class="mt-5 grid gap-4 md:grid-cols-4">
                    <div class="md:col-span-2">
                        <Label for="search">Buscar</Label>
                        <div class="relative mt-2">
                            <Search
                                class="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                            />
                            <Input
                                id="search"
                                v-model="filters.q"
                                type="search"
                                class="pl-9"
                                placeholder="Título, autor o ISBN"
                            />
                        </div>
                    </div>
                    <div>
                        <Label for="category">Categoría</Label>
                        <select
                            id="category"
                            v-model="filters.category"
                            class="mt-2 h-9 w-full rounded-md border border-input bg-background px-3 text-sm shadow-xs outline-none focus-visible:ring-3 focus-visible:ring-ring/50"
                        >
                            <option value="">Todas las categorías</option>
                            <option
                                v-for="category in categories"
                                :key="category.id"
                                :value="category.slug"
                            >
                                {{ category.name }}
                            </option>
                        </select>
                    </div>
                    <div>
                        <Label for="format">Formato</Label>
                        <select
                            id="format"
                            v-model="filters.format"
                            class="mt-2 h-9 w-full rounded-md border border-input bg-background px-3 text-sm shadow-xs outline-none focus-visible:ring-3 focus-visible:ring-ring/50"
                        >
                            <option value="">Todos los formatos</option>
                            <option
                                v-for="format in formats"
                                :key="format.value"
                                :value="format.value"
                            >
                                {{ format.label }}
                            </option>
                        </select>
                    </div>
                </div>

                <div class="mt-5 flex flex-wrap gap-2">
                    <Button type="submit" size="sm">Aplicar filtros</Button>
                    <Button
                        type="button"
                        size="sm"
                        variant="ghost"
                        @click="clearFilters"
                        >Limpiar</Button
                    >
                    <span
                        class="ml-auto self-center text-xs text-muted-foreground"
                    >
                        {{ products.total }}
                        {{ products.total === 1 ? 'resultado' : 'resultados' }}
                    </span>
                </div>
            </form>

            <div
                v-if="products.data.length"
                class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-4"
            >
                <ProductCard
                    v-for="product in products.data"
                    :key="product.id"
                    :product="product"
                />
            </div>
            <div
                v-else
                class="mt-8 rounded-2xl border border-dashed border-border py-16 text-center"
            >
                <p class="font-serif text-xl font-semibold">
                    No encontramos resultados
                </p>
                <p class="mt-2 text-sm text-muted-foreground">
                    Prueba con otro término o limpia los filtros.
                </p>
                <Button
                    variant="outline"
                    size="sm"
                    class="mt-5"
                    @click="clearFilters"
                    >Limpiar filtros</Button
                >
            </div>

            <nav
                v-if="products.last_page > 1"
                class="mt-10 flex flex-wrap justify-center gap-2"
                aria-label="Paginación"
            >
                <Link
                    v-for="link in products.links"
                    :key="link.label"
                    :href="link.url ?? '#'"
                    class="min-w-9 rounded-md border border-input px-3 py-2 text-center text-sm transition-colors hover:bg-accent"
                    :class="
                        link.active
                            ? 'bg-foreground text-background hover:bg-foreground'
                            : 'bg-background'
                    "
                    :aria-current="link.active ? 'page' : undefined"
                    v-html="link.label"
                />
            </nav>
        </section>
    </div>
</template>
