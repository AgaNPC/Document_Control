<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Enterprise EDMS - ISO 14001, 45001, 27001</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- PDF.js CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
    </script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-slate-100 font-sans text-slate-800 antialiased min-h-screen flex flex-col" x-data="notificationSystem()">

    <!-- Top Enterprise Navbar -->
    <header class="bg-slate-900 text-white shadow-lg sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between h-16">
            <!-- Brand & Logo -->
            <div class="flex items-center space-x-3">
                <div class="bg-blue-600 p-2 rounded-lg text-white font-bold text-xl shadow">
                    EDMS
                </div>
                <div>
                    <h1 class="font-bold text-lg leading-tight tracking-wide">Enterprise Document Control</h1>
                    <p class="text-xs text-slate-400">ISO 14001 • ISO 45001 • ISO 27001 Compliant</p>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="hidden md:flex space-x-6">
                <a href="{{ route('repository.index') }}" 
                   class="px-3 py-2 rounded-md text-sm font-medium transition {{ request()->routeIs('repository.*') ? 'bg-blue-600 text-white shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    📁 Perpustakaan Dokumen
                </a>
                <a href="{{ route('print.index') }}" 
                   class="px-3 py-2 rounded-md text-sm font-medium transition {{ request()->routeIs('print.*') ? 'bg-blue-600 text-white shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    🖨️ Penggandaan Dokumen
                </a>
                <a href="{{ route('lifecycle.index') }}" 
                   class="px-3 py-2 rounded-md text-sm font-medium transition {{ request()->routeIs('lifecycle.*') ? 'bg-blue-600 text-white shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    ⚙️ Pengendalian Dokumen
                </a>
            </nav>

            <!-- User Menu & Notification Bell -->
            <div class="flex items-center space-x-4">
                <!-- In-App Notification Bell Component -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open; fetchNotifications()" class="relative p-2 text-slate-300 hover:text-white focus:outline-none transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <!-- Red Badge Counter -->
                        <template x-if="unreadCount > 0">
                            <span class="absolute top-1 right-1 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full shadow"
                                  x-text="unreadCount"></span>
                        </template>
                    </button>

                    <!-- Notifications Dropdown -->
                    <div x-show="open" @click.away="open = false" x-cloak
                         class="absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-xl border border-slate-200 z-50 text-slate-800 overflow-hidden">
                        <div class="p-3 bg-slate-900 text-white font-bold text-sm flex justify-between items-center">
                            <span>Notifikasi Tasks & Approval</span>
                            <span class="text-xs text-blue-400" x-text="notifications.length + ' item'"></span>
                        </div>
                        <div class="max-h-80 overflow-y-auto divide-y divide-slate-100">
                            <template x-if="notifications.length === 0">
                                <div class="p-4 text-center text-sm text-slate-500">Tidak ada notifikasi tugas pending.</div>
                            </template>
                            <template x-for="item in notifications" :key="item.NotificationID">
                                <div class="p-3 hover:bg-slate-50 transition cursor-pointer" @click="openQuickAction(item)">
                                    <div class="flex items-center justify-between text-xs font-semibold text-blue-600 mb-1">
                                        <span x-text="item.Title"></span>
                                        <span class="text-slate-400" x-text="item.created_at"></span>
                                    </div>
                                    <p class="text-xs text-slate-700 font-medium" x-text="item.Message"></p>
                                    <div class="mt-2 text-right">
                                        <span class="inline-block bg-blue-100 text-blue-800 text-[10px] px-2 py-0.5 rounded font-bold">
                                            Action Needed &rarr;
                                        </span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- User Role Switcher -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center space-x-2 text-xs bg-slate-800 px-3 py-1.5 rounded-full border border-slate-700 hover:bg-slate-700 transition">
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                        <span class="font-bold text-white">{{ Auth::user()->name ?? 'Karyawan' }}</span>
                        <span class="text-slate-400">({{ Auth::user()->role ?? 'Guest' }})</span>
                    </button>
                    <div x-show="open" @click.away="open = false" x-cloak
                         class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-xl border border-slate-200 z-50 text-slate-800 py-1">
                        <div class="px-4 py-2 border-b border-slate-100 text-xs font-bold text-slate-500 uppercase">
                            Switch Testing Role
                        </div>
                        @foreach(\App\Models\User::all() as $u)
                            <form action="{{ route('login.switch', $u->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-1.5 text-xs hover:bg-slate-100 transition flex items-center justify-between">
                                    <span>{{ $u->name }}</span>
                                    <span class="font-semibold text-slate-500">[{{ $u->role }}]</span>
                                </button>
                            </form>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Success / Error Alert Banner -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-300 text-emerald-800 px-4 py-3 rounded-lg text-sm flex items-center space-x-2 shadow-sm">
                <span>✅</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if($errors->any())
            <div class="bg-red-50 border border-red-300 text-red-800 px-4 py-3 rounded-lg text-sm shadow-sm">
                <p class="font-bold">Gagal memproses:</p>
                <ul class="list-disc pl-5 mt-1 space-y-0.5">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <!-- Main Content Area -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-400 text-xs py-4 text-center border-t border-slate-800">
        Enterprise EDMS &copy; 2026 PT Enterprise Corp • Dokumen Terdokumentasi ISO 14001, 45001, 27001
    </footer>

    <!-- Notification Quick Action Modal -->
    <div x-show="quickActionModal" x-cloak class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full p-6 relative border border-slate-200">
            <button @click="quickActionModal = false" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 font-bold text-lg">&times;</button>
            <h3 class="text-lg font-bold text-slate-900 border-b pb-2 mb-4" x-text="activeNotification?.Title"></h3>
            
            <div class="space-y-3 text-sm">
                <div class="bg-blue-50 p-3 rounded-lg border border-blue-200 text-blue-900">
                    <p class="font-semibold" x-text="activeNotification?.Message"></p>
                </div>

                <template x-if="activeNotification?.request">
                    <div class="grid grid-cols-2 gap-2 text-xs bg-slate-50 p-3 rounded-lg border">
                        <div><span class="font-bold text-slate-500">Tipe Pengajuan:</span> <span class="font-bold text-blue-700" x-text="activeNotification.request.RequestType"></span></div>
                        <div><span class="font-bold text-slate-500">Nomor Dokumen:</span> <span class="font-semibold text-slate-800" x-text="activeNotification.request.DocNumber"></span></div>
                        <div><span class="font-bold text-slate-500">Judul Dokumen:</span> <span class="font-semibold text-slate-800" x-text="activeNotification.request.Title"></span></div>
                        <div><span class="font-bold text-slate-500">Departemen:</span> <span class="font-semibold text-slate-800" x-text="activeNotification.request.Department"></span></div>
                        <div class="col-span-2"><span class="font-bold text-slate-500">Alasan:</span> <span class="text-slate-700" x-text="activeNotification.request.Reason"></span></div>
                    </div>
                </template>

                <!-- Rejection Notes Input -->
                <div x-show="showRejectInput" class="mt-3">
                    <label class="block text-xs font-bold text-red-700 mb-1">Catatan Penolakan (Wajib diisi):</label>
                    <textarea x-model="rejectNotes" class="w-full text-xs p-2 border border-red-300 rounded focus:ring-2 focus:ring-red-500 focus:outline-none" rows="2" placeholder="Masukkan alasan penolakan..."></textarea>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-6 flex justify-end space-x-3 pt-3 border-t">
                <button @click="quickActionModal = false" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-lg">Batal</button>
                <button @click="handleReject()" class="px-4 py-2 text-xs font-bold bg-red-600 hover:bg-red-700 text-white rounded-lg shadow">
                    [ Tolak ]
                </button>
                <button @click="handleApprove()" class="px-4 py-2 text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg shadow">
                    [ Setujui ]
                </button>
            </div>
        </div>
    </div>

    <!-- Alpine Notification Logic -->
    <script>
        function notificationSystem() {
            return {
                unreadCount: 0,
                notifications: [],
                quickActionModal: false,
                activeNotification: null,
                showRejectInput: false,
                rejectNotes: '',

                init() {
                    this.pollUnreadCount();
                    setInterval(() => this.pollUnreadCount(), 5000);
                },

                pollUnreadCount() {
                    fetch('/api/notifications/unread-count')
                        .then(res => res.json())
                        .then(data => {
                            this.unreadCount = data.unread_count;
                        })
                        .catch(err => console.error(err));
                },

                fetchNotifications() {
                    fetch('/api/notifications')
                        .then(res => res.json())
                        .then(data => {
                            this.notifications = data.notifications;
                        });
                },

                openQuickAction(item) {
                    this.activeNotification = item;
                    this.showRejectInput = false;
                    this.rejectNotes = '';
                    this.quickActionModal = true;
                },

                handleApprove() {
                    if (!this.activeNotification) return;
                    fetch(`/api/notifications/${this.activeNotification.NotificationID}/approve`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ notes: 'Disetujui' })
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.quickActionModal = false;
                        this.pollUnreadCount();
                        this.fetchNotifications();
                        window.location.reload();
                    });
                },

                handleReject() {
                    if (!this.showRejectInput) {
                        this.showRejectInput = true;
                        return;
                    }

                    if (!this.rejectNotes.trim()) {
                        alert('Catatan penolakan wajib diisi!');
                        return;
                    }

                    fetch(`/api/notifications/${this.activeNotification.NotificationID}/reject`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ notes: this.rejectNotes })
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.quickActionModal = false;
                        this.pollUnreadCount();
                        this.fetchNotifications();
                        window.location.reload();
                    });
                }
            }
        }
    </script>

</body>
</html>
