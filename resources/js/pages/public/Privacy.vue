<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import ContentPage from '@/components/public/ContentPage.vue';
import { contact, cookies } from '@/routes';
</script>

<template>
    <ContentPage
        eyebrow="Legal"
        title="Aviso de privacidad"
        intro="En Los Cuentos de Kafka respetamos tu privacidad. Esta página explica qué información recabamos, para qué la usamos y qué opciones tienes."
    >
        <section>
            <h2 class="font-serif text-xl font-semibold text-foreground">
                Responsable de los datos
            </h2>
            <p class="mt-3">
                Los Cuentos de Kafka, S. de R.L. de C.V., con domicilio en calle
                Oriente 4, frente al parque principal del centro, Orizaba,
                Veracruz, es responsable del tratamiento de los datos personales
                que nos proporciones.
            </p>
        </section>

        <section>
            <h2 class="font-serif text-xl font-semibold text-foreground">
                Datos que recabamos
            </h2>
            <p class="mt-3">
                Cuando usas el formulario de contacto recabamos tu nombre,
                correo electrónico, teléfono opcional y el mensaje que nos
                envías. Si creas una cuenta, también tratamos los datos
                necesarios para autenticarte. Las preferencias de cookies se
                guardan en tu navegador.
            </p>
        </section>

        <section>
            <h2 class="font-serif text-xl font-semibold text-foreground">
                Finalidades
            </h2>
            <p class="mt-3">
                Usamos tus datos para responder tus mensajes, atender consultas
                sobre disponibilidad y productos, administrar tu cuenta cuando
                exista y mejorar la experiencia del sitio. No vendemos ni
                compartimos tus datos personales con terceros para fines
                publicitarios.
            </p>
        </section>

        <section>
            <h2 class="font-serif text-xl font-semibold text-foreground">
                Conservación y seguridad
            </h2>
            <p class="mt-3">
                Conservamos la información únicamente durante el tiempo
                necesario para las finalidades descritas. Aplicamos medidas
                razonables para protegerla contra accesos no autorizados,
                pérdida o uso indebido.
            </p>
        </section>

        <section>
            <h2 class="font-serif text-xl font-semibold text-foreground">
                Tus derechos
            </h2>
            <p class="mt-3">
                Puedes solicitar acceso, rectificación, cancelación u oposición
                al tratamiento de tus datos. Para hacerlo, utiliza nuestro
                <Link
                    :href="contact()"
                    class="text-foreground underline underline-offset-4"
                    >formulario de contacto</Link
                >
                e indica claramente tu solicitud.
            </p>
        </section>

        <section>
            <h2 class="font-serif text-xl font-semibold text-foreground">
                Cambios y cookies
            </h2>
            <p class="mt-3">
                Podemos actualizar este aviso para reflejar cambios en nuestros
                procesos. Te recomendamos consultarlo periódicamente. Para
                conocer el uso de cookies, revisa nuestra
                <Link
                    :href="cookies()"
                    class="text-foreground underline underline-offset-4"
                    >política de cookies</Link
                >.
            </p>
        </section>
    </ContentPage>
</template>
