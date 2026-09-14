<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentRequest;
use App\Models\RequestApprovalLog;
use App\Models\UserNotification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class NotificationController extends Controller
{
    public function unreadCount()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['unread_count' => 0]);
        }

        $count = UserNotification::where('UserID', $user->id)
            ->where('IsRead', false)
            ->where('IsHandled', false)
            ->count();

        return response()->json(['unread_count' => $count]);
    }

    public function list()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['notifications' => []]);
        }

        $notifications = UserNotification::where('UserID', $user->id)
            ->where('IsHandled', false)
            ->with(['request.targetDocument', 'request.requester'])
            ->latest()
            ->get();

        return response()->json(['notifications' => $notifications]);
    }

    public function approve(Request $request, $id)
    {
        $notification = UserNotification::findOrFail($id);
        $docReq = DocumentRequest::findOrFail($notification->RequestID);
        $user = Auth::user() ?? User::first();

        // Log approval
        RequestApprovalLog::create([
            'RequestID' => $docReq->RequestID,
            'StepOrder' => $docReq->CurrentStepOrder,
            'ApproverID' => $user->id,
            'Action' => 'APPROVED',
            'Notes' => $request->input('notes', 'Disetujui'),
            'ActionDate' => now(),
        ]);

        $notification->update(['IsRead' => true, 'IsHandled' => true]);

        if ($docReq->CurrentStepOrder == 1) {
            // Layer 1 (PIC) approved -> Move to Layer 2 (Section Head)
            $docReq->update([
                'CurrentStepOrder' => 2,
                'CurrentStatus' => 'PENDING_L2',
            ]);
            $this->createNotificationForNextStep($docReq, 2, 'Section Head');
        } elseif ($docReq->CurrentStepOrder == 2) {
            // Layer 2 (Section Head) approved -> Move to Layer 3 (Department Head)
            $docReq->update([
                'CurrentStepOrder' => 3,
                'CurrentStatus' => 'PENDING_L3',
            ]);
            $this->createNotificationForNextStep($docReq, 3, 'Department Head');
        } elseif ($docReq->CurrentStepOrder == 3) {
            // Layer 3 (Dept Head) approved -> FINAL EXECUTION
            $docReq->update([
                'CurrentStatus' => 'APPROVED',
            ]);

            $this->executeSystemTrigger($docReq);

            // Notify requester
            UserNotification::create([
                'UserID' => $docReq->RequestedBy,
                'RequestID' => $docReq->RequestID,
                'Title' => 'Pengajuan Disetujui Sepenuhnya',
                'Message' => "Permohonan {$docReq->RequestType} dokumen '{$docReq->Title}' telah disetujui oleh Department Head & sistem telah diperbarui.",
            ]);
        }

        return response()->json(['status' => 'success', 'message' => 'Permohonan berhasil disetujui.']);
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'notes' => 'required|string|min:3',
        ], [
            'notes.required' => 'Wajib mengisi alasan penolakan!',
        ]);

        $notification = UserNotification::findOrFail($id);
        $docReq = DocumentRequest::findOrFail($notification->RequestID);
        $user = Auth::user() ?? User::first();

        // Log rejection
        RequestApprovalLog::create([
            'RequestID' => $docReq->RequestID,
            'StepOrder' => $docReq->CurrentStepOrder,
            'ApproverID' => $user->id,
            'Action' => 'REJECTED',
            'Notes' => $request->notes,
            'ActionDate' => now(),
        ]);

        $notification->update(['IsRead' => true, 'IsHandled' => true]);

        // Stop workflow
        $docReq->update(['CurrentStatus' => 'REJECTED']);

        // Notify requester with rejection reason
        UserNotification::create([
            'UserID' => $docReq->RequestedBy,
            'RequestID' => $docReq->RequestID,
            'Title' => 'Pengajuan Ditolak',
            'Message' => "Permohonan {$docReq->RequestType} dokumen '{$docReq->Title}' ditolak pada Layer {$docReq->CurrentStepOrder}. Catatan: {$request->notes}",
        ]);

        return response()->json(['status' => 'success', 'message' => 'Permohonan telah ditolak.']);
    }

    private function createNotificationForNextStep(DocumentRequest $docReq, int $stepOrder, string $roleName)
    {
        $nextApprover = User::where('role', $roleName)
            ->where('department', $docReq->Department)
            ->first() ?? User::where('role', $roleName)->first();

        if ($nextApprover) {
            UserNotification::create([
                'UserID' => $nextApprover->id,
                'RequestID' => $docReq->RequestID,
                'Title' => "Approval Task Layer {$stepOrder} ({$roleName})",
                'Message' => "Permohonan {$docReq->RequestType} dokumen '{$docReq->Title}' membutuhkan persetujuan Layer {$stepOrder}.",
            ]);
        }
    }

    private function executeSystemTrigger(DocumentRequest $docReq)
    {
        if ($docReq->RequestType === 'REGISTRATION') {
            $masterPath = 'documents/' . $docReq->DocNumber . '.pdf';
            if ($docReq->TempFilePath && Storage::disk('local')->exists($docReq->TempFilePath)) {
                Storage::disk('local')->move($docReq->TempFilePath, $masterPath);
            } else {
                // Ensure master file placeholder exists
                Storage::disk('local')->put($masterPath, "%PDF-1.4 sample master document content");
            }

            Document::create([
                'DocNumber' => $docReq->DocNumber,
                'Title' => $docReq->Title,
                'CompanyID' => 1,
                'Department' => $docReq->Department,
                'Category' => $docReq->Category,
                'CurrentRevision' => 'Rev 00',
                'Status' => 'ACTIVE',
                'ConfidentialityLevel' => 'INTERNAL',
                'FilePath' => $masterPath,
            ]);
        } elseif ($docReq->RequestType === 'REVISION') {
            $oldDoc = Document::findOrFail($docReq->TargetDocumentID);
            $oldDoc->update(['Status' => 'SUPERSEDED']);

            // Calculate next revision string: Rev 00 -> Rev 01, Rev 01 -> Rev 02
            $currentNum = (int) filter_var($oldDoc->CurrentRevision, FILTER_SANITIZE_NUMBER_INT);
            $nextRev = sprintf('Rev %02d', $currentNum + 1);

            $masterPath = 'documents/' . $oldDoc->DocNumber . '_' . str_replace(' ', '_', $nextRev) . '.pdf';
            if ($docReq->TempFilePath && Storage::disk('local')->exists($docReq->TempFilePath)) {
                Storage::disk('local')->move($docReq->TempFilePath, $masterPath);
            } else {
                Storage::disk('local')->put($masterPath, "%PDF-1.4 revised document content");
            }

            Document::create([
                'DocNumber' => $oldDoc->DocNumber,
                'Title' => $oldDoc->Title,
                'CompanyID' => $oldDoc->CompanyID,
                'Department' => $oldDoc->Department,
                'Category' => $oldDoc->Category,
                'CurrentRevision' => $nextRev,
                'Status' => 'ACTIVE',
                'ConfidentialityLevel' => $oldDoc->ConfidentialityLevel,
                'FilePath' => $masterPath,
            ]);
        } elseif ($docReq->RequestType === 'OBSOLETE') {
            $doc = Document::findOrFail($docReq->TargetDocumentID);
            $doc->update(['Status' => 'OBSOLETE']);
        }
    }
}
