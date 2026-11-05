<script setup>
import SiteLayout from '@/Layouts/SiteLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('super_admin.login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Super Admin Login" />

    <SiteLayout>
        <div class="auth-overlay">
            <div class="auth-card">
                <Link :href="route('home')" class="auth-close">&times;</Link>
                <h1>Super Admin Login</h1>
                <form class="form-stack" @submit.prevent="submit">
                    <div class="input-group">
                        <label for="super-admin-login-email">Email</label>
                        <input id="super-admin-login-email" v-model="form.email" type="email" required autofocus>
                        <p v-if="form.errors.email" class="error-text">{{ form.errors.email }}</p>
                    </div>
                    <div class="input-group">
                        <label for="super-admin-login-password">Password</label>
                        <input id="super-admin-login-password" v-model="form.password" type="password" required>
                        <p v-if="form.errors.password" class="error-text">{{ form.errors.password }}</p>
                    </div>
                    <div class="checkbox-group">
                        <label>
                            <input type="checkbox" v-model="form.remember">
                            <span>Remember me</span>
                        </label>
                    </div>
                    <button type="submit" class="btn">Log In</button>
                </form>
                            </div>
        </div>
    </SiteLayout>
</template>
