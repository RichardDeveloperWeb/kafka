<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Pencil, Plus, Trash2 } from '@lucide/vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
    create as createProduct,
    destroy as destroyProduct,
    edit as editProduct,
    index as productsIndex,
} from '@/actions/App/Http/Controllers/Admin/ProductController';
import { index as adminDashboard } from '@/actions/App/Http/Controllers/Admin/DashboardController';
import { formatPrice } from '@/lib/formatPrice';
import type { AdminCategory, AdminProduct } from '@/types';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Administración', href: adminDashboard() },
            { title: 'Productos', href: productsIndex() },
        ],
    },
});

const props = defineProps<{
    products: AdminProduct[];
    categories: AdminCategory[];
}>();

const destroy = (product: AdminProduct): void => {
    if (!window.confirm(`¿Eliminar "${product.title}"?`)) {
        return;
    }

    router.delete(destroyProduct(product.slug).url, { preserveScroll: true });
};
</script>

<template>
    <div>
        <Head title="Productos" />

        <div class="flex flex-col gap-6">
            <section class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <h1 class="font-serif text-2xl font-semibold">Productos</h1>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Administra el catálogo, los precios y las preventas.
                    </p>
                </div>
                <Button as-child>
                    <Link :href="createProduct()">
                        <Plus />
                        Nuevo producto
                    </Link>
                </Button>
            </section>

            <Card v-if="props.categories.length > 0">
                <CardHeader>
                    <CardTitle class="text-sm font-medium"
                        >Categorías</CardTitle
                    >
                </CardHeader>
                <CardContent class="flex flex-wrap gap-2">
                    <Badge
                        v-for="category in props.categories"
                        :key="category.id"
                        variant="outline"
                    >
                        {{ category.name }}
                        <span class="tabular-nums">{{
                            category.productsCount ?? 0
                        }}</span>
                    </Badge>
                </CardContent>
            </Card>

            <div class="overflow-x-auto rounded-xl border border-border">
                <table class="w-full min-w-[720px] text-left text-sm">
                    <thead
                        class="bg-muted/50 text-xs text-muted-foreground uppercase"
                    >
                        <tr>
                            <th class="px-4 py-3 font-medium">Producto</th>
                            <th class="px-4 py-3 font-medium">Categoría</th>
                            <th class="px-4 py-3 font-medium">Precio</th>
                            <th class="px-4 py-3 font-medium">Stock</th>
                            <th class="px-4 py-3 font-medium">Estado</th>
                            <th class="px-4 py-3 text-right font-medium">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="product in props.products"
                            :key="product.id"
                            class="border-t border-border"
                        >
                            <td class="px-4 py-3">
                                <p class="max-w-xs truncate font-medium">
                                    {{ product.title }}
                                </p>
                                <p
                                    class="truncate text-xs text-muted-foreground"
                                >
                                    {{ product.author ?? 'Sin autor' }}
                                </p>
                            </td>
                            <td class="px-4 py-3 text-muted-foreground">
                                {{ product.categoryName ?? '—' }}
                            </td>
                            <td class="px-4 py-3 tabular-nums">
                                {{ formatPrice(product.price) }}
                            </td>
                            <td class="px-4 py-3 tabular-nums">
                                {{ product.stock }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-2">
                                    <Badge
                                        :variant="
                                            product.isActive
                                                ? 'secondary'
                                                : 'outline'
                                        "
                                    >
                                        {{
                                            product.isActive
                                                ? 'Activo'
                                                : 'Inactivo'
                                        }}
                                    </Badge>
                                    <Badge
                                        v-if="product.isPreorder"
                                        variant="outline"
                                    >
                                        Preventa
                                    </Badge>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-2">
                                    <Button
                                        as-child
                                        size="sm"
                                        variant="ghost"
                                        :aria-label="`Editar ${product.title}`"
                                    >
                                        <Link :href="editProduct(product.slug)">
                                            <Pencil />
                                            <span class="sr-only">Editar</span>
                                        </Link>
                                    </Button>
                                    <Button
                                        type="button"
                                        size="sm"
                                        variant="ghost"
                                        :aria-label="`Eliminar ${product.title}`"
                                        @click="destroy(product)"
                                    >
                                        <Trash2 />
                                        <span class="sr-only">Eliminar</span>
                                    </Button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="props.products.length === 0">
                            <td colspan="6" class="px-4 py-12 text-center">
                                <p class="text-sm text-muted-foreground">
                                    Todavía no hay productos registrados.
                                </p>
                                <Button as-child class="mt-4" size="sm">
                                    <Link :href="createProduct()">
                                        Crear el primero
                                    </Link>
                                </Button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
