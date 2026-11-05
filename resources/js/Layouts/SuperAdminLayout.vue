<script setup>
import '../../css/admin.css';

import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="super-admin-shell">
        <header class="super-admin-header">
            <Link :href="route('super_admin.dashboard')" class="super-admin-brand">
                Student Monitoring System — Super Admin
            </Link>
            <nav aria-label="Super admin navigation">
                <Link :href="route('super_admin.dashboard')">Dashboard</Link>
                <Link :href="route('super_admin.schools')">Manage Schools</Link>
                <Link :href="route('logout')" method="post" as="button">Log out</Link>
            </nav>
        </header>
        <slot />
    </div>
</template>

<style scoped>
.super-admin-shell {
    min-height: 100vh;
    background: #f8fafc;
}

.super-admin-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    padding: 1rem 2rem;
    background: #0f172a;
    color: #fff;
}

.super-admin-brand {
    color: inherit;
    font-weight: 700;
    text-decoration: none;
}

nav {
    display: flex;
    align-items: center;
    gap: 1rem;
}

nav :deep(a),
nav :deep(button) {
    border: 0;
    background: transparent;
    color: inherit;
    cursor: pointer;
    font: inherit;
    text-decoration: none;
}
</style>
