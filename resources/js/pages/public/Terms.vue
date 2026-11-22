<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import ContentPage from '@/components/public/ContentPage.vue';
import { contact } from '@/routes';
</script>

<template>
    <ContentPage
        eyebrow="Legal"
        title="Términos y condiciones"
        intro="Estas condiciones describen cómo usar el sitio y el catálogo de Los Cuentos de Kafka. Al navegar o enviarnos un mensaje, aceptas lo aquí establecido."
    >
        <section>
            <h2 class="font-serif text-xl font-semibold text-foreground">
                Uso del sitio
            </h2>
            <p class="mt-3">
                Puedes explorar el catálogo y contactarnos con fines
                informativos. Te comprometes a proporcionar información veraz en
                el formulario de contacto y a no utilizar el sitio para
                actividades ilícitas o que afecten a otros usuarios.
            </p>
        </section>

        <section>
            <h2 class="font-serif text-xl font-semibold text-foreground">
                Catálogo y precios
            </h2>
            <p class="mt-3">
                Los títulos, formatos, disponibilidades y precios mostrados
                pueden cambiar sin previo aviso. Los precios se expresan en
                pesos mexicanos e incluyen los impuestos aplicables cuando
                corresponda. La información es informativa y no constituye una
                oferta contractual.
            </p>
        </section>

        <section>
            <h2 class="font-serif text-xl font-semibold text-foreground">
                Consultas, apartados y pedidos
            </h2>
            <p class="mt-3">
                En esta etapa del sitio, la apartado de ejemplares y el proceso
                de compra se gestionan mediante consulta directa. Si te interesa
                un producto, envíanos un mensaje desde
                <Link
                    :href="contact()"
                    class="text-foreground underline underline-offset-4"
                    >contacto</Link
                >
                y confirmaremos disponibilidad, condiciones y plazos antes de
                cualquier compromiso.
            </p>
        </section>

        <section>
            <h2 class="font-serif text-xl font-semibold text-foreground">
                Propiedad intelectual
            </h2>
            <p class="mt-3">
                Los textos, marcas y elementos visuales del sitio pertenecen a
                Los Cuentos de Kafka o a sus respectivos titulares. No está
                permitido reproducirlos total o parcialmente sin autorización.
            </p>
        </section>

        <section>
            <h2 class="font-serif text-xl font-semibold text-foreground">
                Responsabilidad y cambios
            </h2>
            <p class="mt-3">
                Nos esforzamos por mantener la información actualizada, pero no
                garantizamos la disponibilidad continua del sitio. Podemos
                modificar estas condiciones en cualquier momento; la versión
                publicada será la aplicable desde su fecha de actualización.
            </p>
        </section>
    </ContentPage>
</template>
