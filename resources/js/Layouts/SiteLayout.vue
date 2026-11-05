<script setup>
import '../../css/barol.css';

import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage();
const user = computed(() => page.props.auth?.user ?? null);
const currentSchool = computed(() => page.props.currentSchool ?? null);
const isParentSection = computed(() => route().current('parent.*'));
const isParentRegister = computed(() => route().current('register') || route().current('parent.register'));
const isTeacherSection = computed(() => route().current('teacher.*'));
const isAdminSection = computed(() => route().current('admin.*'));
const isAdminRegister = computed(() => route().current('admin.register'));
const hasAdminAccess = computed(() => user.value?.role === 'admin');
const hasTeacherAccess = computed(() => user.value?.role === 'teacher');
const hasParentAccess = computed(() => user.value?.role === 'parent');
const homeRoute = computed(() => {
    if (!user.value) {
        return route('home');
    }

    if (user.value.role === 'super_admin') {
        return route('super_admin.dashboard');
    }

    if (user.value.role === 'admin') {
        return route('admin.dashboard');
    }

    if (user.value.role === 'teacher') {
        return route('teacher.dashboard');
    }

    if (user.value.role === 'parent') {
        return route('parent.dashboard');
    }

    return route('home');
});
</script>

<template>
    <div class="site-shell">
        <header class="site-header">
            <div class="site-header__inner">
                <Link :href="homeRoute" class="logo-container">
                    <span class="logo-mark" aria-hidden="true">
                        <svg class="logo-symbol" viewBox="0 0 64 64" role="img">
                            <circle cx="32" cy="32" r="28" fill="currentColor" opacity="0.14" />
                            <path
                                d="M18 27l14-7 14 7-14 7-14-7z"
                                fill="none"
                                stroke="currentColor"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="4"
                            />
                            <path
                                d="M24 34v6c0 4 16 4 16 0v-6"
                                fill="none"
                                stroke="currentColor"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="4"
                            />
                            <path
                                d="M18 45h8l7-8 5 5 8-11"
                                fill="none"
                                stroke="currentColor"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="4"
                            />
                            <path
                                d="M46 31v8h-8"
                                fill="none"
                                stroke="currentColor"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="4"
                            />
                        </svg>
                    </span>
                    <span class="logo-copy">
                        <span class="logo-title">Student Monitoring System</span>
                        <span v-if="currentSchool" class="school-badge">{{ currentSchool.name }}</span>
                    </span>
                </Link>

                <nav class="main-nav">
                    <Link :href="homeRoute">Home</Link>
                    <a :href="`${route('home')}#features`">About</a>
                    <a :href="`${route('home')}#contact`">Contact</a>

                    <template v-if="!user">
                        <Link :href="route('parent.login')">Parent Log in</Link>
                        <Link :href="route('teacher.login')">Teacher Log in</Link>
                        <Link :href="route('admin.login')">Admin Log in</Link>
                    </template>
                    <Link v-else :href="route('logout')" method="post" as="button">Log out</Link>
                </nav>
            </div>
        </header>

        <main class="site-main">
            <slot />
        </main>

        <footer class="site-footer-main">
            <p>&copy; 2025 Student Monitoring System. All Rights Reserved.</p>
        </footer>
    </div>
</template>
