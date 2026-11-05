<script setup>
import SiteLayout from '@/Layouts/SiteLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    monthlyData: Array,
});
</script>

<template>
    <Head title="Attendance" />

    <SiteLayout>
        <div class="page-shell">
            <div class="page-container">
                <div class="page-heading page-heading--spacious">
                    <h1>Monthly Attendance Summary</h1>
                    <p>Summary of student attendance by month.</p>
                </div>
                <div class="table-shell">
                    <table>
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th>Days Present</th>
                                <th>Days Absent</th>
                                <th>Attendance Rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="month in monthlyData" :key="month.month">
                                <td>{{ month.month }}</td>
                                <td>{{ month.present }}</td>
                                <td>{{ month.absent }}</td>
                                <td>{{ month.rate }}%</td>
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
