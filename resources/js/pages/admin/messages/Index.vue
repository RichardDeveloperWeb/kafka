<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { index as adminDashboard } from '@/actions/App/Http/Controllers/Admin/DashboardController';
import {
    index as messagesIndex,
    show as messageShow,
} from '@/actions/App/Http/Controllers/Admin/MessageController';
import type { AdminMessage } from '@/types';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Administración', href: adminDashboard() },
            { title: 'Mensajes', href: messagesIndex() },
        ],
    },
});

const props = defineProps<{
    messages: AdminMessage[];
}>();

const formatDate = (value: string | null): string =>
    value ? new Date(value).toLocaleString('es-MX') : '—';
</script>

<template>
    <div>
        <Head title="Mensajes" />

        <div class="flex flex-col gap-6">
            <section>
                <h1 class="font-serif text-2xl font-semibold">Mensajes</h1>
                <p class="mt-1 text-sm text-muted-foreground">
                    Revisa los contactos recibidos desde el sitio.
                </p>
            </section>

            <div class="overflow-x-auto rounded-xl border border-border">
                <table class="w-full min-w-[760px] text-left text-sm">
                    <thead
                        class="bg-muted/50 text-xs text-muted-foreground uppercase"
                    >
                        <tr>
                            <th class="px-4 py-3 font-medium">Contacto</th>
                            <th class="px-4 py-3 font-medium">Mensaje</th>
                            <th class="px-4 py-3 font-medium">Fecha</th>
                            <th class="px-4 py-3 font-medium">Estado</th>
                            <th class="px-4 py-3 text-right font-medium">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="message in props.messages"
                            :key="message.id"
                            class="border-t border-border"
                        >
                            <td class="px-4 py-3">
                                <p class="font-medium">{{ message.name }}</p>
                                <p
                                    class="truncate text-xs text-muted-foreground"
                                >
                                    {{ message.email }}
                                </p>
                                <p
                                    v-if="message.phone"
                                    class="text-xs text-muted-foreground"
                                >
                                    {{ message.phone }}
                                </p>
                            </td>
                            <td class="px-4 py-3">
                                <p
                                    class="max-w-md truncate text-muted-foreground"
                                >
                                    {{ message.message }}
                                </p>
                            </td>
                            <td class="px-4 py-3 text-xs text-muted-foreground">
                                {{ formatDate(message.createdAt) }}
                            </td>
                            <td class="px-4 py-3">
                                <Badge
                                    :variant="
                                        message.readAt ? 'outline' : 'secondary'
                                    "
                                >
                                    {{ message.readAt ? 'Leído' : 'Nuevo' }}
                                </Badge>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <Button as-child size="sm" variant="outline">
                                    <Link :href="messageShow(message.id)">
                                        Ver detalle
                                    </Link>
                                </Button>
                            </td>
                        </tr>
                        <tr v-if="props.messages.length === 0">
                            <td colspan="5" class="px-4 py-12 text-center">
                                <p class="text-sm text-muted-foreground">
                                    No hay mensajes registrados.
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
