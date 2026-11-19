<script setup>
import SuperAdminLayout from '@/Layouts/SuperAdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    school: Object,
    users: Array,
    students: Array,
});

const showAddUserModal = ref(false);

const userForm = useForm({
    name: '',
    email: '',
    role: 'admin',
    password: '',
});

const openAddUser = () => {
    userForm.reset();
    showAddUserModal.value = true;
};

const closeAddUser = () => {
    showAddUserModal.value = false;
    userForm.reset();
};

const submitUserForm = () => {
    userForm.post(route('super_admin.school.users.store', props.school.id), {
        onSuccess: () => {
            closeAddUser();
        },
    });
};
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
                        <div v-if="school.subdomain" class="info-item">
                            <label>Portal Link</label>
                            <p>
                                <a :href="`/?school_subdomain=${school.subdomain}`" target="_blank" style="color: #059669; font-weight: 700; text-decoration: none;">
                                    Open School Portal ↗
                                </a>
                            </p>
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

                <!-- Users Section -->
                <div class="data-section">
                    <div class="section-header-row">
                        <h2>Users ({{ users.length }})</h2>
                        <button @click="openAddUser" class="btn btn-primary btn-sm">
                            + Add User / Admin
                        </button>
                    </div>

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

                <!-- Students Section -->
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

                <!-- Add User Modal -->
                <div v-if="showAddUserModal" class="modal-overlay" @click="closeAddUser">
                    <div class="modal-content" @click.stop>
                        <div class="modal-header">
                            <h2>Add User to {{ school.name }}</h2>
                            <button class="btn-close" @click="closeAddUser">&times;</button>
                        </div>
                        <form @submit.prevent="submitUserForm" class="form">
                            <div class="form-group">
                                <label>Full Name</label>
                                <input v-model="userForm.name" type="text" class="form-control" placeholder="e.g. Maria Santos" required />
                                <p v-if="userForm.errors.name" class="error-text">{{ userForm.errors.name }}</p>
                            </div>
                            <div class="form-group">
                                <label>Email Address</label>
                                <input v-model="userForm.email" type="email" class="form-control" placeholder="e.g. admin@school.com" required />
                                <p v-if="userForm.errors.email" class="error-text">{{ userForm.errors.email }}</p>
                            </div>
                            <div class="form-group">
                                <label>Role</label>
                                <select v-model="userForm.role" class="form-control" required>
                                    <option value="admin">School Admin (Full Management)</option>
                                    <option value="teacher">Teacher (Classroom & Grades)</option>
                                    <option value="parent">Parent</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Password (min. 6 characters)</label>
                                <input v-model="userForm.password" type="password" class="form-control" placeholder="••••••••" required />
                                <p v-if="userForm.errors.password" class="error-text">{{ userForm.errors.password }}</p>
                            </div>
                            <div class="form-actions">
                                <button type="button" @click="closeAddUser" class="btn btn-secondary">Cancel</button>
                                <button type="submit" class="btn btn-primary" :disabled="userForm.processing">
                                    {{ userForm.processing ? 'Saving...' : 'Create User' }}
                                </button>
                            </div>
                        </form>
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
    margin: 0;
    font-size: 1.25rem;
    font-weight: 600;
}

.section-header-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
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

.btn {
    display: inline-block;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-size: 0.875rem;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.2s;
    border: none;
    cursor: pointer;
}

.btn-primary {
    background-color: #3b82f6;
    color: white;
}

.btn-primary:hover {
    background-color: #2563eb;
}

.btn-secondary {
    background-color: #6b7280;
    color: white;
}

.btn-secondary:hover {
    background-color: #4b5563;
}

.btn-sm {
    padding: 0.35rem 0.75rem;
    font-size: 0.8rem;
}

.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}

.modal-content {
    background: white;
    padding: 1.75rem;
    border-radius: 8px;
    max-width: 500px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.modal-header h2 {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 600;
}

.btn-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    padding: 0;
    line-height: 1;
}

.form {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.form-group label {
    font-weight: 500;
    font-size: 0.875rem;
}

.form-control {
    padding: 0.5rem;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 0.875rem;
}

.form-control:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.error-text {
    margin: 0;
    color: #ef4444;
    font-size: 0.8rem;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 0.5rem;
    margin-top: 1rem;
}
</style>
