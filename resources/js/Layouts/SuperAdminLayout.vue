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
    font-size: 1.1rem;
}

nav {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
}

nav :deep(a),
nav :deep(button) {
    border: 0;
    background: transparent;
    color: inherit;
    cursor: pointer;
    font: inherit;
    text-decoration: none;
    font-size: 0.95rem;
    padding: 0.35rem 0.65rem;
    border-radius: 6px;
    transition: background 0.15s ease;
}

nav :deep(a):hover,
nav :deep(button):hover {
    background: rgba(255, 255, 255, 0.12);
}

@media (max-width: 768px) {
    .super-admin-header {
        flex-direction: column;
        align-items: flex-start;
        padding: 1rem;
        gap: 0.85rem;
    }

    .super-admin-brand {
        font-size: 1rem;
    }

    nav {
        width: 100%;
        gap: 0.5rem;
    }

    nav :deep(a),
    nav :deep(button) {
        background: rgba(255, 255, 255, 0.08);
        font-size: 0.88rem;
    }
}
</style>
