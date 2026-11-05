<script setup>
import SiteLayout from '@/Layouts/SiteLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    students: {
        type: Array,
        default: () => [],
    },
});

const form = useForm({
    name: '',
    email: '',
    student_id: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Parent Registration" />

    <SiteLayout>
        <div class="auth-overlay">
            <div class="auth-card">
                <Link :href="route('home')" class="auth-close">&times;</Link>
                <h1>Parent Registration</h1>

                <form class="form-stack" @submit.prevent="submit">
                    <div class="input-group">
                        <label for="parent-register-name">Full Name</label>
                        <input id="parent-register-name" v-model="form.name" type="text" required autofocus>
                        <p v-if="form.errors.name" class="error-text">{{ form.errors.name }}</p>
                    </div>

                    <div class="input-group">
                        <label for="parent-register-email">Email</label>
                        <input id="parent-register-email" v-model="form.email" type="email" required>
                        <p v-if="form.errors.email" class="error-text">{{ form.errors.email }}</p>
                    </div>

                    <div class="input-group">
                        <label for="parent-register-student">Student</label>
                        <select id="parent-register-student" v-model="form.student_id">
                            <option value="">Link later</option>
                            <option v-for="student in students" :key="student.id" :value="student.id">
                                {{ student.name }}
                            </option>
                        </select>
                        <p v-if="form.errors.student_id" class="error-text">{{ form.errors.student_id }}</p>
                    </div>

                    <div class="input-group">
                        <label for="parent-register-password">Password</label>
                        <input id="parent-register-password" v-model="form.password" type="password" required>
                        <p v-if="form.errors.password" class="error-text">{{ form.errors.password }}</p>
                    </div>

                    <div class="input-group">
                        <label for="parent-register-password-confirmation">Confirm Password</label>
                        <input
                            id="parent-register-password-confirmation"
                            v-model="form.password_confirmation"
                            type="password"
                            required
                        >
                    </div>

                    <button type="submit" class="btn" :disabled="form.processing">Register</button>
                </form>

                <p class="auth-helper">
                    Already have an account?
                    <Link :href="route('parent.login')">Login here</Link>
                </p>
            </div>
        </div>
    </SiteLayout>
</template>
