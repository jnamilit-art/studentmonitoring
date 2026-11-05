<script setup>
import SiteLayout from '@/Layouts/SiteLayout.vue';
import { computed } from 'vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';

const page = usePage();
const flashSuccess = computed(() => page.props.flash?.success ?? '');
const flashInfo = computed(() => page.props.flash?.info ?? '');
const flashError = computed(() => page.props.flash?.error ?? '');
const currentSchool = computed(() => page.props.currentSchool ?? null);

// Debug: log currentSchool to console
console.log('Current School:', currentSchool.value);

const form = useForm({
    name: '',
    email: '',
    message: '',
});

const submitForm = () => {
    form.post(route('contact.submit'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <Head :title="currentSchool ? `${currentSchool.name} - Student Monitoring System` : 'Home'" />

    <SiteLayout>
        <div class="home-shell">
            <section class="hero-section">
                <div class="hero-panel">
                    <h1 v-if="currentSchool">
                        Welcome to the Student Monitoring System of {{ currentSchool.name }}
                    </h1>
                    <h1 v-else>
                        Welcome to the Student Monitoring System
                    </h1>
                    <p>
                        This platform allows parents and teachers to monitor students'
                        performance, attendance, and behavior in real time.
                    </p>
                </div>
            </section>

            <section id="features" class="section-shell section-shell--light">
                <div class="section-shell__inner">
                    <div class="section-heading">
                        <h2>Key Features of Our System</h2>
                    </div>

                    <div class="feature-grid">
                        <div class="feature-card">
                            <i class="fas fa-graduation-cap feature-icon"></i>
                            <h3>Real-Time Grade Tracking</h3>
                            <p>
                                Instantly view your child's latest test scores,
                                assignment grades, and overall academic performance
                                as soon as they are updated by teachers.
                            </p>
                        </div>

                        <div class="feature-card">
                            <i class="fas fa-calendar-check feature-icon"></i>
                            <h3>Daily Attendance Monitoring</h3>
                            <p>
                                Stay informed about your child's presence in school.
                                Receive notifications for absences or tardies to ensure
                                they are safe and punctual.
                            </p>
                        </div>

                        <div class="feature-card">
                            <i class="fas fa-comments feature-icon"></i>
                            <h3>Behavior &amp; Participation Insights</h3>
                            <p>
                                Access teacher notes on classroom behavior,
                                participation levels, and important conduct updates
                                to support your child's development.
                            </p>
                        </div>
                    </div>

                    <div class="action-row">
                        <Link :href="route('parent.login')" class="btn-primary">
                            Start Now
                        </Link>
                    </div>
                </div>
            </section>

            <section id="contact" class="section-shell section-shell--white">
                <div class="section-shell__inner">
                    <div class="section-heading">
                        <h2>Get In Touch</h2>
                        <p>
                            Have questions? We'd love to hear from you. Fill out the form below
                            and we'll get back to you as soon as possible.
                        </p>
                    </div>

                    <div class="flash-stack">
                        <p v-if="flashSuccess" class="flash-message flash-message--success">
                            {{ flashSuccess }}
                        </p>
                        <p v-if="flashInfo" class="flash-message flash-message--info">
                            {{ flashInfo }}
                        </p>
                        <p v-if="flashError" class="flash-message flash-message--error">
                            {{ flashError }}
                        </p>
                    </div>

                    <div class="info-grid">
                        <div class="contact-block">
                            <h3>Contact Information</h3>
                            <p>penaranda street, surigao city, surigao del norte</p>
                            <p>09123456789</p>
                            <p>pilotschool@gmail.com</p>
                        </div>

                        <div class="content-panel form-panel">
                            <form class="form-stack" @submit.prevent="submitForm">
                                <div class="form-group">
                                    <input v-model="form.name" type="text" placeholder="Your Name" required>
                                    <p v-if="form.errors.name" class="error-text">{{ form.errors.name }}</p>
                                </div>

                                <div class="form-group">
                                    <input v-model="form.email" type="email" placeholder="Your Email" required>
                                    <p v-if="form.errors.email" class="error-text">{{ form.errors.email }}</p>
                                </div>

                                <div class="form-group">
                                    <textarea v-model="form.message" placeholder="Your Message" rows="6" required></textarea>
                                    <p v-if="form.errors.message" class="error-text">{{ form.errors.message }}</p>
                                </div>

                                <button type="submit" class="btn-submit" :disabled="form.processing">
                                    {{ form.processing ? 'Sending...' : 'Send Message' }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </SiteLayout>
</template>
