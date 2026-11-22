<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ArrowRight, BookOpen, MapPin, Menu } from '@lucide/vue';
import { Button } from '@/components/ui/button';
import {
    Sheet,
    SheetClose,
    SheetContent,
    SheetDescription,
    SheetHeader,
    SheetTitle,
    SheetTrigger,
} from '@/components/ui/sheet';
import { index as catalogIndex } from '@/actions/App/Http/Controllers/CatalogController';
import { about, contact, home } from '@/routes';

const navigation = [
    { label: 'Inicio', href: home() },
    { label: 'Catálogo', href: catalogIndex() },
    { label: 'Nosotros', href: about() },
    { label: 'Contacto', href: contact() },
];
</script>

<template>
    <header
        class="sticky top-0 z-40 border-b border-border/80 bg-background/90 backdrop-blur-xl"
    >
        <div
            class="mx-auto flex h-18 max-w-7xl items-center justify-between gap-6 px-4 sm:px-6 lg:px-8"
        >
            <Link :href="home()" class="group flex items-center gap-3">
                <span
                    class="flex size-10 items-center justify-center rounded-xl bg-foreground text-background transition-transform group-hover:-rotate-6"
                >
                    <BookOpen class="size-5" />
                </span>
                <span class="flex flex-col leading-none">
                    <span
                        class="font-serif text-base font-semibold tracking-tight"
                        >Los Cuentos de Kafka</span
                    >
                    <span
                        class="mt-1 text-[11px] tracking-[0.18em] text-muted-foreground uppercase"
                        >Orizaba · Veracruz</span
                    >
                </span>
            </Link>

            <nav class="hidden items-center gap-8 md:flex">
                <Link
                    v-for="item in navigation"
                    :key="item.label"
                    :href="item.href"
                    class="text-sm font-medium text-muted-foreground transition-colors hover:text-foreground"
                >
                    {{ item.label }}
                </Link>
            </nav>

            <div class="hidden md:block">
                <Button as-child size="sm">
                    <Link :href="catalogIndex()">
                        Explorar catálogo
                        <ArrowRight />
                    </Link>
                </Button>
            </div>

            <Sheet>
                <SheetTrigger as-child>
                    <Button
                        variant="ghost"
                        size="icon"
                        class="md:hidden"
                        aria-label="Abrir menú"
                    >
                        <Menu />
                    </Button>
                </SheetTrigger>
                <SheetContent side="right" class="w-80">
                    <SheetHeader>
                        <SheetTitle class="font-serif"
                            >Los Cuentos de Kafka</SheetTitle
                        >
                        <SheetDescription class="flex items-start gap-2">
                            <MapPin class="mt-0.5 size-4 shrink-0" />
                            Calle Oriente 4, frente al parque principal,
                            Orizaba.
                        </SheetDescription>
                    </SheetHeader>
                    <nav class="mt-8 flex flex-col gap-1">
                        <SheetClose
                            v-for="item in navigation"
                            :key="item.label"
                            as-child
                        >
                            <Link
                                :href="item.href"
                                class="rounded-lg px-3 py-2.5 text-sm font-medium text-muted-foreground transition-colors hover:bg-accent hover:text-foreground"
                            >
                                {{ item.label }}
                            </Link>
                        </SheetClose>
                    </nav>
                    <div class="mt-6 px-3">
                        <SheetClose as-child>
                            <Button class="w-full" as-child>
                                <Link :href="catalogIndex()">Ver catálogo</Link>
                            </Button>
                        </SheetClose>
                    </div>
                </SheetContent>
            </Sheet>
        </div>
    </header>
</template>
