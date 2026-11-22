<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { Form } from '@inertiajs/vue3';
import { MapPin, MessageCircle } from '@lucide/vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { store } from '@/actions/App/Http/Controllers/ContactController';
import { privacy } from '@/routes';
import { Link } from '@inertiajs/vue3';
import type { SiteInfo } from '@/types';

const site = usePage<{ site: SiteInfo }>().props.site;
</script>

<template>
    <div>
        <Head title="Contacto" />

        <section class="border-b border-border bg-card/50">
            <div class="mx-auto max-w-5xl px-4 py-16 sm:px-6 lg:px-8">
                <p
                    class="text-xs font-semibold tracking-[0.2em] text-amber-700 uppercase dark:text-amber-400"
                >
                    Contacto
                </p>
                <h1
                    class="mt-4 font-serif text-4xl font-semibold tracking-tight sm:text-5xl"
                >
                    Hablemos de libros
                </h1>
                <p
                    class="mt-5 max-w-2xl text-base leading-7 text-muted-foreground"
                >
                    Escríbenos para consultar disponibilidad, sugerir un título
                    o saber más sobre nuestro espacio de lectura. Te
                    responderemos a la brevedad.
                </p>
            </div>
        </section>

        <section
            class="mx-auto grid max-w-5xl gap-10 px-4 py-14 sm:px-6 lg:grid-cols-[1.3fr_0.7fr] lg:px-8"
        >
            <Form
                v-bind="store.form()"
                :reset-on-success="[
                    'name',
                    'email',
                    'phone',
                    'message',
                    'consent',
                    'privacy_accepted',
                ]"
                class="rounded-2xl border border-border bg-background p-6 sm:p-8"
                v-slot="{ errors, processing }"
            >
                <div class="flex items-center gap-3">
                    <span
                        class="flex size-10 items-center justify-center rounded-xl bg-secondary text-secondary-foreground"
                    >
                        <MessageCircle class="size-5" />
                    </span>
                    <div>
                        <h2 class="font-serif text-xl font-semibold">
                            Envíanos un mensaje
                        </h2>
                        <p class="text-xs text-muted-foreground">
                            Todos los campos marcados son obligatorios.
                        </p>
                    </div>
                </div>

                <div class="mt-7 grid gap-5 sm:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="name">Nombre</Label>
                        <Input
                            id="name"
                            name="name"
                            type="text"
                            autocomplete="name"
                            placeholder="Tu nombre completo"
                            required
                        />
                        <InputError :message="errors.name" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="email">Correo electrónico</Label>
                        <Input
                            id="email"
                            name="email"
                            type="email"
                            autocomplete="email"
                            placeholder="tu@correo.com"
                            required
                        />
                        <InputError :message="errors.email" />
                    </div>
                </div>

                <div class="mt-5 grid gap-2">
                    <Label for="phone"
                        >Teléfono
                        <span class="font-normal text-muted-foreground"
                            >(opcional)</span
                        ></Label
                    >
                    <Input
                        id="phone"
                        name="phone"
                        type="tel"
                        autocomplete="tel"
                        placeholder="272 000 0000"
                    />
                    <InputError :message="errors.phone" />
                </div>

                <div class="mt-5 grid gap-2">
                    <Label for="message">Mensaje</Label>
                    <Textarea
                        id="message"
                        name="message"
                        rows="6"
                        placeholder="¿En qué podemos ayudarte?"
                        required
                    />
                    <InputError :message="errors.message" />
                </div>

                <div class="mt-6 space-y-4">
                    <div class="flex items-start gap-3">
                        <Checkbox id="consent" name="consent" value="1" />
                        <div class="grid gap-1">
                            <Label for="consent" class="leading-5"
                                >Acepto que Los Cuentos de Kafka me contacte
                                para responder mi mensaje.</Label
                            >
                            <InputError :message="errors.consent" />
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <Checkbox
                            id="privacy_accepted"
                            name="privacy_accepted"
                            value="1"
                        />
                        <div class="grid gap-1">
                            <Label for="privacy_accepted" class="leading-5">
                                He leído y acepto el
                                <Link
                                    :href="privacy()"
                                    class="underline underline-offset-4"
                                    >aviso de privacidad</Link
                                >.
                            </Label>
                            <InputError :message="errors.privacy_accepted" />
                        </div>
                    </div>
                </div>

                <Button
                    type="submit"
                    class="mt-7 w-full sm:w-auto"
                    :disabled="processing"
                >
                    {{ processing ? 'Enviando…' : 'Enviar mensaje' }}
                </Button>
            </Form>

            <aside class="space-y-4">
                <div class="rounded-2xl border border-border bg-card p-6">
                    <h2 class="font-serif text-lg font-semibold">Visítanos</h2>
                    <p
                        class="mt-3 flex items-start gap-2 text-sm leading-6 text-muted-foreground"
                    >
                        <MapPin class="mt-1 size-4 shrink-0" />
                        {{ site.address }}
                    </p>
                </div>
                <div class="rounded-2xl bg-foreground p-6 text-background">
                    <h2 class="font-serif text-lg font-semibold">
                        Nuestro espacio
                    </h2>
                    <p class="mt-3 text-sm leading-6 text-background/70">
                        Contamos con un área de lectura y un espacio de debate.
                        Pregúntanos por nuestras próximas actividades cuando nos
                        escribas.
                    </p>
                </div>
            </aside>
        </section>
    </div>
</template>
