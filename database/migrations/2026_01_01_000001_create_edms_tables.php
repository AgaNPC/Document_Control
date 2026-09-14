<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add role & department to users table if missing
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role', 50)->default('Karyawan')->after('email');
            }
            if (!Schema::hasColumn('users', 'department')) {
                $table->string('department', 100)->default('General')->after('role');
            }
        });

        Schema::create('documents', function (Blueprint $table) {
            $table->id('DocumentID');
            $table->string('DocNumber', 100)->unique();
            $table->string('Title', 255);
            $table->unsignedBigInteger('CompanyID')->default(1);
            $table->string('Department', 100);
            $table->string('Category', 50); // K3, Lingkungan, IT/Keamanan, Mutu
            $table->string('CurrentRevision', 10)->default('Rev 00');
            $table->string('Status', 20)->default('ACTIVE'); // ACTIVE, SUPERSEDED, OBSOLETE
            $table->string('ConfidentialityLevel', 20)->default('INTERNAL');
            $table->string('FilePath', 500); // Path file di shared storage
            $table->timestamps();
        });

        Schema::create('document_requests', function (Blueprint $table) {
            $table->id('RequestID');
            $table->string('RequestType', 20); // REGISTRATION, REVISION, OBSOLETE, PRINT
            $table->unsignedBigInteger('TargetDocumentID')->nullable();
            $table->string('DocNumber', 100)->nullable();
            $table->string('Title', 255);
            $table->string('Department', 100);
            $table->string('Category', 50)->nullable();
            $table->text('Reason');
            $table->string('TempFilePath', 500)->nullable();
            $table->integer('CopyCount')->nullable(); // Untuk tipe PRINT
            $table->string('PlacementLocation', 255)->nullable(); // Untuk lokasi cetak
            $table->integer('CurrentStepOrder')->default(1); // 1: PIC, 2: SecHead, 3: DeptHead
            $table->string('CurrentStatus', 30)->default('PENDING_L1'); // PENDING_L1, PENDING_L2, PENDING_L3, APPROVED, REJECTED
            $table->unsignedBigInteger('RequestedBy');
            $table->timestamps();
        });

        Schema::create('request_approval_logs', function (Blueprint $table) {
            $table->id('LogID');
            $table->unsignedBigInteger('RequestID');
            $table->integer('StepOrder');
            $table->unsignedBigInteger('ApproverID');
            $table->string('Action', 20); // APPROVED, REJECTED
            $table->text('Notes')->nullable();
            $table->timestamp('ActionDate')->useCurrent();
        });

        Schema::create('user_notifications', function (Blueprint $table) {
            $table->id('NotificationID');
            $table->unsignedBigInteger('UserID');
            $table->unsignedBigInteger('RequestID');
            $table->string('Title', 150);
            $table->text('Message');
            $table->boolean('IsRead')->default(false);
            $table->boolean('IsHandled')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_notifications');
        Schema::dropIfExists('request_approval_logs');
        Schema::dropIfExists('document_requests');
        Schema::dropIfExists('documents');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'department']);
        });
    }
};
