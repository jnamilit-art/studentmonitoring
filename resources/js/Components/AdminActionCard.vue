<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    href: {
        type: String,
        required: true,
    },
    title: {
        type: String,
        required: true,
    },
    description: {
        type: String,
        default: '',
    },
});
</script>

<template>
    <Link :href="href" class="management-card clickable">
        <h3 class="management-card__title">
            <span>{{ title }}</span>
        </h3>
        <p v-if="description">{{ description }}</p>
    </Link>
</template>
