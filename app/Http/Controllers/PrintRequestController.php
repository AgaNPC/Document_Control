<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentRequest;
use App\Models\UserNotification;
use App\Models\User;
use App\Services\WatermarkService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrintRequestController extends Controller
{
    protected $watermarkService;

    public function __construct(WatermarkService $watermarkService)
    {
        $this->watermarkService = $watermarkService;
    }

    public function index()
    {
        $activeDocuments = Document::where('Status', 'ACTIVE')->get();
        $printRequests = DocumentRequest::where('RequestType', 'PRINT')
            ->with(['targetDocument', 'requester', 'approvalLogs.approver'])
            ->latest()
            ->paginate(10);

        return view('print.index', compact('activeDocuments', 'printRequests'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'document_id' => 'required|exists:documents,DocumentID',
            'copy_count' => 'required|integer|min:1|max:100',
            'reason' => 'required|string|max:500',
        ]);

        $document = Document::findOrFail($request->document_id);
        $user = Auth::user();

        $printReq = DocumentRequest::create([
            'RequestType' => 'PRINT',
            'TargetDocumentID' => $document->DocumentID,
            'DocNumber' => $document->DocNumber,
            'Title' => $document->Title,
            'Department' => $document->Department,
            'Category' => $document->Category,
            'Reason' => $request->reason,
            'CopyCount' => $request->copy_count,
            'CurrentStepOrder' => 1,
            'CurrentStatus' => 'PENDING_L1',
            'RequestedBy' => $user ? $user->id : 1,
        ]);

        // Create notification for Layer 1 PIC of the department
        $pic = User::where('role', 'PIC')->where('department', $document->Department)->first()
            ?? User::where('role', 'PIC')->first();

        if ($pic) {
            UserNotification::create([
                'UserID' => $pic->id,
                'RequestID' => $printReq->RequestID,
                'Title' => 'Permohonan Cetak Terkontrol',
                'Message' => "Permohonan cetak {$request->copy_count} eksemplar untuk '{$document->Title}' oleh {$user->name}",
            ]);
        }

        return redirect()->back()->with('success', 'Permohonan cetak terkontrol berhasil diajukan!');
    }

    public function download($id)
    {
        $printReq = DocumentRequest::where('RequestType', 'PRINT')
            ->where('CurrentStatus', 'APPROVED')
            ->findOrFail($id);

        $document = Document::findOrFail($printReq->TargetDocumentID);
        $approverLog = $printReq->approvalLogs()->where('Action', 'APPROVED')->latest()->first();
        $approverName = $approverLog && $approverLog->approver ? $approverLog->approver->name : 'Dept Head';
        $requesterName = $printReq->requester ? $printReq->requester->name : 'Pemohon';

        $stampText = sprintf(
            "CONTROLLED COPY No. [%04d] - Approver: %s - Requester: %s - %s",
            $printReq->RequestID,
            $approverName,
            $requesterName,
            now()->format('Y-m-d H:i:s')
        );

        $stampedPdf = $this->watermarkService->generateControlledCopy($document->FilePath, $stampText);

        return response($stampedPdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="CONTROLLED_COPY_' . $printReq->DocNumber . '.pdf"',
        ]);
    }
}
