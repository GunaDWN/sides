<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class TemplatePreviewService
{
    /**
     * Generate preview images from a template file (PDF or DOCX).
     * Returns array of generated image paths relative to storage.
     *
     * @return array{pages: string[], page_count: int, page_width_mm: float, page_height_mm: float}
     */
    public function generatePreview(string $templatePath): array
    {
        $fullPath = Storage::disk('local')->path($templatePath);
        if (!file_exists($fullPath)) {
            throw new \Exception("Template file not found: {$templatePath}");
        }

        $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));

        // For DOCX, convert to PDF first using LibreOffice
        if (in_array($extension, ['doc', 'docx'])) {
            $fullPath = $this->convertDocxToPdf($fullPath);
            $extension = 'pdf';
        }

        if ($extension === 'pdf') {
            return $this->generatePdfPreview($fullPath, $templatePath);
        }

        throw new \Exception("Unsupported template format: {$extension}");
    }

    /**
     * Convert DOCX to PDF using LibreOffice headless.
     */
    private function convertDocxToPdf(string $docxPath): string
    {
        $outputDir = storage_path('app/private/template-previews');
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        $command = sprintf(
            'libreoffice --headless --convert-to pdf --outdir %s %s 2>&1',
            escapeshellarg($outputDir),
            escapeshellarg($docxPath)
        );

        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            throw new \Exception('Failed to convert DOCX to PDF: ' . implode("\n", $output));
        }

        $pdfFilename = pathinfo($docxPath, PATHINFO_FILENAME) . '.pdf';
        $pdfPath = $outputDir . '/' . $pdfFilename;

        if (!file_exists($pdfPath)) {
            throw new \Exception('PDF conversion output not found.');
        }

        return $pdfPath;
    }

    /**
     * Generate preview images from PDF using pdftoppm.
     */
    private function generatePdfPreview(string $pdfPath, string $originalTemplatePath): array
    {
        // Create unique output directory based on template path hash
        $hash = md5($originalTemplatePath . filemtime($pdfPath));
        $outputDir = storage_path('app/private/template-previews/' . $hash);

        // Check if already generated
        if (is_dir($outputDir) && count(glob($outputDir . '/*.png')) > 0) {
            return $this->collectPreviewResults($outputDir, $pdfPath);
        }

        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        // Use pdftoppm to convert PDF pages to PNG images (200 DPI for good quality)
        $command = sprintf(
            'pdftoppm -png -r 200 %s %s/page 2>&1',
            escapeshellarg($pdfPath),
            escapeshellarg($outputDir)
        );

        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            throw new \Exception('Failed to generate PDF preview: ' . implode("\n", $output));
        }

        return $this->collectPreviewResults($outputDir, $pdfPath);
    }

    /**
     * Collect preview image paths and page dimensions.
     */
    private function collectPreviewResults(string $outputDir, string $pdfPath): array
    {
        $pages = glob($outputDir . '/*.png');
        sort($pages); // Ensure page order

        // Get PDF page dimensions using pdfinfo
        $pageWidthMm = 210.0; // Default A4
        $pageHeightMm = 297.0;

        $command = sprintf('pdfinfo %s 2>&1', escapeshellarg($pdfPath));
        exec($command, $output, $returnCode);

        if ($returnCode === 0) {
            foreach ($output as $line) {
                if (preg_match('/Page size:\s+([\d.]+)\s+x\s+([\d.]+)\s+pts/', $line, $matches)) {
                    // Convert points to mm (1 pt = 0.3528 mm)
                    $pageWidthMm = round((float)$matches[1] * 0.3528, 2);
                    $pageHeightMm = round((float)$matches[2] * 0.3528, 2);
                    break;
                }
            }
        }

        // Convert absolute paths to relative paths for the route
        $relativePaths = array_map(function ($path) {
            return basename(dirname($path)) . '/' . basename($path);
        }, $pages);

        return [
            'pages' => $relativePaths,
            'page_count' => count($pages),
            'page_width_mm' => $pageWidthMm,
            'page_height_mm' => $pageHeightMm,
        ];
    }

    /**
     * Get the full path of a preview image.
     */
    public function getPreviewImagePath(string $relativePath): ?string
    {
        $fullPath = storage_path('app/private/template-previews/' . $relativePath);
        return file_exists($fullPath) ? $fullPath : null;
    }

    /**
     * Clear preview cache for a template.
     */
    public function clearPreview(string $templatePath): void
    {
        $fullPath = Storage::disk('local')->path($templatePath);
        if (!file_exists($fullPath)) {
            return;
        }

        $hash = md5($templatePath . filemtime($fullPath));
        $outputDir = storage_path('app/private/template-previews/' . $hash);

        if (is_dir($outputDir)) {
            array_map('unlink', glob($outputDir . '/*'));
            rmdir($outputDir);
        }
    }
}
