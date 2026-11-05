<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';

defineProps({
    students: Array,
    grades: Array,
});

const subjects = ['Mathematics', 'Science', 'English', 'Filipino'];

const form = useForm({
    student_id: '',
    subject: '',
    grading_period: '',
    grade: '',
});

const submit = () => {
    form.post(route('teacher.grades.store'), {
        onSuccess: () => form.reset(),
    });
};

const destroyGrade = (id) => {
    if (!window.confirm('Are you sure you want to delete this grade?')) return;

    router.delete(route('teacher.grades.destroy', id));
};
</script>

<template>
    <Head title="Manage Grades" />

    <AdminLayout>
        <div class="page-shell">
            <div class="page-container stack-sections">
                <div class="content-panel">
                    <div class="content-panel__header">
                        <h1 class="content-panel__title">Manage Student Grades</h1>
                    </div>
                    <div class="stack-sections">
                        <div>
                            <h2 class="subsection-title">Update Grade</h2>
                            <form class="form-stack" @submit.prevent="submit">
                                <div class="form-group">
                                    <label>Student Name</label>
                                    <select v-model="form.student_id" required>
                                        <option disabled value="">Select a Student</option>
                                        <option v-for="student in students" :key="student.id" :value="student.id">{{ student.name }}</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Subject</label>
                                    <select v-model="form.subject" required>
                                        <option disabled value="">Select a Subject</option>
                                        <option v-for="subject in subjects" :key="subject" :value="subject">{{ subject }}</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Grading Period</label>
                                    <select v-model="form.grading_period" required>
                                        <option disabled value="">Select a Period</option>
                                        <option value="1">1st Grading</option>
                                        <option value="2">2nd Grading</option>
                                        <option value="3">3rd Grading</option>
                                        <option value="4">4th Grading</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Grade</label>
                                    <input v-model="form.grade" type="number" min="0" max="100" step="0.01" required>
                                </div>
                                <button type="submit" class="btn">Update Grade</button>
                            </form>
                        </div>

                        <div>
                            <h2 class="subsection-title">All Student Grades</h2>
                            <div class="table-shell">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Student</th>
                                            <th>Subject</th>
                                            <th>Period</th>
                                            <th>Grade</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="grade in grades" :key="grade.id">
                                            <td>{{ grade.student_name }}</td>
                                            <td>{{ grade.subject }}</td>
                                            <td>{{ grade.grading_period }}th Grading</td>
                                            <td>{{ grade.grade }}</td>
                                            <td>
                                                <div class="toolbar">
                                                    <Link :href="route('teacher.grades.edit', grade.id)" class="btn-inline">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </Link>
                                                    <button type="button" class="btn-danger" @click="destroyGrade(grade.id)">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr v-if="!grades.length">
                                            <td colspan="5">No grades recorded yet.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="centered-actions">
                    <Link :href="route('teacher.dashboard')" class="btn-back"><i class="fas fa-arrow-left"></i> Back to Teacher's Control</Link>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

