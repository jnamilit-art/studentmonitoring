<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';

const page = usePage();
const props = defineProps({
    school: Object,
    counts: Object,
});
const user = page.props.auth?.user ?? null;
</script>

<template>
    <Head title="Admin Dashboard" />

    <AdminLayout>
        <div class="page-shell">
            <div class="page-container">
                <div class="dashboard-section">
                    <h1>Welcome Admin {{ user?.name }}</h1>
                    <p v-if="school">
                        You are managing <strong>{{ school.name }}</strong>
                    </p>
                    <p v-else>
                        No school assigned yet. Please contact the system administrator.
                    </p>
                </div>

                <div class="stats-grid">
                    <div class="stat-card">
                        <h3>{{ counts?.teachers || 0 }}</h3>
                        <p>Teachers</p>
                    </div>
                    <div class="stat-card">
                        <h3>{{ counts?.parents || 0 }}</h3>
                        <p>Parents</p>
                    </div>
                    <div class="stat-card">
                        <h3>{{ counts?.students || 0 }}</h3>
                        <p>Students</p>
                    </div>
                </div>

                <div class="admin-grid">
                    <Link :href="route('admin.users')" class="management-card clickable">
                        <h3 class="management-card__title"><i class="fas fa-users"></i><span>Manage Users</span></h3>
                        <p>View parent, teacher, and admin accounts for this school.</p>
                    </Link>
                    <Link :href="route('admin.teachers')" class="management-card clickable">
                        <h3 class="management-card__title"><i class="fas fa-chalkboard-teacher"></i><span>Manage Teachers</span></h3>
                        <p>Assign teachers to grade levels and manage their accounts.</p>
                    </Link>
                    <Link :href="route('admin.grade-levels')" class="management-card clickable">
                        <h3 class="management-card__title"><i class="fas fa-layer-group"></i><span>Manage Grade Levels</span></h3>
                        <p>Create and manage grade levels for your school.</p>
                    </Link>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin: 30px 0;
}

.stat-card {
    background: white;
    padding: 30px;
    border-radius: 18px;
    box-shadow: 0 8px 24px rgba(31, 41, 55, 0.08);
    text-align: center;
}

.stat-card h3 {
    font-size: 48px;
    font-weight: 700;
    color: var(--brand);
    margin: 0 0 10px 0;
}

.stat-card p {
    font-size: 16px;
    color: var(--muted);
    margin: 0;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
</style>

