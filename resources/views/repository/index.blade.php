@extends('layouts.app')

@section('content')
<div x-data="repositoryView()" class="space-y-6">

    <!-- Description & Search Hero Card -->
    <div class="bg-paper p-6 rounded-card border border-[#d4e0ed] shadow-card-sm space-y-4">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h3 class="text-base font-bold text-[#0b3558] flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#006bff]" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z" />
                    </svg>
                    <span>Perpustakaan Dokumen Terdokumentasi</span>
                </h3>
                <p class="text-xs text-[#476788] mt-1 font-normal">Cari cepat dari 2.500+ dokumen SOP & regulasi berstatus ACTIVE terproteksi ISO 14001, 45001, 27001.</p>
            </div>
            <div class="text-xs bg-[#f0f3f8] text-[#004eba] px-4 py-2.5 rounded-full font-bold border border-[#d4e0ed] shrink-0">
                Total Dokumen Active: <span class="text-[#006bff] font-extrabold">{{ $documents->total() }}</span>
            </div>
        </div>

        <!-- Universal Smart Search Bar -->
        <form method="GET" action="{{ route('repository.index') }}" class="pt-2">
            <div class="relative flex items-center shadow-card-sm rounded-btn">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-[#476788]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari berdasarkan nama departemen (cth: Finance, K3LH), judul, nomor SOP, atau kategori..."
                       class="w-full pl-12 pr-28 py-3.5 border border-[#d4e0ed] rounded-btn bg-[#f8f9fb] focus:bg-paper focus:ring-2 focus:ring-[#006bff] focus:outline-none text-sm text-[#0b3558] font-medium transition">
                <button type="submit" class="absolute right-2 px-5 py-2 bg-[#006bff] hover:bg-[#0058d4] text-white font-bold text-xs rounded-btn transition shadow-sm">
                    Cari Dokumen
                </button>
            </div>

            <!-- Quick Search Tags / Keyword Suggestion Chips -->
            <div class="flex items-center space-x-2 mt-3 text-xs overflow-x-auto pb-1">
                <span class="text-[11px] font-semibold text-[#476788] shrink-0">Pencarian Populer:</span>
                <a href="{{ route('repository.index', ['search' => 'Finance']) }}" 
                   class="px-3 py-1 rounded-full bg-[#f0f3f8] hover:bg-[#e6f0ff] text-[#004eba] font-medium border border-[#d4e0ed] transition shrink-0">
                    Finance
                </a>
                <a href="{{ route('repository.index', ['search' => 'K3LH']) }}" 
                   class="px-3 py-1 rounded-full bg-[#f0f3f8] hover:bg-[#e6f0ff] text-[#004eba] font-medium border border-[#d4e0ed] transition shrink-0">
                    K3LH
                </a>
                <a href="{{ route('repository.index', ['search' => 'IT System']) }}" 
                   class="px-3 py-1 rounded-full bg-[#f0f3f8] hover:bg-[#e6f0ff] text-[#004eba] font-medium border border-[#d4e0ed] transition shrink-0">
                    IT System
                </a>
                <a href="{{ route('repository.index', ['search' => 'Quality']) }}" 
                   class="px-3 py-1 rounded-full bg-[#f0f3f8] hover:bg-[#e6f0ff] text-[#004eba] font-medium border border-[#d4e0ed] transition shrink-0">
                    Quality Assurance
                </a>
                <a href="{{ route('repository.index', ['search' => 'Lingkungan']) }}" 
                   class="px-3 py-1 rounded-full bg-[#f0f3f8] hover:bg-[#e6f0ff] text-[#004eba] font-medium border border-[#d4e0ed] transition shrink-0">
                    Lingkungan (ISO 14001)
                </a>
                @if(request('search') || request('department') || request('category'))
                    <a href="{{ route('repository.index') }}" class="px-3 py-1 rounded-full bg-red-50 text-red-600 font-bold border border-red-200 hover:bg-red-100 transition shrink-0 ml-auto">
                        Reset Pencarian
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Main Layout: Folder Explorer + Document Table -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

        <!-- Folder Explorer Sidebar -->
        <div class="lg:col-span-1 bg-paper p-5 rounded-card border border-[#d4e0ed] shadow-card-sm space-y-4">
            <h4 class="text-xs font-bold text-[#476788] uppercase tracking-wider border-b border-[#d4e0ed] pb-3">
                Hirarki Folder Departemen
            </h4>
            
            <div class="text-xs space-y-2">
                <!-- Company Root -->
                <div class="font-bold text-[#0b3558] flex items-center gap-2 p-2 bg-[#f0f3f8] rounded-btn border border-[#d4e0ed]">
                    <svg class="w-4 h-4 text-[#006bff]" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5s0 0 0 0m0 3h-1.5s0 0 0 0m0 3h1.5s0 0 0 0m0 3h-1.5s0 0 0 0m6-9h1.5s0 0 0 0m0 3h-1.5s0 0 0 0m0 3h1.5s0 0 0 0m0 3h-1.5s0 0 0 0" />
                    </svg>
                    <span>PT Enterprise Corp</span>
                </div>

                <!-- Department Nodes -->
                <div class="pl-3 space-y-1 border-l border-[#d4e0ed]">
                    <a href="{{ route('repository.index') }}" 
                       class="block text-xs text-[#0b3558] hover:text-[#006bff] font-semibold py-1.5 px-3 rounded-btn hover:bg-[#f0f3f8] transition {{ (!request('department') && !request('search')) ? 'bg-[#e6f0ff] text-[#006bff] font-bold' : '' }}">
                        Semua Departemen
                    </a>
                    @foreach($departments as $dept)
                        <a href="{{ route('repository.index', ['search' => $dept]) }}" 
                           class="flex items-center justify-between py-1.5 px-3 rounded-btn hover:bg-[#f0f3f8] font-semibold text-[#0b3558] transition {{ request('search') == $dept ? 'bg-[#e6f0ff] text-[#006bff] font-bold' : '' }}">
                            <span class="flex items-center gap-2 truncate">
                                <svg class="w-3.5 h-3.5 text-[#476788]" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z" />
                                </svg>
                                <span class="truncate">{{ $dept }}</span>
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Document List Table Section -->
        <div class="lg:col-span-3 space-y-6">

            <!-- Active Search Filter Badge Indicator -->
            @if(request('search'))
                <div class="bg-[#e6f0ff] border border-[#d4e0ed] px-4 py-2.5 rounded-btn flex items-center justify-between text-xs text-[#004eba]">
                    <span>Hasil pencarian untuk kata kunci: <strong class="text-[#0b3558]">"{{ request('search') }}"</strong></span>
                    <a href="{{ route('repository.index') }}" class="font-bold text-red-600 hover:underline">Hapus Filter &times;</a>
                </div>
            @endif

            <!-- Document List Table -->
            <div class="bg-paper rounded-card border border-[#d4e0ed] shadow-card-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-[#0b3558] text-white uppercase text-[11px] font-bold">
                            <tr>
                                <th class="p-4">No. Dokumen</th>
                                <th class="p-4">Judul Dokumen</th>
                                <th class="p-4">Departemen</th>
                                <th class="p-4">Kategori</th>
                                <th class="p-4">Revisi</th>
                                <th class="p-4">Status</th>
                                <th class="p-4 text-center">In-App Stream</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#f0f3f8]">
                            @forelse($documents as $doc)
                                <tr class="hover:bg-[#f8f9fb] transition">
                                    <td class="p-4 font-mono font-bold text-[#006bff]">{{ $doc->DocNumber }}</td>
                                    <td class="p-4 font-bold text-[#0b3558]">{{ $doc->Title }}</td>
                                    <td class="p-4 text-[#476788] font-medium">{{ $doc->Department }}</td>
                                    <td class="p-4">
                                        <span class="px-3 py-1 rounded-full font-semibold text-[11px] bg-[#e6f0ff] text-[#004eba] border border-[#d4e0ed]">
                                            {{ $doc->Category }}
                                        </span>
                                    </td>
                                    <td class="p-4 font-mono text-[#0b3558] font-bold">{{ $doc->CurrentRevision }}</td>
                                    <td class="p-4">
                                        <span class="px-3 py-1 rounded-full font-bold text-[10px] bg-[#006bff] text-white">
                                            ACTIVE
                                        </span>
                                    </td>
                                    <td class="p-4 text-center">
                                        <button @click="openPdfViewer('{{ route('documents.stream', $doc->DocumentID) }}', '{{ $doc->DocNumber }} - {{ $doc->Title }}')"
                                                class="bg-[#0b3558] hover:bg-[#082843] text-white px-4 py-2 rounded-btn text-xs font-semibold transition shadow-sm inline-flex items-center justify-center gap-2">
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            <span>Pratinjau PDF</span>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="p-8 text-center text-[#476788] font-medium">Dokumen tidak ditemukan untuk kata kunci yang dicari.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-[#d4e0ed] bg-[#f8f9fb]">
                    {{ $documents->links() }}
                </div>
            </div>

        </div>
    </div>

    <!-- PDF.js In-App Secure Viewer Modal -->
    <div x-show="pdfViewerModal" x-cloak class="fixed inset-0 bg-[#0b3558]/50 backdrop-blur-sm z-50 flex items-center justify-center p-6">
        <div class="bg-[#0b3558] rounded-card shadow-calendly max-w-5xl w-full h-[88vh] flex flex-col overflow-hidden border border-[#d4e0ed]">
            <div class="bg-[#082843] px-6 py-4 border-b border-[#476788]/30 flex items-center justify-between text-white">
                <div class="flex items-center space-x-3 truncate">
                    <span class="bg-[#006bff] text-[10px] font-bold px-2.5 py-0.5 rounded-full uppercase tracking-wider shrink-0">Secure PDF Viewer</span>
                    <h3 class="text-xs font-bold truncate max-w-md" x-text="activeDocTitle"></h3>
                </div>
                <div class="flex items-center space-x-3 text-xs shrink-0">
                    <button @click="prevPage()" class="bg-[#0b3558] px-3 py-1.5 rounded-btn hover:bg-[#006bff] transition">&larr; Prev</button>
                    <span class="font-medium">Halaman <span x-text="pageNum"></span> / <span x-text="pageCount"></span></span>
                    <button @click="nextPage()" class="bg-[#0b3558] px-3 py-1.5 rounded-btn hover:bg-[#006bff] transition">Next &rarr;</button>
                    <button @click="closePdfViewer()" class="bg-red-600 hover:bg-red-700 px-4 py-1.5 rounded-btn font-bold ml-4 text-white">&times; Tutup</button>
                </div>
            </div>

            <div class="flex-1 overflow-auto p-6 flex justify-center items-center bg-[#f8f9fb] relative select-none" oncontextmenu="return false;">
                <div class="relative shadow-calendly bg-paper rounded-product overflow-hidden border border-[#d4e0ed]">
                    <canvas id="pdf-render-canvas"></canvas>
                    <div class="absolute inset-0 pointer-events-none flex items-center justify-center rotate-[-30deg] opacity-20">
                        <div class="text-red-700 font-extrabold text-xl text-center leading-tight">
                            [INTERNAL EDMS DOCUMENT]<br>
                            ISO 14001 / 45001 / 27001<br>
                            DIARSIPKAN OLEH: {{ Auth::user()->name ?? 'Karyawan' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    function repositoryView() {
        return {
            pdfViewerModal: false,
            activeDocTitle: '',
            pdfDoc: null,
            pageNum: 1,
            pageCount: 0,

            openPdfViewer(url, title) {
                this.activeDocTitle = title;
                this.pdfViewerModal = true;
                this.pageNum = 1;

                pdfjsLib.getDocument(url).promise.then(pdf => {
                    this.pdfDoc = pdf;
                    this.pageCount = pdf.numPages;
                    this.renderPage(this.pageNum);
                }).catch(err => {
                    console.error("Error loading PDF: ", err);
                });
            },

            renderPage(num) {
                if (!this.pdfDoc) return;
                this.pdfDoc.getPage(num).then(page => {
                    const viewport = page.getViewport({ scale: 1.2 });
                    const canvas = document.getElementById('pdf-render-canvas');
                    const ctx = canvas.getContext('2d');
                    canvas.height = viewport.height;
                    canvas.width = viewport.width;

                    const renderContext = {
                        canvasContext: ctx,
                        viewport: viewport
                    };
                    page.render(renderContext);
                });
            },

            prevPage() {
                if (this.pageNum <= 1) return;
                this.pageNum--;
                this.renderPage(this.pageNum);
            },

            nextPage() {
                if (this.pageNum >= this.pageCount) return;
                this.pageNum++;
                this.renderPage(this.pageNum);
            },

            closePdfViewer() {
                this.pdfViewerModal = false;
                this.pdfDoc = null;
            }
        }
    }
</script>
@endsection
