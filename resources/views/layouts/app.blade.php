<!DOCTYPE html>
<html lang="id" class="h-full bg-[#f8f9fb]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Enterprise EDMS - ISO 14001, 45001, 27001</title>
    
    <!-- Google Font: Manrope -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: {
                            DEFAULT: '#0b3558',
                            hover: '#082843',
                        },
                        signal: {
                            DEFAULT: '#006bff',
                            hover: '#0058d4',
                        },
                        slate: {
                            gray: '#476788',
                        },
                        mist: '#a6bbd1',
                        cloud: '#f8f9fb',
                        paper: '#ffffff',
                        pebble: '#f0f3f8',
                        hairline: '#d4e0ed',
                        cobalt: '#004eba',
                    },
                    fontFamily: {
                        sans: ['Manrope', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    },
                    borderRadius: {
                        'card': '24px',
                        'product': '16px',
                        'btn': '8px',
                        'input': '8px',
                    },
                    boxShadow: {
                        'calendly': 'rgba(71, 103, 136, 0.04) 0px 4px 5px 0px, rgba(71, 103, 136, 0.03) 0px 8px 15px 0px, rgba(71, 103, 136, 0.08) 0px 30px 50px 0px',
                        'card-sm': 'rgba(71, 103, 136, 0.04) 0px 4px 5px 0px, rgba(71, 103, 136, 0.03) 0px 4px 10px 0px, rgba(71, 103, 136, 0.05) 0px 10px 20px 0px',
                    }
                }
            }
        }
    </script>
    
    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- PDF.js CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
    </script>
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Manrope', sans-serif; color: #0b3558; }
    </style>
