<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    grade: Object,
    students: Array,
});

const subjects = ['Mathematics', 'Science', 'English', 'Filipino'];

const form = useForm({
    student_id: props.grade.student_id,
    subject: props.grade.subject,
    grading_period: String(props.grade.grading_period),
    grade: props.grade.grade,
});

const submit = () => {
    form.put(route('teacher.grades.update', props.grade.id));
};
</script>

<template>
    <Head title="Edit Grade" />

    <AdminLayout>
        <div class="page-shell">
            <div class="page-container stack-sections">
                <div class="content-panel">
                    <div class="content-panel__header">
                        <h1 class="content-panel__title">Edit Student Grade</h1>
                    </div>
                    <form class="form-stack" @submit.prevent="submit">
                        <div class="form-group">
                            <label>Student Name</label>
                            <select v-model="form.student_id" required>
                                <option v-for="student in students" :key="student.id" :value="student.id">{{ student.name }}</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Subject</label>
                            <select v-model="form.subject" required>
                                <option v-for="subject in subjects" :key="subject" :value="subject">{{ subject }}</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Grading Period</label>
                            <select v-model="form.grading_period" required>
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
                        <div class="toolbar">
                            <button type="submit" class="btn">Update Grade</button>
                            <Link :href="route('teacher.grades')" class="btn-secondary">Cancel</Link>
                        </div>
                    </form>
                </div>
                <div class="centered-actions">
                    <Link :href="route('teacher.grades')" class="btn-back"><i class="fas fa-arrow-left"></i> Back to Grades</Link>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

