<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Services\WatermarkService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    protected $watermarkService;

    public function __construct(WatermarkService $watermarkService)
    {
        $this->watermarkService = $watermarkService;
    }

    public function index(Request $request)
    {
        $query = Document::query()->where('Status', 'ACTIVE');

        if ($request->filled('department')) {
            $query->where('Department', $request->department);
        }

        if ($request->filled('category')) {
            $query->where('Category', $request->category);
        }

        if ($request->filled('doc_number')) {
            $query->where('DocNumber', 'like', '%' . $request->doc_number . '%');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('Title', 'like', "%{$search}%")
                  ->orWhere('DocNumber', 'like', "%{$search}%")
                  ->orWhere('Category', 'like', "%{$search}%");
            });
        }

        $documents = $query->latest()->paginate(15)->withQueryString();

        // Get tree structure data: Companies > Departments > Categories
        $departments = Document::where('Status', 'ACTIVE')->select('Department')->distinct()->pluck('Department');
        $categories = ['K3', 'Lingkungan', 'IT/Keamanan', 'Mutu'];

        return view('repository.index', compact('documents', 'departments', 'categories'));
    }

    public function stream($id)
    {
        $document = Document::findOrFail($id);
        $user = Auth::user() ?? (object)['name' => 'Karyawan EDMS', 'email' => 'guest@company.com'];

        $watermarkText = sprintf(
            "[INTERNAL] - Diakses oleh: %s - %s",
            $user->name,
            now()->format('Y-m-d H:i:s')
        );

        $pdfContent = $this->watermarkService->generateDynamicWatermark($document->FilePath, $watermarkText);

        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($document->FilePath) . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }
}
