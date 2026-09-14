@extends('layouts.app')

@section('content')
<div x-data="{ showModal: false }" class="space-y-6">

    <!-- Header Title -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white p-6 rounded-xl shadow-sm border border-slate-200">
        <div>
            <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                <span>🖨️</span> Penggandaan Dokumen (Controlled Copy & Print Management)
            </h2>
            <p class="text-xs text-slate-500 mt-1">Sistem kontrol cetak fisik terkontrol sesuai audit ISO 27001 & ISO 14001/45001. Setiap cetakan fisik wajib memiliki nomor register terkontrol.</p>
        </div>
        <button @click="showModal = true" class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-4 py-2.5 rounded-lg shadow transition flex items-center gap-2">
            <span>+</span> Form Permohonan Cetak Terkontrol
        </button>
    </div>

    <!-- Print Log & Distribution Audit Table -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-4 bg-slate-900 text-white flex justify-between items-center">
            <h3 class="text-xs font-bold uppercase tracking-wider">Log Permohonan & Distribusi Salinan Fisik</h3>
            <span class="text-xs text-slate-400">ISO 27001 Clause 7.5 Audit Log</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-100 text-slate-700 uppercase text-[11px] font-bold border-b">
                    <tr>
                        <th class="p-3">Req ID</th>
                        <th class="p-3">Dokumen</th>
                        <th class="p-3">Jumlah Eksemplar</th>
                        <th class="p-3">Pemohon</th>
                        <th class="p-3">Lokasi Penempatan</th>
                        <th class="p-3">Status Approval</th>
                        <th class="p-3 text-center">Salinan Terkontrol</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($printRequests as $req)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="p-3 font-mono font-bold text-slate-600">#PRINT-{{ sprintf('%04d', $req->RequestID) }}</td>
                            <td class="p-3">
                                <div class="font-bold text-blue-700 font-mono">{{ $req->DocNumber }}</div>
                                <div class="text-slate-800 font-medium">{{ $req->Title }}</div>
                            </td>
                            <td class="p-3 font-bold text-slate-800">{{ $req->CopyCount }} Eksemplar</td>
                            <td class="p-3 text-slate-700 font-semibold">{{ $req->requester->name ?? 'User' }}</td>
                            <td class="p-3 text-slate-600">{{ $req->PlacementLocation ?? '-' }}</td>
                            <td class="p-3">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold shadow-sm
                                    {{ $req->CurrentStatus == 'APPROVED' ? 'bg-emerald-600 text-white' : '' }}
                                    {{ str_starts_with($req->CurrentStatus, 'PENDING') ? 'bg-amber-500 text-white' : '' }}
                                    {{ $req->CurrentStatus == 'REJECTED' ? 'bg-red-600 text-white' : '' }}">
                                    {{ $req->CurrentStatus }}
                                </span>
                            </td>
                            <td class="p-3 text-center">
                                @if($req->CurrentStatus === 'APPROVED')
                                    <a href="{{ route('print.download', $req->RequestID) }}"
                                       class="bg-emerald-700 hover:bg-emerald-800 text-white px-3 py-1.5 rounded-lg text-xs font-bold transition shadow inline-flex items-center gap-1">
                                        <span>📥</span> Download Controlled Copy PDF
                                    </a>
                                @else
                                    <span class="text-[11px] text-slate-400 font-medium italic">Menunggu Dept Head</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-6 text-center text-slate-500 font-medium">Belum ada riwayat permohonan cetak terkontrol.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-t bg-slate-50">
            {{ $printRequests->links() }}
        </div>
    </div>

    <!-- Modal Form Permohonan Cetak -->
    <div x-show="showModal" x-cloak class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full p-6 relative border border-slate-200">
            <button @click="showModal = false" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 font-bold text-lg">&times;</button>
            
            <h3 class="text-base font-bold text-slate-900 border-b pb-2 mb-4 flex items-center gap-2">
                <span>📑</span> Form Permohonan Cetak Salinan Terkontrol
            </h3>

            <form action="{{ route('print.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Pilih Dokumen Aktif:</label>
                    <select name="document_id" required class="w-full p-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <option value="">-- Pilih Dokumen --</option>
                        @foreach($activeDocuments as $d)
                            <option value="{{ $d->DocumentID }}">{{ $d->DocNumber }} - {{ $d->Title }} ({{ $d->Department }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Jumlah Eksemplar:</label>
                    <input type="number" name="copy_count" value="1" min="1" max="100" required class="w-full p-2 border border-slate-300 rounded-lg">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Lokasi Penempatan Salinan Fisik (Ruang / Stasiun Kerja):</label>
                    <input type="text" name="placement_location" required placeholder="Contoh: Ruang Control Room Lantai 2..." class="w-full p-2 border border-slate-300 rounded-lg">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Alasan Cetak / Keperluan Audit:</label>
                    <textarea name="reason" required rows="3" placeholder="Jelaskan alasan kebutuhan dokumen fisik terkontrol..." class="w-full p-2 border border-slate-300 rounded-lg"></textarea>
                </div>

                <div class="pt-3 border-t flex justify-end space-x-2">
                    <button type="button" @click="showModal = false" class="px-4 py-2 font-bold text-slate-600 hover:bg-slate-100 rounded-lg">Batal</button>
                    <button type="submit" class="px-4 py-2 font-bold bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow">Kirim Permohonan</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
