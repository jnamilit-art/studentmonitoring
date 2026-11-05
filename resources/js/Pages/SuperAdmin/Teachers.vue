<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    teachers: {
        type: Array,
        required: true,
    },
    gradeLevels: {
        type: Array,
        required: true,
    },
});

const form = useForm({
    grade_level: '',
});

const assignedCount = computed(() => props.teachers.filter((teacher) => teacher.grade_level).length);
const unassignedCount = computed(() => props.teachers.length - assignedCount.value);

const updateGradeLevel = (teacher) => {
    form.grade_level = teacher.grade_level || '';
    form.put(route('admin.users.update', teacher.id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Manage Teachers" />

    <AdminLayout>
        <div class="page-shell">
            <div class="page-container">
                <div class="dashboard-section">
                    <div class="section-header">
                        <h1>Manage Teachers</h1>
                        <Link :href="route('admin.dashboard')" class="btn-back">Back</Link>
                    </div>
                    <p>View registered teachers and manage their grade-level assignments.</p>
                </div>

                <div class="teacher-summary-grid">
                    <div class="teacher-summary-card">
                        <span class="teacher-summary-card__label">Registered</span>
                        <strong>{{ props.teachers.length }}</strong>
                    </div>
                    <div class="teacher-summary-card">
                        <span class="teacher-summary-card__label">Assigned</span>
                        <strong>{{ assignedCount }}</strong>
                    </div>
                    <div class="teacher-summary-card teacher-summary-card--attention">
                        <span class="teacher-summary-card__label">Unassigned</span>
                        <strong>{{ unassignedCount }}</strong>
                    </div>
                </div>

                <div class="management-table-wrap teacher-table-wrap">
                    <table class="management-table">
                        <thead>
                            <tr>
                                <th>Teacher</th>
                                <th>Email</th>
                                <th>Current Assignment</th>
                                <th>Change Grade Level</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="teacher in props.teachers" :key="teacher.id">
                                <td>
                                    <div class="teacher-identity">
                                        <span class="teacher-avatar">{{ teacher.name.charAt(0).toUpperCase() }}</span>
                                        <strong>{{ teacher.name }}</strong>
                                    </div>
                                </td>
                                <td class="teacher-email">{{ teacher.email }}</td>
                                <td>
                                    <span
                                        class="teacher-grade-pill"
                                        :class="{ 'teacher-grade-pill--empty': !teacher.grade_level }"
                                    >
                                        {{ teacher.grade_level || 'Unassigned' }}
                                    </span>
                                </td>
                                <td class="teacher-select-cell">
                                    <select v-model="teacher.grade_level" class="teacher-grade-select">
                                        <option value="">Unassigned</option>
                                        <option v-for="gradeLevel in props.gradeLevels" :key="gradeLevel" :value="gradeLevel">
                                            {{ gradeLevel }}
                                        </option>
                                    </select>
                                    <p v-if="form.errors.grade_level" class="error-text">{{ form.errors.grade_level }}</p>
                                </td>
                                <td>
                                    <button
                                        type="button"
                                        class="btn-inline teacher-save-button"
                                        :disabled="form.processing"
                                        @click="updateGradeLevel(teacher)"
                                    >
                                        {{ form.processing ? 'Saving' : 'Save' }}
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="props.teachers.length === 0">
                                <td colspan="5" class="muted-empty">No registered teachers yet.</td>
                            </tr>
                        </tbody>
                    </table>
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
