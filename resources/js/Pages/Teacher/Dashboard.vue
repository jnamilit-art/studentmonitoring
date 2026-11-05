<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({
    teacher: {
        type: Object,
        default: () => ({
            name: null,
            grade_level: null,
        }),
    },
    stats: {
        type: Object,
        default: () => ({
            students: 0,
            grades: 0,
            attendance: 0,
            announcements: 0,
        }),
    },
    latestAnnouncement: {
        type: Object,
        default: null,
    },
    recentGrades: {
        type: Array,
        default: () => [],
    },
    recentAttendance: {
        type: Array,
        default: () => [],
    },
    recentBehavior: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <Head title="Teacher Dashboard" />

    <AdminLayout>
        <div class="dashboard-container">
            <section class="hero-card">
                <div>
                    <p class="eyebrow">Teacher's Control</p>
                    <h1>Welcome, {{ teacher?.name || 'Teacher' }}</h1>
                    <p class="dashboard-subtitle">
                        Grade level:
                        <strong>{{ teacher?.grade_level || 'No grade level assigned yet' }}</strong>
                    </p>
                </div>

                <div class="hero-note">
                    <p>Use this dashboard to monitor current school records and jump into classroom management tools.</p>
                </div>
            </section>

            <section class="stat-cards">
                <div class="stat-card">
                    <div class="stat-icon">S</div>
                    <div class="stat-content">
                        <h3>Total Students</h3>
                        <p class="stat-number">{{ stats.students }}</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">G</div>
                    <div class="stat-content">
                        <h3>Grades Submitted</h3>
                        <p class="stat-number">{{ stats.grades }}</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">A</div>
                    <div class="stat-content">
                        <h3>Attendance Records</h3>
                        <p class="stat-number">{{ stats.attendance }}</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">N</div>
                    <div class="stat-content">
                        <h3>Announcements</h3>
                        <p class="stat-number">{{ stats.announcements }}</p>
                    </div>
                </div>
            </section>

            <section class="action-cards">
                <div class="action-card">
                    <h3>Manage Students</h3>
                    <p>Add, edit, or remove students from your class records.</p>
                    <Link :href="route('teacher.students')" class="btn btn-primary">
                        Manage Students
                    </Link>
                </div>

                <div class="action-card">
                    <h3>Record Grades</h3>
                    <p>Enter and maintain student grades and assessments.</p>
                    <Link :href="route('teacher.grades')" class="btn btn-primary">
                        Manage Grades
                    </Link>
                </div>

                <div class="action-card">
                    <h3>Track Attendance</h3>
                    <p>Review and record daily attendance data.</p>
                    <Link :href="route('teacher.attendance')" class="btn btn-primary">
                        Manage Attendance
                    </Link>
                </div>

                <div class="action-card">
                    <h3>Behavior Notes</h3>
                    <p>Keep behavior records and participation notes organized.</p>
                    <Link :href="route('teacher.behavior')" class="btn btn-primary">
                        Manage Behavior
                    </Link>
                </div>

                <div class="action-card">
                    <h3>Announcements</h3>
                    <p>Create and publish updates for parents and students.</p>
                    <Link :href="route('teacher.announcements')" class="btn btn-primary">
                        Manage Announcements
                    </Link>
                </div>
            </section>

            <section class="dashboard-grid">
                <article class="panel panel-wide">
                    <div class="panel-header">
                        <h2>Latest Announcement</h2>
                    </div>

                    <div v-if="latestAnnouncement" class="announcement-card">
                        <h3>{{ latestAnnouncement.title }}</h3>
                        <p class="meta">{{ latestAnnouncement.date }}</p>
                        <p>{{ latestAnnouncement.content }}</p>
                    </div>

                    <p v-else class="empty-state">No announcements have been posted yet.</p>
                </article>

                <article class="panel">
                    <div class="panel-header">
                        <h2>Recent Grades</h2>
                    </div>

                    <div v-if="recentGrades.length" class="list">
                        <div v-for="grade in recentGrades" :key="grade.id" class="list-item">
                            <strong>{{ grade.student_name || 'Unknown student' }}</strong>
                            <span>{{ grade.subject }} - Period {{ grade.grading_period }}</span>
                            <span class="list-value">{{ grade.grade }}</span>
                        </div>
                    </div>

                    <p v-else class="empty-state">No grade records yet.</p>
                </article>

                <article class="panel">
                    <div class="panel-header">
                        <h2>Recent Attendance</h2>
                    </div>

                    <div v-if="recentAttendance.length" class="list">
                        <div v-for="attendance in recentAttendance" :key="attendance.id" class="list-item">
                            <strong>{{ attendance.student_name || 'Unknown student' }}</strong>
                            <span>{{ attendance.date }}</span>
                            <span class="list-value">{{ attendance.status }}</span>
                        </div>
                    </div>

                    <p v-else class="empty-state">No attendance records yet.</p>
                </article>

                <article class="panel panel-wide">
                    <div class="panel-header">
                        <h2>Recent Behavior Notes</h2>
                    </div>

                    <div v-if="recentBehavior.length" class="list">
                        <div v-for="behavior in recentBehavior" :key="behavior.id" class="list-item behavior-item">
                            <div>
                                <strong>{{ behavior.student_name || 'Unknown student' }}</strong>
                                <span>{{ behavior.date }} - {{ behavior.teacher || 'Teacher' }}</span>
                            </div>
                            <p>{{ behavior.note }}</p>
                        </div>
                    </div>

                    <p v-else class="empty-state">No behavior notes yet.</p>
                </article>
            </section>
        </div>
    </AdminLayout>
</template>

<style scoped>
.dashboard-container {
    padding: 2rem;
    display: grid;
    gap: 1.75rem;
    background:
        radial-gradient(circle at top right, rgba(234, 179, 8, 0.12), transparent 22rem),
        linear-gradient(180deg, #f8fafc 0%, #eef4ff 100%);
    min-height: 100%;
}

.hero-card {
    background: linear-gradient(135deg, #0f172a, #1d4ed8);
    color: #fff;
    border-radius: 1rem;
    padding: 2rem;
    display: grid;
    grid-template-columns: minmax(0, 2fr) minmax(0, 1fr);
    gap: 1.5rem;
    box-shadow: 0 18px 45px rgba(15, 23, 42, 0.18);
}

.eyebrow {
    text-transform: uppercase;
    letter-spacing: 0.12em;
    font-size: 0.75rem;
    opacity: 0.75;
    margin-bottom: 0.75rem;
}

.hero-card h1 {
    font-size: 2rem;
    font-weight: 700;
    margin: 0 0 0.5rem;
}

.dashboard-subtitle,
.hero-note p {
    margin: 0;
    color: rgba(255, 255, 255, 0.86);
}

.hero-note {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 0.85rem;
    padding: 1rem 1.1rem;
    align-self: end;
}

.stat-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.stat-card,
.action-card,
.panel {
    background: rgba(255, 255, 255, 0.96);
    border: 1px solid rgba(148, 163, 184, 0.18);
    border-radius: 1rem;
    box-shadow: 0 14px 30px rgba(15, 23, 42, 0.06);
}

.stat-card {
    padding: 1.25rem;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.stat-icon {
    width: 3rem;
    height: 3rem;
    border-radius: 0.85rem;
    background: linear-gradient(135deg, #f59e0b, #ea580c);
    color: #fff;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
}

.stat-content h3 {
    margin: 0 0 0.2rem;
    font-size: 0.92rem;
    color: #64748b;
}

.stat-number {
    margin: 0;
    font-size: 1.75rem;
    font-weight: 700;
    color: #0f172a;
}

.action-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 1rem;
}

.action-card {
    padding: 1.35rem;
}

.action-card h3,
.panel-header h2,
.announcement-card h3 {
    margin: 0 0 0.5rem;
    color: #0f172a;
}

.action-card p,
.announcement-card p,
.list-item span,
.behavior-item p,
.empty-state {
    color: #475569;
}

.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.75rem 1.1rem;
    border-radius: 0.7rem;
    font-weight: 600;
    text-decoration: none;
    transition: background 0.2s ease, transform 0.2s ease;
}

.btn-primary {
    background: #1d4ed8;
    color: #fff;
}

.btn-primary:hover {
    background: #1e40af;
    transform: translateY(-1px);
}

.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 1rem;
}

.panel {
    padding: 1.25rem;
}

.panel-wide {
    grid-column: span 2;
}

.panel-header {
    margin-bottom: 0.9rem;
}

.meta {
    font-size: 0.9rem;
    color: #64748b;
}

.list {
    display: grid;
    gap: 0.85rem;
}

.list-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    padding-bottom: 0.85rem;
    border-bottom: 1px solid #e2e8f0;
}

.list-item:last-child {
    border-bottom: 0;
    padding-bottom: 0;
}

.list-item strong {
    color: #0f172a;
}

.list-value {
    font-weight: 600;
    color: #1d4ed8;
}

.behavior-item {
    display: grid;
    align-items: start;
}

.behavior-item p {
    margin: 0.4rem 0 0;
}

.empty-state {
    margin: 0;
}

@media (max-width: 900px) {
    .hero-card,
    .dashboard-grid {
        grid-template-columns: 1fr;
    }

    .panel-wide {
        grid-column: span 1;
    }
}

@media (max-width: 640px) {
    .dashboard-container {
        padding: 1rem;
    }

    .hero-card {
        padding: 1.35rem;
    }

    .list-item {
        flex-direction: column;
        align-items: flex-start;
    }
}
</style>
