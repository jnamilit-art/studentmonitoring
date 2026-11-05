<script setup>
import SiteLayout from '@/Layouts/SiteLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    announcements: Array,
});
</script>

<template>
    <Head title="Announcements" />

    <SiteLayout>
        <div class="page-shell">
            <div class="page-container">
                <div class="page-heading page-heading--spacious">
                    <h1>School Announcements</h1>
                    <p>Stay up-to-date with the latest news and events from the school.</p>
                </div>
                <div class="announcement-feed">
                    <div v-for="announcement in announcements" :key="announcement.id" class="announcement-card">
                        <h3>{{ announcement.title }}</h3>
                        <span class="announcement-date">{{ announcement.date }}</span>
                        <p>{{ announcement.content }}</p>
                    </div>
                    <p v-if="!announcements.length" class="muted-empty">No announcements available.</p>
                </div>
                <div class="centered-actions">
                    <Link :href="route('parent.dashboard')" class="btn-back"><i class="fas fa-arrow-left"></i> Back to Dashboard</Link>
                </div>
            </div>
        </div>
    </SiteLayout>
</template>
