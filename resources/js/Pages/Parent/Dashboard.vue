<script setup>
import SiteLayout from '@/Layouts/SiteLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    student: Object,
    gradeSummary: Object,
    recentAttendance: Array,
    latestBehavior: Object,
    latestAnnouncement: Object,
});
</script>

<template>
    <Head title="Parent Dashboard" />

    <SiteLayout>
        <div class="page-shell">
            <div class="page-container">
                <div class="page-heading page-heading--spacious">
                    <h1>Welcome Parent's</h1>
                    <p v-if="student">Welcome! Here is the current summary for your student.</p>
                    <p v-else>Your account is logged in. A student has not been linked to this parent account yet.</p>
                </div>
                <div v-if="!student" class="content-panel content-panel--tight">
                    <p>Please ask the administrator to connect your email to a student profile so grades, attendance, and behavior details will appear here.</p>
                </div>
                <div class="dashboard-grid dashboard-grid--wide">
                    <Link :href="route('parent.grades')" class="dashboard-card clickable">
                        <div class="card-title-row">
                            <i class="fas fa-graduation-cap card-icon"></i>
                            <h3 class="dashboard-card__title">Real-Time Grades</h3>
                        </div>
                        <p>Student: <strong>{{ student?.name ?? 'N/A' }}</strong></p>
                        <ul class="summary-list">
                            <li v-for="(grade, subject) in gradeSummary" :key="subject">{{ subject }}: <strong>{{ grade }}</strong></li>
                            <li v-if="!Object.keys(gradeSummary ?? {}).length">No grades available yet.</li>
                        </ul>
                    </Link>
                    <Link :href="route('parent.attendance')" class="dashboard-card clickable">
                        <div class="card-title-row">
                            <i class="fas fa-calendar-check card-icon"></i>
                            <h3 class="dashboard-card__title">Daily Attendance</h3>
                        </div>
                        <div class="table-shell content-panel--tight">
                            <table class="mini-table">
                                <thead><tr><th>Date</th><th>Status</th></tr></thead>
                                <tbody>
                                    <tr v-for="attendance in recentAttendance" :key="attendance.id">
                                        <td>{{ attendance.date }}</td>
                                        <td>{{ attendance.status }}</td>
                                    </tr>
                                    <tr v-if="!recentAttendance?.length">
                                        <td colspan="2">No attendance records yet.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </Link>
                    <Link :href="route('parent.behavior')" class="dashboard-card clickable">
                        <div class="card-title-row">
                            <i class="fas fa-comments card-icon"></i>
                            <h3 class="dashboard-card__title">Behavior &amp; Participation</h3>
                        </div>
                        <p><strong>Latest Teacher Note:</strong></p>
                        <p class="quote-note">{{ latestBehavior?.note ?? 'No behavior notes recorded.' }}</p>
                    </Link>
                    <Link :href="route('parent.announcements')" class="dashboard-card clickable">
                        <div class="card-title-row">
                            <i class="fas fa-bullhorn card-icon"></i>
                            <h3 class="dashboard-card__title">Announcements</h3>
                        </div>
                        <p><strong>Latest Announcement:</strong></p>
                        <p class="quote-note">{{ latestAnnouncement?.content ?? 'No recent announcements.' }}</p>
                    </Link>
                </div>
            </div>
        </div>
    </SiteLayout>
</template>
