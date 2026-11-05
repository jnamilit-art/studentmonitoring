<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    announcement: Object,
});

const form = useForm({
    title: props.announcement.title,
    content: props.announcement.content,
});

const submit = () => {
    form.put(route('teacher.announcements.update', props.announcement.id));
};
</script>

<template>
    <Head title="Edit Announcement" />

    <AdminLayout>
        <div class="page-shell">
            <div class="page-container stack-sections">
                <div class="content-panel">
                    <div class="content-panel__header">
                        <h1 class="content-panel__title">Edit Announcement</h1>
                    </div>
                    <form class="form-stack" @submit.prevent="submit">
                        <div class="form-group">
                            <label>Title</label>
                            <input v-model="form.title" type="text" required>
                        </div>
                        <div class="form-group">
                            <label>Content</label>
                            <textarea v-model="form.content" rows="4" required></textarea>
                        </div>
                        <div class="toolbar">
                            <button type="submit" class="btn">Update Announcement</button>
                            <Link :href="route('teacher.announcements')" class="btn-secondary">Cancel</Link>
                        </div>
                    </form>
                </div>
                <div class="centered-actions">
                    <Link :href="route('teacher.announcements')" class="btn-back"><i class="fas fa-arrow-left"></i> Back to Announcements</Link>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

