@extends('layouts.app')

@section('content')
<div x-data="{ activeTab: 'registration', showLogModal: false, activeLogs: [] }" class="space-y-6">

    <!-- Description Banner -->
    <div class="bg-paper p-6 rounded-card border border-[#d4e0ed] shadow-card-sm">
        <h3 class="text-base font-bold text-[#0b3558] flex items-center gap-2">
            <svg class="w-5 h-5 text-[#006bff]" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
            </svg>
            <span>Pengendalian Dokumen (Document Lifecycle Control)</span>
        </h3>
        <p class="text-xs text-[#476788] mt-1 font-normal">Alur sekuensial 3-Layer Approval (PIC Layer 1 &rarr; Section Head Layer 2 &rarr; Department Head Layer 3). Persetujuan Dept Head mengeksekusi sistem secara otomatis.</p>
    </div>

    <!-- Sub-module Navigation Tabs -->
    <div class="flex space-x-2 border-b border-[#d4e0ed]">
        <button @click="activeTab = 'registration'" 
                :class="activeTab === 'registration' ? 'border-[#006bff] text-[#006bff] bg-paper font-bold shadow-sm' : 'border-transparent text-[#476788] hover:text-[#0b3558] bg-[#f8f9fb]'"
                class="px-5 py-3 rounded-t-product border-b-2 text-xs transition font-semibold">
            1. Pendaftaran Dokumen Baru
        </button>
        <button @click="activeTab = 'revision'" 
                :class="activeTab === 'revision' ? 'border-[#006bff] text-[#006bff] bg-paper font-bold shadow-sm' : 'border-transparent text-[#476788] hover:text-[#0b3558] bg-[#f8f9fb]'"
                class="px-5 py-3 rounded-t-product border-b-2 text-xs transition font-semibold">
            2. Revisi Dokumen Eksisting
        </button>
        <button @click="activeTab = 'obsolete'" 
                :class="activeTab === 'obsolete' ? 'border-[#006bff] text-[#006bff] bg-paper font-bold shadow-sm' : 'border-transparent text-[#476788] hover:text-[#0b3558] bg-[#f8f9fb]'"
                class="px-5 py-3 rounded-t-product border-b-2 text-xs transition font-semibold">
            3. Penarikan Dokumen (Obsolete)
        </button>
    </div>

    <!-- Sub-module 1: Pendaftaran Dokumen Baru -->
    <div x-show="activeTab === 'registration'" x-cloak class="bg-paper p-6 rounded-card border border-[#d4e0ed] shadow-card-sm">
        <h4 class="text-xs font-bold text-[#0b3558] uppercase tracking-wider border-b border-[#d4e0ed] pb-3 mb-5">Pengajuan Pendaftaran Dokumen Baru (Draft Upload &rarr; Master Relocation)</h4>
        <form action="{{ route('lifecycle.registration') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
            @csrf
            <div>
                <label class="block font-bold text-[#0b3558] mb-1.5">Nomor Dokumen Standard:</label>
                <input type="text" name="doc_number" required placeholder="SOP-K3-2026-001..." class="w-full p-3 border border-[#d4e0ed] rounded-input bg-[#f8f9fb] text-[#0b3558]">
            </div>
            <div>
                <label class="block font-bold text-[#0b3558] mb-1.5">Judul Dokumen:</label>
                <input type="text" name="title" required placeholder="SOP Keselamatan Kerja Listrik..." class="w-full p-3 border border-[#d4e0ed] rounded-input bg-[#f8f9fb] text-[#0b3558]">
            </div>
            <div>
                <label class="block font-bold text-[#0b3558] mb-1.5">Departemen Pemilik:</label>
                <select name="department" required class="w-full p-3 border border-[#d4e0ed] rounded-input bg-[#f8f9fb] text-[#0b3558]">
                    <option value="K3LH">K3LH (Safety & Environment)</option>
                    <option value="IT System">IT System & Security</option>
                    <option value="Quality Assurance">Quality Assurance (Mutu)</option>
                    <option value="Maintenance">Maintenance & Engineering</option>
                </select>
            </div>
            <div>
                <label class="block font-bold text-[#0b3558] mb-1.5">Kategori Dokumen:</label>
                <select name="category" required class="w-full p-3 border border-[#d4e0ed] rounded-input bg-[#f8f9fb] text-[#0b3558]">
                    <option value="K3">K3 (Keselamatan & Kesehatan Kerja)</option>
                    <option value="Lingkungan">Lingkungan (ISO 14001)</option>
                    <option value="IT/Keamanan">IT / Keamanan Informasi (ISO 27001)</option>
                    <option value="Mutu">Mutu & Operasional</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block font-bold text-[#0b3558] mb-1.5">Upload File Draft PDF Baru:</label>
                <input type="file" name="document_file" accept="application/pdf" required class="w-full p-3 border border-[#d4e0ed] rounded-input bg-[#f8f9fb] text-[#0b3558]">
            </div>
            <div class="md:col-span-2">
                <label class="block font-bold text-[#0b3558] mb-1.5">Alasan Penerbitan Dokumen Baru:</label>
                <textarea name="reason" required rows="3" placeholder="Jelaskan latar belakang penerbitan dokumen..." class="w-full p-3 border border-[#d4e0ed] rounded-input bg-[#f8f9fb] text-[#0b3558]"></textarea>
            </div>
            <div class="md:col-span-2 text-right">
                <button type="submit" class="bg-[#006bff] hover:bg-[#0058d4] text-white font-semibold px-6 py-3 rounded-btn shadow-sm transition">
                    Kirim Pengajuan Pendaftaran (L1 PIC)
                </button>
            </div>
        </form>
    </div>

    <!-- Sub-module 2: Revisi Dokumen Eksisting -->
    <div x-show="activeTab === 'revision'" x-cloak class="bg-paper p-6 rounded-card border border-[#d4e0ed] shadow-card-sm">
        <h4 class="text-xs font-bold text-[#0b3558] uppercase tracking-wider border-b border-[#d4e0ed] pb-3 mb-5">Pengajuan Revisi Dokumen (Meningkatkan Revision Rev 00 &rarr; Rev 01)</h4>
        <form action="{{ route('lifecycle.revision') }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
            @csrf
            <div>
                <label class="block font-bold text-[#0b3558] mb-1.5">Pilih Dokumen Eksisting yang Ingin Direvisi:</label>
                <select name="target_document_id" required class="w-full p-3 border border-[#d4e0ed] rounded-input bg-[#f8f9fb] text-[#0b3558]">
                    <option value="">-- Pilih Dokumen Active --</option>
                    @foreach($activeDocuments as $doc)
                        <option value="{{ $doc->DocumentID }}">{{ $doc->DocNumber }} - {{ $doc->Title }} (Current: {{ $doc->CurrentRevision }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block font-bold text-[#0b3558] mb-1.5">Upload File Draft PDF Pengganti Baru:</label>
                <input type="file" name="document_file" accept="application/pdf" required class="w-full p-3 border border-[#d4e0ed] rounded-input bg-[#f8f9fb] text-[#0b3558]">
            </div>
            <div>
                <label class="block font-bold text-[#0b3558] mb-1.5">Catatan Perubahan (Change Log):</label>
                <textarea name="reason" required rows="3" placeholder="Jelaskan perubahan klausul/pasal pada dokumen revisi..." class="w-full p-3 border border-[#d4e0ed] rounded-input bg-[#f8f9fb] text-[#0b3558]"></textarea>
            </div>
            <div class="text-right">
                <button type="submit" class="bg-[#006bff] hover:bg-[#0058d4] text-white font-semibold px-6 py-3 rounded-btn shadow-sm transition">
                    Kirim Pengajuan Revisi (L1 PIC)
                </button>
            </div>
        </form>
    </div>

    <!-- Sub-module 3: Penarikan Dokumen / Obsolete -->
    <div x-show="activeTab === 'obsolete'" x-cloak class="bg-paper p-6 rounded-card border border-[#d4e0ed] shadow-card-sm">
        <h4 class="text-xs font-bold text-[#0b3558] uppercase tracking-wider border-b border-[#d4e0ed] pb-3 mb-5">Pengajuan Penarikan Dokumen Kadaluwarsa (Obsolete)</h4>
        <form action="{{ route('lifecycle.obsolete') }}" method="POST" class="space-y-4 text-xs">
            @csrf
            <div>
                <label class="block font-bold text-[#0b3558] mb-1.5">Pilih Dokumen yang Akan Ditarik (Obsolete):</label>
                <select name="target_document_id" required class="w-full p-3 border border-[#d4e0ed] rounded-input bg-[#f8f9fb] text-[#0b3558]">
                    <option value="">-- Pilih Dokumen Active --</option>
                    @foreach($activeDocuments as $doc)
                        <option value="{{ $doc->DocumentID }}">{{ $doc->DocNumber }} - {{ $doc->Title }} ({{ $doc->Department }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block font-bold text-[#0b3558] mb-1.5">Alasan Penarikan Dokumen (Obsolescence Reason):</label>
                <textarea name="reason" required rows="3" placeholder="Jelaskan alasan penarikan/kadaluwarsa dokumen..." class="w-full p-3 border border-[#d4e0ed] rounded-input bg-[#f8f9fb] text-[#0b3558]"></textarea>
            </div>
            <div class="text-right">
                <button type="submit" class="bg-[#0b3558] hover:bg-[#082843] text-white font-semibold px-6 py-3 rounded-btn shadow-sm transition">
                    Kirim Pengajuan Obsolete (L1 PIC)
                </button>
            </div>
        </form>
    </div>

    <!-- Status Tracking & Approval Table -->
    <div class="bg-paper rounded-card border border-[#d4e0ed] shadow-card-sm overflow-hidden">
        <div class="p-4 bg-[#0b3558] text-white flex justify-between items-center">
            <h4 class="text-xs font-bold uppercase tracking-wider">Daftar Pengajuan & Progress Approval 3 Layer</h4>
            <span class="text-[11px] bg-[#006bff] text-white px-2.5 py-0.5 rounded-full font-bold">Sequential Workflow</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#f0f3f8] text-[#0b3558] uppercase text-[11px] font-bold border-b border-[#d4e0ed]">
                    <tr>
                        <th class="p-4">Req ID</th>
                        <th class="p-4">Tipe Workflow</th>
                        <th class="p-4">Dokumen</th>
                        <th class="p-4">Pemohon</th>
                        <th class="p-4">Posisi Layer Approval</th>
                        <th class="p-4">Status Akhir</th>
                        <th class="p-4 text-center">Audit Log</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#f0f3f8]">
                    @forelse($requests as $r)
                        <tr class="hover:bg-[#f8f9fb] transition">
                            <td class="p-4 font-mono font-bold text-[#476788]">#REQ-{{ sprintf('%04d', $r->RequestID) }}</td>
                            <td class="p-4">
                                <span class="px-3 py-1 rounded-full font-semibold text-[11px] uppercase bg-[#e6f0ff] text-[#004eba] border border-[#d4e0ed]">
                                    {{ $r->RequestType }}
                                </span>
                            </td>
                            <td class="p-4">
                                <div class="font-bold text-[#006bff] font-mono">{{ $r->DocNumber }}</div>
                                <div class="text-[#0b3558] font-bold">{{ $r->Title }}</div>
                            </td>
                            <td class="p-4 text-[#0b3558] font-medium">{{ $r->requester->name ?? 'User' }}</td>
                            <td class="p-4 font-semibold">
                                @if($r->CurrentStatus === 'APPROVED')
                                    <span class="text-[#006bff] font-bold">Layer 3 Approved (System Updated)</span>
                                @elseif($r->CurrentStatus === 'REJECTED')
                                    <span class="text-red-600 font-bold">Ditolak</span>
                                @else
                                    <span class="text-[#0b3558]">Layer {{ $r->CurrentStepOrder }} ({{ $r->CurrentStepOrder == 1 ? 'PIC' : ($r->CurrentStepOrder == 2 ? 'Section Head' : 'Department Head') }})</span>
                                @endif
                            </td>
                            <td class="p-4">
                                <span class="px-3 py-1 rounded-full text-[11px] font-bold
                                    {{ $r->CurrentStatus == 'APPROVED' ? 'bg-[#006bff] text-white' : '' }}
                                    {{ str_starts_with($r->CurrentStatus, 'PENDING') ? 'bg-[#f0f3f8] text-[#004eba] border border-[#d4e0ed]' : '' }}
                                    {{ $r->CurrentStatus == 'REJECTED' ? 'bg-red-600 text-white' : '' }}">
                                    {{ $r->CurrentStatus }}
                                </span>
                            </td>
                            <td class="p-4 text-center">
                                <button @click="activeLogs = {{ json_encode($r->approvalLogs) }}; showLogModal = true"
                                        class="bg-[#f0f3f8] hover:bg-[#e6f0ff] text-[#0b3558] px-3.5 py-1.5 rounded-btn text-xs font-semibold border border-[#d4e0ed] transition">
                                    Audit Log
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-[#476788] font-medium">Belum ada pengajuan pengendalian dokumen.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-[#d4e0ed] bg-[#f8f9fb]">
            {{ $requests->links() }}
        </div>
    </div>

    <!-- Audit Log Modal -->
    <div x-show="showLogModal" x-cloak class="fixed inset-0 bg-[#0b3558]/40 backdrop-blur-sm z-50 flex items-center justify-center p-6">
        <div class="bg-paper rounded-card shadow-calendly max-w-lg w-full p-6 relative border border-[#d4e0ed]">
            <button @click="showLogModal = false" class="absolute top-5 right-5 text-[#476788] hover:text-[#0b3558] font-bold text-xl">&times;</button>
            <h3 class="text-base font-bold text-[#0b3558] border-b border-[#d4e0ed] pb-3 mb-4">Audit Approval Trail Log</h3>
            <div class="space-y-3 max-h-60 overflow-y-auto">
                <template x-if="activeLogs.length === 0">
                    <p class="text-xs text-[#476788] italic text-center">Belum ada catatan approval.</p>
                </template>
                <template x-for="log in activeLogs" :key="log.LogID">
                    <div class="p-3.5 rounded-product border text-xs bg-[#f8f9fb] border-[#d4e0ed]">
                        <div class="flex justify-between font-bold text-[#0b3558]">
                            <span x-text="'Layer ' + log.StepOrder + ' - ' + log.Action"></span>
                            <span class="text-[#476788] font-normal" x-text="log.ActionDate"></span>
                        </div>
                        <div class="mt-1 text-[#476788]" x-text="'Approver ID: ' + log.ApproverID + ' | Catatan: ' + (log.Notes || '-')"></div>
                    </div>
                </template>
            </div>
        </div>
    </div>

</div>
@endsection
