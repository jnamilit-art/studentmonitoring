<script setup>
import SiteLayout from '@/Layouts/SiteLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('admin.register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Admin Registration" />

    <SiteLayout>
        <div class="auth-overlay">
            <div class="auth-card">
                <Link :href="route('home')" class="auth-close">&times;</Link>
                <h1>Admin Registration</h1>
                <form class="form-stack" @submit.prevent="submit">
                    <div class="input-group">
                        <label for="admin-register-name">Full Name</label>
                        <input id="admin-register-name" v-model="form.name" type="text" required autofocus>
                        <p v-if="form.errors.name" class="error-text">{{ form.errors.name }}</p>
                    </div>

                    <div class="input-group">
                        <label for="admin-register-email">Email</label>
                        <input id="admin-register-email" v-model="form.email" type="email" required>
                        <p v-if="form.errors.email" class="error-text">{{ form.errors.email }}</p>
                    </div>

                    <div class="input-group">
                        <label for="admin-register-password">Password</label>
                        <input id="admin-register-password" v-model="form.password" type="password" required>
                        <p v-if="form.errors.password" class="error-text">{{ form.errors.password }}</p>
                    </div>

                    <div class="input-group">
                        <label for="admin-register-password-confirmation">Confirm Password</label>
                        <input id="admin-register-password-confirmation" v-model="form.password_confirmation" type="password" required>
                    </div>

                    <button type="submit" class="btn">Register</button>
                </form>
                <p class="auth-helper">
                    Already have an account?
                    <Link :href="route('admin.login')">Login here</Link>
                </p>
            </div>
        </div>
    </SiteLayout>
</template>
