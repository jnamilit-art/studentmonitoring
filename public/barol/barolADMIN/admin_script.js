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
                name: "Jerry Joie Ungui",
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

    // Function to save data to localStorage
    function saveData() {
        localStorage.setItem('schoolData', JSON.stringify(schoolData));
    }

    // --- Page Elements ---
    const adminLoginModal = document.getElementById('admin-login-modal');
    const adminDashboardPanel = document.getElementById('admin-dashboard-panel');
    const manageAnnouncementsPanel = document.getElementById('manage-announcements-panel');
    const manageGradesPanel = document.getElementById('manage-grades-panel');
    const manageAttendancePanel = document.getElementById('manage-attendance-panel');
    const manageBehaviorPanel = document.getElementById('manage-behavior-panel');
    const manageStudentsPanel = document.getElementById('manage-students-panel');

    // --- Modal Elements ---
    const closeAdminModalBtn = document.getElementById('close-admin-modal-btn');

    // --- Form Elements ---
    const adminLoginFormElement = document.getElementById('admin-login-form-element');
    const registerForm = document.getElementById('register-form');
    const showRegisterLink = document.getElementById('show-register');
    const showLoginLink = document.getElementById('show-login');
 
    // --- Dashboard Card Elements ---
    const manageAnnouncementsCard = document.getElementById('manage-announcements-card');
    const manageGradesCard = document.getElementById('manage-grades-card');
    const manageAttendanceCard = document.getElementById('manage-attendance-card');
    const manageBehaviorCard = document.getElementById('manage-behavior-card');
    const manageStudentsCard = document.getElementById('manage-students-card');

    // --- Back Button Elements ---
    const backToAdminDashboardBtn = document.getElementById('back-to-admin-dashboard-btn');
    const backToAdminFromGradesBtn = document.getElementById('back-to-admin-from-grades-btn');
    const backToAdminFromAttendanceBtn = document.getElementById('back-to-admin-from-attendance-btn');
    const backToAdminFromBehaviorBtn = document.getElementById('back-to-admin-from-behavior-btn');
    const backToAdminFromStudentsBtn = document.getElementById('back-to-admin-from-students-btn');

    // --- Admin Form Elements ---
    const addAnnouncementForm = document.getElementById('add-announcement-form');
    const updateGradeForm = document.getElementById('update-grade-form');
    const logAttendanceForm = document.getElementById('log-attendance-form');
    const logBehaviorForm = document.getElementById('log-behavior-form');
    const addStudentForm = document.getElementById('add-student-form');

    // --- Admin Login & UI Control ---
    function showView(viewToShow) {
        [adminDashboardPanel, manageAnnouncementsPanel, manageGradesPanel, manageAttendancePanel, manageBehaviorPanel, manageStudentsPanel].forEach(panel => {
            panel.style.display = 'none';
        });
        if (viewToShow) {
            viewToShow.style.display = 'block';
        }
    }

    function closeAdminModal() {
        adminLoginModal.style.display = 'none';
    }

    function handleAdminLogin(e) {
        e.preventDefault();
        const emailInput = document.getElementById('admin-login-email');
        const passwordInput = document.getElementById('admin-login-password');

        if (emailInput.value === 'admin' && passwordInput.value === 'admin123') {
            sessionStorage.setItem('isAdminLoggedIn', 'true');
            updateUIForAdminLogin();
        } else {
            alert('Invalid admin credentials. Please try again.');
            passwordInput.value = '';
        }
    }

    function updateUIForAdminLogin() {
        closeAdminModal();
        showView(adminDashboardPanel);
        populateAdminStudentDropdowns();
        window.scrollTo(0, 0);
    }

    // Check if admin is logged in on page load
    if (sessionStorage.getItem('isAdminLoggedIn') === 'true') {
        updateUIForAdminLogin();
    } else {
        adminLoginModal.style.display = 'flex';
    }

    // --- Event Listeners ---
    adminLoginFormElement.addEventListener('submit', handleAdminLogin);
    closeAdminModalBtn.addEventListener('click', () => {
        // Prevent closing if not logged in
        if (sessionStorage.getItem('isAdminLoggedIn') !== 'true') {
            window.location.href = '../index.html';
        } else {
            closeAdminModal();
        }
    });

    // --- Admin Dashboard Navigation ---
    manageAnnouncementsCard.addEventListener('click', () => {
        renderAdminAnnouncements();
        showView(manageAnnouncementsPanel);
    });
    manageGradesCard.addEventListener('click', () => showView(manageGradesPanel));
    manageAttendanceCard.addEventListener('click', () => showView(manageAttendancePanel));
    manageBehaviorCard.addEventListener('click', () => showView(manageBehaviorPanel));
    manageStudentsCard.addEventListener('click', () => {
        renderStudentManagementPage();
        showView(manageStudentsPanel);
    });

    // Back buttons
    [backToAdminDashboardBtn, backToAdminFromGradesBtn, backToAdminFromAttendanceBtn, backToAdminFromBehaviorBtn, backToAdminFromStudentsBtn].forEach(btn => {
        btn.addEventListener('click', () => showView(adminDashboardPanel));
    });

    // --- Helper Functions ---
    function populateAdminStudentDropdowns() {
        const selects = [
            document.getElementById('student-name-grades'),
            document.getElementById('student-name-attendance'),
            document.getElementById('student-name-behavior')
        ];

        selects.forEach(select => {
            if (!select) return;
            const currentValue = select.value;
            select.innerHTML = '<option value="" disabled selected>Select a Student</option>';
            schoolData.students.forEach(student => {
                const option = new Option(student.name, student.name);
                select.add(option);
            });
            select.value = currentValue; // Preserve selection if possible
        });
    }

    // --- Admin Announcement Management ---
    function renderAdminAnnouncements() {
        const adminAnnouncementsList = document.getElementById('admin-announcements-list');
        adminAnnouncementsList.innerHTML = '';

        [...schoolData.announcements].reverse().forEach((announcement, index) => {
            const adminItem = document.createElement('div');
            adminItem.className = 'admin-announcement-item';
            adminItem.innerHTML = `
                <span><strong>${announcement.title}</strong> - ${announcement.content.substring(0, 30)}...</span>
                <button class="btn btn-delete" data-index="${schoolData.announcements.length - 1 - index}">Delete</button>
            `;
            adminAnnouncementsList.appendChild(adminItem);
        });

        adminAnnouncementsList.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function() {
                const indexToDelete = parseInt(this.dataset.index, 10);
                schoolData.announcements.splice(indexToDelete, 1);
                saveData();
                renderAdminAnnouncements();
            });
        });
    }

    addAnnouncementForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const newAnnouncement = {
            title: document.getElementById('announcement-title').value,
            content: document.getElementById('announcement-content').value,
            date: new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })
        };
        schoolData.announcements.unshift(newAnnouncement);
        saveData();
        renderAdminAnnouncements();
        addAnnouncementForm.reset();
    });

    // --- Admin Update Grade Management ---
    updateGradeForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const studentName = document.getElementById('student-name-grades').value;
        const subject = document.getElementById('subject-grades').value;
        const period = document.getElementById('grading-period').value;
        const newGrade = parseInt(document.getElementById('grade-value').value, 10);

        const periodMap = { "1st Grading": 0, "2nd Grading": 1, "3rd Grading": 2, "4th Grading": 3 };
        const gradeIndex = periodMap[period];
        const studentToUpdate = schoolData.students.find(s => s.name === studentName);

        if (studentToUpdate && gradeIndex !== undefined && studentToUpdate.grades[subject]) {
            studentToUpdate.grades[subject][gradeIndex] = newGrade;
            saveData();

            const recentUpdatesTableBody = document.getElementById('recent-grades-tbody');
            const newRow = recentUpdatesTableBody.insertRow(0);
            newRow.innerHTML = `<td>${studentName}</td><td>${subject}</td><td>${period}</td><td>${newGrade}</td>`;

            alert(`Successfully updated ${studentName}'s grade for ${subject} (${period}).`);
            updateGradeForm.reset();
        } else {
            alert(`Error: Could not update grade. Please check student, subject, and period.`);
        }
    });

    // --- Admin Log Attendance Management ---
    logAttendanceForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const studentName = document.getElementById('student-name-attendance').value;
        const date = document.getElementById('attendance-date').value;
        const status = document.getElementById('attendance-status').value;
        const studentToUpdate = schoolData.students.find(s => s.name === studentName);

        if (studentToUpdate) {
            studentToUpdate.attendance.push({ date, status });
            saveData();

            const recentLogsBody = document.getElementById('recent-attendance-tbody');
            const newRow = recentLogsBody.insertRow(0);
            newRow.innerHTML = `<td>${studentName}</td><td>${date}</td><td>${status}</td>`;

            alert(`Attendance logged for ${studentName}.`);
            logAttendanceForm.reset();
        } else {
            alert(`Error: Could not find student "${studentName}".`);
        }
    });

    // --- Admin Log Behavior Management ---
    logBehaviorForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const studentName = document.getElementById('student-name-behavior').value;
        const date = document.getElementById('behavior-date').value;
        const note = document.getElementById('behavior-note').value;
        const studentToUpdate = schoolData.students.find(s => s.name === studentName);

        if (studentToUpdate) {
            if (!studentToUpdate.behavior) studentToUpdate.behavior = [];
            studentToUpdate.behavior.push({ date, note, teacher: "Admin" });
            saveData();

            const recentLogsBody = document.getElementById('recent-behavior-tbody');
            const newRow = recentLogsBody.insertRow(0);
            newRow.innerHTML = `<td>${studentName}</td><td>${date}</td><td>${note.substring(0, 40)}...</td>`;

            alert(`Behavior note logged for ${studentName}.`);
            logBehaviorForm.reset();
        } else {
            alert(`Error: Could not find student "${studentName}".`);
        }
    });

    // --- Admin Student Management ---
    function renderStudentManagementPage() {
        const studentListContainer = document.getElementById('admin-students-list');
        studentListContainer.innerHTML = '';

        schoolData.students.forEach((student, index) => {
            const studentItem = document.createElement('div');
            studentItem.className = 'admin-student-item';
            studentItem.innerHTML = `
                <span><strong>${student.name}</strong> (Parent Email: ${student.parentEmail})</span>
                <button class="btn btn-delete" data-index="${index}">Delete</button>
            `;
            studentListContainer.appendChild(studentItem);
        });

        studentListContainer.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function() {
                const studentIndex = parseInt(this.dataset.index, 10);
                const studentName = schoolData.students[studentIndex].name;
                if (confirm(`Are you sure you want to delete ${studentName}? This action cannot be undone.`)) {
                    schoolData.students.splice(studentIndex, 1);
                    saveData();
                    renderStudentManagementPage();
                    populateAdminStudentDropdowns();
                }
            });
        });
    }

    addStudentForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const studentName = document.getElementById('student-full-name').value.trim();
        const parentEmail = document.getElementById('parent-email').value.trim();

        if (!studentName || !parentEmail) {
            alert("Please fill out all fields.");
            return;
        }

        const newStudent = {
            id: schoolData.students.length > 0 ? Math.max(...schoolData.students.map(s => s.id)) + 1 : 1,
            name: studentName,
            parentEmail: parentEmail,
            grades: { "Mathematics": [null, null, null, null], "Science": [null, null, null, null], "English": [null, null, null, null], "Filipino": [null, null, null, null] },
            attendance: [],
            behavior: []
        };

        schoolData.students.push(newStudent);
        saveData();
        alert(`Student "${studentName}" has been successfully added!`);
        renderStudentManagementPage();
        populateAdminStudentDropdowns();
        addStudentForm.reset();
    });
});