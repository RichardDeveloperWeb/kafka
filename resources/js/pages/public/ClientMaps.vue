<script setup lang="ts">
import ContentPage from '@/components/public/ContentPage.vue';
import { Badge } from '@/components/ui/badge';

type HierarchyNode = {
    title: string;
    detail: string;
    children?: HierarchyNode[];
};

const hierarchy: HierarchyNode[] = [
    {
        title: 'Mi cuenta',
        detail: 'Resumen de actividad y accesos rápidos del cliente.',
        children: [
            {
                title: 'Catálogo',
                detail: 'Exploración de productos y ficha de detalle.',
            },
            {
                title: 'Carrito',
                detail: 'Productos elegidos antes de confirmar la compra.',
                children: [
                    {
                        title: 'Finalizar compra',
                        detail: 'Dirección de entrega y confirmación del pedido.',
                    },
                ],
            },
            {
                title: 'Preventas',
                detail: 'Reservas, depósitos y fechas de salida.',
            },
            {
                title: 'Pedidos',
                detail: 'Historial de compras del cliente.',
                children: [
                    {
                        title: 'Detalle de pedido',
                        detail: 'Artículos, totales y estado de la compra.',
                    },
                ],
            },
            {
                title: 'Direcciones',
                detail: 'Direcciones guardadas para la entrega.',
                children: [
                    {
                        title: 'Crear dirección',
                        detail: 'Formulario para añadir una dirección nueva.',
                    },
                    {
                        title: 'Editar dirección',
                        detail: 'Actualización de una dirección existente.',
                    },
                ],
            },
        ],
    },
];
</script>

