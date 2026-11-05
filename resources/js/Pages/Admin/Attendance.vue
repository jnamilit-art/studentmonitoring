<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';

defineProps({
    students: Array,
    attendances: Array,
});

const form = useForm({
    student_id: '',
    date: new Date().toISOString().slice(0, 10),
    status: 'Present',
});

const submit = () => {
    form.post(route('teacher.attendance.store'), {
        onSuccess: () => {
            form.student_id = '';
            form.date = new Date().toISOString().slice(0, 10);
            form.status = 'Present';
        },
    });
};

const destroyAttendance = (id) => {
    if (!window.confirm('Are you sure you want to delete this attendance record?')) return;

    router.delete(route('teacher.attendance.destroy', id));
};
</script>

<template>
    <Head title="Manage Attendance" />

    <AdminLayout>
        <div class="page-shell">
            <div class="page-container stack-sections">
                <div class="content-panel">
                    <div class="content-panel__header">
                        <h1 class="content-panel__title">Manage Student Attendance</h1>
                    </div>
                    <div class="stack-sections">
                        <div>
                            <h2 class="subsection-title">Log Attendance</h2>
                            <form class="form-stack" @submit.prevent="submit">
                                <div class="form-group">
                                    <label>Student Name</label>
                                    <select v-model="form.student_id" required>
                                        <option disabled value="">Select a Student</option>
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
                                <button type="submit" class="btn">Log Attendance</button>
                            </form>
                        </div>

                        <div>
                            <h2 class="subsection-title">Recent Attendance Logs</h2>
                            <div class="table-shell">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Student</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="attendance in attendances" :key="attendance.id">
                                            <td>{{ attendance.student_name }}</td>
                                            <td>{{ attendance.date }}</td>
                                            <td>{{ attendance.status }}</td>
                                            <td>
                                                <div class="toolbar">
                                                    <Link :href="route('teacher.attendance.edit', attendance.id)" class="btn-inline">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </Link>
                                                    <button type="button" class="btn-danger" @click="destroyAttendance(attendance.id)">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr v-if="!attendances.length">
                                            <td colspan="4">No attendance records yet.</td>
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

