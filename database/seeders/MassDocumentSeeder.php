<?php

namespace Database\Seeders;

use App\Models\Document;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MassDocumentSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ensure master sample PDF exists in storage
        if (!Storage::disk('local')->exists('documents')) {
            Storage::disk('local')->makeDirectory('documents');
        }

        $samplePdfContent = "%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj 2 0 obj<</Type/Pages/Count 1/Kids[3 0 R]>>endobj 3 0 obj<</Type/Page/MediaBox[0 0 612 792]/Parent 2 0 R/Resources<<>>>>endobj\nxref\n0 4\n0000000000 65535 f\n0000000009 00000 n\n0000000052 00000 n\n0000000102 00000 n\ntrailer<</Size 4/Root 1 0 R>>\nstartxref\n178\n%%EOF";
        Storage::disk('local')->put('documents/sample_master.pdf', $samplePdfContent);

        // 2. Data generators
        $departments = ['K3LH', 'IT System', 'Quality Assurance', 'Maintenance', 'HR & General', 'Production', 'Supply Chain', 'Finance & Audit'];
        $categories = ['K3', 'Lingkungan', 'IT/Keamanan', 'Mutu'];
        $confidentialities = ['INTERNAL', 'RESTRICTED', 'CONFIDENTIAL'];
        $revisions = ['Rev 00', 'Rev 01', 'Rev 02', 'Rev 03'];

        $titleTemplates = [
            'SOP Keselamatan dan Kesehatan Kerja Operasional %s',
            'Instruksi Kerja Pengelolaan Limbah B3 dan Emisi %s',
            'Prosedur Keamanan Informasi dan Akses Data Server %s',
            'SOP Pengendalian Mutu dan Kalibrasi Alat %s',
            'Prosedur Pemeliharaan Rutin Mesin Utama %s',
            'SOP Penanganan Keadaan Darurat dan Kebakaran %s',
            'Prosedur Evaluasi Vendor dan Rantai Pasok %s',
            'SOP Manajemen Risiko dan Compliance Audit %s',
        ];

        $batchSize = 500;
        $totalRecords = 2500;
        $records = [];

        $this->command->info("Memulai pembuatan {$totalRecords} dummy data dokumen...");

        for ($i = 1; $i <= $totalRecords; $i++) {
            $dept = $departments[array_rand($departments)];
            $cat = $categories[array_rand($categories)];
            $conf = $confidentialities[array_rand($confidentialities)];
            $rev = $revisions[array_rand($revisions)];
            $template = $titleTemplates[array_rand($titleTemplates)];

            $docNumber = sprintf('SOP-%s-2026-%04d', strtoupper(substr($cat, 0, 3)), $i);
            $title = sprintf($template, "Bagian " . sprintf('%04d', $i));

            $records[] = [
                'DocNumber' => $docNumber,
                'Title' => $title,
                'CompanyID' => 1,
                'Department' => $dept,
                'Category' => $cat,
                'CurrentRevision' => $rev,
                'Status' => 'ACTIVE',
                'ConfidentialityLevel' => $conf,
                'FilePath' => 'documents/sample_master.pdf',
                'created_at' => now()->subDays(rand(1, 365)),
                'updated_at' => now(),
            ];

            if (count($records) >= $batchSize) {
                Document::insert($records);
                $records = [];
                $this->command->info("Tersimpan " . ($i) . " / {$totalRecords} dokumen...");
            }
        }

        if (count($records) > 0) {
            Document::insert($records);
        }

        $this->command->info("Berhasil menambahkan {$totalRecords} data dokumen ke database!");
    }
}
