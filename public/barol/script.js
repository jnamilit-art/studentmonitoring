document.addEventListener('DOMContentLoaded', function() {
    // =================================================================
    // IN-MEMORY DATA STORE (Simple "Database")
    // In a real application, this would be fetched from a server.
    // For now, we'll simulate it. We'll try to load from localStorage
    // to persist data across page reloads.
    // =================================================================
    const initialSchoolData = {
        students: [
            {
                id: 1,
                name: "Jerry Jouie Ungui",
                parentEmail: "parent@example.com",
                grades: {
                    "Mathematics": [92, 94, 90, null], "Science": [88, 90, 89, null],
                    "English": [90, 88, 91, null], "Filipino": [89, 91, 90, null]
                },
                attendance: [
                    { date: '2024-05-01', status: 'Present' }, { date: '2024-05-02', status: 'Present' },
                    { date: '2024-05-03', status: 'Absent' }, { date: '2024-05-06', status: 'Present' },
                    { date: '2024-05-07', status: 'Tardy' },
                ],
                behavior: [
                    { date: '2024-05-20', note: "Excellent participation in class discussions this week. Keep up the great work!", teacher: "Mr. Smith" },
                    { date: '2024-05-14', note: "Was very helpful to a classmate who was struggling with a concept.", teacher: "Mrs. Davis" }
                ]
            },
        ],
        announcements: [
            {
                title: "Parent-Teacher Conference", date: "May 22, 2024",
                content: "Our quarterly Parent-Teacher Conference will be held on June 5th. Please use the online portal to sign up for a time slot with your child's teachers. We look forward to seeing you there!"
            },
            {
                title: "No Classes: Memorial Day", date: "May 20, 2024",
                content: "In observance of Memorial Day, there will be no classes on Monday, May 27th. Classes will resume on Tuesday, May 28th."
            }
        ]
    };

    // Load data from localStorage or use initial data
    let schoolData = JSON.parse(localStorage.getItem('schoolData')) || initialSchoolData;

    // Function to save data to localStorage (will be used by admin page)
    // We define it here so both scripts could theoretically use it if merged.
    function saveData() {
        localStorage.setItem('schoolData', JSON.stringify(schoolData));
    }
    // =================================================================


    // --- Page Elements ---
    const mainContent = document.querySelector('.content-area');
    const featuresSection = document.getElementById('features');
    const contactSection = document.getElementById('contact');
    const dashboardPanel = document.getElementById('dashboard-panel');
    const gradesDashboard = document.getElementById('grades-dashboard');
    const attendanceDashboard = document.getElementById('attendance-dashboard');
    const behaviorDashboard = document.getElementById('behavior-dashboard');
    const announcementsDashboard = document.getElementById('announcements-dashboard');

    // --- Modal Elements ---
    const loginModal = document.getElementById('login-modal');
    const loginBtn = document.getElementById('login-btn');
    const startNowBtn = document.getElementById('start-now-btn');
    const closeModalBtn = document.getElementById('close-modal-btn');

    // --- Form Switching Elements ---
    const loginForm = document.getElementById('login-form');
    const registerForm = document.getElementById('register-form');
    const showRegisterLink = document.getElementById('show-register');
    const showLoginLink = document.getElementById('show-login');

    // --- Form Elements ---
    const loginFormElement = document.getElementById('login-form-element');
    const registerFormElement = document.getElementById('register-form-element');

    // --- Dashboard Elements ---
    const viewGradesCard = document.getElementById('view-grades-card');
    const viewAttendanceCard = document.getElementById('view-attendance-card');
    const viewBehaviorCard = document.getElementById('view-behavior-card');
    const viewAnnouncementsCard = document.getElementById('view-announcements-card');
    const backToDashboardBtn = document.getElementById('back-to-dashboard-btn');
    const backToDashboardFromAttendanceBtn = document.getElementById('back-to-dashboard-from-attendance-btn');
    const backToDashboardFromBehaviorBtn = document.getElementById('back-to-dashboard-from-behavior-btn');
    const backToDashboardFromAnnouncementsBtn = document.getElementById('back-to-dashboard-from-announcements-btn');


    // --- Modal Logic ---
    const openLoginModal = (e) => {
        e.preventDefault();
        loginModal.style.display = 'flex';
    };

    // Show modal when header login or start now button is clicked
    loginBtn.addEventListener('click', openLoginModal);
    startNowBtn.addEventListener('click', openLoginModal);


    // Function to close the modal
    function closeModal() {
        loginModal.style.display = 'none';
    }
    closeModalBtn.addEventListener('click', closeModal);
    loginModal.addEventListener('click', function(e) {
        if (e.target === loginModal) {
            closeModal();
        }
    });

    // --- Form Switching Logic ---
    showRegisterLink.addEventListener('click', function(e) {
        e.preventDefault();
        loginForm.style.display = 'none';
        populateStudentRegistrationDropdown(); // Populate dropdown when form is shown
        registerForm.style.display = 'block';
    });

    showLoginLink.addEventListener('click', function(e) {
        e.preventDefault();
        registerForm.style.display = 'none';
        loginForm.style.display = 'block';
    });

    // --- Login/Logout Logic ---
    function handleLogin(e) {
        e.preventDefault();

        const isRegistering = registerForm.style.display === 'block';
        let student;

        if (isRegistering) {
            const selectedStudentId = document.getElementById('register-student').value;
            student = schoolData.students.find(s => s.id == selectedStudentId);
        } else {
            const email = document.getElementById('login-email').value;
            student = schoolData.students.find(s => s.parentEmail.toLowerCase() === email.toLowerCase());
        }

        if (!student) {
            alert("Login failed. Could not find a student with the provided information.");
            return;
        }

        // Store the logged-in student's data globally for other functions to use
        window.loggedInStudent = student;

        closeModal();
        updateUIForLogin();
    }

    // Both login and register forms will trigger a login
    loginFormElement.addEventListener('submit', handleLogin);
    registerFormElement.addEventListener('submit', handleLogin);

    function handleLogout(e) {
        e.preventDefault();
        updateUIForLogout();
    }

    function showView(viewToShow) {
        // Hide all main views
        mainContent.style.display = 'none';
        featuresSection.style.display = 'none';
        contactSection.style.display = 'none';
        dashboardPanel.style.display = 'none';
        gradesDashboard.style.display = 'none';
        attendanceDashboard.style.display = 'none';
        behaviorDashboard.style.display = 'none';
        announcementsDashboard.style.display = 'none';

        // Show the requested view
        if (viewToShow === 'public') {
            mainContent.style.display = 'flex';
            featuresSection.style.display = 'block';
            contactSection.style.display = 'block';
        } else if (viewToShow) {
            viewToShow.style.display = 'block';
        }
    }

    function updateUIForLogin() {
        // Change header button to "Log out"
        loginBtn.textContent = 'Log out';
        loginBtn.removeEventListener('click', openLoginModal);
        loginBtn.addEventListener('click', handleLogout);

        // Show the dashboard and render data for the logged-in student
        showView(dashboardPanel);

        // Render the dashboard with the latest data
        renderParentDashboard();
        renderDetailedGrades(); // This is for the detailed view, but good to have ready
        renderAnnouncements(); // Render announcements to update the snippet
        renderParentAttendanceSummary();
        renderDetailedAttendance();
        renderParentBehaviorSummary();
        renderDetailedBehavior();

        // Scroll to top
        window.scrollTo(0, 0);
    }

    // --- Dynamic Parent Dashboard Rendering ---
    function renderParentDashboard() {
        const student = window.loggedInStudent;

        // --- Update Summary Card ---
        document.getElementById('dashboard-student-name').textContent = student.name;
        const gradesSummaryList = document.getElementById('dashboard-grades-summary');
        gradesSummaryList.innerHTML = ''; // Clear static content

        // Function to convert number grade to letter grade
        const toLetterGrade = (grade) => {
            if (grade === null || grade === undefined) return 'N/A';
            if (grade >= 93) return 'A';
            if (grade >= 90) return 'A-';
            if (grade >= 87) return 'B+';
            if (grade >= 83) return 'B';
            if (grade >= 80) return 'B-';
            if (grade >= 75) return 'C';
            return 'F';
        };

        for (const subject in student.grades) {
            // For the summary, we'll show the latest available grade
            const gradesForSubject = student.grades[subject].filter(g => g !== null);
            const latestGrade = gradesForSubject.length > 0 ? gradesForSubject[gradesForSubject.length - 1] : null;

            const li = document.createElement('li');
            li.innerHTML = `${subject}: <strong>${toLetterGrade(latestGrade)}</strong>`;
            gradesSummaryList.appendChild(li);
        }
    }

    // --- Dynamic Parent Attendance Rendering ---
    function renderParentAttendanceSummary() {
        const student = window.loggedInStudent;
        const attendanceLogBody = document.getElementById('dashboard-attendance-log');
        attendanceLogBody.innerHTML = '';

        // Get the last 3 attendance records, most recent first
        const recentRecords = [...student.attendance].reverse().slice(0, 3);

        if (recentRecords.length === 0) {
            attendanceLogBody.innerHTML = '<tr><td colspan="2">No attendance records found.</td></tr>';
            return;
        }

        recentRecords.forEach(record => {
            const row = attendanceLogBody.insertRow();
            row.innerHTML = `<td>${record.date}</td><td>${record.status}</td>`;
        });
    }

    function renderDetailedAttendance() {
        const student = window.loggedInStudent;
        const tableBody = document.getElementById('detailed-attendance-body');
        tableBody.innerHTML = '';

        // Group attendance by month
        const monthlyData = {};
        student.attendance.forEach(record => {
            const month = new Date(record.date).toLocaleString('default', { month: 'long', year: 'numeric' });
            if (!monthlyData[month]) {
                monthlyData[month] = { present: 0, absent: 0, total: 0 };
            }
            if (record.status === 'Present' || record.status === 'Tardy') {
                monthlyData[month].present++;
            }
            if (record.status === 'Absent') {
                monthlyData[month].absent++;
            }
        });

        for (const month in monthlyData) {
            const data = monthlyData[month];
            const total = data.present + data.absent;
            const rate = total > 0 ? ((data.present / total) * 100).toFixed(1) : '100.0';
            const row = tableBody.insertRow();
            row.innerHTML = `
                <td>${month}</td>
                <td>${data.present}</td>
                <td>${data.absent}</td>
                <td>${rate}%</td>
            `;
        }
    }

    // --- Dynamic Parent Behavior Rendering ---
    function renderParentBehaviorSummary() {
        const student = window.loggedInStudent;
        const summaryCard = document.querySelector('#view-behavior-card blockquote');

        if (student.behavior && student.behavior.length > 0) {
            // Get the most recent note
            const latestNote = [...student.behavior].sort((a, b) => new Date(b.date) - new Date(a.date))[0];
            summaryCard.textContent = `"${latestNote.note}"`;
        } else {
            summaryCard.textContent = "No behavior notes recorded.";
        }
    }

    function renderDetailedBehavior() {
        const student = window.loggedInStudent;
        const tableBody = document.querySelector('#behavior-dashboard tbody');
        tableBody.innerHTML = '';

        if (!student.behavior || student.behavior.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="3">No behavior notes found.</td></tr>';
            return;
        }

        const sortedBehavior = [...student.behavior].sort((a, b) => new Date(b.date) - new Date(a.date));

        sortedBehavior.forEach(record => {
            const row = tableBody.insertRow();
            row.innerHTML = `
                <td>${record.date}</td>
                <td>${record.note}</td>
                <td>${record.teacher}</td>
            `;
        });
    }

    // --- Dynamic Detailed Grades Table Rendering ---
    function renderDetailedGrades() {
        const student = window.loggedInStudent;
        const tableBody = document.getElementById('detailed-grades-body');
        tableBody.innerHTML = ''; // Clear static content

        for (const subject in student.grades) {
            const row = document.createElement('tr');
            const grades = student.grades[subject];

            // Calculate average, ignoring nulls
            const validGrades = grades.filter(g => g !== null);
            const average = validGrades.length > 0 
                ? (validGrades.reduce((a, b) => a + b, 0) / validGrades.length).toFixed(1) 
                : '-';

            // Create the row HTML
            row.innerHTML = `
                <td>${subject}</td>
                <td>${grades[0] !== null ? grades[0] : '-'}</td>
                <td>${grades[1] !== null ? grades[1] : '-'}</td>
                <td>${grades[2] !== null ? grades[2] : '-'}</td>
                <td>${grades[3] !== null ? grades[3] : '-'}</td>
                <td>${average}</td>
            `;

            tableBody.appendChild(row);
        }
    }

    // --- Helper Functions ---
    function populateStudentRegistrationDropdown() {
        const selectElement = document.getElementById('register-student');
        // Clear existing options except the placeholder
        selectElement.innerHTML = '<option value="" disabled selected>Select Your Child</option>';
        schoolData.students.forEach(student => {
            selectElement.innerHTML += `<option value="${student.id}">${student.name}</option>`;
        });
    }

    // Initial render on page load for the case where a user might already be "logged in"
    // This logic is now handled by the login flow.

    function updateUIForLogout() {
        // Change header button to "Log out"
        loginBtn.textContent = 'Log in';
        loginBtn.removeEventListener('click', handleLogout);
        loginBtn.addEventListener('click', openLoginModal);

        // Show public content
        showView('public');
    }

    // --- Dashboard Navigation ---
    viewGradesCard.addEventListener('click', () => {
        showView(gradesDashboard);
    });

    backToDashboardBtn.addEventListener('click', () => {
        showView(dashboardPanel);
    });

    viewAttendanceCard.addEventListener('click', () => {
        showView(attendanceDashboard);
    });

    backToDashboardFromAttendanceBtn.addEventListener('click', () => {
        showView(dashboardPanel);
    });

    viewBehaviorCard.addEventListener('click', () => {
        showView(behaviorDashboard);
    });

    backToDashboardFromBehaviorBtn.addEventListener('click', () => {
        showView(dashboardPanel);
    });

    viewAnnouncementsCard.addEventListener('click', () => {
        showView(announcementsDashboard);
    });

    backToDashboardFromAnnouncementsBtn.addEventListener('click', () => {
        showView(dashboardPanel);
    });

    // --- Announcement Rendering (for Parent and Dashboard Snippet) ---
    function renderAnnouncements() {
        const parentAnnouncementsList = document.querySelector('#announcements-dashboard .announcements-list');
        const latestAnnouncementCard = document.querySelector('#view-announcements-card blockquote');

        // Clear existing lists
        parentAnnouncementsList.innerHTML = '';

        // Render from the central data store, most recent first
        [...schoolData.announcements].forEach((announcement) => {
            // Render for parent view
            const parentCard = document.createElement('div');
            parentCard.className = 'announcement-card';
            parentCard.innerHTML = `
                <h4>${announcement.title}</h4>
                <span class="announcement-date">${announcement.date}</span>
                <p>${announcement.content}</p>
            `;
            parentAnnouncementsList.appendChild(parentCard);
        });

        // Update latest announcement snippet
        if (schoolData.announcements.length > 0) {
            latestAnnouncementCard.textContent = `"${schoolData.announcements[0].content}"`;
        } else {
            latestAnnouncementCard.textContent = "No recent announcements.";
        }
    }
});
    