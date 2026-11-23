<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Pencil, Plus, Trash2 } from '@lucide/vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { index as adminDashboard } from '@/actions/App/Http/Controllers/Admin/DashboardController';
import {
    create as createUser,
    destroy as destroyUser,
    edit as editUser,
    index as usersIndex,
} from '@/actions/App/Http/Controllers/Admin/UserController';
import type { AdminUser } from '@/types';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Administración', href: adminDashboard() },
            { title: 'Usuarios', href: usersIndex() },
        ],
    },
});

const props = defineProps<{
    users: AdminUser[];
}>();

const destroy = (user: AdminUser): void => {
    if (!window.confirm(`¿Eliminar al usuario "${user.name}"?`)) {
        return;
    }

    router.delete(destroyUser(user.id).url, { preserveScroll: true });
};
</script>

<template>
    <div>
        <Head title="Usuarios" />

        <div class="flex flex-col gap-6">
            <section class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <h1 class="font-serif text-2xl font-semibold">Usuarios</h1>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Administra las cuentas y sus permisos.
                    </p>
                </div>
                <Button as-child>
                    <Link :href="createUser()">
                        <Plus />
                        Nuevo usuario
                    </Link>
                </Button>
            </section>

            <div class="overflow-x-auto rounded-xl border border-border">
                <table class="w-full min-w-[840px] text-left text-sm">
                    <thead
                        class="bg-muted/50 text-xs text-muted-foreground uppercase"
                    >
                        <tr>
                            <th class="px-4 py-3 font-medium">Usuario</th>
                            <th class="px-4 py-3 font-medium">Teléfono</th>
                            <th class="px-4 py-3 font-medium">Roles</th>
                            <th class="px-4 py-3 font-medium">Registro</th>
                            <th class="px-4 py-3 text-right font-medium">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="user in props.users"
                            :key="user.id"
                            class="border-t border-border"
                        >
                            <td class="px-4 py-3">
                                <p class="font-medium">{{ user.name }}</p>
                                <p
                                    class="truncate text-xs text-muted-foreground"
                                >
                                    {{ user.email }}
                                </p>
                            </td>
                            <td class="px-4 py-3 text-muted-foreground">
                                {{ user.phone ?? '—' }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-2">
                                    <Badge
                                        v-for="role in user.roles"
                                        :key="role"
                                        variant="outline"
                                    >
                                        {{ role }}
                                    </Badge>
                                    <span
                                        v-if="user.roles.length === 0"
                                        class="text-xs text-muted-foreground"
                                    >
                                        Sin roles
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-xs text-muted-foreground">
                                {{
                                    user.createdAt
                                        ? new Date(
                                              user.createdAt,
                                          ).toLocaleDateString('es-MX')
                                        : '—'
                                }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-2">
                                    <Button
                                        as-child
                                        size="sm"
                                        variant="ghost"
                                        :aria-label="`Editar ${user.name}`"
                                    >
                                        <Link :href="editUser(user.id)">
                                            <Pencil />
                                            <span class="sr-only">Editar</span>
                                        </Link>
                                    </Button>
                                    <Button
                                        type="button"
                                        size="sm"
                                        variant="ghost"
                                        :aria-label="`Eliminar ${user.name}`"
                                        @click="destroy(user)"
                                    >
                                        <Trash2 />
                                        <span class="sr-only">Eliminar</span>
                                    </Button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="props.users.length === 0">
                            <td colspan="5" class="px-4 py-12 text-center">
                                <p class="text-sm text-muted-foreground">
                                    No hay usuarios registrados.
                                </p>
                                <Button as-child class="mt-4" size="sm">
                                    <Link :href="createUser()">
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