<template>
    <ContentPage
        eyebrow="Estructura"
        title="Mapas del área de cliente"
        intro="Una guía pública de cómo se organiza el área de cliente: primero la jerarquía de sus páginas y después los bloques que componen una pantalla representativa."
    >
        <section aria-labelledby="hierarchy-title">
            <div
                class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between"
            >
                <div>
                    <p
                        class="text-xs font-semibold tracking-[0.18em] text-muted-foreground uppercase"
                    >
                        01 / Estructura
                    </p>
                    <h2
                        id="hierarchy-title"
                        class="mt-2 font-serif text-2xl font-semibold text-foreground"
                    >
                        Mapa jerárquico
                    </h2>
                </div>
                <Badge variant="outline" class="w-fit">
                    Requiere iniciar sesión
                </Badge>
            </div>

            <p class="mt-3 text-sm leading-7 text-muted-foreground">
                El área de cliente se organiza desde Mi cuenta. Las rutas hijas
                muestran los flujos principales y sus pantallas de detalle.
            </p>

            <div
                class="mt-6 rounded-2xl border border-border bg-card p-5 sm:p-7"
            >
                <ol class="space-y-4">
                    <li v-for="node in hierarchy" :key="node.title">
                        <article
                            class="rounded-xl border border-border bg-background p-5"
                        >
                            <div
                                class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between"
                            >
                                <div>
                                    <h3 class="font-semibold text-foreground">
                                        {{ node.title }}
                                    </h3>
                                    <p
                                        class="mt-1 text-sm text-muted-foreground"
                                    >
                                        {{ node.detail }}
                                    </p>
                                </div>
                                <Badge variant="secondary" class="w-fit">
                                    Raíz
                                </Badge>
                            </div>

                            <ol
                                v-if="node.children"
                                class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-3"
                            >
                                <li
                                    v-for="child in node.children"
                                    :key="child.title"
                                    class="rounded-lg border border-border bg-card p-4"
                                >
                                    <h4
                                        class="text-sm font-semibold text-foreground"
                                    >
                                        {{ child.title }}
                                    </h4>
                                    <p
                                        class="mt-1 text-xs leading-5 text-muted-foreground"
                                    >
                                        {{ child.detail }}
                                    </p>

                                    <ol
                                        v-if="child.children"
                                        class="mt-4 space-y-2 border-l border-border pl-3"
                                    >
                                        <li
                                            v-for="leaf in child.children"
                                            :key="leaf.title"
                                        >
                                            <p
                                                class="text-xs font-medium text-foreground"
                                            >
                                                {{ leaf.title }}
                                            </p>
                                            <p
                                                class="text-xs text-muted-foreground"
                                            >
                                                {{ leaf.detail }}
                                            </p>
                                        </li>
                                    </ol>
                                </li>
                            </ol>
                        </article>
                    </li>
                </ol>
            </div>
        </section>

        <section aria-labelledby="blocks-title">
            <div>
                <p
                    class="text-xs font-semibold tracking-[0.18em] text-muted-foreground uppercase"
                >
                    02 / Composición
                </p>
                <h2
                    id="blocks-title"
                    class="mt-2 font-serif text-2xl font-semibold text-foreground"
                >
                    Mapa de bloques
                </h2>
                <p class="mt-3 text-sm leading-7 text-muted-foreground">
                    Vista simplificada de una pantalla de cliente: navegación,
                    encabezado, contenido, resumen y pie.
                </p>
            </div>

            <div
                class="mt-6 rounded-2xl border border-border bg-card p-5 sm:p-7"
            >
                <div class="grid gap-4 lg:grid-cols-[13rem_1fr]">
                    <div
                        class="rounded-xl border border-border bg-background p-5"
                    >
                        <p
                            class="text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                        >
                            Navegación
                        </p>
                        <p
                            class="mt-3 font-serif text-lg font-semibold text-foreground"
                        >
                            Barra lateral
                        </p>
                        <p class="mt-2 text-sm leading-6 text-muted-foreground">
                            Enlaces del área de cliente y enlaces de
                            administración cuando el usuario tiene permisos.
                        </p>
                        <ul
                            class="mt-4 space-y-2 text-xs text-muted-foreground"
                        >
                            <li>Mi cuenta</li>
                            <li>Catálogo</li>
                            <li>Carrito</li>
                            <li>Preventas</li>
                            <li>Pedidos</li>
                            <li>Direcciones</li>
                        </ul>
                    </div>

                    <div class="flex flex-col gap-4">
                        <div
                            class="rounded-xl border border-dashed border-border bg-background p-5"
                        >
                            <p
                                class="text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                            >
                                Bloque 01
                            </p>
                            <p
                                class="mt-2 font-serif text-lg font-semibold text-foreground"
                            >
                                Encabezado de página
                            </p>
                            <p class="mt-2 text-sm text-muted-foreground">
                                Título, descripción corta y acción principal de
                                la sección.
                            </p>
                        </div>

                        <div class="grid gap-4 md:grid-cols-[1.4fr_0.8fr]">
                            <div
                                class="rounded-xl border border-border bg-background p-5"
                            >
                                <p
                                    class="text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                                >
                                    Bloque 02
                                </p>
                                <p
                                    class="mt-2 font-serif text-lg font-semibold text-foreground"
                                >
                                    Contenido principal
                                </p>
                                <p class="mt-2 text-sm text-muted-foreground">
                                    Listado de tarjetas con la información del
                                    cliente y sus acciones.
                                </p>
                                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                                    <div
                                        class="rounded-lg border border-dashed border-border p-4 text-xs text-muted-foreground"
                                    >
                                        Tarjeta de resumen
                                    </div>
                                    <div
                                        class="rounded-lg border border-dashed border-border p-4 text-xs text-muted-foreground"
                                    >
                                        Tarjeta de resumen
                                    </div>
                                </div>
                            </div>

                            <div
                                class="rounded-xl border border-border bg-background p-5"
                            >
                                <p
                                    class="text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                                >
                                    Bloque 03
                                </p>
                                <p
                                    class="mt-2 font-serif text-lg font-semibold text-foreground"
                                >
                                    Panel lateral
                                </p>
                                <p class="mt-2 text-sm text-muted-foreground">
                                    Totales, filtros o detalles de apoyo según
                                    la pantalla.
                                </p>
                            </div>
                        </div>

                        <div
                            class="rounded-xl border border-dashed border-border bg-background p-4"
                        >
                            <p class="text-xs font-semibold text-foreground">
                                Pie de la pantalla
                            </p>
                            <p class="mt-1 text-xs text-muted-foreground">
                                Ayuda, enlaces relacionados y cierre de sesión.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </ContentPage>
</template>
