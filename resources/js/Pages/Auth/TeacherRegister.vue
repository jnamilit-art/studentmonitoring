<script setup>
import SiteLayout from '@/Layouts/SiteLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    gradeLevels: {
        type: Array,
        required: true,
    },
});

const form = useForm({
    name: '',
    email: '',
    grade_level: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('teacher.register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Teacher Registration" />

    <SiteLayout>
        <div class="auth-overlay">
            <div class="auth-card">
                <Link :href="route('home')" class="auth-close">&times;</Link>
                <h1>Teacher Registration</h1>
                <form class="form-stack" @submit.prevent="submit">
                    <div class="input-group">
                        <label for="teacher-register-name">Full Name</label>
                        <input id="teacher-register-name" v-model="form.name" type="text" required autofocus>
                        <p v-if="form.errors.name" class="error-text">{{ form.errors.name }}</p>
                    </div>

                    <div class="input-group">
                        <label for="teacher-register-email">Email</label>
                        <input id="teacher-register-email" v-model="form.email" type="email" required>
                        <p v-if="form.errors.email" class="error-text">{{ form.errors.email }}</p>
                    </div>

                    <div class="input-group">
                        <label for="teacher-register-grade-level">Select Your Grade Level</label>
                        <select id="teacher-register-grade-level" v-model="form.grade_level">
                            <option disabled value="">--Grade Level's--</option>
                            <option v-for="gradeLevel in gradeLevels" :key="gradeLevel" :value="gradeLevel">
                                {{ gradeLevel }}
                            </option>
                        </select>
                        <p v-if="form.errors.grade_level" class="error-text">{{ form.errors.grade_level }}</p>
                    </div>

                    <div class="input-group">
                        <label for="teacher-register-password">Password</label>
                        <input id="teacher-register-password" v-model="form.password" type="password" required>
                        <p v-if="form.errors.password" class="error-text">{{ form.errors.password }}</p>
                    </div>

                    <div class="input-group">
                        <label for="teacher-register-password-confirmation">Confirm Password</label>
                        <input id="teacher-register-password-confirmation" v-model="form.password_confirmation" type="password" required>
                    </div>

                    <button type="submit" class="btn">Register</button>
                </form>
                <p class="auth-helper">
                    Already have an account?
                    <Link :href="route('teacher.login')">Login here</Link>
                </p>
            </div>
        </div>
    </SiteLayout>
</template>
