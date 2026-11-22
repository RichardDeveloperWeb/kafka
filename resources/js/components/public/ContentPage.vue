<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

defineProps<{
    eyebrow: string;
    title: string;
    intro: string;
}>();
</script>

<template>
    <div>
        <Head :title="title" />
        <section class="border-b border-border bg-card/50">
            <div class="mx-auto max-w-4xl px-4 py-16 sm:px-6 lg:px-8">
                <p
                    class="text-xs font-semibold tracking-[0.2em] text-amber-700 uppercase dark:text-amber-400"
                >
                    {{ eyebrow }}
                </p>
                <h1
                    class="mt-4 font-serif text-4xl font-semibold tracking-tight sm:text-5xl"
                >
                    {{ title }}
                </h1>
                <p
                    class="mt-5 max-w-2xl text-base leading-7 text-muted-foreground"
                >
                    {{ intro }}
                </p>
            </div>
        </section>
        <section class="mx-auto max-w-4xl px-4 py-14 sm:px-6 lg:px-8">
            <div class="space-y-12 text-sm leading-7 text-muted-foreground">
                <slot />
            </div>
        </section>
    </div>
</template>
