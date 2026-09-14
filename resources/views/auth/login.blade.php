<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Enterprise EDMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-900 min-h-screen flex items-center justify-center p-4">

    <div class="bg-white rounded-2xl shadow-2xl max-w-xl w-full p-8 border border-slate-700">
        <!-- Logo & Title -->
        <div class="text-center space-y-2 mb-8">
            <div class="inline-block bg-blue-600 text-white font-black text-3xl px-4 py-2 rounded-xl shadow-lg">
                EDMS
            </div>
            <h1 class="text-2xl font-black text-slate-900">Enterprise Document System</h1>
            <p class="text-xs text-slate-500">ISO 14001 • ISO 45001 • ISO 27001 Informasi Terdokumentasi</p>
        </div>

        <div class="bg-blue-50 border border-blue-200 text-blue-900 p-4 rounded-xl text-xs mb-6">
            <p class="font-bold mb-1">📌 Pilih User & Role untuk Pengujian Sistem (Role-Based Access Control):</p>
            <p class="text-slate-600">Anda dapat langsung beralih di antara role pemohon dan approver 3-layer.</p>
        </div>

        <!-- Role Select Grid -->
        <div class="space-y-3">
            @foreach($users as $user)
                <form action="{{ route('login.switch', $user->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full text-left p-4 rounded-xl border border-slate-200 hover:border-blue-500 hover:bg-blue-50/50 transition flex items-center justify-between group">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-full bg-slate-900 text-white flex items-center justify-center font-bold text-sm">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </div>
                            <div>
                                <h3 class="font-bold text-sm text-slate-800 group-hover:text-blue-600">{{ $user->name }}</h3>
                                <p class="text-xs text-slate-500">{{ $user::class ? $user->email : '' }} • Dept: <span class="font-semibold text-slate-700">{{ $user->department }}</span></p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="px-3 py-1 rounded-full text-xs font-bold shadow-sm 
                                {{ $user->role == 'Department Head' ? 'bg-purple-600 text-white' : '' }}
                                {{ $user->role == 'Section Head' ? 'bg-indigo-600 text-white' : '' }}
                                {{ $user->role == 'PIC' ? 'bg-blue-600 text-white' : '' }}
                                {{ $user->role == 'IT Admin' ? 'bg-slate-700 text-white' : '' }}
                                {{ $user->role == 'Karyawan' ? 'bg-emerald-600 text-white' : '' }}">
                                {{ $user->role }}
                            </span>
                        </div>
                    </button>
                </form>
            @endforeach
        </div>

        <div class="mt-8 text-center text-xs text-slate-400">
            Enterprise Electronic Document Management System &copy; 2026
        </div>
    </div>

</body>
</html>
