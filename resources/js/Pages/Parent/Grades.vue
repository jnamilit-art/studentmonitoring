<script setup>
import SiteLayout from '@/Layouts/SiteLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    gradesBySubject: Object,
});

const averageFor = (grades) => {
    const validGrades = grades.filter((grade) => grade !== null);

    if (!validGrades.length) return '-';

    return (validGrades.reduce((sum, grade) => sum + Number(grade), 0) / validGrades.length).toFixed(1);
};
</script>

<template>
    <Head title="Student Grades" />

    <SiteLayout>
        <div class="page-shell">
            <div class="page-container">
                <div class="page-heading page-heading--spacious">
                    <h1>Student Grades</h1>
                    <p>Detailed academic performance for each grading period.</p>
                </div>
                <div class="table-shell">
                    <table>
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th>1st Grading</th>
                                <th>2nd Grading</th>
                                <th>3rd Grading</th>
                                <th>4th Grading</th>
                                <th>Average</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(grades, subject) in gradesBySubject" :key="subject">
                                <td>{{ subject }}</td>
                                <td>{{ grades[0] ?? '-' }}</td>
                                <td>{{ grades[1] ?? '-' }}</td>
                                <td>{{ grades[2] ?? '-' }}</td>
                                <td>{{ grades[3] ?? '-' }}</td>
                                <td>{{ averageFor(grades) }}</td>
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
