<script setup>
import { ref } from 'vue';
import SiteLayout from '@/Layouts/SiteLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    gradeLevels: {
        type: Array,
        required: true,
    },
});

const isLogin = ref(true);

const loginForm = useForm({
    email: '',
    password: '',
    remember: false,
});

const registerForm = useForm({
    name: '',
    email: '',
    grade_level: '',
    password: '',
    password_confirmation: '',
});

const submitLogin = () => {
    loginForm.post(route('teacher.login'), {
        onFinish: () => loginForm.reset('password'),
    });
};

const submitRegister = () => {
    registerForm.post(route('teacher.register'), {
        onFinish: () => registerForm.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Teacher Portal" />

    <SiteLayout>
        <div class="auth-overlay">
            <div class="auth-card">
                <Link :href="route('home')" class="auth-close">&times;</Link>
                
                <div class="auth-tabs">
                    <button 
                        type="button" 
                        class="tab-btn" 
                        :class="{ active: isLogin }"
                        @click="isLogin = true"
                    >
                        Login
                    </button>
                    <button 
                        type="button" 
                        class="tab-btn" 
                        :class="{ active: !isLogin }"
                        @click="isLogin = false"
                    >
                        Register
                    </button>
                </div>

                <!-- Login Form -->
                <form v-if="isLogin" class="form-stack" @submit.prevent="submitLogin">
                    <h1>Teacher Login</h1>
                    <div class="input-group">
                        <label for="teacher-login-email">Email</label>
                        <input id="teacher-login-email" v-model="loginForm.email" type="email" required autofocus>
                        <p v-if="loginForm.errors.email" class="error-text">{{ loginForm.errors.email }}</p>
                    </div>
                    <div class="input-group">
                        <label for="teacher-login-password">Password</label>
                        <input id="teacher-login-password" v-model="loginForm.password" type="password" required>
                        <p v-if="loginForm.errors.password" class="error-text">{{ loginForm.errors.password }}</p>
                    </div>
                    <div class="checkbox-group">
                        <label>
                            <input type="checkbox" v-model="loginForm.remember">
                            <span>Remember me</span>
                        </label>
                    </div>
                    <button type="submit" class="btn">Log In</button>
                </form>

                <!-- Registration Form -->
                <form v-else class="form-stack" @submit.prevent="submitRegister">
                    <h1>Teacher Registration</h1>
                    <div class="input-group">
                        <label for="teacher-register-name">Full Name</label>
                        <input id="teacher-register-name" v-model="registerForm.name" type="text" required autofocus>
                        <p v-if="registerForm.errors.name" class="error-text">{{ registerForm.errors.name }}</p>
                    </div>
                    <div class="input-group">
                        <label for="teacher-register-email">Email</label>
                        <input id="teacher-register-email" v-model="registerForm.email" type="email" required>
                        <p v-if="registerForm.errors.email" class="error-text">{{ registerForm.errors.email }}</p>
                    </div>
                    <div class="input-group">
                        <label for="teacher-register-grade-level">Select Your Grade Level</label>
                        <select id="teacher-register-grade-level" v-model="registerForm.grade_level">
                            <option disabled value="">--Grade Level's--</option>
                            <option v-for="gradeLevel in gradeLevels" :key="gradeLevel" :value="gradeLevel">
                                {{ gradeLevel }}
                            </option>
                        </select>
                        <p v-if="registerForm.errors.grade_level" class="error-text">{{ registerForm.errors.grade_level }}</p>
                    </div>
                    <div class="input-group">
                        <label for="teacher-register-password">Password</label>
                        <input id="teacher-register-password" v-model="registerForm.password" type="password" required>
                        <p v-if="registerForm.errors.password" class="error-text">{{ registerForm.errors.password }}</p>
                    </div>
                    <div class="input-group">
                        <label for="teacher-register-password-confirmation">Confirm Password</label>
                        <input id="teacher-register-password-confirmation" v-model="registerForm.password_confirmation" type="password" required>
                    </div>
                    <button type="submit" class="btn">Register</button>
                </form>
            </div>
        </div>
    </SiteLayout>
</template>

<style scoped>
.auth-tabs {
    display: flex;
    margin-bottom: 2rem;
    border-bottom: 1px solid #e5e7eb;
}

.tab-btn {
    flex: 1;
    padding: 1rem;
    background: none;
    border: none;
    border-bottom: 2px solid transparent;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.2s;
}

.tab-btn.active {
    border-bottom-color: #3b82f6;
    color: #3b82f6;
}

.tab-btn:hover {
    background: #f9fafb;
}
</style>
