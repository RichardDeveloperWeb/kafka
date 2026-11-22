<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ArrowUpRight } from '@lucide/vue';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent } from '@/components/ui/card';
import { show as catalogShow } from '@/actions/App/Http/Controllers/CatalogController';
import { formatPrice } from '@/lib/formatPrice';
import type { CatalogProduct } from '@/types';

const props = defineProps<{
    product: CatalogProduct;
}>();

const initials = props.product.title
    .split(' ')
    .slice(0, 2)
    .map((word) => word[0])
    .join('');
</script>

<template>
    <Card
        class="group overflow-hidden border-border/80 bg-card transition-all hover:-translate-y-1 hover:shadow-lg"
    >
        <Link :href="catalogShow(product.slug)" class="block">
            <div
                class="relative flex aspect-4/3 items-center justify-center overflow-hidden bg-secondary/70"
            >
                <div
                    class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(255,255,255,0.65),transparent_55%)] dark:bg-[radial-gradient(circle_at_top_left,rgba(255,255,255,0.08),transparent_55%)]"
                ></div>
                <span
                    class="relative font-serif text-5xl font-semibold text-foreground/20"
                    >{{ initials }}</span
                >
                <Badge
                    :variant="product.isAvailable ? 'secondary' : 'outline'"
                    class="absolute top-3 right-3 bg-background/90"
                >
                    {{ product.availabilityLabel ?? 'Sin información' }}
                </Badge>
            </div>
            <CardContent class="space-y-2 p-5">
                <p
                    class="text-xs tracking-[0.14em] text-muted-foreground uppercase"
                >
                    {{
                        product.category?.name ??
                        product.formatLabel ??
                        'Producto'
                    }}
                </p>
                <h3
                    class="line-clamp-2 font-serif text-lg leading-snug font-semibold group-hover:underline"
                >
                    {{ product.title }}
                </h3>
                <p class="line-clamp-1 text-sm text-muted-foreground">
                    {{
                        product.author ??
                        product.publisher ??
                        'Los Cuentos de Kafka'
                    }}
                </p>
                <div class="flex items-center justify-between pt-3">
                    <span class="font-semibold tabular-nums">{{
                        formatPrice(product.price)
                    }}</span>
                    <span
                        class="flex size-8 items-center justify-center rounded-full border border-border text-muted-foreground transition-colors group-hover:border-foreground group-hover:text-foreground"
                    >
                        <ArrowUpRight class="size-4" />
                    </span>
                </div>
            </CardContent>
        </Link>
    </Card>
</template>
