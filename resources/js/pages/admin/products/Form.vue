<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Save } from '@lucide/vue';
import { computed, watch } from 'vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { index as adminDashboard } from '@/actions/App/Http/Controllers/Admin/DashboardController';
import {
    index as productsIndex,
    store as storeProduct,
    update as updateProduct,
} from '@/actions/App/Http/Controllers/Admin/ProductController';
import type { AdminCategory, AdminOption, AdminProduct } from '@/types';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Administración', href: adminDashboard() },
            { title: 'Productos', href: productsIndex() },
            { title: 'Detalle', href: '#' },
        ],
    },
});

const props = defineProps<{
    product: AdminProduct | null;
    categories: AdminCategory[];
    formats: AdminOption[];
    availabilities: AdminOption[];
}>();

const availabilityOptions = computed<AdminOption[]>(() =>
    props.availabilities.length > 0
        ? props.availabilities
        : [
              { value: 'en_existencia', label: 'En existencia' },
              { value: 'agotado', label: 'Agotado' },
              { value: 'bajo_pedido', label: 'Bajo pedido' },
          ],
);

const toDateInput = (value: string | null): string =>
    value ? value.slice(0, 10) : '';

const form = useForm({
    category_id: props.product?.categoryId ?? props.categories[0]?.id ?? 0,
    title: props.product?.title ?? '',
    slug: props.product?.slug ?? '',
    author: props.product?.author ?? '',
    publisher: props.product?.publisher ?? '',
    isbn: props.product?.isbn ?? '',
    format: props.product?.format ?? '',
    language: props.product?.language ?? '',
    pages: props.product?.pages ?? '',
    edition_year: props.product?.editionYear ?? '',
    synopsis: props.product?.synopsis ?? '',
    price: props.product?.price ?? 0,
    stock: props.product?.stock ?? 0,
    availability:
        props.product?.availability ??
        availabilityOptions.value[0]?.value ??
        '',
    deposit_amount: props.product?.depositAmount ?? '',
    releases_at: toDateInput(props.product?.releasesAt ?? null),
    is_active: props.product?.isActive ?? true,
});

const isPreorder = computed(() => form.availability === 'bajo_pedido');

watch(isPreorder, (value) => {
    if (!value) {
        form.deposit_amount = '';
        form.releases_at = '';
    }
});

const submit = (): void => {
    if (props.product) {
        form.patch(updateProduct(props.product.slug).url, {
            preserveScroll: true,
        });

        return;
    }

    form.post(storeProduct().url, { preserveScroll: true });
};
</script>

