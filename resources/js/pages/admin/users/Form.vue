<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Save } from '@lucide/vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { index as adminDashboard } from '@/actions/App/Http/Controllers/Admin/DashboardController';
import {
    index as usersIndex,
    store as storeUser,
    update as updateUser,
} from '@/actions/App/Http/Controllers/Admin/UserController';
import type { AdminOption, AdminUser } from '@/types';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Administración', href: adminDashboard() },
            { title: 'Usuarios', href: usersIndex() },
            { title: 'Detalle', href: '#' },
        ],
    },
});

const props = defineProps<{
    user: AdminUser | null;
    roles: AdminOption[];
}>();

const form = useForm({
    name: props.user?.name ?? '',
    email: props.user?.email ?? '',
    password: '',
    phone: props.user?.phone ?? '',
    roles: [...(props.user?.roles ?? [])],
});

const setRole = (role: string, checked: boolean | 'indeterminate'): void => {
    form.roles =
        checked === true
            ? [...form.roles, role]
            : form.roles.filter((item) => item !== role);
};

const submit = (): void => {
    if (props.user) {
        form.patch(updateUser(props.user.id).url, { preserveScroll: true });

        return;
    }

    form.post(storeUser().url, { preserveScroll: true });
};
</script>

<template>
    <div>
        <Head :title="props.user ? 'Editar usuario' : 'Nuevo usuario'" />

        <div class="flex flex-col gap-6">
            <section>
                <h1 class="font-serif text-2xl font-semibold">
                    {{ props.user ? 'Editar usuario' : 'Nuevo usuario' }}
                </h1>
                <p class="mt-1 text-sm text-muted-foreground">
                    Define los datos de acceso y los roles asignados.
                </p>
            </section>

            <Card class="max-w-3xl">
                <CardHeader>
                    <CardTitle class="font-serif text-xl"
                        >Datos del usuario</CardTitle
                    >
                </CardHeader>
                <CardContent>
                    <form
                        class="grid gap-5 sm:grid-cols-2"
                        @submit.prevent="submit"
                    >
                        <div class="grid gap-2">
                            <Label for="name">Nombre</Label>
                            <Input
                                id="name"
                                v-model="form.name"
                                required
                                autocomplete="name"
                            />
                            <InputError :message="form.errors.name" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="email">Correo</Label>
                            <Input
                                id="email"
                                v-model="form.email"
                                type="email"
                                required
                                autocomplete="username"
                            />
                            <InputError :message="form.errors.email" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="password">
                                Contraseña
                                <span
                                    v-if="props.user"
                                    class="text-muted-foreground"
                                    >(dejar vacía para conservar)</span
                                >
                            </Label>
                            <Input
                                id="password"
                                v-model="form.password"
                                type="password"
                                autocomplete="new-password"
                                :required="!props.user"
                            />
                            <InputError :message="form.errors.password" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="phone">Teléfono</Label>
                            <Input id="phone" v-model="form.phone" type="tel" />
                            <InputError :message="form.errors.phone" />
                        </div>

                        <fieldset class="grid gap-3 sm:col-span-2">
                            <legend class="text-sm font-medium">Roles</legend>
                            <div class="grid gap-3 sm:grid-cols-2">
                                <label
                                    v-for="role in props.roles"
                                    :key="role.value"
                                    class="flex items-center gap-2"
                                >
                                    <Checkbox
                                        :id="`role-${role.value}`"
                                        :checked="
                                            form.roles.includes(role.value)
                                        "
                                        @update:checked="
                                            setRole(role.value, $event)
                                        "
                                    />
                                    <span class="text-sm">{{
                                        role.label
                                    }}</span>
                                </label>
                            </div>
                            <InputError :message="form.errors.roles" />
                        </fieldset>

                        <div class="flex justify-end gap-3 sm:col-span-2">
                            <Button as-child type="button" variant="outline">
                                <Link :href="usersIndex()">Cancelar</Link>
                            </Button>
                            <Button type="submit" :disabled="form.processing">
                                <Save />
                                {{
                                    props.user
                                        ? 'Guardar cambios'
                                        : 'Crear usuario'
                                }}
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