</head>
<body class="h-full font-sans antialiased bg-[#f8f9fb] text-[#0b3558]" x-data="globalEdmsApp()">

    <div class="min-h-full flex">

        <!-- Left Sidebar -->
        <aside class="w-64 bg-paper text-[#0b3558] flex flex-col fixed inset-y-0 z-40 border-r border-[#d4e0ed]">
            <!-- Sidebar Header & Brand -->
            <div class="h-20 px-6 flex items-center space-x-3 border-b border-[#d4e0ed]">
                <div class="bg-[#006bff] text-white font-bold text-base w-9 h-9 rounded-btn flex items-center justify-center shadow-sm shrink-0">
                    ED
                </div>
                <div class="truncate">
                    <h1 class="font-bold text-sm text-[#0b3558] tracking-tight truncate leading-tight">Document Control</h1>
                    <p class="text-[11px] text-[#476788] font-medium tracking-normal">ISO 14001 • 45001 • 27001</p>
                </div>
            </div>

            <!-- Navigation Links -->
            <div class="flex-1 py-6 px-4 space-y-6 overflow-y-auto">
                <div>
                    <p class="px-3 text-[11px] font-semibold text-[#476788] uppercase tracking-wider mb-3">Workspace</p>
                    <nav class="space-y-1.5">
                        <!-- Repository Link -->
                        <a href="{{ route('repository.index') }}" 
                           class="flex items-center space-x-3 px-3.5 py-2.5 rounded-btn text-xs font-semibold transition {{ request()->routeIs('repository.*') ? 'bg-[#006bff] text-white' : 'text-[#0b3558] hover:bg-[#f0f3f8]' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9.75h16.5m-16.5 0a2.25 2.25 0 01-2.25-2.25V6.75a2.25 2.25 0 012.25-2.25h4.875c.621 0 1.18.272 1.558.742l1.034 1.284a1.5 1.5 0 001.169.558h6.864a2.25 2.25 0 012.25 2.25v.75m-16.5 0v8.25A2.25 2.25 0 006 20.25h12a2.25 2.25 0 002.25-2.25V9.75" />
                            </svg>
                            <span>Perpustakaan Dokumen</span>
                        </a>

                        <!-- Controlled Copy Link -->
                        <a href="{{ route('print.index') }}" 
                           class="flex items-center space-x-3 px-3.5 py-2.5 rounded-btn text-xs font-semibold transition {{ request()->routeIs('print.*') ? 'bg-[#006bff] text-white' : 'text-[#0b3558] hover:bg-[#f0f3f8]' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231a1.125 1.125 0 01-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-19.126 0C1.008 7.441.24 8.375.24 9.456v6.294A2.25 2.25 0 002.49 18h1.092" />
                            </svg>
                            <span>Penggandaan Dokumen</span>
                        </a>

                        <!-- Lifecycle Link -->
                        <a href="{{ route('lifecycle.index') }}" 
                           class="flex items-center space-x-3 px-3.5 py-2.5 rounded-btn text-xs font-semibold transition {{ request()->routeIs('lifecycle.*') ? 'bg-[#006bff] text-white' : 'text-[#0b3558] hover:bg-[#f0f3f8]' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                            </svg>
                            <span>Pengendalian Dokumen</span>
                        </a>
                    </nav>
                </div>

                <!-- Approval Status Box -->
                <div class="bg-[#f0f3f8] p-3.5 rounded-product border border-[#d4e0ed]">
                    <div class="flex items-center justify-between text-xs font-semibold text-[#0b3558] mb-1">
                        <span>Antrean Task</span>
                        <span class="bg-[#004eba] text-white text-[11px] font-bold px-2 py-0.5 rounded-full" x-text="unreadCount">0</span>
                    </div>
                    <p class="text-[12px] text-[#476788]">Tugas approval sekuensial yang memerlukan tindakan Anda.</p>
                </div>
            </div>

            <!-- User Profile & Role Switcher Footer -->
            <div class="p-4 border-t border-[#d4e0ed] bg-paper relative" x-data="{ open: false }">
                <button @click="open = !open" class="w-full flex items-center justify-between p-2 rounded-btn bg-[#f0f3f8] hover:bg-[#e6f0ff] transition text-left border border-[#d4e0ed]">
                    <div class="flex items-center space-x-2.5 truncate">
                        <div class="w-8 h-8 rounded-btn bg-[#0b3558] text-white flex items-center justify-center font-bold text-xs shrink-0">
                            {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 2)) }}
                        </div>
                        <div class="truncate">
                            <p class="text-xs font-bold text-[#0b3558] truncate leading-none">{{ Auth::user()->name ?? 'Karyawan' }}</p>
                            <span class="text-[11px] font-semibold text-[#004eba] block mt-1">{{ Auth::user()->role ?? 'Guest' }}</span>
                        </div>
                    </div>
                    <svg class="w-4 h-4 text-[#476788] shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                    </svg>
                </button>

                <!-- Role Switcher Popover -->
                <div x-show="open" @click.away="open = false" x-cloak
                     class="absolute bottom-full left-4 right-4 mb-2 bg-paper border border-[#d4e0ed] rounded-product shadow-calendly overflow-hidden py-1 z-50 text-xs">
                    <div class="px-3.5 py-2 bg-[#f0f3f8] font-semibold text-[11px] text-[#476788] uppercase tracking-wider border-b border-[#d4e0ed]">
                        Switch Testing Role
                    </div>
                    <div class="max-h-48 overflow-y-auto divide-y divide-[#f0f3f8]">
                        @foreach(\App\Models\User::all() as $u)
                            <form action="{{ route('login.switch', $u->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full text-left px-3.5 py-2 hover:bg-[#f0f3f8] transition flex items-center justify-between text-[#0b3558]">
                                    <span class="truncate font-medium">{{ $u->name }}</span>
                                    <span class="text-[11px] px-2 py-0.5 rounded-full bg-[#f0f3f8] text-[#004eba] font-medium shrink-0 ml-1">{{ $u->role }}</span>
                                </button>
                            </form>
                        @endforeach
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="pl-64 flex-1 flex flex-col min-w-0">

            <!-- Top Navbar -->
            <header class="h-20 bg-paper border-b border-[#d4e0ed] sticky top-0 z-30 px-8 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-[#0b3558]">
                        @if(request()->routeIs('repository.*')) Perpustakaan Dokumen @endif
                        @if(request()->routeIs('print.*')) Penggandaan Dokumen @endif
                        @if(request()->routeIs('lifecycle.*')) Pengendalian Dokumen @endif
                    </h2>
                    <p class="text-xs text-[#476788] font-normal">Sistem Informasi Terdokumentasi ISO 14001, 45001, 27001</p>
                </div>

                <!-- Notifications Dropdown -->
                <div class="flex items-center space-x-3 relative" x-data="{ dropdownOpen: false }">
                    <button @click="dropdownOpen = !dropdownOpen; fetchNotifications()" 
                            class="relative p-2.5 rounded-btn bg-[#f0f3f8] hover:bg-[#e6f0ff] text-[#0b3558] border border-[#d4e0ed] transition flex items-center justify-center">
                        <svg class="w-5 h-5 text-[#0b3558]" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                        </svg>
                        <template x-if="unreadCount > 0">
                            <span class="absolute -top-1 -right-1 bg-[#006bff] text-white text-[10px] font-bold w-4 h-4 rounded-full flex items-center justify-center shadow"
                                  x-text="unreadCount"></span>
                        </template>
                    </button>

                    <!-- Notifications Dropdown Panel -->
                    <div x-show="dropdownOpen" @click.away="dropdownOpen = false" x-cloak
                         class="absolute right-0 top-14 w-96 bg-paper rounded-card shadow-calendly border border-[#d4e0ed] z-50 text-[#0b3558] overflow-hidden">
                        <div class="p-4 bg-[#0b3558] text-white font-semibold text-xs flex justify-between items-center">
                            <span>Tugas Approval Pending</span>
                            <span class="text-[11px] bg-[#006bff] px-2.5 py-0.5 rounded-full font-bold" x-text="notifications.length + ' Item'"></span>
                        </div>
                        <div class="max-h-80 overflow-y-auto divide-y divide-[#f0f3f8]">
                            <template x-if="notifications.length === 0">
                                <div class="p-6 text-center text-xs text-[#476788]">Tidak ada antrean tugas approval.</div>
                            </template>
                            <template x-for="item in notifications" :key="item.NotificationID">
                                <div class="p-4 hover:bg-[#f0f3f8] transition cursor-pointer" @click="openQuickAction(item); dropdownOpen = false">
                                    <div class="flex items-center justify-between text-xs font-bold text-[#006bff] mb-1">
                                        <span x-text="item.Title"></span>
                                        <span class="text-[10px] text-[#476788] font-normal" x-text="item.created_at"></span>
                                    </div>
                                    <p class="text-xs text-[#0b3558] font-normal" x-text="item.Message"></p>
                                    <div class="mt-2.5 text-right">
                                        <span class="inline-block bg-[#f0f3f8] text-[#004eba] text-[11px] px-2.5 py-1 rounded-full font-semibold border border-[#d4e0ed]">
                                            Quick Action &rarr;
                                        </span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Alert Banner -->
            <div class="px-8 mt-6">
                @if(session('success'))
                    <div class="bg-[#f0f3f8] border border-[#d4e0ed] text-[#0b3558] px-4 py-3 rounded-btn text-xs font-semibold flex items-center space-x-2 shadow-sm">
                        <svg class="w-4 h-4 text-[#006bff] shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif
                @if($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-btn text-xs shadow-sm">
                        <p class="font-bold mb-1">Periksa kembali data pengajuan:</p>
                        <ul class="list-disc pl-5 space-y-0.5">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            <!-- Page Body -->
            <main class="flex-1 px-8 py-6 overflow-y-auto">
                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="bg-paper border-t border-[#d4e0ed] py-4 px-8 text-center text-[#476788] text-xs font-normal">
                Enterprise EDMS &copy; 2026 PT Enterprise Corp • ISO 14001, ISO 45001, ISO 27001 Standard
            </footer>
        </div>
    </div>

    <!-- Quick Action Modal -->
    <div x-show="quickActionModal" x-cloak class="fixed inset-0 bg-[#0b3558]/40 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-paper rounded-card shadow-calendly max-w-xl w-full p-6 relative border border-[#d4e0ed]">
            <button @click="quickActionModal = false" class="absolute top-5 right-5 text-[#476788] hover:text-[#0b3558] font-bold text-xl">&times;</button>
            <h3 class="text-base font-bold text-[#0b3558] border-b border-[#d4e0ed] pb-3 mb-4" x-text="activeNotification?.Title"></h3>
            
            <div class="space-y-4 text-xs">
                <div class="bg-[#f0f3f8] p-4 rounded-product border border-[#d4e0ed] text-[#0b3558]">
                    <p class="font-medium" x-text="activeNotification?.Message"></p>
                </div>

                <template x-if="activeNotification?.request">
                    <div class="grid grid-cols-2 gap-3 text-xs bg-[#f8f9fb] p-4 rounded-product border border-[#d4e0ed]">
                        <div><span class="font-semibold text-[#476788]">Tipe Pengajuan:</span> <span class="font-bold text-[#006bff]" x-text="activeNotification.request.RequestType"></span></div>
                        <div><span class="font-semibold text-[#476788]">Nomor Dokumen:</span> <span class="font-bold text-[#0b3558]" x-text="activeNotification.request.DocNumber"></span></div>
                        <div class="col-span-2"><span class="font-semibold text-[#476788]">Judul Dokumen:</span> <span class="font-bold text-[#0b3558]" x-text="activeNotification.request.Title"></span></div>
                        <div><span class="font-semibold text-[#476788]">Departemen:</span> <span class="font-medium text-[#0b3558]" x-text="activeNotification.request.Department"></span></div>
                        <div class="col-span-2"><span class="font-semibold text-[#476788]">Alasan:</span> <span class="text-[#0b3558]" x-text="activeNotification.request.Reason"></span></div>
                    </div>
                </template>

                <div x-show="showRejectInput" class="mt-3">
                    <label class="block text-xs font-semibold text-red-600 mb-1">Catatan Penolakan (Wajib diisi):</label>
                    <textarea x-model="rejectNotes" class="w-full text-xs p-3 border border-red-200 rounded-btn focus:ring-2 focus:ring-red-500 focus:outline-none" rows="2" placeholder="Masukkan alasan penolakan..."></textarea>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3 pt-4 border-t border-[#d4e0ed]">
                <button @click="quickActionModal = false" class="px-4 py-2.5 text-xs font-semibold text-[#476788] hover:bg-[#f0f3f8] rounded-btn">Batal</button>
                <button @click="handleReject()" class="px-4 py-2.5 text-xs font-semibold bg-[#0b3558] hover:bg-navy-hover text-white rounded-btn shadow-sm">
                    Tolak Pengajuan
                </button>
                <button @click="handleApprove()" class="px-5 py-2.5 text-xs font-semibold bg-[#006bff] hover:bg-signal-hover text-white rounded-btn shadow-sm">
                    Setujui Pengajuan
                </button>
            </div>
        </div>
    </div>

    <!-- Alpine App State -->
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
