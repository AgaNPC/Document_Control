<!DOCTYPE html>
<html lang="id" class="h-full bg-[#f8f9fb]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Enterprise EDMS</title>
    <!-- Google Font: Manrope -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Manrope', sans-serif; color: #0b3558; }
        .shadow-calendly {
            box-shadow: 0px 4px 5px 0px rgba(71, 103, 136, 0.04), 0px 8px 15px 0px rgba(71, 103, 136, 0.03), 0px 30px 50px 0px rgba(71, 103, 136, 0.08);
        }
    </style>
</head>
<body class="bg-[#f8f9fb] min-h-screen flex items-center justify-center p-6 text-[#0b3558]">

    <div class="bg-white rounded-[24px] shadow-calendly max-w-xl w-full p-10 border border-[#d4e0ed]">
        <!-- Brand Header -->
        <div class="text-center space-y-2 mb-8">
            <div class="inline-block bg-[#006bff] text-white font-bold text-xl px-5 py-2.5 rounded-[8px] shadow-sm">
                EDMS
            </div>
            <h1 class="text-2xl font-bold text-[#0b3558] tracking-tight">Enterprise Document Control</h1>
            <p class="text-xs text-[#476788]">ISO 14001 • ISO 45001 • ISO 27001 Informasi Terdokumentasi</p>
        </div>

        <div class="bg-[#f0f3f8] border border-[#d4e0ed] text-[#0b3558] p-4 rounded-[16px] text-xs mb-6">
            <p class="font-bold mb-1">Pilih Akun Testing Role (Role-Based Access Control):</p>
            <p class="text-[#476788]">Pilih salah satu peran di bawah ini untuk mensimulasikan alur 3-layer approval sekuensial.</p>
        </div>

        <!-- Role Select Grid -->
        <div class="space-y-3">
            @foreach($users as $user)
                <form action="{{ route('login.switch', $user->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full text-left p-4 rounded-[16px] border border-[#d4e0ed] bg-white hover:bg-[#f0f3f8] hover:border-[#006bff] transition flex items-center justify-between group shadow-sm">
                        <div class="flex items-center space-x-3.5">
                            <div class="w-10 h-10 rounded-[8px] bg-[#0b3558] text-white flex items-center justify-center font-bold text-xs shrink-0">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </div>
                            <div>
                                <h3 class="font-bold text-sm text-[#0b3558] group-hover:text-[#006bff] transition">{{ $user->name }}</h3>
                                <p class="text-xs text-[#476788]">{{ $user->email }} • Dept: <span class="font-semibold text-[#0b3558]">{{ $user->department }}</span></p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold 
                                {{ $user->role == 'Department Head' ? 'bg-[#006bff] text-white' : '' }}
                                {{ $user->role == 'Section Head' ? 'bg-[#004eba] text-white' : '' }}
                                {{ $user->role == 'PIC' ? 'bg-[#0b3558] text-white' : '' }}
                                {{ $user->role == 'IT Admin' ? 'bg-[#476788] text-white' : '' }}
                                {{ $user->role == 'Karyawan' ? 'bg-[#f0f3f8] text-[#004eba] border border-[#d4e0ed]' : '' }}">
                                {{ $user->role }}
                            </span>
                        </div>
                    </button>
                </form>
            @endforeach
        </div>

        <div class="mt-8 text-center text-xs text-[#476788]">
            Enterprise Electronic Document Management System &copy; 2026
        </div>
    </div>

</body>
</html>
