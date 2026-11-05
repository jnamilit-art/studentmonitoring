<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    gradeLevels: {
        type: Array,
        required: true,
    },
    unassignedTeachers: {
        type: Array,
        required: true,
    },
});

const editingId = ref(null);
const editingTeacherId = ref(null);
const expandedGradeLevelId = ref(null);
const editForm = useForm({
    title: '',
});
const teacherForm = useForm({
    email: '',
});

const startEditing = (gradeLevel) => {
    expandedGradeLevelId.value = gradeLevel.id;
    editingId.value = gradeLevel.id;
    editForm.reset();
    editForm.clearErrors();
    editForm.title = gradeLevel.title;
};

const cancelEditing = () => {
    editingId.value = null;
    editForm.reset();
    editForm.clearErrors();
};

const toggleGradeLevel = (gradeLevel) => {
    expandedGradeLevelId.value = expandedGradeLevelId.value === gradeLevel.id ? null : gradeLevel.id;
};

const startTeacherEditing = (teacher) => {
    editingTeacherId.value = teacher.id;
    teacherForm.reset();
    teacherForm.clearErrors();
    teacherForm.email = teacher.email;
};

const cancelTeacherEditing = () => {
    editingTeacherId.value = null;
    teacherForm.reset();
    teacherForm.clearErrors();
};

const saveGradeLevel = (gradeLevel) => {
    editForm.put(route('admin.grade-levels.update', gradeLevel.id), {
        preserveScroll: true,
        onSuccess: () => cancelEditing(),
    });
};

const destroyGradeLevel = (gradeLevel) => {
    if (!window.confirm(`Are you sure you want to delete ${gradeLevel.title}? Teachers assigned here will become unassigned.`)) {
        return;
    }

    router.delete(route('admin.grade-levels.destroy', gradeLevel.id), {
        preserveScroll: true,
    });
};

const saveTeacherEmail = (teacher) => {
    teacherForm.put(route('admin.users.update', teacher.id), {
        preserveScroll: true,
        onSuccess: () => cancelTeacherEditing(),
    });
};

const destroyTeacher = (teacher) => {
    if (!window.confirm(`Are you sure you want to delete teacher ${teacher.name}?`)) {
        return;
    }

    router.delete(route('admin.users.destroy', teacher.id), {
        preserveScroll: true,
        onSuccess: () => {
            if (editingTeacherId.value === teacher.id) {
                cancelTeacherEditing();
            }
        },
    });
};
</script>

<template>
    <Head title="Manage Grade Level" />

    <AdminLayout>
        <div class="page-shell">
            <div class="page-container">
                <div class="dashboard-section">
                    <div class="section-header">
                        <h1>Manage Grade Level</h1>
                        <Link :href="route('admin.dashboard')" class="btn-back">Back</Link>
                    </div>
                    <p>View the teachers who registered under each grade level.</p>
                </div>

                <div class="admin-grid">
                    <article
                        v-for="gradeLevel in props.gradeLevels"
                        :key="gradeLevel.id"
                        class="management-card grade-level-card"
                        :class="{
                            'grade-level-card--editing': editingId === gradeLevel.id,
                            'grade-level-card--expanded': expandedGradeLevelId === gradeLevel.id,
                        }"
                    >
                        <button type="button" class="grade-level-card__toggle" @click="toggleGradeLevel(gradeLevel)">
                            <h3 class="management-card__title grade-level-card__title">
                                <span>{{ gradeLevel.title }}</span>
                                <span class="grade-level-card__meta">
                                    {{ gradeLevel.teachers.length }} {{ gradeLevel.teachers.length === 1 ? 'teacher' : 'teachers' }}
                                </span>
                            </h3>
                            <span class="grade-level-card__chevron">
                                {{ expandedGradeLevelId === gradeLevel.id ? '−' : '+' }}
                            </span>
                        </button>

                        <div v-if="expandedGradeLevelId === gradeLevel.id" class="grade-level-card__body">
                            <div class="grade-level-panel">
                                <h4 class="grade-level-panel__title">Grade-Level Settings</h4>
                                <template v-if="editingId !== gradeLevel.id">
                                    <div class="grade-level-panel__row">
                                        <span class="grade-level-panel__label">Title</span>
                                        <strong>{{ gradeLevel.title }}</strong>
                                    </div>
                                    <div class="list-row__actions">
                                        <button type="button" class="btn-inline" @click="startEditing(gradeLevel)">Edit Grade Level</button>
                                        <button type="button" class="btn-danger" @click="destroyGradeLevel(gradeLevel)">Delete Grade Level</button>
                                    </div>
                                </template>
                                <form v-else class="grade-level-edit" @submit.prevent="saveGradeLevel(gradeLevel)">
                                    <input v-model="editForm.title" type="text" class="field-input" required>
                                    <p v-if="editForm.errors.title" class="error-text">{{ editForm.errors.title }}</p>
                                    <div class="list-row__actions">
                                        <button type="submit" class="btn-inline">Save</button>
                                        <button type="button" class="btn-secondary" @click="cancelEditing">Cancel</button>
                                    </div>
                                </form>
                            </div>

                            <div v-if="gradeLevel.teachers.length">
                                <div
                                    v-for="teacher in gradeLevel.teachers"
                                    :key="teacher.id"
                                    class="grade-level-teacher"
                                >
                                    <template v-if="editingTeacherId === teacher.id">
                                        <div class="grade-level-teacher__content">
                                            <strong>{{ teacher.name }}</strong>
                                            <form class="grade-level-teacher__form" @submit.prevent="saveTeacherEmail(teacher)">
                                                <input v-model="teacherForm.email" type="email" class="field-input" required>
                                                <p v-if="teacherForm.errors.email" class="error-text">{{ teacherForm.errors.email }}</p>
                                            </form>
                                        </div>
                                        <div class="list-row__actions">
                                            <button type="button" class="btn-inline" @click="saveTeacherEmail(teacher)">Save</button>
                                            <button type="button" class="btn-secondary" @click="cancelTeacherEditing">Cancel</button>
                                            <button type="button" class="btn-danger" @click="destroyTeacher(teacher)">Delete Teacher</button>
                                        </div>
                                    </template>
                                    <template v-else>
                                        <div class="grade-level-teacher__content">
                                            <strong>{{ teacher.name }}</strong><br>
                                            <span>{{ teacher.email }}</span>
                                        </div>
                                        <div class="list-row__actions">
                                            <button type="button" class="btn-inline" @click="startTeacherEditing(teacher)">Edit Email</button>
                                            <button type="button" class="btn-danger" @click="destroyTeacher(teacher)">Delete Teacher</button>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <p v-else>No teachers registered for this grade level yet.</p>
                        </div>
                    </article>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.btn-back {
    padding: 8px 16px;
    background: var(--brand);
    color: white;
    border-radius: var(--radius-sm);
    text-decoration: none;
    font-weight: 500;
    transition: background 0.2s;
}

.btn-back:hover {
    background: var(--brand-deep);
}
</style>
