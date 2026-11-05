<script setup>
import SuperAdminLayout from '@/Layouts/SuperAdminLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    school: Object,
    users: Array,
    students: Array,
});
</script>

<template>
    <Head :title="`School Details - ${school.name}`" />

    <SuperAdminLayout>
        <div class="page-shell">
            <div class="page-container">
                <div class="dashboard-section">
                    <Link :href="route('super_admin.dashboard')" class="back-link">
                        &larr; Back to Dashboard
                    </Link>
                    <h1>{{ school.name }}</h1>
                    <p>School details and associated data.</p>
                </div>

                <div class="school-info-card">
                    <h2>School Information</h2>
                    <div class="info-grid">
                        <div class="info-item">
                            <label>School Name</label>
                            <p>{{ school.name }}</p>
                        </div>
                        <div class="info-item">
                            <label>Address</label>
                            <p>{{ school.address || 'Not provided' }}</p>
                        </div>
                        <div class="info-item">
                            <label>Phone</label>
                            <p>{{ school.phone || 'Not provided' }}</p>
                        </div>
                        <div class="info-item">
                            <label>Email</label>
                            <p>{{ school.email || 'Not provided' }}</p>
                        </div>
                        <div class="info-item">
                            <label>Status</label>
                            <p>
                                <span :class="['status-badge', school.is_active ? 'active' : 'inactive']">
                                    {{ school.is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </p>
                        </div>
                        <div class="info-item">
                            <label>Created At</label>
                            <p>{{ school.created_at }}</p>
                        </div>
                    </div>
                </div>

                <div class="stats-summary">
                    <div class="stat-item">
                        <h3>{{ users.length }}</h3>
                        <p>Total Users</p>
                    </div>
                    <div class="stat-item">
                        <h3>{{ students.length }}</h3>
                        <p>Total Students</p>
                    </div>
                </div>

                <div class="data-section">
                    <h2>Users ({{ users.length }})</h2>
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Created At</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="user in users" :key="user.id">
                                    <td>{{ user.name }}</td>
                                    <td>{{ user.email }}</td>
                                    <td>
                                        <span :class="['role-badge', user.role]">
                                            {{ user.role }}
                                        </span>
                                    </td>
                                    <td>{{ user.created_at }}</td>
                                </tr>
                                <tr v-if="users.length === 0">
                                    <td colspan="4" class="text-center">No users found</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="data-section">
                    <h2>Students ({{ students.length }})</h2>
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Parent Email</th>
                                    <th>Created At</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="student in students" :key="student.id">
                                    <td>{{ student.name }}</td>
                                    <td>{{ student.parent_email }}</td>
                                    <td>{{ student.created_at }}</td>
                                </tr>
                                <tr v-if="students.length === 0">
                                    <td colspan="3" class="text-center">No students found</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </SuperAdminLayout>
</template>

<style scoped>
.back-link {
    display: inline-block;
    margin-bottom: 0.5rem;
    color: #3b82f6;
    text-decoration: none;
    font-weight: 500;
}

.back-link:hover {
    text-decoration: underline;
}

.school-info-card {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    margin-bottom: 1.5rem;
}

.school-info-card h2 {
    margin: 0 0 1rem 0;
    font-size: 1.25rem;
    font-weight: 600;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.info-item label {
    font-size: 0.875rem;
    font-weight: 500;
    color: #6b7280;
}

.info-item p {
    margin: 0;
    font-size: 1rem;
    color: #111827;
}

.stats-summary {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.stat-item {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    text-align: center;
}

.stat-item h3 {
    margin: 0 0 0.5rem 0;
    font-size: 2rem;
    font-weight: 700;
    color: #111827;
}

.stat-item p {
    margin: 0;
    font-size: 0.875rem;
    color: #6b7280;
}

.data-section {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    margin-bottom: 1.5rem;
}

.data-section h2 {
    margin: 0 0 1rem 0;
    font-size: 1.25rem;
    font-weight: 600;
}

.table-container {
    overflow-x: auto;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
}

.data-table th,
.data-table td {
    padding: 0.75rem 1rem;
    text-align: left;
    border-bottom: 1px solid #e5e7eb;
}

.data-table th {
    background-color: #f9fafb;
    font-weight: 600;
    font-size: 0.875rem;
    color: #6b7280;
}

.data-table tbody tr:hover {
    background-color: #f9fafb;
}

.status-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 500;
}

.status-badge.active {
    background-color: #d1fae5;
    color: #065f46;
}

.status-badge.inactive {
    background-color: #fee2e2;
    color: #991b1b;
}

.role-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 500;
    text-transform: capitalize;
}

.role-badge.admin {
    background-color: #dbeafe;
    color: #1e40af;
}

.role-badge.teacher {
    background-color: #fef3c7;
    color: #92400e;
}

.role-badge.parent {
    background-color: #e0e7ff;
    color: #3730a3;
}

.role-badge.super_admin {
    background-color: #fce7f3;
    color: #9d174d;
}

.text-center {
    text-align: center;
}
</style>
