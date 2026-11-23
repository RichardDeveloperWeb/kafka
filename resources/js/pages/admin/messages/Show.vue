<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowLeft, CheckCheck, Trash2 } from '@lucide/vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { index as adminDashboard } from '@/actions/App/Http/Controllers/Admin/DashboardController';
import {
    destroy as destroyMessage,
    index as messagesIndex,
    update as updateMessage,
} from '@/actions/App/Http/Controllers/Admin/MessageController';
import type { AdminMessage } from '@/types';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Administración', href: adminDashboard() },
            { title: 'Mensajes', href: messagesIndex() },
            { title: 'Detalle', href: '#' },
        ],
    },
});

const props = defineProps<{
    message: AdminMessage;
}>();

const formatDate = (value: string | null): string =>
    value ? new Date(value).toLocaleString('es-MX') : '—';

const toggleRead = (): void => {
    router.patch(
        updateMessage(props.message.id).url,
        {
            read_at: props.message.readAt ? null : new Date().toISOString(),
        },
        { preserveScroll: true },
    );
};

const destroy = (): void => {
    if (!window.confirm(`¿Eliminar el mensaje de ${props.message.name}?`)) {
        return;
    }

    router.delete(destroyMessage(props.message.id).url, {
        preserveScroll: true,
    });
};
</script>

<template>
    <div>
        <Head title="Detalle del mensaje" />

        <div class="flex flex-col gap-6">
            <section>
                <Link
                    :href="messagesIndex()"
                    class="inline-flex items-center gap-2 text-sm text-muted-foreground transition-colors hover:text-foreground"
                >
                    <ArrowLeft class="size-4" />
                    Volver a mensajes
                </Link>
                <div class="mt-6 flex flex-wrap items-center gap-3">
                    <h1 class="font-serif text-2xl font-semibold">
                        {{ props.message.name }}
                    </h1>
                    <Badge
                        :variant="
                            props.message.readAt ? 'outline' : 'secondary'
                        "
                    >
                        {{ props.message.readAt ? 'Leído' : 'Nuevo' }}
                    </Badge>
                </div>
                <p class="mt-1 text-sm text-muted-foreground">
                    Recibido el {{ formatDate(props.message.createdAt) }}
                </p>
            </section>

            <div class="grid gap-6 lg:grid-cols-[1.5fr_0.8fr]">
                <Card>
                    <CardHeader>
                        <CardTitle class="font-serif text-xl"
                            >Mensaje</CardTitle
                        >
                    </CardHeader>
                    <CardContent>
                        <p class="leading-7 whitespace-pre-line">
                            {{ props.message.message }}
                        </p>
                    </CardContent>
                </Card>

                <div class="grid gap-6">
                    <Card>
                        <CardHeader>
                            <CardTitle class="font-serif text-xl"
                                >Contacto</CardTitle
                            >
                        </CardHeader>
                        <CardContent class="grid gap-3 text-sm">
                            <div class="grid gap-1">
                                <span class="text-xs text-muted-foreground"
                                    >Correo</span
                                >
                                <span class="truncate">{{
                                    props.message.email
                                }}</span>
                            </div>
                            <div class="grid gap-1">
                                <span class="text-xs text-muted-foreground"
                                    >Teléfono</span
                                >
                                <span>{{ props.message.phone ?? '—' }}</span>
                            </div>
                            <div class="grid gap-1">
                                <span class="text-xs text-muted-foreground"
                                    >Leído</span
                                >
                                <span>{{
                                    formatDate(props.message.readAt)
                                }}</span>
                            </div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle class="font-serif text-xl"
                                >Acciones</CardTitle
                            >
                        </CardHeader>
                        <CardContent class="grid gap-3">
                            <Button
                                type="button"
                                variant="outline"
                                @click="toggleRead"
                            >
                                <CheckCheck />
                                {{
                                    props.message.readAt
                                        ? 'Marcar como no leído'
                                        : 'Marcar como leído'
                                }}
                            </Button>
                            <Button
                                type="button"
                                variant="destructive"
                                @click="destroy"
                            >
                                <Trash2 />
                                Eliminar mensaje
                            </Button>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </div>
</template>
