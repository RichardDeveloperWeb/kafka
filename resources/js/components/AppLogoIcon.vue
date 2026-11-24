<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

type Props = {
    className?: HTMLAttributes['class'];
    alt?: string;
};

withDefaults(defineProps<Props>(), {
    alt: '',
});
</script>

<template>
    <img
        src="/logo.png"
        :alt="alt"
        class="object-contain"
        :class="className"
        v-bind="$attrs"
    />
</template>
