<script setup>
import SiteLayout from '@/Layouts/SiteLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    behaviors: Array,
});
</script>

<template>
    <Head title="Behavior & Participation" />

    <SiteLayout>
        <div class="page-shell">
            <div class="page-container">
                <div class="page-heading page-heading--spacious">
                    <h1>Behavior &amp; Participation</h1>
                    <p>A log of teacher notes regarding classroom conduct and participation.</p>
                </div>
                <div class="table-shell">
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Behavior Note</th>
                                <th>Teacher</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="behavior in behaviors" :key="behavior.id">
                                <td>{{ behavior.date }}</td>
                                <td>{{ behavior.note }}</td>
                                <td>{{ behavior.teacher }}</td>
                            </tr>
                            <tr v-if="!behaviors.length">
                                <td colspan="3">No behavior notes found.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="centered-actions">
                    <Link :href="route('parent.dashboard')" class="btn-back"><i class="fas fa-arrow-left"></i> Back to Dashboard</Link>
                </div>
            </div>
        </div>
    </SiteLayout>
</template>
