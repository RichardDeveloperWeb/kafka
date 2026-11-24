<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { MapPin, Menu, ShoppingCart, UserRound } from '@lucide/vue';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { Badge } from '@/components/ui/badge';
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
import { index as cartIndex } from '@/actions/App/Http/Controllers/CartController';
import { index as catalogIndex } from '@/actions/App/Http/Controllers/CatalogController';
import { about, contact, dashboard, home, login, register } from '@/routes';

const { auth, cartCount } = usePage().props;

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
                <AppLogoIcon
                    class="size-10 rounded-xl transition-transform group-hover:-rotate-6"
                />
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

            <div class="hidden items-center gap-2 md:flex">
                <Button as-child size="sm" variant="ghost">
                    <Link :href="cartIndex()" class="relative">
                        <ShoppingCart />
                        <span>Carrito</span>
                        <Badge
                            v-if="cartCount > 0"
                            class="absolute -top-2 -right-2 flex size-5 items-center justify-center rounded-full p-0 text-[10px] tabular-nums"
                        >
                            {{ cartCount }}
                        </Badge>
                    </Link>
                </Button>
                <template v-if="auth.user">
                    <Button as-child size="sm">
                        <Link :href="dashboard()">
                            <UserRound />
                            Mi cuenta
                        </Link>
                    </Button>
                </template>
                <template v-else>
                    <Button as-child size="sm" variant="ghost">
                        <Link :href="login()">Ingresar</Link>
                    </Button>
                    <Button as-child size="sm">
                        <Link :href="register()">Crear cuenta</Link>
                    </Button>
                </template>
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
                    <div
                        class="mt-4 flex flex-col gap-1 border-t border-border pt-4"
                    >
                        <SheetClose as-child>
                            <Link
                                :href="cartIndex()"
                                class="flex items-center justify-between rounded-lg px-3 py-2.5 text-sm font-medium text-muted-foreground transition-colors hover:bg-accent hover:text-foreground"
                            >
                                <span>Carrito</span>
                                <Badge
                                    v-if="cartCount > 0"
                                    class="tabular-nums"
                                >
                                    {{ cartCount }}
                                </Badge>
                            </Link>
                        </SheetClose>
                        <template v-if="auth.user">
                            <SheetClose as-child>
                                <Link
                                    :href="dashboard()"
                                    class="rounded-lg px-3 py-2.5 text-sm font-medium text-muted-foreground transition-colors hover:bg-accent hover:text-foreground"
                                >
                                    Mi cuenta
                                </Link>
                            </SheetClose>
                        </template>
                        <template v-else>
                            <SheetClose as-child>
                                <Link
                                    :href="login()"
                                    class="rounded-lg px-3 py-2.5 text-sm font-medium text-muted-foreground transition-colors hover:bg-accent hover:text-foreground"
                                >
                                    Ingresar
                                </Link>
                            </SheetClose>
                            <SheetClose as-child>
                                <Link
                                    :href="register()"
                                    class="rounded-lg px-3 py-2.5 text-sm font-medium text-muted-foreground transition-colors hover:bg-accent hover:text-foreground"
                                >
                                    Crear cuenta
                                </Link>
                            </SheetClose>
                        </template>
                    </div>
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
