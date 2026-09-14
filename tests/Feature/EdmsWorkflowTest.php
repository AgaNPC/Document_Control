<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\DocumentRequest;
use App\Models\UserNotification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EdmsWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected $picUser;
    protected $secHeadUser;
    protected $deptHeadUser;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');

        $this->picUser = User::create([
            'name' => 'PIC Tester',
            'email' => 'pic@test.com',
            'password' => bcrypt('password'),
            'role' => 'PIC',
            'department' => 'K3LH',
        ]);

        $this->secHeadUser = User::create([
            'name' => 'SecHead Tester',
            'email' => 'sechead@test.com',
            'password' => bcrypt('password'),
            'role' => 'Section Head',
            'department' => 'K3LH',
        ]);

        $this->deptHeadUser = User::create([
            'name' => 'DeptHead Tester',
            'email' => 'depthead@test.com',
            'password' => bcrypt('password'),
            'role' => 'Department Head',
            'department' => 'K3LH',
        ]);
    }

    public function test_document_watermarked_stream()
    {
        Storage::disk('local')->put('documents/test.pdf', '%PDF-1.4 test sample content');

        $doc = Document::create([
            'DocNumber' => 'SOP-TEST-001',
            'Title' => 'Test Watermark Document',
            'CompanyID' => 1,
            'Department' => 'K3LH',
            'Category' => 'K3',
            'CurrentRevision' => 'Rev 00',
            'Status' => 'ACTIVE',
            'ConfidentialityLevel' => 'INTERNAL',
            'FilePath' => 'documents/test.pdf',
        ]);

        $response = $this->actingAs($this->picUser)->get(route('documents.stream', $doc->DocumentID));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_sequential_3_layer_approval_executes_registration()
    {
        $file = UploadedFile::fake()->create('draft.pdf', 100, 'application/pdf');

        // 1. Submit Registration Request
        $response = $this->actingAs($this->picUser)->post(route('lifecycle.registration'), [
            'doc_number' => 'SOP-NEW-100',
            'title' => 'New Procedure for Electrical Safety',
            'department' => 'K3LH',
            'category' => 'K3',
            'reason' => 'Mandatory annual safety update',
            'document_file' => $file,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('document_requests', [
            'DocNumber' => 'SOP-NEW-100',
            'CurrentStatus' => 'PENDING_L1',
            'CurrentStepOrder' => 1,
        ]);

        $docReq = DocumentRequest::where('DocNumber', 'SOP-NEW-100')->first();
        $l1Notif = UserNotification::where('RequestID', $docReq->RequestID)->first();

        // 2. Layer 1 Approval (PIC)
        $this->actingAs($this->picUser)
             ->post(route('api.notifications.approve', $l1Notif->NotificationID), ['notes' => 'L1 Approved'])
             ->assertStatus(200);

        $docReq->refresh();
        $this->assertEquals('PENDING_L2', $docReq->CurrentStatus);

        // 3. Layer 2 Approval (Section Head)
        $l2Notif = UserNotification::where('RequestID', $docReq->RequestID)->where('IsHandled', false)->first();
        $this->actingAs($this->secHeadUser)
             ->post(route('api.notifications.approve', $l2Notif->NotificationID), ['notes' => 'L2 Approved'])
             ->assertStatus(200);

        $docReq->refresh();
        $this->assertEquals('PENDING_L3', $docReq->CurrentStatus);

        // 4. Layer 3 Approval (Dept Head) -> Triggers automatic database state changes & master relocation
        $l3Notif = UserNotification::where('RequestID', $docReq->RequestID)->where('IsHandled', false)->first();
        $this->actingAs($this->deptHeadUser)
             ->post(route('api.notifications.approve', $l3Notif->NotificationID), ['notes' => 'L3 Approved'])
             ->assertStatus(200);

        $docReq->refresh();
        $this->assertEquals('APPROVED', $docReq->CurrentStatus);

        // Verify document is now ACTIVE in Repository with Rev 00
        $this->assertDatabaseHas('documents', [
            'DocNumber' => 'SOP-NEW-100',
            'Status' => 'ACTIVE',
            'CurrentRevision' => 'Rev 00',
        ]);
    }
}
