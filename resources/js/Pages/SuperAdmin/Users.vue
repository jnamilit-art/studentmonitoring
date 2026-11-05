<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    users: {
        type: Array,
        required: true,
    },
});

const form = useForm({
    role: '',
});

const updateRole = (user) => {
    form.role = user.role;
    form.put(route('admin.users.update', user.id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Manage Users" />

    <AdminLayout>
        <div class="page-shell">
            <div class="page-container">
                <div class="dashboard-section">
                    <div class="section-header">
                        <h1>Manage User Roles</h1>
                        <Link :href="route('admin.dashboard')" class="btn-back">Back</Link>
                    </div>
                    <p>Choose which accounts should act as parents, teachers, or admins.</p>
                </div>

                <div class="management-table-wrap">
                    <table class="management-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Save</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="user in props.users" :key="user.id">
                                <td>{{ user.name }}</td>
                                <td>{{ user.email }}</td>
                                <td>
                                    <select v-model="user.role">
                                        <option value="parent">Parent</option>
                                        <option value="teacher">Teacher</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                </td>
                                <td>
                                    <button type="button" class="btn-inline" @click="updateRole(user)">Save</button>
                                </td>
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
