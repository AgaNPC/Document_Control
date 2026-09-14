<?php

namespace App\Services;

use setasign\Fpdi\Fpdi;
use Fpdf\Fpdf;
use Illuminate\Support\Facades\Storage;

class WatermarkService
{
    /**
     * Generate dynamic watermark on PDF file and output string stream
     */
    public function generateDynamicWatermark(string $sourcePath, string $watermarkText): string
    {
        $realPath = Storage::disk('local')->path($sourcePath);

        if (!file_exists($realPath)) {
            // Fallback: generate a dummy PDF with the watermark
            return $this->createFallbackPdf("Document content placeholder.\n" . $watermarkText);
        }

        try {
            $pdf = new Fpdi();
            $pageCount = $pdf->setSourceFile($realPath);

            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $templateId = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($templateId);

                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($templateId);

                // Add diagonal semi-transparent style watermark text
                $pdf->SetFont('Helvetica', 'B', 10);
                $pdf->SetTextColor(180, 180, 180); // Light gray
                $pdf->SetXY(10, 5);
                $pdf->Cell(0, 10, $watermarkText, 0, 0, 'C');

                // Diagonal watermark in center
                $pdf->SetFont('Helvetica', 'B', 14);
                $pdf->SetTextColor(200, 200, 200);
                $pdf->SetXY(15, $size['height'] / 2);
                $pdf->Cell(0, 10, $watermarkText, 0, 0, 'C');
            }

            return $pdf->Output('S');
        } catch (\Exception $e) {
            return $this->createFallbackPdf("EDMS Watermarked Document\n" . $watermarkText);
        }
    }

    /**
     * Generate Controlled Copy Stamped PDF
     */
    public function generateControlledCopy(string $sourcePath, string $stampText): string
    {
        $realPath = Storage::disk('local')->path($sourcePath);

        if (!file_exists($realPath)) {
            return $this->createFallbackPdf("CONTROLLED COPY STAMPED DOCUMENT\n" . $stampText);
        }

        try {
            $pdf = new Fpdi();
            $pageCount = $pdf->setSourceFile($realPath);

            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $templateId = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($templateId);

                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($templateId);

                // Add Red Controlled Copy Header Box
                $pdf->SetFont('Helvetica', 'B', 9);
                $pdf->SetFillColor(254, 226, 226); // Light red box
                $pdf->SetDrawColor(220, 38, 38); // Dark red border
                $pdf->SetTextColor(153, 27, 27); // Red text
                
                $pdf->Rect(5, 5, $size['width'] - 10, 12, 'DF');
                $pdf->SetXY(5, 6);
                $pdf->MultiCell($size['width'] - 10, 4, $stampText, 0, 'C');
            }

            return $pdf->Output('S');
        } catch (\Exception $e) {
            return $this->createFallbackPdf($stampText);
        }
    }

    /**
     * Create a basic PDF fallback if FPDI fails or sample missing
     */
    private function createFallbackPdf(string $text): string
    {
        $pdf = new \FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Helvetica', 'B', 12);
        $pdf->SetTextColor(50, 50, 50);
        $pdf->MultiCell(0, 10, $text);
        return $pdf->Output('S');
    }
}
