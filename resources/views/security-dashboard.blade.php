<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Security Dashboard - Real-time Monitoring</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        @keyframes pulse-ring {
            0% { transform: scale(0.95); opacity: 1; }
            100% { transform: scale(1.5); opacity: 0; }
        }
        .pulse-ring {
            animation: pulse-ring 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        @keyframes slide-in {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        .slide-in {
            animation: slide-in 0.3s ease-out;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 min-h-screen">
    
    <!-- Login Modal -->
    <div id="loginModal" class="fixed inset-0 bg-black bg-opacity-75 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="bg-gray-800 rounded-2xl shadow-2xl p-8 w-full max-w-md border border-gray-700">
            <div class="text-center mb-8">
                <div class="mx-auto w-16 h-16 bg-gradient-to-br from-red-500 to-purple-600 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                <h2 class="text-3xl font-bold text-white">Security Dashboard</h2>
                <p class="text-gray-400 mt-2">Admin Authentication Required</p>
            </div>
            
            <form id="loginForm" class="space-y-6">
                <div>
                    <label class="block text-gray-300 text-sm font-semibold mb-2">Username</label>
                    <input type="text" id="username" class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition" placeholder="admin" required>
                </div>
                
                <div>
                    <label class="block text-gray-300 text-sm font-semibold mb-2">Password</label>
                    <input type="password" id="password" class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition" placeholder="••••••••" required>
                </div>
                
                <div id="loginError" class="hidden bg-red-500 bg-opacity-10 border border-red-500 text-red-500 px-4 py-3 rounded-lg text-sm"></div>
                
                <button type="submit" class="w-full bg-gradient-to-r from-red-500 to-purple-600 text-white font-bold py-3 px-6 rounded-lg hover:from-red-600 hover:to-purple-700 transition transform hover:scale-105">
                    Access Dashboard
                </button>
            </form>
        </div>
    </div>

    <!-- Main Dashboard (Hidden until authenticated) -->
    <div id="dashboard" class="hidden">
        <!-- Header -->
        <header class="bg-gray-800 border-b border-gray-700 shadow-lg">
            <div class="max-w-7xl mx-auto px-6 py-4">
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-purple-600 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-white">Security Dashboard</h1>
                            <p class="text-gray-400 text-sm">Real-time Threat Monitoring</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center space-x-2">
                            <span class="relative flex h-3 w-3">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                            </span>
                            <span class="text-green-400 text-sm font-semibold">Live</span>
                        </div>
                        <button id="logoutBtn" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition font-semibold">
                            Logout
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <main class="max-w-7xl mx-auto px-6 py-8">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Attacks -->
                <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-2xl shadow-xl p-6 text-white">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-red-100 text-sm font-semibold uppercase">Total Attacks</p>
                            <p id="totalAttacks" class="text-4xl font-bold mt-2">0</p>
                        </div>
                        <div class="bg-red-400 bg-opacity-30 rounded-full p-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="flex items-center text-red-100 text-sm">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd"></path>
                        </svg>
                        Real-time
                    </div>
                </div>

                <!-- Blocked IPs -->
                <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl shadow-xl p-6 text-white">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-orange-100 text-sm font-semibold uppercase">Blocked IPs</p>
                            <p id="blockedCount" class="text-4xl font-bold mt-2">0</p>
                        </div>
                        <div class="bg-orange-400 bg-opacity-30 rounded-full p-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="flex items-center text-orange-100 text-sm">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                        </svg>
                        Auto-blocked
                    </div>
                </div>

                <!-- Suspicious Activity -->
                <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-2xl shadow-xl p-6 text-white">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-yellow-100 text-sm font-semibold uppercase">Suspicious</p>
                            <p id="suspiciousCount" class="text-4xl font-bold mt-2">0</p>
                        </div>
                        <div class="bg-yellow-400 bg-opacity-30 rounded-full p-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="flex items-center text-yellow-100 text-sm">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                        </svg>
                        Monitoring
                    </div>
                </div>

                <!-- System Status -->
                <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl shadow-xl p-6 text-white">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-green-100 text-sm font-semibold uppercase">System Status</p>
                            <p class="text-2xl font-bold mt-2">Healthy</p>
                        </div>
                        <div class="bg-green-400 bg-opacity-30 rounded-full p-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="flex items-center text-green-100 text-sm">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        All systems operational
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Real-time Activity Feed -->
                <div class="lg:col-span-2">
                    <div class="bg-gray-800 rounded-2xl shadow-xl border border-gray-700 overflow-hidden">
                        <div class="p-6 border-b border-gray-700 bg-gradient-to-r from-gray-800 to-gray-750">
                            <div class="flex items-center justify-between">
                                <h2 class="text-xl font-bold text-white flex items-center">
                                    <span class="relative flex h-3 w-3 mr-3">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                                    </span>
                                    Security Activity Feed
                                </h2>
                                <button id="clearFeed" class="px-3 py-1 bg-gray-700 hover:bg-gray-600 text-gray-300 rounded-lg text-sm transition">
                                    Clear
                                </button>
                            </div>
                        </div>
                        <div id="activityFeed" class="p-6 space-y-3 max-h-[600px] overflow-y-auto">
                            <div class="text-center text-gray-500 py-8">
                                <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                                <p>Waiting for security events...</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- System Information -->
                <div class="space-y-6">
                    <!-- Server Stats -->
                    <div class="bg-gray-800 rounded-2xl shadow-xl border border-gray-700 p-6">
                        <h3 class="text-lg font-bold text-white mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path>
</svg>
Server Information
</h3>
<div class="space-y-3">
<div class="flex justify-between items-center py-2 border-b border-gray-700">
<span class="text-gray-400 text-sm">PHP Version</span>
<span id="phpVersion" class="text-white font-semibold text-sm">-</span>
</div>
<div class="flex justify-between items-center py-2 border-b border-gray-700">
<span class="text-gray-400 text-sm">Laravel Version</span>
<span id="laravelVersion" class="text-white font-semibold text-sm">-</span>
</div>
<div class="flex justify-between items-center py-2 border-b border-gray-700">
<span class="text-gray-400 text-sm">Memory Usage</span>
<span id="memoryUsage" class="text-white font-semibold text-sm">-</span>
</div>
<div class="flex justify-between items-center py-2">
<span class="text-gray-400 text-sm">Memory Limit</span>
<span id="memoryLimit" class="text-white font-semibold text-sm">-</span>
</div>
</div>
</div>
                <!-- Blocked IPs List -->
                <div class="bg-gray-800 rounded-2xl shadow-xl border border-gray-700 p-6">
                    <h3 class="text-lg font-bold text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                        </svg>
                        Currently Blocked
                    </h3>
                    <div id="blockedList" class="space-y-2 max-h-64 overflow-y-auto">
                        <div class="text-center text-gray-500 py-4 text-sm">
                            No blocked IPs
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script type="module">
        import Echo from 'https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/+esm';
        import Pusher from 'https://cdn.jsdelivr.net/npm/pusher-js@8.4.0-rc2/+esm';

        window.Pusher = Pusher;

        // Stats tracking
        let stats = {
            totalAttacks: 0,
            blockedIps: new Set(),
            suspiciousIps: new Set(),
        };

        // Login functionality
        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            const errorDiv = document.getElementById('loginError');
            const submitBtn = e.target.querySelector('button[type="submit"]');
            
            submitBtn.disabled = true;
            submitBtn.textContent = 'Authenticating...';
            
            try {
                const response = await fetch('/security-dashboard/auth', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        // Ensure CSRF token is grabbed fresh from meta tag
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    // FIX: Ensure cookies are sent with request
                    credentials: 'same-origin', 
                    body: JSON.stringify({ username, password })
                });

                // Handle 419 specifically (CSRF mismatch)
                if (response.status === 419) {
                    throw new Error('Session expired. Please refresh the page.');
                }

                const data = await response.json();
                
                if (response.ok && data.success) {
                    errorDiv.classList.add('hidden');
                    document.getElementById('loginModal').classList.add('hidden');
                    document.getElementById('dashboard').classList.remove('hidden');
                    initializeDashboard();
                } else {
                    errorDiv.textContent = data.message || 'Invalid credentials. Please try again.';
                    errorDiv.classList.remove('hidden');
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Access Dashboard';
                }
            } catch (error) {
                console.error('Auth error:', error);
                errorDiv.textContent = error.message || 'Authentication failed. Please check console.';
                errorDiv.classList.remove('hidden');
                submitBtn.disabled = false;
                submitBtn.textContent = 'Access Dashboard';
            }
        });

        // Logout functionality
        document.getElementById('logoutBtn').addEventListener('click', async () => {
            try {
                await fetch('/security-dashboard/logout', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                location.reload();
            } catch (error) {
                console.error('Logout error:', error);
                location.reload();
            }
        });

        // Clear feed
        document.getElementById('clearFeed').addEventListener('click', () => {
            document.getElementById('activityFeed').innerHTML = `
                <div class="text-center text-gray-500 py-8">
                    <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    <p>Waiting for security events...</p>
                </div>
            `;
        });

        async function initializeDashboard() {
            console.log('🚀 Initializing dashboard...');
            
            // Load initial stats
            await loadSystemStats();
            
            // Initialize WebSocket connection
            initializeWebSocket();
            
            // Refresh stats every 30 seconds
            setInterval(loadSystemStats, 30000);
        }

        async function loadSystemStats() {
            try {
                const response = await fetch('/security-dashboard/stats', {
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                
                if (!response.ok) {
                    throw new Error('Failed to fetch stats');
                }
                
                const data = await response.json();
                
                console.log('📊 Stats loaded:', data);
                
                // Update blocked IPs
                data.blocked_ips.forEach(item => stats.blockedIps.add(item.ip));
                document.getElementById('blockedCount').textContent = stats.blockedIps.size;
                
                // Update suspicious IPs
                data.suspicious_ips.forEach(item => stats.suspiciousIps.add(item.ip));
                document.getElementById('suspiciousCount').textContent = stats.suspiciousIps.size;
                
                // Update system info
                document.getElementById('phpVersion').textContent = data.system_info.php_version;
                document.getElementById('laravelVersion').textContent = data.system_info.laravel_version;
                document.getElementById('memoryUsage').textContent = data.system_info.memory_usage;
                document.getElementById('memoryLimit').textContent = data.system_info.memory_limit;
                
                // Update blocked list
                updateBlockedList(data.blocked_ips);
                
            } catch (error) {
                console.error('❌ Failed to load stats:', error);
            }
        }

        function updateBlockedList(blockedIps) {
            const list = document.getElementById('blockedList');
            
            if (blockedIps.length === 0) {
                list.innerHTML = `
                    <div class="text-center text-gray-500 py-4 text-sm">
                        No blocked IPs
                    </div>
                `;
                return;
            }
            
            list.innerHTML = blockedIps.map(item => `
                <div class="bg-gray-700 rounded-lg p-3 border border-red-500 border-opacity-30">
                    <div class="flex items-center justify-between">
                        <span class="text-white font-mono text-sm">${item.ip}</span>
                        <span class="text-xs text-red-400">Blocked</span>
                    </div>
                    <div class="text-xs text-gray-400 mt-1">
                        Until: ${new Date(item.blocked_until).toLocaleString()}
                    </div>
                </div>
            `).join('');
        }

        function initializeWebSocket() {
            try {
                const echo = new Echo({
                    broadcaster: 'reverb',
                    key: '{{ env('REVERB_APP_KEY') }}',
                    wsHost: '{{ env('REVERB_HOST') }}',
                    wsPort: {{ env('REVERB_PORT', 8080) }},
                    wssPort: {{ env('REVERB_PORT', 8080) }},
                    forceTLS: false,
                    enabledTransports: ['ws', 'wss'],
                    disableStats: true,
                });

                echo.channel('security-monitor')
                    .listen('.SecurityEvent', (e) => {
                        console.log('🔔 Security event received:', e);
                        handleSecurityEvent(e);
                    });

                console.log('✅ WebSocket connected to security-monitor channel');
            } catch (error) {
                console.error('❌ WebSocket connection failed:', error);
            }
        }

        function handleSecurityEvent(event) {
            const { type, data } = event;
            
            stats.totalAttacks++;
            document.getElementById('totalAttacks').textContent = stats.totalAttacks;
            
            let icon, bgColor, borderColor, title, message;
            
            switch(type) {
                case 'ip_blocked':
                    icon = '🚫';
                    bgColor = 'bg-red-900 bg-opacity-30';
                    borderColor = 'border-red-500';
                    title = 'IP Blocked';
                    message = `${data.ip} blocked for ${data.reason}`;
                    stats.blockedIps.add(data.ip);
                    document.getElementById('blockedCount').textContent = stats.blockedIps.size;
                    break;
                    
                case 'blocked_attempt':
                    icon = '⛔';
                    bgColor = 'bg-orange-900 bg-opacity-30';
                    borderColor = 'border-orange-500';
                    title = 'Blocked Attempt';
                    message = `${data.ip} tried to access ${data.path}`;
                    break;
                    
                case 'suspicious_activity':
                    icon = '⚠️';
                    bgColor = 'bg-yellow-900 bg-opacity-30';
                    borderColor = 'border-yellow-500';
                    title = 'Suspicious Activity';
                    message = `${data.ip} - ${data.count} suspicious actions`;
                    stats.suspiciousIps.add(data.ip);
                    document.getElementById('suspiciousCount').textContent = stats.suspiciousIps.size;
                    break;
                    
                case 'failed_login':
                    icon = '🔐';
                    bgColor = 'bg-red-900 bg-opacity-30';
                    borderColor = 'border-red-500';
                    title = 'Failed Login';
                    message = `${data.email} from ${data.ip}`;
                    break;
                    
                case 'user_login':
                    icon = '✅';
                    bgColor = 'bg-green-900 bg-opacity-30';
                    borderColor = 'border-green-500';
                    title = 'Successful Login';
                    message = `${data.email} from ${data.ip}`;
                    break;
                    
                case 'user_registered':
                    icon = '👤';
                    bgColor = 'bg-blue-900 bg-opacity-30';
                    borderColor = 'border-blue-500';
                    title = 'New Registration';
                    message = `${data.email} from ${data.ip}`;
                    break;
                    
                default:
                    icon = 'ℹ️';
                    bgColor = 'bg-gray-700';
                    borderColor = 'border-gray-600';
                    title = type;
                    message = JSON.stringify(data);
            }
            
            addActivityItem(icon, bgColor, borderColor, title, message, event.timestamp);
        }

        function addActivityItem(icon, bgColor, borderColor, title, message, timestamp) {
            const feed = document.getElementById('activityFeed');
            
            // Remove placeholder if exists
            if (feed.querySelector('.text-center')) {
                feed.innerHTML = '';
            }
            
            const item = document.createElement('div');
            item.className = `slide-in ${bgColor} border ${borderColor} rounded-lg p-4`;
            item.innerHTML = `
                <div class="flex items-start space-x-3">
                    <div class="text-2xl">${icon}</div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between mb-1">
                            <h4 class="text-white font-semibold text-sm">${title}</h4>
                            <span class="text-gray-400 text-xs">${new Date(timestamp).toLocaleTimeString()}</span>
                        </div>
                        <p class="text-gray-300 text-sm break-words">${message}</p>
                    </div>
                </div>
            `;
            
            feed.insertBefore(item, feed.firstChild);
            
            // Keep only last 50 items
            while (feed.children.length > 50) {
                feed.removeChild(feed.lastChild);
            }
        }
    </script>
</body>
</html>