<script setup lang="ts">
import { ref } from 'vue';
import CookieConsent from '@/components/public/CookieConsent.vue';
import PublicFooter from '@/components/public/PublicFooter.vue';
import PublicHeader from '@/components/public/PublicHeader.vue';

const cookieConsent = ref<InstanceType<typeof CookieConsent> | null>(null);
</script>

<template>
    <div class="flex min-h-screen flex-col bg-background text-foreground">
        <PublicHeader />
        <main class="flex-1">
            <slot />
        </main>
        <PublicFooter
            @open-cookie-preferences="cookieConsent?.openPreferences()"
        />
        <CookieConsent ref="cookieConsent" />
    </div>
</template>
