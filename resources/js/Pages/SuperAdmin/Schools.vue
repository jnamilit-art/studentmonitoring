<script setup>
import SuperAdminLayout from '@/Layouts/SuperAdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    schools: Array,
    appDomain: String,
});

const showAddForm = ref(false);
const editingSchool = ref(null);

const addForm = useForm({
    name: '',
    subdomain: '',
    address: '',
    phone: '',
    email: '',
});

const editForm = useForm({
    name: '',
    subdomain: '',
    address: '',
    phone: '',
    email: '',
    is_active: true,
});

const openAddForm = () => {
    showAddForm.value = true;
    addForm.reset();
};

const closeAddForm = () => {
    showAddForm.value = false;
    addForm.reset();
};

const submitAddForm = () => {
    addForm.post(route('super_admin.schools.store'), {
        onSuccess: () => {
            closeAddForm();
        },
    });
};

const openEditForm = (school) => {
    editingSchool.value = school;
    editForm.name = school.name;
    editForm.subdomain = school.subdomain || '';
    editForm.address = school.address || '';
    editForm.phone = school.phone || '';
    editForm.email = school.email || '';
    editForm.is_active = school.is_active;
};

const closeEditForm = () => {
    editingSchool.value = null;
    editForm.reset();
};

const submitEditForm = () => {
    editForm.put(route('super_admin.schools.update', editingSchool.value.id), {
        onSuccess: () => {
            closeEditForm();
        },
    });
};

const deleteSchool = (schoolId) => {
    if (confirm('Are you sure you want to delete this school? This action cannot be undone.')) {
        useForm().delete(route('super_admin.schools.destroy', schoolId));
    }
};

const openSchoolUrl = (school) => {
    // For local development, use query parameter
    if (props.appDomain === 'localhost' || !props.appDomain) {
        window.open(`${window.location.origin}/?school_subdomain=${school.subdomain}`, '_blank');
    } else {
        // For production, use actual subdomain
        window.open(`http://${school.subdomain}.${props.appDomain}`, '_blank');
    }
};
</script>

