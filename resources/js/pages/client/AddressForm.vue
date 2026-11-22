<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Save } from '@lucide/vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    store as addressStore,
    update as addressUpdate,
} from '@/actions/App/Http/Controllers/AddressController';
import { dashboard } from '@/routes';
import { index as addressesIndex } from '@/routes/addresses';
import type { AddressData } from '@/types';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Mi cuenta', href: dashboard() },
            { title: 'Direcciones', href: addressesIndex() },
            { title: 'Detalle', href: '#' },
        ],
    },
});

const props = defineProps<{
    address: AddressData | null;
}>();

const form = useForm({
    type: props.address?.type ?? 'envio',
    label: props.address?.label ?? '',
    recipient_name: props.address?.recipientName ?? '',
    street: props.address?.street ?? '',
    exterior_number: props.address?.exteriorNumber ?? '',
    interior_number: props.address?.interiorNumber ?? '',
    neighborhood: props.address?.neighborhood ?? '',
    city: props.address?.city ?? '',
    state: props.address?.state ?? 'Veracruz',
    postal_code: props.address?.postalCode ?? '',
    country: props.address?.country ?? 'MX',
    phone: props.address?.phone ?? '',
    is_default: props.address?.isDefault ?? false,
});

const submit = (): void => {
    if (props.address) {
        form.patch(addressUpdate(props.address.id).url, {
            preserveScroll: true,
        });

        return;
    }

    form.post(addressStore().url, { preserveScroll: true });
};
</script>

<template>
    <div>
        <Head :title="props.address ? 'Editar dirección' : 'Nueva dirección'" />

        <div class="flex flex-1 flex-col gap-6 p-4">
            <section>
                <p
                    class="text-xs font-semibold tracking-[0.18em] text-amber-700 uppercase dark:text-amber-400"
                >
                    Entregas
                </p>
                <h1 class="mt-2 font-serif text-3xl font-semibold">
                    {{ props.address ? 'Editar dirección' : 'Nueva dirección' }}
                </h1>
                <p class="mt-2 text-sm text-muted-foreground">
                    Usaremos estos datos al confirmar tus pedidos.
                </p>
            </section>

            <Card class="max-w-4xl">
                <CardHeader>
                    <CardTitle class="font-serif text-xl"
                        >Datos de la dirección</CardTitle
                    >
                </CardHeader>
                <CardContent>
                    <form
                        class="grid gap-5 sm:grid-cols-2"
                        @submit.prevent="submit"
                    >
                        <div class="grid gap-2">
                            <Label for="type">Tipo</Label>
                            <select
                                id="type"
                                v-model="form.type"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none"
                            >
                                <option value="envio">Envío</option>
                                <option value="facturacion">Facturación</option>
                            </select>
                            <InputError :message="form.errors.type" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="label">Etiqueta (opcional)</Label>
                            <Input
                                id="label"
                                v-model="form.label"
                                type="text"
                                placeholder="Casa, oficina..."
                            />
                            <InputError :message="form.errors.label" />
                        </div>

                        <div class="grid gap-2 sm:col-span-2">
                            <Label for="recipient_name">Quién recibe</Label>
                            <Input
                                id="recipient_name"
                                v-model="form.recipient_name"
                                type="text"
                                autocomplete="name"
                                required
                            />
                            <InputError :message="form.errors.recipient_name" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="street">Calle</Label>
                            <Input
                                id="street"
                                v-model="form.street"
                                type="text"
                                required
                            />
                            <InputError :message="form.errors.street" />
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div class="grid gap-2">
                                <Label for="exterior_number"
                                    >Número exterior</Label
                                >
                                <Input
                                    id="exterior_number"
                                    v-model="form.exterior_number"
                                    type="text"
                                    required
                                />
                                <InputError
                                    :message="form.errors.exterior_number"
                                />
                            </div>
                            <div class="grid gap-2">
                                <Label for="interior_number">Interior</Label>
                                <Input
                                    id="interior_number"
                                    v-model="form.interior_number"
                                    type="text"
                                />
                                <InputError
                                    :message="form.errors.interior_number"
                                />
                            </div>
                        </div>

                        <div class="grid gap-2">
                            <Label for="neighborhood">Colonia (opcional)</Label>
                            <Input
                                id="neighborhood"
                                v-model="form.neighborhood"
                                type="text"
                            />
                            <InputError :message="form.errors.neighborhood" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="city">Ciudad</Label>
                            <Input
                                id="city"
                                v-model="form.city"
                                type="text"
                                required
                            />
                            <InputError :message="form.errors.city" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="state">Estado</Label>
                            <Input
                                id="state"
                                v-model="form.state"
                                type="text"
                                required
                            />
                            <InputError :message="form.errors.state" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="postal_code">Código postal</Label>
                            <Input
                                id="postal_code"
                                v-model="form.postal_code"
                                type="text"
                                maxlength="10"
                                required
                            />
                            <InputError :message="form.errors.postal_code" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="country">País</Label>
                            <Input
                                id="country"
                                v-model="form.country"
                                type="text"
                                maxlength="2"
                                required
                            />
                            <InputError :message="form.errors.country" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="phone">Teléfono (opcional)</Label>
                            <Input id="phone" v-model="form.phone" type="tel" />
                            <InputError :message="form.errors.phone" />
                        </div>

                        <label
                            for="is_default"
                            class="flex items-center gap-2 sm:col-span-2"
                        >
                            <Checkbox
                                id="is_default"
                                v-model:checked="form.is_default"
                            />
                            <span class="text-sm"
                                >Usar como dirección predeterminada</span
                            >
                        </label>

                        <div class="flex justify-end gap-3 sm:col-span-2">
                            <Button as-child type="button" variant="outline">
                                <Link :href="addressesIndex()">Cancelar</Link>
                            </Button>
                            <Button type="submit" :disabled="form.processing">
                                <Save class="size-4" />
                                Guardar dirección
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
