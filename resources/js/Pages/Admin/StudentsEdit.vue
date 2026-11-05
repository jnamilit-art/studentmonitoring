<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    student: Object,
});

const form = useForm({
    name: props.student.name,
    parent_email: props.student.parent_email,
});

const submit = () => {
    form.put(route('teacher.students.update', props.student.id));
};
</script>

<template>
    <Head title="Edit Student" />

    <AdminLayout>
        <div class="page-shell">
            <div class="page-container stack-sections">
                <div class="content-panel">
                    <div class="content-panel__header">
                        <h1 class="content-panel__title">Edit Student</h1>
                    </div>
                    <form class="form-stack" @submit.prevent="submit">
                        <div class="form-group">
                            <label>Full Name</label>
                            <input v-model="form.name" type="text" required>
                        </div>
                        <div class="form-group">
                            <label>Parent's Email</label>
                            <input v-model="form.parent_email" type="email" required>
                        </div>
                        <div class="toolbar">
                            <button type="submit" class="btn">Update Student</button>
                            <Link :href="route('teacher.students')" class="btn-secondary">Cancel</Link>
                        </div>
                    </form>
                </div>
                <div class="centered-actions">
                    <Link :href="route('teacher.students')" class="btn-back"><i class="fas fa-arrow-left"></i> Back to Students</Link>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

