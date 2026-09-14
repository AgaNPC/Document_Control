<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-100">
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
<body class="h-full font-sans text-slate-800 antialiased bg-slate-100" x-data="globalEdmsApp()">

    <div class="min-h-full flex">

        <!-- Left Sidebar -->
        <aside class="w-64 bg-slate-900 text-slate-300 flex flex-col fixed inset-y-0 z-40 border-r border-slate-800 shadow-xl">
            <!-- Sidebar Header & Logo -->
            <div class="h-16 px-4 flex items-center space-x-3 border-b border-slate-800 bg-slate-950">
                <div class="bg-blue-600 text-white font-black text-lg p-2 rounded-lg shadow-md flex items-center justify-center w-9 h-9">
                    E
                </div>
                <div class="truncate">
                    <h1 class="font-black text-sm text-white leading-tight tracking-wide truncate">Enterprise EDMS</h1>
                    <p class="text-[10px] text-blue-400 font-semibold tracking-wider">ISO 14001 • 45001 • 27001</p>
                </div>
            </div>

            <!-- Navigation Links Section -->
            <div class="flex-1 py-4 px-3 space-y-6 overflow-y-auto">
                <div>
                    <p class="px-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2">Modul Utama</p>
                    <nav class="space-y-1">
                        <!-- Perpustakaan Dokumen -->
                        <a href="{{ route('repository.index') }}" 
                           class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-xs font-bold transition group {{ request()->routeIs('repository.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/50' : 'hover:bg-slate-800 text-slate-400 hover:text-white' }}">
                            <span class="text-base">📁</span>
                            <span class="flex-1">Perpustakaan Dokumen</span>
                        </a>

                        <!-- Penggandaan Dokumen -->
                        <a href="{{ route('print.index') }}" 
                           class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-xs font-bold transition group {{ request()->routeIs('print.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/50' : 'hover:bg-slate-800 text-slate-400 hover:text-white' }}">
                            <span class="text-base">🖨️</span>
                            <span class="flex-1">Penggandaan Dokumen</span>
                        </a>

                        <!-- Pengendalian Dokumen -->
                        <a href="{{ route('lifecycle.index') }}" 
                           class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-xs font-bold transition group {{ request()->routeIs('lifecycle.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/50' : 'hover:bg-slate-800 text-slate-400 hover:text-white' }}">
                            <span class="text-base">⚙️</span>
                            <span class="flex-1">Pengendalian Dokumen</span>
                        </a>
                    </nav>
                </div>

                <!-- Workflow Notification Status -->
                <div class="bg-slate-800/60 p-3 rounded-xl border border-slate-700/50">
                    <div class="flex items-center justify-between text-[11px] font-bold text-slate-300 mb-1">
                        <span>Approval Tasks</span>
                        <span class="bg-red-600 text-white text-[10px] px-1.5 py-0.2 rounded-full" x-text="unreadCount">0</span>
                    </div>
                    <p class="text-[10px] text-slate-400">Notifikasi tugas approval sekuensial milik akun Anda.</p>
                </div>
            </div>

            <!-- Sidebar User Profile & Role Switcher Footer -->
            <div class="p-3 border-t border-slate-800 bg-slate-950/80 relative" x-data="{ open: false }">
                <button @click="open = !open" class="w-full flex items-center justify-between p-2 rounded-xl bg-slate-800/80 hover:bg-slate-800 transition text-left">
                    <div class="flex items-center space-x-2.5 truncate">
                        <div class="w-8 h-8 rounded-lg bg-blue-600 text-white flex items-center justify-center font-bold text-xs shrink-0 shadow">
                            {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 2)) }}
                        </div>
                        <div class="truncate">
                            <p class="text-xs font-bold text-white truncate leading-none">{{ Auth::user()->name ?? 'Karyawan' }}</p>
                            <span class="text-[10px] font-semibold text-emerald-400 block mt-0.5">{{ Auth::user()->role ?? 'Guest' }}</span>
                        </div>
                    </div>
                    <span class="text-xs text-slate-400">&uarr;</span>
                </button>

                <!-- Role Switcher Dropdown Popover -->
                <div x-show="open" @click.away="open = false" x-cloak
                     class="absolute bottom-full left-3 right-3 mb-2 bg-slate-900 border border-slate-700 rounded-xl shadow-2xl overflow-hidden py-1 z-50 text-xs">
                    <div class="px-3 py-1.5 bg-slate-950 font-bold text-[10px] text-slate-400 uppercase tracking-wider border-b border-slate-800">
                        Switch Testing User / Role
                    </div>
                    <div class="max-h-48 overflow-y-auto divide-y divide-slate-800">
                        @foreach(\App\Models\User::all() as $u)
                            <form action="{{ route('login.switch', $u->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full text-left px-3 py-2 hover:bg-slate-800 transition flex items-center justify-between text-slate-300 hover:text-white">
                                    <span class="truncate font-semibold">{{ $u->name }}</span>
                                    <span class="text-[10px] px-1.5 py-0.5 rounded bg-slate-800 font-bold text-slate-400 shrink-0 ml-1">{{ $u->role }}</span>
                                </button>
                            </form>
                        @endforeach
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content Wrapper (Offset for Left Sidebar) -->
        <div class="pl-64 flex-1 flex flex-col min-w-0">

            <!-- Top Header Bar -->
            <header class="h-16 bg-white border-b border-slate-200 sticky top-0 z-30 px-6 flex items-center justify-between shadow-sm">
                <div>
                    <h2 class="text-base font-black text-slate-900">
                        @if(request()->routeIs('repository.*')) Perpustakaan Dokumen (Document Repository) @endif
                        @if(request()->routeIs('print.*')) Penggandaan Dokumen (Controlled Copy & Print) @endif
                        @if(request()->routeIs('lifecycle.*')) Pengendalian Dokumen (Document Lifecycle Control) @endif
                    </h2>
                    <p class="text-[11px] text-slate-500 font-medium">Sistem Informasi Terdokumentasi Terintegrasi Perusahaan</p>
                </div>

                <!-- Top Right Actions: Notification Bell -->
                <div class="flex items-center space-x-3 relative" x-data="{ dropdownOpen: false }">
                    <!-- Bell Button -->
                    <button @click="dropdownOpen = !dropdownOpen; fetchNotifications()" 
                            class="relative p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <template x-if="unreadCount > 0">
                            <span class="absolute -top-1 -right-1 bg-red-600 text-white text-[10px] font-black w-4 h-4 rounded-full flex items-center justify-center shadow"
                                  x-text="unreadCount"></span>
                        </template>
                    </button>

                    <!-- Notifications Dropdown Card List -->
                    <div x-show="dropdownOpen" @click.away="dropdownOpen = false" x-cloak
                         class="absolute right-0 top-12 w-96 bg-white rounded-2xl shadow-2xl border border-slate-200 z-50 text-slate-800 overflow-hidden">
                        <div class="p-3.5 bg-slate-900 text-white font-bold text-xs flex justify-between items-center">
                            <span>Notifikasi Tugas Approval</span>
                            <span class="text-[10px] bg-blue-600 px-2 py-0.5 rounded-full" x-text="notifications.length + ' Pending'"></span>
                        </div>
                        <div class="max-h-80 overflow-y-auto divide-y divide-slate-100">
                            <template x-if="notifications.length === 0">
                                <div class="p-6 text-center text-xs text-slate-500 font-medium">Tidak ada tugas approval yang pending.</div>
                            </template>
                            <template x-for="item in notifications" :key="item.NotificationID">
                                <div class="p-3.5 hover:bg-slate-50 transition cursor-pointer" @click="openQuickAction(item); dropdownOpen = false">
                                    <div class="flex items-center justify-between text-xs font-bold text-blue-600 mb-1">
                                        <span x-text="item.Title"></span>
                                        <span class="text-[10px] text-slate-400 font-normal" x-text="item.created_at"></span>
                                    </div>
                                    <p class="text-xs text-slate-700 font-medium" x-text="item.Message"></p>
                                    <div class="mt-2 text-right">
                                        <span class="inline-block bg-blue-50 text-blue-700 text-[10px] px-2 py-0.5 rounded-md font-bold border border-blue-200">
                                            Buka Quick Action &rarr;
                                        </span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Alert Notification Banner -->
            <div class="px-6 mt-4">
                @if(session('success'))
                    <div class="bg-emerald-50 border border-emerald-300 text-emerald-800 px-4 py-3 rounded-xl text-xs font-semibold flex items-center space-x-2 shadow-sm">
                        <span class="text-base">✅</span>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif
                @if($errors->any())
                    <div class="bg-red-50 border border-red-300 text-red-800 px-4 py-3 rounded-xl text-xs shadow-sm">
                        <p class="font-bold mb-1">Terjadi kesalahan input:</p>
                        <ul class="list-disc pl-5 space-y-0.5">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            <!-- Page Main Content Area -->
            <main class="flex-1 px-6 py-6 overflow-y-auto">
                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t border-slate-200 py-3 px-6 text-center text-slate-500 text-[11px] font-medium">
                Enterprise EDMS &copy; 2026 PT Enterprise Corp • System Standard ISO 14001, 45001, 27001
            </footer>
        </div>
    </div>

    <!-- Global Quick Action Approval Modal -->
    <div x-show="quickActionModal" x-cloak class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-xl w-full p-6 relative border border-slate-200">
            <button @click="quickActionModal = false" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 font-bold text-xl">&times;</button>
            <h3 class="text-base font-bold text-slate-900 border-b pb-2 mb-4" x-text="activeNotification?.Title"></h3>
            
            <div class="space-y-3 text-xs">
                <div class="bg-blue-50 p-3.5 rounded-xl border border-blue-200 text-blue-900">
                    <p class="font-semibold" x-text="activeNotification?.Message"></p>
                </div>

                <template x-if="activeNotification?.request">
                    <div class="grid grid-cols-2 gap-2 text-[11px] bg-slate-50 p-3 rounded-xl border border-slate-200">
                        <div><span class="font-bold text-slate-500">Tipe Pengajuan:</span> <span class="font-bold text-blue-700" x-text="activeNotification.request.RequestType"></span></div>
                        <div><span class="font-bold text-slate-500">Nomor Dokumen:</span> <span class="font-semibold text-slate-800" x-text="activeNotification.request.DocNumber"></span></div>
                        <div class="col-span-2"><span class="font-bold text-slate-500">Judul Dokumen:</span> <span class="font-semibold text-slate-800" x-text="activeNotification.request.Title"></span></div>
                        <div><span class="font-bold text-slate-500">Departemen:</span> <span class="font-semibold text-slate-800" x-text="activeNotification.request.Department"></span></div>
                        <div class="col-span-2"><span class="font-bold text-slate-500">Alasan:</span> <span class="text-slate-700" x-text="activeNotification.request.Reason"></span></div>
                    </div>
                </template>

                <!-- Mandatory Rejection Notes Field -->
                <div x-show="showRejectInput" class="mt-3">
                    <label class="block text-xs font-bold text-red-700 mb-1">Catatan Penolakan (Wajib diisi):</label>
                    <textarea x-model="rejectNotes" class="w-full text-xs p-2.5 border border-red-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:outline-none" rows="2" placeholder="Masukkan alasan penolakan..."></textarea>
                </div>
            </div>

            <!-- Modal Action Buttons -->
            <div class="mt-6 flex justify-end space-x-3 pt-3 border-t">
                <button @click="quickActionModal = false" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl">Batal</button>
                <button @click="handleReject()" class="px-4 py-2 text-xs font-bold bg-red-600 hover:bg-red-700 text-white rounded-xl shadow">
                    [ Tolak ]
                </button>
                <button @click="handleApprove()" class="px-4 py-2 text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl shadow">
                    [ Setujui ]
                </button>
            </div>
        </div>
    </div>

    <!-- Alpine Global State Function -->
    <script>
        function globalEdmsApp() {
            return {
                unreadCount: 0,
                notifications: [],
                quickActionModal: false,
                activeNotification: null,
                showRejectInput: false,
                rejectNotes: '',

                init() {
                    this.pollUnreadCount();
                    setInterval(() => this.pollUnreadCount(), 4000);
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
