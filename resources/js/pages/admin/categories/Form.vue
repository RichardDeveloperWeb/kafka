<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Save } from '@lucide/vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { index as adminDashboard } from '@/actions/App/Http/Controllers/Admin/DashboardController';
import {
    index as categoriesIndex,
    store as storeCategory,
    update as updateCategory,
} from '@/actions/App/Http/Controllers/Admin/CategoryController';
import type { AdminCategory } from '@/types';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Administración', href: adminDashboard() },
            { title: 'Categorías', href: categoriesIndex() },
            { title: 'Detalle', href: '#' },
        ],
    },
});

const props = defineProps<{
    category: AdminCategory | null;
}>();

const form = useForm({
    name: props.category?.name ?? '',
    slug: props.category?.slug ?? '',
    description: props.category?.description ?? '',
    is_active: props.category?.isActive ?? true,
});

const submit = (): void => {
    if (props.category) {
        form.patch(updateCategory(props.category.slug).url, {
            preserveScroll: true,
        });

        return;
    }

    form.post(storeCategory().url, { preserveScroll: true });
};
</script>

<template>
    <div>
        <Head
            :title="props.category ? 'Editar categoría' : 'Nueva categoría'"
        />

        <div class="flex flex-col gap-6">
            <section>
                <h1 class="font-serif text-2xl font-semibold">
                    {{
                        props.category ? 'Editar categoría' : 'Nueva categoría'
                    }}
                </h1>
                <p class="mt-1 text-sm text-muted-foreground">
                    Define el nombre y la descripción de la categoría.
                </p>
            </section>

            <Card class="max-w-2xl">
                <CardHeader>
                    <CardTitle class="font-serif text-xl"
                        >Datos de la categoría</CardTitle
                    >
                </CardHeader>
                <CardContent>
                    <form class="grid gap-5" @submit.prevent="submit">
                        <div class="grid gap-2">
                            <Label for="name">Nombre</Label>
                            <Input
                                id="name"
                                v-model="form.name"
                                required
                                autocomplete="off"
                            />
                            <InputError :message="form.errors.name" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="slug">Slug</Label>
                            <Input id="slug" v-model="form.slug" required />
                            <InputError :message="form.errors.slug" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="description">Descripción</Label>
                            <Textarea
                                id="description"
                                v-model="form.description"
                                rows="4"
                            />
                            <InputError :message="form.errors.description" />
                        </div>

                        <label class="flex items-center gap-3 text-sm">
                            <Checkbox
                                id="is_active"
                                v-model:checked="form.is_active"
                            />
                            Categoría activa en el catálogo
                        </label>

                        <div class="flex justify-end gap-3">
                            <Button as-child type="button" variant="outline">
                                <Link :href="categoriesIndex()">Cancelar</Link>
                            </Button>
                            <Button type="submit" :disabled="form.processing">
                                <Save />
                                {{
                                    props.category
                                        ? 'Guardar cambios'
                                        : 'Crear categoría'
                                }}
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
