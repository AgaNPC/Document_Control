@extends('layouts.app')

@section('content')
<div x-data="{ showModal: false }" class="space-y-6">

    <!-- Header Description Banner -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
        <div>
            <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                <span>🖨️</span> Penggandaan Dokumen (Controlled Copy & Print Management)
            </h3>
            <p class="text-xs text-slate-500 mt-0.5">Pengendalian cetak fisik terkontrol sesuai ISO 27001 & ISO 14001/45001. Setiap salinan memiliki stempel register terkontrol.</p>
        </div>
        <button @click="showModal = true" class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl shadow transition flex items-center gap-2 shrink-0">
            <span>+</span> Form Permohonan Cetak Terkontrol
        </button>
    </div>

    <!-- Print Log & Distribution Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-4 bg-slate-900 text-white flex justify-between items-center">
            <h4 class="text-xs font-bold uppercase tracking-wider">Log Permohonan & Distribusi Salinan Fisik Terkontrol</h4>
            <span class="text-[10px] bg-slate-800 text-slate-300 px-2 py-0.5 rounded font-mono">ISO 27001 Audit Trail</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-100 text-slate-700 uppercase text-[11px] font-bold border-b">
                    <tr>
                        <th class="p-3.5">Req ID</th>
                        <th class="p-3.5">Dokumen Target</th>
                        <th class="p-3.5">Jumlah Eksemplar</th>
                        <th class="p-3.5">Pemohon</th>
                        <th class="p-3.5">Lokasi Penempatan</th>
                        <th class="p-3.5">Status Approval</th>
                        <th class="p-3.5 text-center">Download Stamped PDF</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($printRequests as $req)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="p-3.5 font-mono font-bold text-slate-600">#PRINT-{{ sprintf('%04d', $req->RequestID) }}</td>
                            <td class="p-3.5">
                                <div class="font-bold text-blue-700 font-mono">{{ $req->DocNumber }}</div>
                                <div class="text-slate-800 font-medium">{{ $req->Title }}</div>
                            </td>
                            <td class="p-3.5 font-bold text-slate-800">{{ $req->CopyCount }} Eksemplar</td>
                            <td class="p-3.5 text-slate-700 font-semibold">{{ $req->requester->name ?? 'User' }}</td>
                            <td class="p-3.5 text-slate-600">{{ $req->PlacementLocation ?? '-' }}</td>
                            <td class="p-3.5">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold shadow-sm
                                    {{ $req->CurrentStatus == 'APPROVED' ? 'bg-emerald-600 text-white' : '' }}
                                    {{ str_starts_with($req->CurrentStatus, 'PENDING') ? 'bg-amber-500 text-white' : '' }}
                                    {{ $req->CurrentStatus == 'REJECTED' ? 'bg-red-600 text-white' : '' }}">
                                    {{ $req->CurrentStatus }}
                                </span>
                            </td>
                            <td class="p-3.5 text-center">
                                @if($req->CurrentStatus === 'APPROVED')
                                    <a href="{{ route('print.download', $req->RequestID) }}"
                                       class="bg-emerald-700 hover:bg-emerald-800 text-white px-3 py-1.5 rounded-xl text-xs font-bold transition shadow inline-flex items-center gap-1.5">
                                        <span>📥</span> Download Controlled Copy PDF
                                    </a>
                                @else
                                    <span class="text-[11px] text-slate-400 font-medium italic">Menunggu Approval Dept Head</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-500 font-medium">Belum ada riwayat permohonan cetak terkontrol.</td>
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
    <div x-show="showModal" x-cloak class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full p-6 relative border border-slate-200">
            <button @click="showModal = false" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 font-bold text-xl">&times;</button>
            
            <h3 class="text-base font-bold text-slate-900 border-b pb-2 mb-4 flex items-center gap-2">
                <span>📑</span> Form Permohonan Cetak Salinan Terkontrol
            </h3>

            <form action="{{ route('print.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Pilih Dokumen Aktif:</label>
                    <select name="document_id" required class="w-full p-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <option value="">-- Pilih Dokumen --</option>
                        @foreach($activeDocuments as $d)
                            <option value="{{ $d->DocumentID }}">{{ $d->DocNumber }} - {{ $d->Title }} ({{ $d->Department }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Jumlah Eksemplar:</label>
                    <input type="number" name="copy_count" value="1" min="1" max="100" required class="w-full p-2.5 border border-slate-300 rounded-xl">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Lokasi Penempatan Salinan Fisik (Ruang / Stasiun Kerja):</label>
                    <input type="text" name="placement_location" required placeholder="Contoh: Control Room Lantai 2..." class="w-full p-2.5 border border-slate-300 rounded-xl">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Alasan Cetak / Keperluan Audit:</label>
                    <textarea name="reason" required rows="3" placeholder="Jelaskan kebutuhan cetak fisik..." class="w-full p-2.5 border border-slate-300 rounded-xl"></textarea>
                </div>

                <div class="pt-3 border-t flex justify-end space-x-2">
                    <button type="button" @click="showModal = false" class="px-4 py-2 font-bold text-slate-600 hover:bg-slate-100 rounded-xl">Batal</button>
                    <button type="submit" class="px-4 py-2 font-bold bg-blue-600 hover:bg-blue-700 text-white rounded-xl shadow">Kirim Permohonan</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
