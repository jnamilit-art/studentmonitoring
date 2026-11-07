<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';

defineProps({
    students: Array,
});

const form = useForm({
    name: '',
    parent_email: '',
    student_email: '',
});

const submit = () => {
    form.post(route('teacher.students.store'), {
        onSuccess: () => form.reset(),
    });
};

const destroyStudent = (id) => {
    if (!window.confirm('Are you sure you want to delete this student?')) return;

    router.delete(route('teacher.students.destroy', id));
};
</script>

<template>
    <Head title="Manage Parents and Students" />

    <AdminLayout>
        <div class="page-shell">
            <div class="page-container stack-sections">
                <div class="content-panel">
                    <div class="content-panel__header">
                        <h1 class="content-panel__title">Manage Parents and Students</h1>
                    </div>
                    <div class="stack-sections">
                        <div>
                            <h2 class="subsection-title">Add New Student</h2>
                            <form class="form-stack" @submit.prevent="submit">
                                <div class="form-group">
                                    <label>Full Name</label>
                                    <input v-model="form.name" type="text" placeholder="e.g. John Doe" required>
                                </div>
                                <div class="form-group">
                                    <label>Parent's Email (receives notifications)</label>
                                    <input v-model="form.parent_email" type="email" placeholder="parent@example.com" required>
                                </div>
                                <div class="form-group">
                                    <label>Student's Email (Optional - receives notifications)</label>
                                    <input v-model="form.student_email" type="email" placeholder="student@example.com">
                                </div>
                                <button type="submit" class="btn" :disabled="form.processing">
                                    {{ form.processing ? 'Adding...' : 'Add Student' }}
                                </button>
                            </form>
                        </div>

                        <div>
                            <h2 class="subsection-title">Current Students</h2>
                            <div v-if="students.length" class="list-stack">
                                <div v-for="student in students" :key="student.id" class="list-row">
                                    <div class="list-row__content">
                                        <strong>{{ student.name }}</strong>
                                        <div style="font-size: 0.9rem; color: #64748b; margin-top: 2px;">
                                            <span>Parent: {{ student.parent_email }}</span>
                                            <span v-if="student.student_email" style="margin-left: 12px;">Student: {{ student.student_email }}</span>
                                        </div>
                                    </div>
                                    <div class="list-row__actions">
                                        <Link :href="route('teacher.students.edit', student.id)" class="btn-inline">Edit</Link>
                                        <button type="button" class="btn-danger" @click="destroyStudent(student.id)">Delete</button>
                                    </div>
                                </div>
                            </div>
                            <p v-else class="muted-empty">No students yet.</p>
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
