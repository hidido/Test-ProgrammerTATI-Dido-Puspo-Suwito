// Wait until the DOM is fully loaded before running the script
document.addEventListener('DOMContentLoaded', () => {
    
    // --- DATABASE SIMULATION ---
    // Simulating database tables for users and logs. In a real application,
    // this data would come from a server-side API.
    const users = [
        { id: 1, name: 'Budi', role: 'Kepala Dinas', superiorId: null, password: 'password' },
        { id: 2, name: 'Anto', role: 'Kepala Bidang 1', superiorId: 1, password: 'password' },
        { id: 3, name: 'Citra', role: 'Kepala Bidang 2', superiorId: 1, password: 'password' },
        { id: 4, name: 'Doni', role: 'Staff', superiorId: 2, password: 'password' },
        { id: 5, name: 'Eka', role: 'Staff', superiorId: 3, password: 'password' },
    ];

    let logs = [
        { id: 1, userId: 4, activity: 'Menyiapkan laporan bulanan untuk Kepala Bidang 1.', date: '2025-07-30', status: 'Pending' },
        { id: 2, userId: 5, activity: 'Melakukan riset untuk proyek baru.', date: '2025-07-30', status: 'Disetujui' },
        { id: 3, userId: 2, activity: 'Rapat koordinasi dengan semua staff.', date: '2025-07-30', status: 'Pending' },
        { id: 4, userId: 1, activity: 'Menghadiri rapat pimpinan daerah.', date: '2025-07-31', status: 'Pending' },
        { id: 5, userId: 4, activity: 'Follow up vendor untuk pengadaan ATK.', date: '2025-07-31', status: 'Ditolak' },
    ];

    // Variable to hold the currently logged-in user's data
    let currentUser = null;

    // --- DOM ELEMENTS CACHING ---
    // Caching frequently accessed DOM elements improves performance.
    const loginPage = document.getElementById('login-page');
    const appPage = document.getElementById('app-page');
    const loginForm = document.getElementById('login-form');
    const loginError = document.getElementById('login-error');
    const logoutBtn = document.getElementById('logout-btn');
    
    const userNameEl = document.getElementById('user-name');
    const userRoleEl = document.getElementById('user-role');
    
    const navDashboard = document.getElementById('nav-dashboard');
    const navVerification = document.getElementById('nav-verification');
    const dashboardContent = document.getElementById('dashboard-content');
    const verificationContent = document.getElementById('verification-content');
    
    const logList = document.getElementById('log-list');
    const verificationList = document.getElementById('verification-list');
    
    const addLogBtn = document.getElementById('add-log-btn');
    const addLogModal = document.getElementById('add-log-modal');
    const addLogForm = document.getElementById('add-log-form');
    const cancelLogBtn = document.getElementById('cancel-log-btn');

    // --- FUNCTIONS ---
    
    /**
     * Handles the user login process.
     * @param {Event} e The form submission event.
     */
    const handleLogin = (e) => {
        e.preventDefault();
        const username = loginForm.username.value;
        const password = loginForm.password.value;
        
        const user = users.find(u => u.id.toString() === username && u.password === password);
        
        if (user) {
            currentUser = user;
            loginPage.classList.add('hidden');
            appPage.classList.remove('hidden');
            loginError.classList.add('hidden');
            loginForm.reset();
            initializeApp();
        } else {
            loginError.classList.remove('hidden');
        }
    };

    /**
     * Handles the user logout process.
     */
    const handleLogout = () => {
        currentUser = null;
        appPage.classList.add('hidden');
        loginPage.classList.remove('hidden');
    };

    /**
     * Initializes the main application view after a successful login.
     */
    const initializeApp = () => {
        userNameEl.textContent = currentUser.name;
        userRoleEl.textContent = currentUser.role;
        
        // Show verification tab only for users who are superiors to someone.
        const subordinates = users.filter(u => u.superiorId === currentUser.id);
        navVerification.style.display = subordinates.length > 0 ? 'flex' : 'none';
        
        navigateTo('dashboard');
    };
    
    /**
     * Handles navigation between the 'dashboard' and 'verification' pages.
     * @param {string} page The page to navigate to ('dashboard' or 'verification').
     */
    const navigateTo = (page) => {
        // Hide all content sections first
        dashboardContent.classList.add('hidden');
        verificationContent.classList.add('hidden');
        
        // Reset navigation link styles
        navDashboard.classList.remove('bg-gray-900');
        navVerification.classList.remove('bg-gray-900');

        if (page === 'dashboard') {
            dashboardContent.classList.remove('hidden');
            navDashboard.classList.add('bg-gray-900');
            renderDashboard();
        } else if (page === 'verification') {
            verificationContent.classList.remove('hidden');
            navVerification.classList.add('bg-gray-900');
            renderVerification();
        }
    };

    /**
     * Renders the current user's own logs on the dashboard.
     */
    const renderDashboard = () => {
        const userLogs = logs
            .filter(log => log.userId === currentUser.id)
            .sort((a, b) => new Date(b.date) - new Date(a.date));
        
        if (userLogs.length === 0) {
            logList.innerHTML = `<p class="text-center text-gray-500">Anda belum memiliki log harian.</p>`;
            return;
        }

        logList.innerHTML = userLogs.map(log => {
            const statusInfo = {
                'Pending': { color: 'bg-yellow-100 text-yellow-800', icon: 'fas fa-clock' },
                'Disetujui': { color: 'bg-green-100 text-green-800', icon: 'fas fa-check-circle' },
                'Ditolak': { color: 'bg-red-100 text-red-800', icon: 'fas fa-times-circle' },
            };

            return `
                <div class="border-b last:border-b-0 py-3">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-gray-800">${log.activity}</p>
                            <p class="text-xs text-gray-500 mt-1">${new Date(log.date).toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}</p>
                        </div>
                        <div class="flex items-center space-x-2">
                             <span class="text-xs font-medium px-2.5 py-0.5 rounded-full ${statusInfo[log.status].color}">
                                <i class="${statusInfo[log.status].icon} mr-1"></i>
                                ${log.status}
                            </span>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    };

    /**
     * Renders subordinates' logs for the superior to verify.
     */
    const renderVerification = () => {
        const subordinateIds = users.filter(u => u.superiorId === currentUser.id).map(u => u.id);
        const logsToVerify = logs
            .filter(log => subordinateIds.includes(log.userId))
            .sort((a, b) => new Date(b.date) - new Date(a.date));

        if (logsToVerify.length === 0) {
            verificationList.innerHTML = `<p class="text-center text-gray-500">Tidak ada log dari bawahan yang perlu diverifikasi.</p>`;
            return;
        }

        verificationList.innerHTML = logsToVerify.map(log => {
            const employee = users.find(u => u.id === log.userId);
            const isPending = log.status === 'Pending';
            return `
                <div class="border-b last:border-b-0 py-4">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="font-semibold">${employee.name} <span class="font-normal text-gray-600">(${employee.role})</span></p>
                            <p class="text-gray-800 mt-2">${log.activity}</p>
                            <p class="text-xs text-gray-500 mt-1">${new Date(log.date).toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}</p>
                        </div>
                        ${isPending ? `
                        <div class="flex space-x-2 flex-shrink-0">
                            <button data-log-id="${log.id}" class="approve-btn px-3 py-1 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700">
                                <i class="fas fa-check"></i> Setujui
                            </button>
                            <button data-log-id="${log.id}" class="reject-btn px-3 py-1 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700">
                                <i class="fas fa-times"></i> Tolak
                            </button>
                        </div>
                        ` : `
                        <div class="text-sm font-medium ${log.status === 'Disetujui' ? 'text-green-600' : 'text-red-600'}">
                            Telah ${log.status.toLowerCase()}
                        </div>
                        `}
                    </div>
                </div>
            `;
        }).join('');
        
        // Event listeners need to be re-attached every time the list is re-rendered.
        addVerificationListeners();
    };
    
    /**
     * Handles the creation of a new log entry.
     * @param {Event} e The form submission event.
     */
    const handleAddLog = (e) => {
        e.preventDefault();
        const activity = document.getElementById('log-activity').value;
        if (activity.trim()) {
            const newLog = {
                id: logs.length > 0 ? Math.max(...logs.map(l => l.id)) + 1 : 1, // Safer ID generation
                userId: currentUser.id,
                activity: activity,
                date: new Date().toISOString().split('T')[0], // Format: YYYY-MM-DD
                status: 'Pending'
            };
            logs.push(newLog);
            closeModal();
            renderDashboard();
        }
    };
    
    /**
     * Verifies a log by changing its status to 'Disetujui' or 'Ditolak'.
     * @param {number} logId The ID of the log to verify.
     * @param {string} action The action to take ('approve' or 'reject').
     */
    const verifyLog = (logId, action) => {
        const log = logs.find(l => l.id === logId);
        if (log) {
            log.status = action === 'approve' ? 'Disetujui' : 'Ditolak';
            renderVerification();
        }
    };
    
    /**
     * Attaches click event listeners to the approve/reject buttons.
     */
    const addVerificationListeners = () => {
        document.querySelectorAll('.approve-btn').forEach(btn => {
            btn.onclick = () => verifyLog(parseInt(btn.dataset.logId), 'approve');
        });
        document.querySelectorAll('.reject-btn').forEach(btn => {
            btn.onclick = () => verifyLog(parseInt(btn.dataset.logId), 'reject');
        });
    };

    // --- Modal Control Functions ---
    const openModal = () => {
        addLogForm.reset();
        addLogModal.classList.remove('hidden');
        addLogModal.classList.add('flex');
    };
    const closeModal = () => {
        addLogModal.classList.add('hidden');
        addLogModal.classList.remove('flex');
    };

    // --- INITIAL EVENT LISTENERS ---
    loginForm.addEventListener('submit', handleLogin);
    logoutBtn.addEventListener('click', handleLogout);
    
    navDashboard.addEventListener('click', (e) => { e.preventDefault(); navigateTo('dashboard'); });
    navVerification.addEventListener('click', (e) => { e.preventDefault(); navigateTo('verification'); });
    
    addLogBtn.addEventListener('click', openModal);
    cancelLogBtn.addEventListener('click', closeModal);
    addLogForm.addEventListener('submit', handleAddLog);
    
    // Add a global keydown listener to close the modal with the Escape key.
    window.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !addLogModal.classList.contains('hidden')) {
            closeModal();
        }
    });
});
