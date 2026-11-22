<script setup lang="ts">
import ContentPage from '@/components/public/ContentPage.vue';
</script>

<template>
    <ContentPage
        eyebrow="Legal"
        title="Política de cookies"
        intro="Usamos cookies para que el sitio funcione correctamente y, si tú lo permites, para entender y mejorar tu experiencia."
    >
        <section>
            <h2 class="font-serif text-xl font-semibold text-foreground">
                Qué son las cookies
            </h2>
            <p class="mt-3">
                Son pequeños archivos que el sitio guarda en tu navegador.
                Pueden recordar tus preferencias, mantener tu sesión o ayudarnos
                a comprender cómo se utiliza la tienda.
            </p>
        </section>

        <section>
            <h2 class="font-serif text-xl font-semibold text-foreground">
                Cookies que utilizamos
            </h2>
            <div class="mt-4 space-y-4">
                <div class="rounded-xl border border-border p-5">
                    <h3 class="font-semibold text-foreground">
                        Estrictamente necesarias
                    </h3>
                    <p class="mt-2">
                        Permiten funciones básicas como la sesión y la
                        seguridad. No se pueden desactivar.
                    </p>
                </div>
                <div class="rounded-xl border border-border p-5">
                    <h3 class="font-semibold text-foreground">Analíticas</h3>
                    <p class="mt-2">
                        Nos ayudan a saber qué secciones se consultan más para
                        mejorar el catálogo y la navegación.
                    </p>
                </div>
                <div class="rounded-xl border border-border p-5">
                    <h3 class="font-semibold text-foreground">
                        Personalización
                    </h3>
                    <p class="mt-2">
                        Nos permiten mostrarte recomendaciones y novedades más
                        relevantes para tus intereses.
                    </p>
                </div>
            </div>
        </section>

        <section>
            <h2 class="font-serif text-xl font-semibold text-foreground">
                Cómo decidir
            </h2>
            <p class="mt-3">
                Al llegar al sitio verás un aviso donde puedes aceptar todas,
                rechazar las opcionales o configurarlas. Tu elección se guarda
                en este navegador. Si deseas cambiarla, usa el enlace
                <span class="font-medium text-foreground"
                    >Preferencias de cookies</span
                >
                en el pie de página.
            </p>
        </section>

        <section>
            <h2 class="font-serif text-xl font-semibold text-foreground">
                Cookies de terceros
            </h2>
            <p class="mt-3">
                En esta etapa del sitio no integramos cookies publicitarias de
                terceros. Si esto cambia, actualizaremos esta política y
                solicitaremos tu consentimiento antes de activarlas.
            </p>
        </section>
    </ContentPage>
</template>
