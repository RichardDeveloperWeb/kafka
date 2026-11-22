<script setup lang="ts">
import ContentPage from '@/components/public/ContentPage.vue';
</script>

<template>
    <ContentPage
        eyebrow="Nosotros"
        title="Una librería hecha para conversar"
        intro="Nacimos en Orizaba con una idea sencilla: acercar la lectura a la comunidad y darle un lugar donde quedarse."
    >
        <section class="grid gap-6 sm:grid-cols-2">
            <article class="rounded-2xl border border-border bg-card p-7">
                <h2 class="font-serif text-xl font-semibold text-foreground">
                    Nuestra misión
                </h2>
                <p class="mt-3">
                    Acercar la lectura a la comunidad de Orizaba y la región
                    mediante un catálogo variado de libros y un espacio cálido
                    donde las personas puedan leer, descubrir y debatir, con un
                    servicio honesto, cercano y responsable.
                </p>
            </article>
            <article class="rounded-2xl border border-border bg-card p-7">
                <h2 class="font-serif text-xl font-semibold text-foreground">
                    Nuestra visión
                </h2>
                <p class="mt-3">
                    Ser, en los próximos cinco años, la librería de referencia
                    de la región de las Altas Montañas, reconocida por su
                    comunidad de lectores, su atención personalizada y su
                    integración de lo físico con lo digital.
                </p>
            </article>
        </section>

        <section>
            <h2 class="font-serif text-2xl font-semibold text-foreground">
                Cómo empezó
            </h2>
            <div class="mt-5 space-y-4">
                <p>
                    Los Cuentos de Kafka nació en 2026 en Orizaba, Veracruz, del
                    impulso de cuatro amigos que se conocieron en el Instituto
                    Tecnológico de Orizaba: Omar, Charbel, Jesús y Oswaldo.
                </p>
                <p>
                    Nuestro local está en calle Oriente 4, frente al parque
                    principal del centro. Además de libros, contamos con un área
                    de lectura y un espacio de debate pensado para que cada
                    visita sea también una conversación.
                </p>
            </div>
        </section>

        <section>
            <h2 class="font-serif text-2xl font-semibold text-foreground">
                Quiénes hacen posible la librería
            </h2>
            <div class="mt-5 grid gap-3 sm:grid-cols-2">
                <div class="rounded-xl border border-border p-5">
                    <p class="font-semibold text-foreground">Charbel</p>
                    <p class="mt-1">Dirección</p>
                </div>
                <div class="rounded-xl border border-border p-5">
                    <p class="font-semibold text-foreground">Omar</p>
                    <p class="mt-1">Tecnología e inventario</p>
                </div>
                <div class="rounded-xl border border-border p-5">
                    <p class="font-semibold text-foreground">Jesús</p>
                    <p class="mt-1">Logística y editoriales</p>
                </div>
                <div class="rounded-xl border border-border p-5">
                    <p class="font-semibold text-foreground">Oswaldo</p>
                    <p class="mt-1">Ventas y finanzas</p>
                </div>
            </div>
        </section>

        <section>
            <h2 class="font-serif text-2xl font-semibold text-foreground">
                Nuestro giro
            </h2>
            <p>
                Compra, venta, importación, exportación, distribución,
                exhibición y comercialización, al por mayor y por menor, de
                libros físicos, revistas, publicaciones impresas, libros
                electrónicos, audiolibros y material didáctico, así como
                artículos de papelería, regalos y accesorios relacionados con la
                lectura.
            </p>
        </section>
    </ContentPage>
</template>
