@extends('layouts.app')

@section('content')
<div x-data="{ activeTab: 'registration', showLogModal: false, activeLogs: [] }" class="space-y-6">

    <!-- Header Title -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
        <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2">
            <span>⚙️</span> Pengendalian Dokumen (Document Lifecycle Control)
        </h2>
        <p class="text-xs text-slate-500 mt-1">Alur sekuensial 3-Layer Approval (PIC Layer 1 &rarr; Section Head Layer 2 &rarr; Department Head Layer 3). Persetujuan Dept Head mengeksekusi sistem secara otomatis.</p>
    </div>

    <!-- 3 Sub-module Navigation Tabs -->
    <div class="flex space-x-2 border-b border-slate-300">
        <button @click="activeTab = 'registration'" 
                :class="activeTab === 'registration' ? 'border-blue-600 text-blue-600 bg-white font-bold shadow-sm' : 'border-transparent text-slate-600 hover:text-slate-900 bg-slate-100'"
                class="px-4 py-2.5 rounded-t-lg border-b-2 text-xs transition flex items-center gap-2">
            <span>1️⃣</span> Pendaftaran Dokumen Baru
        </button>
        <button @click="activeTab = 'revision'" 
                :class="activeTab === 'revision' ? 'border-blue-600 text-blue-600 bg-white font-bold shadow-sm' : 'border-transparent text-slate-600 hover:text-slate-900 bg-slate-100'"
                class="px-4 py-2.5 rounded-t-lg border-b-2 text-xs transition flex items-center gap-2">
            <span>2️⃣</span> Revisi Dokumen Eksisting
        </button>
        <button @click="activeTab = 'obsolete'" 
                :class="activeTab === 'obsolete' ? 'border-blue-600 text-blue-600 bg-white font-bold shadow-sm' : 'border-transparent text-slate-600 hover:text-slate-900 bg-slate-100'"
                class="px-4 py-2.5 rounded-t-lg border-b-2 text-xs transition flex items-center gap-2">
            <span>3️⃣</span> Penarikan Dokumen (Obsolete)
        </button>
    </div>

    <!-- Sub-module 1: Pendaftaran Dokumen Baru -->
    <div x-show="activeTab === 'registration'" x-cloak class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
        <h3 class="text-sm font-bold text-slate-900 border-b pb-2 mb-4">Pengajuan Pendaftaran Dokumen Baru (Draft Upload &rarr; Staging)</h3>
        <form action="{{ route('lifecycle.registration') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
            @csrf
            <div>
                <label class="block font-bold text-slate-700 mb-1">Nomor Dokumen Standard:</label>
                <input type="text" name="doc_number" required placeholder="SOP-K3-2026-001..." class="w-full p-2 border border-slate-300 rounded-lg">
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1">Judul Dokumen:</label>
                <input type="text" name="title" required placeholder="SOP Keselamatan Kerja Listrik Tegangan Tinggi..." class="w-full p-2 border border-slate-300 rounded-lg">
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1">Departemen Pemilik:</label>
                <select name="department" required class="w-full p-2 border border-slate-300 rounded-lg">
                    <option value="K3LH">K3LH (Safety & Environment)</option>
                    <option value="IT System">IT System & Security</option>
                    <option value="Quality Assurance">Quality Assurance (Mutu)</option>
                    <option value="Maintenance">Maintenance & Engineering</option>
                </select>
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1">Kategori Dokumen:</label>
                <select name="category" required class="w-full p-2 border border-slate-300 rounded-lg">
                    <option value="K3">K3 (Keselamatan & Kesehatan Kerja)</option>
                    <option value="Lingkungan">Lingkungan (ISO 14001)</option>
                    <option value="IT/Keamanan">IT / Keamanan Informasi (ISO 27001)</option>
                    <option value="Mutu">Mutu & Operasional</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block font-bold text-slate-700 mb-1">Upload File Draft PDF Baru:</label>
                <input type="file" name="document_file" accept="application/pdf" required class="w-full p-2 border border-slate-300 rounded-lg bg-slate-50">
            </div>
            <div class="md:col-span-2">
                <label class="block font-bold text-slate-700 mb-1">Alasan Penerbitan Dokumen Baru:</label>
                <textarea name="reason" required rows="3" placeholder="Jelaskan latar belakang penerbitan dokumen..." class="w-full p-2 border border-slate-300 rounded-lg"></textarea>
            </div>
            <div class="md:col-span-2 text-right">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-2.5 rounded-lg shadow transition">
                    Kirim Pengajuan Pendaftaran (L1 PIC)
                </button>
            </div>
        </form>
    </div>

    <!-- Sub-module 2: Revisi Dokumen Eksisting -->
    <div x-show="activeTab === 'revision'" x-cloak class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
        <h3 class="text-sm font-bold text-slate-900 border-b pb-2 mb-4">Pengajuan Revisi Dokumen (Meningkatkan Revision Rev 00 &rarr; Rev 01)</h3>
        <form action="{{ route('lifecycle.revision') }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
            @csrf
            <div>
                <label class="block font-bold text-slate-700 mb-1">Pilih Dokumen Eksisting yang Ingin Direvisi:</label>
                <select name="target_document_id" required class="w-full p-2 border border-slate-300 rounded-lg">
                    <option value="">-- Pilih Dokumen Active --</option>
                    @foreach($activeDocuments as $doc)
                        <option value="{{ $doc->DocumentID }}">{{ $doc->DocNumber }} - {{ $doc->Title }} (Current: {{ $doc->CurrentRevision }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1">Upload File Draft PDF Pengganti Baru:</label>
                <input type="file" name="document_file" accept="application/pdf" required class="w-full p-2 border border-slate-300 rounded-lg bg-slate-50">
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1">Catatan Perubahan (Change Log):</label>
                <textarea name="reason" required rows="3" placeholder="Jelaskan perubahan klausul/pasal pada dokumen revisi..." class="w-full p-2 border border-slate-300 rounded-lg"></textarea>
            </div>
            <div class="text-right">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-2.5 rounded-lg shadow transition">
                    Kirim Pengajuan Revisi (L1 PIC)
                </button>
            </div>
        </form>
    </div>

    <!-- Sub-module 3: Penarikan Dokumen / Obsolete -->
    <div x-show="activeTab === 'obsolete'" x-cloak class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
        <h3 class="text-sm font-bold text-slate-900 border-b pb-2 mb-4">Pengajuan Penarikan Dokumen Kadaluwarsa (Obsolete)</h3>
        <form action="{{ route('lifecycle.obsolete') }}" method="POST" class="space-y-4 text-xs">
            @csrf
            <div>
                <label class="block font-bold text-slate-700 mb-1">Pilih Dokumen yang Akan Ditarik (Obsolete):</label>
                <select name="target_document_id" required class="w-full p-2 border border-slate-300 rounded-lg">
                    <option value="">-- Pilih Dokumen Active --</option>
                    @foreach($activeDocuments as $doc)
                        <option value="{{ $doc->DocumentID }}">{{ $doc->DocNumber }} - {{ $doc->Title }} ({{ $doc->Department }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1">Alasan Penarikan Dokumen (Obsolescence Reason):</label>
                <textarea name="reason" required rows="3" placeholder="Jelaskan alasan penarikan/kadaluwarsa dokumen..." class="w-full p-2 border border-slate-300 rounded-lg"></textarea>
            </div>
            <div class="text-right">
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold px-6 py-2.5 rounded-lg shadow transition">
                    Kirim Pengajuan Obsolete (L1 PIC)
                </button>
            </div>
        </form>
    </div>

    <!-- Status Tracking & Approval Table -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-4 bg-slate-900 text-white flex justify-between items-center">
            <h3 class="text-xs font-bold uppercase tracking-wider">Daftar Pengajuan & Progress Approval 3 Layer</h3>
            <span class="text-xs text-slate-400">Sequential Workflow Tracking</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-100 text-slate-700 uppercase text-[11px] font-bold border-b">
                    <tr>
                        <th class="p-3">Req ID</th>
                        <th class="p-3">Tipe Workflow</th>
                        <th class="p-3">Dokumen</th>
                        <th class="p-3">Pemohon</th>
                        <th class="p-3">Posisi Layer Approval</th>
                        <th class="p-3">Status Akhir</th>
                        <th class="p-3 text-center">Audit Trail Log</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($requests as $r)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="p-3 font-mono font-bold text-slate-600">#REQ-{{ sprintf('%04d', $r->RequestID) }}</td>
                            <td class="p-3">
                                <span class="px-2 py-0.5 rounded font-bold text-[10px] uppercase
                                    {{ $r->RequestType == 'REGISTRATION' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $r->RequestType == 'REVISION' ? 'bg-purple-100 text-purple-800' : '' }}
                                    {{ $r->RequestType == 'OBSOLETE' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ $r->RequestType }}
                                </span>
                            </td>
                            <td class="p-3">
                                <div class="font-bold text-blue-700 font-mono">{{ $r->DocNumber }}</div>
                                <div class="text-slate-800 font-medium">{{ $r->Title }}</div>
                            </td>
                            <td class="p-3 text-slate-700 font-semibold">{{ $r->requester->name ?? 'User' }}</td>
                            <td class="p-3 font-semibold">
                                @if($r->CurrentStatus === 'APPROVED')
                                    <span class="text-emerald-700 font-bold">✅ Selesai (Layer 3 Approved)</span>
                                @elseif($r->CurrentStatus === 'REJECTED')
                                    <span class="text-red-700 font-bold">❌ Ditolak</span>
                                @else
                                    <span class="text-amber-700">Layer {{ $r->CurrentStepOrder }} ({{ $r->CurrentStepOrder == 1 ? 'PIC' : ($r->CurrentStepOrder == 2 ? 'Section Head' : 'Department Head') }})</span>
                                @endif
                            </td>
                            <td class="p-3">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold shadow-sm
                                    {{ $r->CurrentStatus == 'APPROVED' ? 'bg-emerald-600 text-white' : '' }}
                                    {{ str_starts_with($r->CurrentStatus, 'PENDING') ? 'bg-amber-500 text-white' : '' }}
                                    {{ $r->CurrentStatus == 'REJECTED' ? 'bg-red-600 text-white' : '' }}">
                                    {{ $r->CurrentStatus }}
                                </span>
                            </td>
                            <td class="p-3 text-center">
                                <button @click="activeLogs = {{ json_encode($r->approvalLogs) }}; showLogModal = true"
                                        class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-3 py-1 rounded text-xs font-bold border">
                                    📜 Lihat Audit Log
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-6 text-center text-slate-500 font-medium">Belum ada riwayat pengajuan pengendalian dokumen.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-t bg-slate-50">
            {{ $requests->links() }}
        </div>
    </div>

    <!-- Audit Log Modal -->
    <div x-show="showLogModal" x-cloak class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full p-6 relative border border-slate-200">
            <button @click="showLogModal = false" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 font-bold text-lg">&times;</button>
            <h3 class="text-base font-bold text-slate-900 border-b pb-2 mb-4">Audit Approval Trail Log</h3>
            <div class="space-y-3 max-h-60 overflow-y-auto">
                <template x-if="activeLogs.length === 0">
                    <p class="text-xs text-slate-500 italic text-center">Belum ada catatan approval.</p>
                </template>
                <template x-for="log in activeLogs" :key="log.LogID">
                    <div class="p-3 rounded-lg border text-xs" :class="log.Action === 'APPROVED' ? 'bg-emerald-50 border-emerald-200' : 'bg-red-50 border-red-200'">
                        <div class="flex justify-between font-bold">
                            <span x-text="'Layer ' + log.StepOrder + ' - ' + log.Action"></span>
                            <span class="text-slate-400" x-text="log.ActionDate"></span>
                        </div>
                        <div class="mt-1 text-slate-700" x-text="'Approver ID: ' + log.ApproverID + ' | Catatan: ' + (log.Notes || '-')"></div>
                    </div>
                </template>
            </div>
        </div>
    </div>

</div>
@endsection
