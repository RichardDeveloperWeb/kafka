<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Pencil, Plus, Trash2 } from '@lucide/vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { index as adminDashboard } from '@/actions/App/Http/Controllers/Admin/DashboardController';
import {
    create as createCategory,
    destroy as destroyCategory,
    edit as editCategory,
    index as categoriesIndex,
} from '@/actions/App/Http/Controllers/Admin/CategoryController';
import type { AdminCategory } from '@/types';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Administración', href: adminDashboard() },
            { title: 'Categorías', href: categoriesIndex() },
        ],
    },
});

const props = defineProps<{
    categories: AdminCategory[];
}>();

const destroy = (category: AdminCategory): void => {
    if (!window.confirm(`¿Eliminar "${category.name}"?`)) {
        return;
    }

    router.delete(destroyCategory(category.slug).url, { preserveScroll: true });
};
</script>

<template>
    <div>
        <Head title="Categorías" />

        <div class="flex flex-col gap-6">
            <section class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <h1 class="font-serif text-2xl font-semibold">
                        Categorías
                    </h1>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Organiza los productos del catálogo.
                    </p>
                </div>
                <Button as-child>
                    <Link :href="createCategory()">
                        <Plus />
                        Nueva categoría
                    </Link>
                </Button>
            </section>

            <div class="overflow-x-auto rounded-xl border border-border">
                <table class="w-full min-w-[640px] text-left text-sm">
                    <thead
                        class="bg-muted/50 text-xs text-muted-foreground uppercase"
                    >
                        <tr>
                            <th class="px-4 py-3 font-medium">Nombre</th>
                            <th class="px-4 py-3 font-medium">Slug</th>
                            <th class="px-4 py-3 font-medium">Descripción</th>
                            <th class="px-4 py-3 font-medium">Productos</th>
                            <th class="px-4 py-3 text-right font-medium">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="category in props.categories"
                            :key="category.id"
                            class="border-t border-border"
                        >
                            <td class="px-4 py-3 font-medium">
                                {{ category.name }}
                            </td>
                            <td class="px-4 py-3 text-muted-foreground">
                                {{ category.slug }}
                            </td>
                            <td class="px-4 py-3">
                                <span
                                    class="block max-w-xs truncate text-muted-foreground"
                                >
                                    {{ category.description ?? '—' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <Badge variant="secondary">
                                    {{ category.productsCount ?? 0 }}
                                </Badge>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-2">
                                    <Button
                                        as-child
                                        size="sm"
                                        variant="ghost"
                                        :aria-label="`Editar ${category.name}`"
                                    >
                                        <Link
                                            :href="editCategory(category.slug)"
                                        >
                                            <Pencil />
                                            <span class="sr-only">Editar</span>
                                        </Link>
                                    </Button>
                                    <Button
                                        type="button"
                                        size="sm"
                                        variant="ghost"
                                        :aria-label="`Eliminar ${category.name}`"
                                        @click="destroy(category)"
                                    >
                                        <Trash2 />
                                        <span class="sr-only">Eliminar</span>
                                    </Button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="props.categories.length === 0">
                            <td colspan="5" class="px-4 py-12 text-center">
                                <p class="text-sm text-muted-foreground">
                                    Todavía no hay categorías registradas.
                                </p>
                                <Button as-child class="mt-4" size="sm">
                                    <Link :href="createCategory()">
                                        Crear la primera
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
