@extends('layouts.app')

@section('content')
<div x-data="repositoryView()" class="space-y-6">

    <!-- Header Title -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white p-6 rounded-xl shadow-sm border border-slate-200">
        <div>
            <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                <span>📁</span> Perpustakaan Dokumen Terdokumentasi (Document Repository)
            </h2>
            <p class="text-xs text-slate-500 mt-1">Menampilkan seluruh SOP & regulasi berstatus ACTIVE dengan In-App PDF Viewer dan dynamic watermark terproteksi.</p>
        </div>
        <div class="text-xs bg-slate-100 text-slate-700 px-3 py-2 rounded-lg font-semibold border">
            Total Dokumen Aktif: <span class="text-blue-600 font-bold text-sm">{{ $documents->total() }}</span>
        </div>
    </div>

    <!-- Filter & Search Bar + Explorer Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

        <!-- Left Sidebar: Directory Tree Explorer -->
        <div class="lg:col-span-1 bg-white p-4 rounded-xl shadow-sm border border-slate-200 space-y-4">
            <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider border-b pb-2">
                Struktur Folder Perusahaan
            </h3>
            
            <div class="text-xs space-y-2">
                <!-- Company Root -->
                <div class="font-bold text-slate-800 flex items-center gap-1">
                    <span>🏢</span> PT Enterprise Corp
                </div>

                <!-- Departments Tree -->
                <div class="pl-4 space-y-1.5 border-l-2 border-slate-200">
                    <a href="{{ route('repository.index') }}" class="block text-slate-700 hover:text-blue-600 font-medium py-1">
                        📂 Semua Departemen
                    </a>
                    @foreach($departments as $dept)
                        <div x-data="{ expanded: false }">
                            <div @click="expanded = !expanded" class="flex items-center justify-between py-1 px-2 rounded hover:bg-slate-100 cursor-pointer font-semibold text-slate-700">
                                <span class="flex items-center gap-1">
                                    <span>📁</span> {{ $dept }}
                                </span>
                                <span class="text-[10px] text-slate-400" x-text="expanded ? '▲' : '▼'"></span>
                            </div>
                            <!-- Category sub-tree -->
                            <div x-show="expanded" x-cloak class="pl-4 py-1 space-y-1">
                                @foreach($categories as $cat)
                                    <a href="{{ route('repository.index', ['department' => $dept, 'category' => $cat]) }}" 
                                       class="block text-[11px] text-slate-600 hover:text-blue-600 hover:font-bold py-0.5">
                                        📄 Kategori: {{ $cat }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Right Main Column: Search, Filter & Document Table -->
        <div class="lg:col-span-3 space-y-4">

            <!-- Multi-Criteria Debounced Search & Filter Form -->
            <form method="GET" action="{{ route('repository.index') }}" class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-xs">
                <!-- Search Bar -->
                <div class="sm:col-span-2 lg:col-span-4">
                    <label class="block font-bold text-slate-600 mb-1">Pencarian Kata Kunci (Nomor / Judul / Kategori):</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Ketik untuk mencari dari 2.500+ dokumen..."
                           class="w-full p-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none text-xs">
                </div>

                <!-- Department Filter -->
                <div>
                    <label class="block font-bold text-slate-600 mb-1">Departemen:</label>
                    <select name="department" class="w-full p-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <option value="">-- Semua Departemen --</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>{{ $dept }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Category Filter -->
                <div>
                    <label class="block font-bold text-slate-600 mb-1">Kategori Standard:</label>
                    <select name="category" class="w-full p-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <option value="">-- Semua Kategori --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Document Number Filter -->
                <div>
                    <label class="block font-bold text-slate-600 mb-1">Nomor Dokumen:</label>
                    <input type="text" name="doc_number" value="{{ request('doc_number') }}" placeholder="SOP-IT-001..." class="w-full p-2 border border-slate-300 rounded-lg">
                </div>

                <!-- Submit & Reset -->
                <div class="flex items-end space-x-2">
                    <button type="submit" class="w-full py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg transition shadow">Filter</button>
                    <a href="{{ route('repository.index') }}" class="py-2 px-3 bg-slate-200 text-slate-700 font-bold rounded-lg text-center">Reset</a>
                </div>
            </form>

            <!-- Document List Table -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-900 text-white uppercase text-[11px] font-bold">
                            <tr>
                                <th class="p-3">No. Dokumen</th>
                                <th class="p-3">Judul Dokumen</th>
                                <th class="p-3">Departemen</th>
                                <th class="p-3">Kategori</th>
                                <th class="p-3">Revisi</th>
                                <th class="p-3">Status</th>
                                <th class="p-3 text-center">Aksi / Viewer</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @forelse($documents as $doc)
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="p-3 font-mono font-bold text-blue-700">{{ $doc->DocNumber }}</td>
                                    <td class="p-3 font-semibold text-slate-800">{{ $doc->Title }}</td>
                                    <td class="p-3 text-slate-600">{{ $doc->Department }}</td>
                                    <td class="p-3">
                                        <span class="px-2 py-0.5 rounded font-bold text-[10px] 
                                            {{ $doc->Category == 'K3' ? 'bg-amber-100 text-amber-800 border border-amber-300' : '' }}
                                            {{ $doc->Category == 'Lingkungan' ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : '' }}
                                            {{ $doc->Category == 'IT/Keamanan' ? 'bg-indigo-100 text-indigo-800 border border-indigo-300' : '' }}
                                            {{ $doc->Category == 'Mutu' ? 'bg-blue-100 text-blue-800 border border-blue-300' : '' }}">
                                            {{ $doc->Category }}
                                        </span>
                                    </td>
                                    <td class="p-3 font-mono text-slate-700 font-semibold">{{ $doc->CurrentRevision }}</td>
                                    <td class="p-3">
                                        <span class="px-2 py-0.5 rounded-full font-bold text-[10px] bg-emerald-600 text-white shadow-sm">
                                            ACTIVE
                                        </span>
                                    </td>
                                    <td class="p-3 text-center">
                                        <button @click="openPdfViewer('{{ route('documents.stream', $doc->DocumentID) }}', '{{ $doc->DocNumber }} - {{ $doc->Title }}')"
                                                class="bg-slate-900 hover:bg-blue-600 text-white px-3 py-1.5 rounded-lg text-xs font-bold transition shadow flex items-center justify-center gap-1 mx-auto">
                                            <span>👁️</span> Pratinjau PDF
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="p-6 text-center text-slate-500 font-medium">Tidak ada dokumen yang sesuai dengan kriteria pencarian.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="p-3 border-t bg-slate-50">
                    {{ $documents->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- In-App Secure PDF Viewer Modal (PDF.js Canvas rendering without print/download) -->
    <div x-show="pdfViewerModal" x-cloak class="fixed inset-0 bg-slate-900/80 backdrop-blur-md z-50 flex items-center justify-center p-4">
        <div class="bg-slate-900 rounded-xl shadow-2xl max-w-5xl w-full h-[90vh] flex flex-col overflow-hidden border border-slate-700">
            <!-- Modal Header -->
            <div class="bg-slate-950 px-4 py-3 border-b border-slate-800 flex items-center justify-between text-white">
                <div class="flex items-center space-x-2">
                    <span class="bg-blue-600 text-[10px] font-bold px-2 py-0.5 rounded uppercase">Secure Viewer</span>
                    <h3 class="text-xs font-bold truncate max-w-md" x-text="activeDocTitle"></h3>
                </div>
                <div class="flex items-center space-x-3 text-xs">
                    <button @click="prevPage()" class="bg-slate-800 px-2 py-1 rounded hover:bg-slate-700">&larr; Prev</button>
                    <span>Halaman <span x-text="pageNum"></span> / <span x-text="pageCount"></span></span>
                    <button @click="nextPage()" class="bg-slate-800 px-2 py-1 rounded hover:bg-slate-700">Next &rarr;</button>
                    <button @click="closePdfViewer()" class="bg-red-600 hover:bg-red-700 px-3 py-1 rounded font-bold ml-4 text-white">&times; Tutup</button>
                </div>
            </div>

            <!-- PDF Canvas Container with Watermark Overlay -->
            <div class="flex-1 overflow-auto p-4 flex justify-center items-center bg-slate-800 relative select-none" oncontextmenu="return false;">
                <div class="relative shadow-2xl bg-white">
                    <canvas id="pdf-render-canvas"></canvas>
                    <!-- In-App Dynamic Security Watermark Overlay -->
                    <div class="absolute inset-0 pointer-events-none flex items-center justify-center rotate-[-30deg] opacity-20">
                        <div class="text-red-700 font-extrabold text-2xl text-center leading-tight">
                            [INTERNAL EDMS DOCUMENT]<br>
                            DIARSIPKAN ISO 14001/45001/27001<br>
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
                });
            },

            renderPage(num) {
                if (!this.pdfDoc) return;
                this.pdfDoc.getPage(num).then(page => {
                    const viewport = page.getViewport({ scale: 1.3 });
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
