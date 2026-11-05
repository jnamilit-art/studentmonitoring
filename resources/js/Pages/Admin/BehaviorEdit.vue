<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    behavior: Object,
    students: Array,
});

const form = useForm({
    student_id: props.behavior.student_id,
    date: props.behavior.date,
    note: props.behavior.note,
});

const submit = () => {
    form.put(route('teacher.behavior.update', props.behavior.id));
};
</script>

<template>
    <Head title="Edit Behavior Note" />

    <AdminLayout>
        <div class="page-shell">
            <div class="page-container stack-sections">
                <div class="content-panel">
                    <div class="content-panel__header">
                        <h1 class="content-panel__title">Edit Behavior Note</h1>
                    </div>
                    <form class="form-stack" @submit.prevent="submit">
                        <div class="form-group">
                            <label>Student Name</label>
                            <select v-model="form.student_id" required>
                                <option v-for="student in students" :key="student.id" :value="student.id">{{ student.name }}</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Date</label>
                            <input v-model="form.date" type="date" required>
                        </div>
                        <div class="form-group">
                            <label>Behavior Note</label>
                            <textarea v-model="form.note" rows="4" required></textarea>
                        </div>
                        <div class="toolbar">
                            <button type="submit" class="btn">Update Note</button>
                            <Link :href="route('teacher.behavior')" class="btn-secondary">Cancel</Link>
                        </div>
                    </form>
                </div>
                <div class="centered-actions">
                    <Link :href="route('teacher.behavior')" class="btn-back"><i class="fas fa-arrow-left"></i> Back to Behavior</Link>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

