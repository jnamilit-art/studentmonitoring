<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';

defineProps({
    announcements: Array,
});

const form = useForm({
    title: '',
    content: '',
});

const submit = () => {
    form.post(route('teacher.announcements.store'), {
        onSuccess: () => form.reset(),
    });
};

const destroyAnnouncement = (id) => {
    if (!window.confirm('Are you sure you want to delete this announcement?')) return;

    router.delete(route('teacher.announcements.destroy', id));
};
</script>

<template>
    <Head title="Manage Announcements" />

    <AdminLayout>
        <div class="page-shell">
            <div class="page-container stack-sections">
                <div class="content-panel">
                    <div class="content-panel__header">
                        <h1 class="content-panel__title">Manage Announcements</h1>
                    </div>

                    <div class="stack-sections">
                        <div>
                            <h2 class="subsection-title">Add New Announcement</h2>
                            <form class="form-stack" @submit.prevent="submit">
                                <div class="form-group">
                                    <label for="announcement-title">Title</label>
                                    <input id="announcement-title" v-model="form.title" type="text" required>
                                </div>
                                <div class="form-group">
                                    <label for="announcement-content">Content</label>
                                    <textarea id="announcement-content" v-model="form.content" rows="4" required></textarea>
                                </div>
                                <button type="submit" class="btn" :disabled="form.processing">Add Announcement</button>
                            </form>
                        </div>

                        <div>
                            <h2 class="subsection-title">Existing Announcements</h2>
                            <div v-if="announcements.length" class="list-stack">
                                <div v-for="announcement in announcements" :key="announcement.id" class="list-row">
                                    <div class="list-row__content">
                                        <strong>{{ announcement.title }}</strong> - {{ announcement.content }}
                                    </div>
                                    <div class="list-row__actions">
                                        <Link :href="route('teacher.announcements.edit', announcement.id)" class="btn-inline">
                                            Edit
                                        </Link>
                                        <button type="button" class="btn-danger" @click="destroyAnnouncement(announcement.id)">Delete</button>
                                    </div>
                                </div>
                            </div>
                            <p v-else class="muted-empty">No announcements yet.</p>
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

