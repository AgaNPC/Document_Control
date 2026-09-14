@extends('layouts.app')

@section('content')
<div x-data="{ showModal: false }" class="space-y-6">

    <!-- Description & Action Banner -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-paper p-6 rounded-card border border-[#d4e0ed] shadow-card-sm">
        <div>
            <h3 class="text-base font-bold text-[#0b3558] flex items-center gap-2">
                <svg class="w-5 h-5 text-[#006bff]" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231a1.125 1.125 0 01-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-19.126 0C1.008 7.441.24 8.375.24 9.456v6.294A2.25 2.25 0 002.49 18h1.092" />
                </svg>
                <span>Penggandaan Dokumen (Controlled Copy Management)</span>
            </h3>
            <p class="text-xs text-[#476788] mt-1 font-normal">Pengendalian izin cetak fisik terregistrasi sesuai audit ISO 27001 & ISO 14001/45001. Setiap hasil cetak memiliki stempel nomor kontrol.</p>
        </div>
        <button @click="showModal = true" class="bg-[#006bff] hover:bg-[#0058d4] text-white font-semibold text-xs px-5 py-3 rounded-btn shadow-sm transition flex items-center gap-2 shrink-0">
            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            <span>Form Permohonan Cetak Terkontrol</span>
        </button>
    </div>

    <!-- Audit Log Table -->
    <div class="bg-paper rounded-card border border-[#d4e0ed] shadow-card-sm overflow-hidden">
        <div class="p-4 bg-[#0b3558] text-white flex justify-between items-center">
            <h4 class="text-xs font-bold uppercase tracking-wider">Log Permohonan & Distribusi Salinan Fisik</h4>
            <span class="text-[11px] bg-[#006bff] text-white px-2.5 py-0.5 rounded-full font-bold">ISO 27001 Audit Trail</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#f0f3f8] text-[#0b3558] uppercase text-[11px] font-bold border-b border-[#d4e0ed]">
                    <tr>
                        <th class="p-4">Req ID</th>
                        <th class="p-4">Dokumen Target</th>
                        <th class="p-4">Jumlah Eksemplar</th>
                        <th class="p-4">Pemohon</th>
                        <th class="p-4">Status Approval</th>
                        <th class="p-4 text-center">Controlled Copy PDF</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#f0f3f8]">
                    @forelse($printRequests as $req)
                        <tr class="hover:bg-[#f8f9fb] transition">
                            <td class="p-4 font-mono font-bold text-[#476788]">#PRINT-{{ sprintf('%04d', $req->RequestID) }}</td>
                            <td class="p-4">
                                <div class="font-bold text-[#006bff] font-mono">{{ $req->DocNumber }}</div>
                                <div class="text-[#0b3558] font-bold">{{ $req->Title }}</div>
                            </td>
                            <td class="p-4 font-bold text-[#0b3558]">{{ $req->CopyCount }} Eksemplar</td>
                            <td class="p-4 text-[#0b3558] font-medium">{{ $req->requester->name ?? 'User' }}</td>
                            <td class="p-4">
                                <span class="px-3 py-1 rounded-full text-[11px] font-bold
                                    {{ $req->CurrentStatus == 'APPROVED' ? 'bg-[#006bff] text-white' : '' }}
                                    {{ str_starts_with($req->CurrentStatus, 'PENDING') ? 'bg-[#f0f3f8] text-[#004eba] border border-[#d4e0ed]' : '' }}
                                    {{ $req->CurrentStatus == 'REJECTED' ? 'bg-red-600 text-white' : '' }}">
                                    {{ $req->CurrentStatus }}
                                </span>
                            </td>
                            <td class="p-4 text-center">
                                @if($req->CurrentStatus === 'APPROVED')
                                    <a href="{{ route('print.download', $req->RequestID) }}"
                                       class="bg-[#0b3558] hover:bg-[#082843] text-white px-4 py-2 rounded-btn text-xs font-semibold transition shadow-sm inline-flex items-center gap-2">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                        </svg>
                                        <span>Download PDF</span>
                                    </a>
                                @else
                                    <span class="text-xs text-[#476788] font-medium">Menunggu Dept Head</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-[#476788] font-medium">Belum ada permohonan cetak terkontrol.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-[#d4e0ed] bg-[#f8f9fb]">
            {{ $printRequests->links() }}
        </div>
    </div>

    <!-- Print Request Modal -->
    <div x-show="showModal" x-cloak class="fixed inset-0 bg-[#0b3558]/40 backdrop-blur-sm z-50 flex items-center justify-center p-6">
        <div class="bg-paper rounded-card shadow-calendly max-w-lg w-full p-6 relative border border-[#d4e0ed]">
            <button @click="showModal = false" class="absolute top-5 right-5 text-[#476788] hover:text-[#0b3558] font-bold text-xl">&times;</button>
            
            <h3 class="text-base font-bold text-[#0b3558] border-b border-[#d4e0ed] pb-3 mb-4">
                Form Permohonan Cetak Salinan Terkontrol
            </h3>

            <form action="{{ route('print.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf

                <div>
                    <label class="block font-bold text-[#0b3558] mb-1.5">Pilih Dokumen Aktif:</label>
                    <select name="document_id" required class="w-full p-3 border border-[#d4e0ed] rounded-input bg-[#f8f9fb] text-[#0b3558] focus:bg-paper focus:outline-none">
                        <option value="">-- Pilih Dokumen --</option>
                        @foreach($activeDocuments as $d)
                            <option value="{{ $d->DocumentID }}">{{ $d->DocNumber }} - {{ $d->Title }} ({{ $d->Department }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-[#0b3558] mb-1.5">Jumlah Eksemplar:</label>
                    <input type="number" name="copy_count" value="1" min="1" max="100" required class="w-full p-3 border border-[#d4e0ed] rounded-input bg-[#f8f9fb] text-[#0b3558]">
                </div>

                <div>
                    <label class="block font-bold text-[#0b3558] mb-1.5">Alasan Cetak / Keperluan Audit:</label>
                    <textarea name="reason" required rows="3" placeholder="Jelaskan kebutuhan dokumen fisik terkontrol..." class="w-full p-3 border border-[#d4e0ed] rounded-input bg-[#f8f9fb] text-[#0b3558]"></textarea>
                </div>

                <div class="pt-4 border-t border-[#d4e0ed] flex justify-end space-x-3">
                    <button type="button" @click="showModal = false" class="px-4 py-2.5 font-semibold text-[#476788] hover:bg-[#f0f3f8] rounded-btn">Batal</button>
                    <button type="submit" class="px-5 py-2.5 font-semibold bg-[#006bff] hover:bg-[#0058d4] text-white rounded-btn shadow-sm">Kirim Permohonan</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
