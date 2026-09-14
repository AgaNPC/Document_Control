<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Document;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Users for 3-layer workflow & RBAC
        $users = [
            [
                'name' => 'Budi Santoso',
                'email' => 'budi@company.com',
                'password' => Hash::make('password'),
                'role' => 'Karyawan',
                'department' => 'K3LH',
            ],
            [
                'name' => 'Ahmad PIC (L1)',
                'email' => 'ahmad.pic@company.com',
                'password' => Hash::make('password'),
                'role' => 'PIC',
                'department' => 'K3LH',
            ],
            [
                'name' => 'Dedi Section Head (L2)',
                'email' => 'dedi.sechead@company.com',
                'password' => Hash::make('password'),
                'role' => 'Section Head',
                'department' => 'K3LH',
            ],
            [
                'name' => 'Eko Department Head (L3)',
                'email' => 'eko.depthead@company.com',
                'password' => Hash::make('password'),
                'role' => 'Department Head',
                'department' => 'K3LH',
            ],
            [
                'name' => 'Rian IT Administrator',
                'email' => 'rian.it@company.com',
                'password' => Hash::make('password'),
                'role' => 'IT Admin',
                'department' => 'IT System',
            ],
        ];

        foreach ($users as $u) {
            User::updateOrCreate(['email' => $u['email']], $u);
        }

        // 2. Ensure storage folders and sample PDF files exist
        if (!Storage::disk('local')->exists('documents')) {
            Storage::disk('local')->makeDirectory('documents');
        }

        // Create sample PDF files using FPDF fallback format
        $samplePdfContent = "%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj 2 0 obj<</Type/Pages/Count 1/Kids[3 0 R]>>endobj 3 0 obj<</Type/Page/MediaBox[0 0 612 792]/Parent 2 0 R/Resources<<>>>>endobj\nxref\n0 4\n0000000000 65535 f\n0000000009 00000 n\n0000000052 00000 n\n0000000102 00000 n\ntrailer<</Size 4/Root 1 0 R>>\nstartxref\n178\n%%EOF";

        $initialDocs = [
            [
                'DocNumber' => 'SOP-K3-2026-001',
                'Title' => 'SOP Keselamatan Kerja Listrik Tegangan Tinggi',
                'CompanyID' => 1,
                'Department' => 'K3LH',
                'Category' => 'K3',
                'CurrentRevision' => 'Rev 00',
                'Status' => 'ACTIVE',
                'ConfidentialityLevel' => 'INTERNAL',
                'FilePath' => 'documents/SOP-K3-2026-001.pdf',
            ],
            [
                'DocNumber' => 'SOP-ENV-2026-002',
                'Title' => 'SOP Pengelolaan Limbah B3 dan Emisi Gas Pabrik (ISO 14001)',
                'CompanyID' => 1,
                'Department' => 'K3LH',
                'Category' => 'Lingkungan',
                'CurrentRevision' => 'Rev 00',
                'Status' => 'ACTIVE',
                'ConfidentialityLevel' => 'INTERNAL',
                'FilePath' => 'documents/SOP-ENV-2026-002.pdf',
            ],
            [
                'DocNumber' => 'SOP-IT-2026-010',
                'Title' => 'SOP Keamanan Informasi & Akses Kontrol Server (ISO 27001)',
                'CompanyID' => 1,
                'Department' => 'IT System',
                'Category' => 'IT/Keamanan',
                'CurrentRevision' => 'Rev 01',
                'Status' => 'ACTIVE',
                'ConfidentialityLevel' => 'RESTRICTED',
                'FilePath' => 'documents/SOP-IT-2026-010.pdf',
            ],
            [
                'DocNumber' => 'SOP-QA-2026-005',
                'Title' => 'SOP Pengendalian Mutu & Kalibrasi Alat Ukur Produksi',
                'CompanyID' => 1,
                'Department' => 'Quality Assurance',
                'Category' => 'Mutu',
                'CurrentRevision' => 'Rev 00',
                'Status' => 'ACTIVE',
                'ConfidentialityLevel' => 'INTERNAL',
                'FilePath' => 'documents/SOP-QA-2026-005.pdf',
            ],
        ];

        foreach ($initialDocs as $doc) {
            Storage::disk('local')->put($doc['FilePath'], $samplePdfContent);
            Document::updateOrCreate(['DocNumber' => $doc['DocNumber']], $doc);
        }
    }
}
