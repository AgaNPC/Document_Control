<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentRequest;
use App\Models\UserNotification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LifecycleController extends Controller
{
    public function index()
    {
        $activeDocuments = Document::where('Status', 'ACTIVE')->get();
        $requests = DocumentRequest::whereIn('RequestType', ['REGISTRATION', 'REVISION', 'OBSOLETE'])
            ->with(['targetDocument', 'requester', 'approvalLogs.approver'])
            ->latest()
            ->paginate(10);

        return view('lifecycle.index', compact('activeDocuments', 'requests'));
    }

    public function storeRegistration(Request $request)
    {
        $request->validate([
            'doc_number' => 'required|string|max:100|unique:documents,DocNumber',
            'title' => 'required|string|max:255',
            'department' => 'required|string|max:100',
            'category' => 'required|string|in:K3,Lingkungan,IT/Keamanan,Mutu',
            'reason' => 'required|string',
            'document_file' => 'required|file|mimes:pdf|max:10240',
        ]);

        $user = Auth::user();
        $filePath = $request->file('document_file')->store('temp', 'local');

        $docReq = DocumentRequest::create([
            'RequestType' => 'REGISTRATION',
            'DocNumber' => $request->doc_number,
            'Title' => $request->title,
            'Department' => $request->department,
            'Category' => $request->category,
            'Reason' => $request->reason,
            'TempFilePath' => $filePath,
            'CurrentStepOrder' => 1,
            'CurrentStatus' => 'PENDING_L1',
            'RequestedBy' => $user ? $user->id : 1,
        ]);

        $this->notifyLayerApprover($docReq, 1);

        return redirect()->back()->with('success', 'Pengajuan dokumen baru berhasil dikirim untuk approval Layer 1 (PIC).');
    }

    public function storeRevision(Request $request)
    {
        $request->validate([
            'target_document_id' => 'required|exists:documents,DocumentID',
            'reason' => 'required|string',
            'document_file' => 'required|file|mimes:pdf|max:10240',
        ]);

        $document = Document::findOrFail($request->target_document_id);
        $user = Auth::user();
        $filePath = $request->file('document_file')->store('temp', 'local');

        $docReq = DocumentRequest::create([
            'RequestType' => 'REVISION',
            'TargetDocumentID' => $document->DocumentID,
            'DocNumber' => $document->DocNumber,
            'Title' => $document->Title,
            'Department' => $document->Department,
            'Category' => $document->Category,
            'Reason' => $request->reason,
            'TempFilePath' => $filePath,
            'CurrentStepOrder' => 1,
            'CurrentStatus' => 'PENDING_L1',
            'RequestedBy' => $user ? $user->id : 1,
        ]);

        $this->notifyLayerApprover($docReq, 1);

        return redirect()->back()->with('success', 'Pengajuan revisi dokumen berhasil dikirim untuk approval Layer 1 (PIC).');
    }

    public function storeObsolete(Request $request)
    {
        $request->validate([
            'target_document_id' => 'required|exists:documents,DocumentID',
            'reason' => 'required|string',
        ]);

        $document = Document::findOrFail($request->target_document_id);
        $user = Auth::user();

        $docReq = DocumentRequest::create([
            'RequestType' => 'OBSOLETE',
            'TargetDocumentID' => $document->DocumentID,
            'DocNumber' => $document->DocNumber,
            'Title' => $document->Title,
            'Department' => $document->Department,
            'Category' => $document->Category,
            'Reason' => $request->reason,
            'CurrentStepOrder' => 1,
            'CurrentStatus' => 'PENDING_L1',
            'RequestedBy' => $user ? $user->id : 1,
        ]);

        $this->notifyLayerApprover($docReq, 1);

        return redirect()->back()->with('success', 'Pengajuan penarikan dokumen (Obsolete) berhasil dikirim untuk approval Layer 1 (PIC).');
    }

    private function notifyLayerApprover(DocumentRequest $docReq, int $stepOrder)
    {
        $roles = [1 => 'PIC', 2 => 'Section Head', 3 => 'Department Head'];
        $roleName = $roles[$stepOrder] ?? 'PIC';

        $approver = User::where('role', $roleName)
            ->where('department', $docReq->Department)
            ->first() ?? User::where('role', $roleName)->first();

        if ($approver) {
            UserNotification::create([
                'UserID' => $approver->id,
                'RequestID' => $docReq->RequestID,
                'Title' => "Approval Tasks Layer {$stepOrder} ({$roleName})",
                'Message' => "Permohonan {$docReq->RequestType} dokumen '{$docReq->Title}' ({$docReq->DocNumber}) membutuhkan verifikasi Anda.",
            ]);
        }
    }
}
