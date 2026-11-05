<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    attendance: Object,
    students: Array,
});

const form = useForm({
    student_id: props.attendance.student_id,
    date: props.attendance.date,
    status: props.attendance.status,
});

const submit = () => {
    form.put(route('teacher.attendance.update', props.attendance.id));
};
</script>

<template>
    <Head title="Edit Attendance" />

    <AdminLayout>
        <div class="page-shell">
            <div class="page-container stack-sections">
                <div class="content-panel">
                    <div class="content-panel__header">
                        <h1 class="content-panel__title">Edit Attendance Record</h1>
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
                            <label>Status</label>
                            <select v-model="form.status" required>
                                <option value="Present">Present</option>
                                <option value="Absent">Absent</option>
                                <option value="Tardy">Tardy</option>
                            </select>
                        </div>
                        <div class="toolbar">
                            <button type="submit" class="btn">Update Attendance</button>
                            <Link :href="route('teacher.attendance')" class="btn-secondary">Cancel</Link>
                        </div>
                    </form>
                </div>
                <div class="centered-actions">
                    <Link :href="route('teacher.attendance')" class="btn-back"><i class="fas fa-arrow-left"></i> Back to Attendance</Link>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

