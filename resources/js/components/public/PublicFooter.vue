<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { BookOpen, MapPin } from '@lucide/vue';
import { Button } from '@/components/ui/button';
import { index as catalogIndex } from '@/actions/App/Http/Controllers/CatalogController';
import {
    about,
    clientMaps,
    contact,
    cookies,
    home,
    privacy,
    terms,
} from '@/routes';
import type { SiteInfo } from '@/types';

defineEmits<{
    (e: 'open-cookie-preferences'): void;
}>();

const site = usePage<{ site: SiteInfo }>().props.site;

const explore = [
    { label: 'Inicio', href: home() },
    { label: 'Catálogo', href: catalogIndex() },
    { label: 'Nosotros', href: about() },
    { label: 'Contacto', href: contact() },
];

const legal = [
    { label: 'Aviso de privacidad', href: privacy() },
    { label: 'Términos y condiciones', href: terms() },
    { label: 'Política de cookies', href: cookies() },
    { label: 'Mapas del área de cliente', href: clientMaps() },
];
</script>

<template>
    <footer class="mt-24 border-t border-border bg-card/60">
        <div
            class="mx-auto grid max-w-7xl gap-12 px-4 py-16 sm:px-6 md:grid-cols-2 lg:grid-cols-4 lg:px-8"
        >
            <div class="space-y-4">
                <div class="flex items-center gap-3">
                    <span
                        class="flex size-9 items-center justify-center rounded-lg bg-foreground text-background"
                    >
                        <BookOpen class="size-4" />
                    </span>
                    <span class="font-serif text-base font-semibold">{{
                        site.name
                    }}</span>
                </div>
                <p class="max-w-xs text-sm leading-6 text-muted-foreground">
                    {{ site.tagline }}. Un espacio para leer, descubrir y
                    debatir en el centro de Orizaba.
                </p>
                <p class="flex items-start gap-2 text-sm text-muted-foreground">
                    <MapPin class="mt-0.5 size-4 shrink-0" />
                    {{ site.address }}
                </p>
            </div>

            <div>
                <h2 class="text-sm font-semibold tracking-wide uppercase">
                    Explora
                </h2>
                <ul class="mt-5 space-y-3">
                    <li v-for="item in explore" :key="item.label">
                        <Link
                            :href="item.href"
                            class="text-sm text-muted-foreground transition-colors hover:text-foreground"
                        >
                            {{ item.label }}
                        </Link>
                    </li>
                </ul>
            </div>

            <div>
                <h2 class="text-sm font-semibold tracking-wide uppercase">
                    Información
                </h2>
                <ul class="mt-5 space-y-3">
                    <li v-for="item in legal" :key="item.label">
                        <Link
                            :href="item.href"
                            class="text-sm text-muted-foreground transition-colors hover:text-foreground"
                        >
                            {{ item.label }}
                        </Link>
                    </li>
                    <li>
                        <Button
                            variant="link"
                            class="h-auto p-0 text-sm font-normal text-muted-foreground"
                            @click="$emit('open-cookie-preferences')"
                        >
                            Preferencias de cookies
                        </Button>
                    </li>
                </ul>
            </div>

            <div class="rounded-2xl border border-border bg-background p-6">
                <h2 class="font-serif text-lg font-semibold">¿Nos visitas?</h2>
                <p class="mt-2 text-sm leading-6 text-muted-foreground">
                    Estamos frente al parque principal. Pasa a explorar nuestras
                    novedades y nuestro rincón de lectura.
                </p>
                <Button as-child variant="outline" size="sm" class="mt-5">
                    <Link :href="contact()">Escríbenos antes</Link>
                </Button>
            </div>
        </div>

        <div class="border-t border-border">
            <div
                class="mx-auto flex max-w-7xl flex-col gap-2 px-4 py-6 text-xs text-muted-foreground sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8"
            >
                <p>
                    © {{ new Date().getFullYear() }} {{ site.legalName }}. Todos
                    los derechos reservados.
                </p>
                <p>Hecho para lectores de las Altas Montañas.</p>
            </div>
        </div>
    </footer>
</template>