<template>
    <Head title="Manage Schools" />

    <SuperAdminLayout>
        <div class="page-shell">
            <div class="page-container">
                <div class="dashboard-section">
                    <h1>Manage Schools</h1>
                    <p>Add, edit, and manage schools using the system.</p>
                </div>

                <div class="actions-bar">
                    <button @click="openAddForm" class="btn btn-primary">
                        Add New School
                    </button>
                </div>

                <!-- Add School Form Modal -->
                <div v-if="showAddForm" class="modal-overlay" @click="closeAddForm">
                    <div class="modal-content" @click.stop>
                        <div class="modal-header">
                            <h2>Add New School</h2>
                            <button @click="closeAddForm" class="btn-close">&times;</button>
                        </div>
                        <form @submit.prevent="submitAddForm" class="form">
                            <div class="form-group">
                                <label for="name">School Name *</label>
                                <input
                                    id="name"
                                    v-model="addForm.name"
                                    type="text"
                                    required
                                    class="form-control"
                                />
                            </div>
                            <div class="form-group">
                                <label for="subdomain">Subdomain *</label>
                                <input
                                    id="subdomain"
                                    v-model="addForm.subdomain"
                                    type="text"
                                    required
                                    pattern="[a-z0-9-]+"
                                    placeholder="e.g., school1"
                                    class="form-control"
                                />
                                <p class="helper-text">This will be used as: {{ addForm.subdomain }}.yoursite.com</p>
                            </div>
                            <div class="form-group">
                                <label for="address">Address</label>
                                <input
                                    id="address"
                                    v-model="addForm.address"
                                    type="text"
                                    class="form-control"
                                />
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone</label>
                                <input
                                    id="phone"
                                    v-model="addForm.phone"
                                    type="text"
                                    class="form-control"
                                />
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input
                                    id="email"
                                    v-model="addForm.email"
                                    type="email"
                                    class="form-control"
                                />
                            </div>
                            <div class="form-actions">
                                <button type="button" @click="closeAddForm" class="btn btn-secondary">
                                    Cancel
                                </button>
                                <button type="submit" class="btn btn-primary" :disabled="addForm.processing">
                                    {{ addForm.processing ? 'Saving...' : 'Add School' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Edit School Form Modal -->
                <div v-if="editingSchool" class="modal-overlay" @click="closeEditForm">
                    <div class="modal-content" @click.stop>
                        <div class="modal-header">
                            <h2>Edit School</h2>
                            <button @click="closeEditForm" class="btn-close">&times;</button>
                        </div>
                        <form @submit.prevent="submitEditForm" class="form">
                            <div class="form-group">
                                <label for="edit-name">School Name *</label>
                                <input
                                    id="edit-name"
                                    v-model="editForm.name"
                                    type="text"
                                    required
                                    class="form-control"
                                />
                            </div>
                            <div class="form-group">
                                <label for="edit-subdomain">Subdomain *</label>
                                <input
                                    id="edit-subdomain"
                                    v-model="editForm.subdomain"
                                    type="text"
                                    required
                                    pattern="[a-z0-9-]+"
                                    placeholder="e.g., school1"
                                    class="form-control"
                                />
                                <p class="helper-text">This will be used as: {{ editForm.subdomain }}.yoursite.com</p>
                            </div>
                            <div class="form-group">
                                <label for="edit-address">Address</label>
                                <input
                                    id="edit-address"
                                    v-model="editForm.address"
                                    type="text"
                                    class="form-control"
                                />
                            </div>
                            <div class="form-group">
                                <label for="edit-phone">Phone</label>
                                <input
                                    id="edit-phone"
                                    v-model="editForm.phone"
                                    type="text"
                                    class="form-control"
                                />
                            </div>
                            <div class="form-group">
                                <label for="edit-email">Email</label>
                                <input
                                    id="edit-email"
                                    v-model="editForm.email"
                                    type="email"
                                    class="form-control"
                                />
                            </div>
                            <div class="form-group checkbox-group">
                                <label>
                                    <input type="checkbox" v-model="editForm.is_active" />
                                    Active
                                </label>
                            </div>
                            <div class="form-actions">
                                <button type="button" @click="closeEditForm" class="btn btn-secondary">
                                    Cancel
                                </button>
                                <button type="submit" class="btn btn-primary" :disabled="editForm.processing">
                                    {{ editForm.processing ? 'Saving...' : 'Update School' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>School Name</th>
                                <th>Subdomain</th>
                                <th>Address</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Users</th>
                                <th>Students</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="school in schools" :key="school.id">
                                <td>{{ school.name }}</td>
                                <td>
                                    <a v-if="school.subdomain" @click.prevent="openSchoolUrl(school)" href="#" class="subdomain-link">
                                        {{ school.subdomain }}.yoursite.com
                                    </a>
                                    <span v-else class="text-muted">-</span>
                                </td>
                                <td>{{ school.address || '-' }}</td>
                                <td>{{ school.phone || '-' }}</td>
                                <td>{{ school.email || '-' }}</td>
                                <td>
                                    <span :class="['status-badge', school.is_active ? 'active' : 'inactive']">
                                        {{ school.is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>{{ school.users_count }}</td>
                                <td>{{ school.students_count }}</td>
                                <td>
                                    <div class="action-buttons">
                                        <Link :href="route('super_admin.school.details', school.id)" class="btn btn-sm btn-view">
                                            View
                                        </Link>
                                        <button @click="openEditForm(school)" class="btn btn-sm btn-edit">
                                            Edit
                                        </button>
                                        <button @click="deleteSchool(school.id)" class="btn btn-sm btn-delete">
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="schools.length === 0">
                                <td colspan="9" class="text-center">No schools found</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </SuperAdminLayout>
</template>

<style scoped>
.actions-bar {
    margin-bottom: 1.5rem;
}

.table-container {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
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

.action-buttons {
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

.text-center {
    text-align: center;
}

.subdomain-link {
    color: #3b82f6;
    font-weight: 500;
    font-family: monospace;
    text-decoration: none;
    transition: color 0.2s;
}

.subdomain-link:hover {
    color: #2563eb;
    text-decoration: underline;
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

.btn-primary:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.btn-secondary {
    background-color: #6b7280;
    color: white;
}

.btn-secondary:hover {
    background-color: #4b5563;
}

.btn-sm {
    padding: 0.25rem 0.75rem;
    font-size: 0.75rem;
}

.btn-view {
    background-color: #3b82f6;
    color: white;
}

.btn-view:hover {
    background-color: #2563eb;
}

.btn-edit {
    background-color: #f59e0b;
    color: white;
}

.btn-edit:hover {
    background-color: #d97706;
}

.btn-delete {
    background-color: #ef4444;
    color: white;
}

.btn-delete:hover {
    background-color: #dc2626;
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
    padding: 2rem;
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

.checkbox-group label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.checkbox-group input[type="checkbox"] {
    width: 1rem;
    height: 1rem;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 0.5rem;
    margin-top: 1rem;
}
</style>