<template>
    <div>
        <Head :title="props.product ? 'Editar producto' : 'Nuevo producto'" />

        <div class="flex flex-col gap-6">
            <section>
                <h1 class="font-serif text-2xl font-semibold">
                    {{ props.product ? 'Editar producto' : 'Nuevo producto' }}
                </h1>
                <p class="mt-1 text-sm text-muted-foreground">
                    Completa la información bibliográfica y comercial del
                    producto.
                </p>
            </section>

            <Card class="max-w-5xl">
                <CardHeader>
                    <CardTitle class="font-serif text-xl"
                        >Datos del producto</CardTitle
                    >
                </CardHeader>
                <CardContent>
                    <form
                        class="grid gap-5 sm:grid-cols-2"
                        @submit.prevent="submit"
                    >
                        <div class="grid gap-2">
                            <Label for="title">Título</Label>
                            <Input
                                id="title"
                                v-model="form.title"
                                required
                                autocomplete="off"
                            />
                            <InputError :message="form.errors.title" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="slug">Slug</Label>
                            <Input id="slug" v-model="form.slug" required />
                            <InputError :message="form.errors.slug" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="category_id">Categoría</Label>
                            <select
                                id="category_id"
                                v-model.number="form.category_id"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none"
                                required
                            >
                                <option
                                    v-for="category in props.categories"
                                    :key="category.id"
                                    :value="category.id"
                                >
                                    {{ category.name }}
                                </option>
                            </select>
                            <InputError :message="form.errors.category_id" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="author">Autor</Label>
                            <Input id="author" v-model="form.author" />
                            <InputError :message="form.errors.author" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="publisher">Editorial</Label>
                            <Input id="publisher" v-model="form.publisher" />
                            <InputError :message="form.errors.publisher" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="isbn">ISBN</Label>
                            <Input id="isbn" v-model="form.isbn" />
                            <InputError :message="form.errors.isbn" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="format">Formato</Label>
                            <select
                                id="format"
                                v-model="form.format"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none"
                            >
                                <option value="">Sin definir</option>
                                <option
                                    v-for="format in props.formats"
                                    :key="format.value"
                                    :value="format.value"
                                >
                                    {{ format.label }}
                                </option>
                            </select>
                            <InputError :message="form.errors.format" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="language">Idioma</Label>
                            <Input id="language" v-model="form.language" />
                            <InputError :message="form.errors.language" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="pages">Páginas</Label>
                            <Input
                                id="pages"
                                v-model.number="form.pages"
                                type="number"
                                min="1"
                            />
                            <InputError :message="form.errors.pages" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="edition_year">Año de edición</Label>
                            <Input
                                id="edition_year"
                                v-model.number="form.edition_year"
                                type="number"
                                min="0"
                            />
                            <InputError :message="form.errors.edition_year" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="price">Precio</Label>
                            <Input
                                id="price"
                                v-model.number="form.price"
                                type="number"
                                min="0"
                                step="0.01"
                                required
                            />
                            <InputError :message="form.errors.price" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="stock">Stock</Label>
                            <Input
                                id="stock"
                                v-model.number="form.stock"
                                type="number"
                                min="0"
                                required
                            />
                            <InputError :message="form.errors.stock" />
                        </div>

                        <div class="grid gap-2 sm:col-span-2">
                            <Label for="synopsis">Sinopsis</Label>
                            <Textarea
                                id="synopsis"
                                v-model="form.synopsis"
                                rows="5"
                            />
                            <InputError :message="form.errors.synopsis" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="availability">Disponibilidad</Label>
                            <Select v-model="form.availability">
                                <SelectTrigger id="availability" class="w-full">
                                    <SelectValue
                                        placeholder="Selecciona una opción"
                                    />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectGroup>
                                        <SelectItem
                                            v-for="option in availabilityOptions"
                                            :key="option.value"
                                            :value="option.value"
                                        >
                                            {{ option.label }}
                                        </SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                            <InputError :message="form.errors.availability" />
                        </div>

                        <div v-if="isPreorder" class="grid gap-2">
                            <Label for="deposit_amount">
                                Depósito
                                <span class="text-muted-foreground"
                                    >(requerido en preventa)</span
                                >
                            </Label>
                            <Input
                                id="deposit_amount"
                                v-model.number="form.deposit_amount"
                                type="number"
                                min="0"
                                step="0.01"
                                :required="isPreorder"
                            />
                            <InputError :message="form.errors.deposit_amount" />
                        </div>

                        <div v-if="isPreorder" class="grid gap-2">
                            <Label for="releases_at">
                                Fecha de salida
                                <span class="text-muted-foreground"
                                    >(requerida en preventa)</span
                                >
                            </Label>
                            <Input
                                id="releases_at"
                                v-model="form.releases_at"
                                type="date"
                                :required="isPreorder"
                            />
                            <InputError :message="form.errors.releases_at" />
                        </div>

                        <label
                            for="is_active"
                            class="flex items-center gap-2 sm:col-span-2"
                        >
                            <Checkbox
                                id="is_active"
                                v-model:checked="form.is_active"
                            />
                            <span class="text-sm">Producto activo</span>
                        </label>
                        <InputError
                            class="sm:col-span-2"
                            :message="form.errors.is_active"
                        />

                        <div class="flex justify-end gap-3 sm:col-span-2">
                            <Button as-child type="button" variant="outline">
                                <Link :href="productsIndex()">Cancelar</Link>
                            </Button>
                            <Button type="submit" :disabled="form.processing">
                                <Save />
                                {{
                                    props.product
                                        ? 'Guardar cambios'
                                        : 'Crear producto'
                                }}
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
