<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';

defineProps({
    students: Array,
    behaviors: Array,
});

const form = useForm({
    student_id: '',
    date: new Date().toISOString().slice(0, 10),
    note: '',
});

const submit = () => {
    form.post(route('teacher.behavior.store'), {
        onSuccess: () => {
            form.student_id = '';
            form.date = new Date().toISOString().slice(0, 10);
            form.note = '';
        },
    });
};

const destroyBehavior = (id) => {
    if (!window.confirm('Are you sure you want to delete this behavior record?')) return;

    router.delete(route('teacher.behavior.destroy', id));
};
</script>

<template>
    <Head title="Manage Behavior" />

    <AdminLayout>
        <div class="page-shell">
            <div class="page-container stack-sections">
                <div class="content-panel">
                    <div class="content-panel__header">
                        <h1 class="content-panel__title">Manage Behavior &amp; Participation</h1>
                    </div>
                    <div class="stack-sections">
                        <div>
                            <h2 class="subsection-title">Log New Behavior Note</h2>
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
                                    <label>Behavior Note</label>
                                    <textarea v-model="form.note" rows="4" required></textarea>
                                </div>
                                <button type="submit" class="btn">Log Note</button>
                            </form>
                        </div>

                        <div>
                            <h2 class="subsection-title">Recent Behavior Logs</h2>
                            <div class="table-shell">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Student</th>
                                            <th>Date</th>
                                            <th>Note</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="behavior in behaviors" :key="behavior.id">
                                            <td>{{ behavior.student_name }}</td>
                                            <td>{{ behavior.date }}</td>
                                            <td>{{ behavior.note.length > 40 ? `${behavior.note.slice(0, 40)}...` : behavior.note }}</td>
                                            <td>
                                                <div class="toolbar">
                                                    <Link :href="route('teacher.behavior.edit', behavior.id)" class="btn-inline">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </Link>
                                                    <button type="button" class="btn-danger" @click="destroyBehavior(behavior.id)">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr v-if="!behaviors.length">
                                            <td colspan="4">No behavior notes yet.</td>
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

