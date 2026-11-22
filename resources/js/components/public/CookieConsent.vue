<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import { Cookie } from '@lucide/vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import { cookies } from '@/routes';

const STORAGE_KEY = 'lck-cookie-consent';

type Consent = {
    essential: true;
    analytics: boolean;
    marketing: boolean;
};

const visible = ref(false);
const preferencesOpen = ref(false);
const analytics = ref(false);
const marketing = ref(false);

function saveConsent(consent: Consent): void {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(consent));
    analytics.value = consent.analytics;
    marketing.value = consent.marketing;
    preferencesOpen.value = false;
    visible.value = false;
}

function openPreferences(): void {
    preferencesOpen.value = true;
}

defineExpose({ openPreferences });

onMounted(() => {
    const stored = localStorage.getItem(STORAGE_KEY);

    if (!stored) {
        visible.value = true;
        return;
    }

    try {
        const consent = JSON.parse(stored) as Partial<Consent>;
        analytics.value = consent.analytics === true;
        marketing.value = consent.marketing === true;
    } catch {
        visible.value = true;
    }
});
</script>

<template>
    <div>
        <div
            v-if="visible"
            class="fixed inset-x-0 bottom-0 z-50 border-t border-border bg-background/95 p-4 shadow-[0_-12px_40px_-24px_rgba(0,0,0,0.45)] backdrop-blur-xl"
        >
            <div
                class="mx-auto flex max-w-7xl flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <div class="flex items-start gap-3">
                    <span
                        class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-secondary text-secondary-foreground"
                    >
                        <Cookie class="size-4" />
                    </span>
                    <div class="space-y-1">
                        <p class="text-sm font-semibold">
                            Usamos cookies para cuidar tu experiencia
                        </p>
                        <p class="text-xs leading-5 text-muted-foreground">
                            Las cookies estrictamente necesarias siempre están
                            activas. Puedes decidir sobre las demás. Consulta
                            nuestra
                            <Link
                                :href="cookies()"
                                class="underline underline-offset-4"
                                >política de cookies</Link
                            >.
                        </p>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2 sm:shrink-0">
                    <Button
                        variant="outline"
                        size="sm"
                        @click="
                            saveConsent({
                                essential: true,
                                analytics: false,
                                marketing: false,
                            })
                        "
                    >
                        Rechazar opcionales
                    </Button>
                    <Button variant="ghost" size="sm" @click="openPreferences"
                        >Configurar</Button
                    >
                    <Button
                        size="sm"
                        @click="
                            saveConsent({
                                essential: true,
                                analytics: true,
                                marketing: true,
                            })
                        "
                    >
                        Aceptar todas
                    </Button>
                </div>
            </div>
        </div>

        <Dialog v-model:open="preferencesOpen">
            <DialogContent class="sm:max-w-lg">
                <DialogHeader>
                    <DialogTitle class="font-serif"
                        >Preferencias de cookies</DialogTitle
                    >
                    <DialogDescription>
                        Elige qué cookies permites. Puedes cambiar esta decisión
                        cuando quieras desde el pie de página.
                    </DialogDescription>
                </DialogHeader>

                <div class="space-y-5 py-2">
                    <div
                        class="flex items-center justify-between gap-6 rounded-xl border border-border p-4"
                    >
                        <div>
                            <Label>Estrictamente necesarias</Label>
                            <p
                                class="mt-1 text-xs leading-5 text-muted-foreground"
                            >
                                Permiten el funcionamiento básico del sitio y no
                                se pueden desactivar.
                            </p>
                        </div>
                        <Switch
                            :model-value="true"
                            disabled
                            aria-label="Cookies necesarias activadas"
                        />
                    </div>

                    <div
                        class="flex items-center justify-between gap-6 rounded-xl border border-border p-4"
                    >
                        <div>
                            <Label for="analytics-cookie">Analíticas</Label>
                            <p
                                class="mt-1 text-xs leading-5 text-muted-foreground"
                            >
                                Nos ayudan a entender cómo se usa la tienda para
                                mejorarla.
                            </p>
                        </div>
                        <Switch
                            id="analytics-cookie"
                            v-model:checked="analytics"
                        />
                    </div>

                    <div
                        class="flex items-center justify-between gap-6 rounded-xl border border-border p-4"
                    >
                        <div>
                            <Label for="marketing-cookie"
                                >Personalización</Label
                            >
                            <p
                                class="mt-1 text-xs leading-5 text-muted-foreground"
                            >
                                Nos permiten mostrarte recomendaciones y
                                novedades relevantes.
                            </p>
                        </div>
                        <Switch
                            id="marketing-cookie"
                            v-model:checked="marketing"
                        />
                    </div>
                </div>

                <DialogFooter>
                    <Button
                        variant="outline"
                        @click="
                            saveConsent({
                                essential: true,
                                analytics: false,
                                marketing: false,
                            })
                        "
                    >
                        Guardar selección
                    </Button>
                    <Button
                        @click="
                            saveConsent({
                                essential: true,
                                analytics: true,
                                marketing: true,
                            })
                        "
                    >
                        Aceptar todas
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>
