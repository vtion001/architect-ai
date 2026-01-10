<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;

class PdfToTextService
{
    public function extract(string $path): string
    {
        if (!file_exists($path)) {
            Log::error("PdfToText: File not found at $path");
            return '';
        }

        try {
            // Check if pdftotext exists
            $binaryPath = '/usr/bin/pdftotext';
            
            // Execute pdftotext -layout <file> -
            // -layout maintains layout somewhat
            // - outputs to stdout
            $result = Process::run("{$binaryPath} -layout " . escapeshellarg($path) . " -");

            if ($result->successful()) {
                return $this->sanitizeUtf8($result->output());
            } else {
                Log::error("PdfToText Failed: " . $result->errorOutput());
                return '';
            }
        } catch (Exception $e) {
            Log::error("PdfToText Exception: " . $e->getMessage());
            return '';
        }
    }

    /**
     * Sanitize text to ensure valid UTF-8 encoding.
     * Removes or replaces malformed UTF-8 characters that would cause json_encode to fail.
     */
    private function sanitizeUtf8(string $text): string
    {
        // First, try to convert from detected encoding to UTF-8
        $encoding = mb_detect_encoding($text, ['UTF-8', 'ISO-8859-1', 'Windows-1252', 'ASCII'], true);
        
        if ($encoding && $encoding !== 'UTF-8') {
            $text = mb_convert_encoding($text, 'UTF-8', $encoding);
        }

        // Remove any remaining invalid UTF-8 sequences
        // Using regex to strip invalid byte sequences
        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $text);
        
        // Use iconv as a fallback to transliterate or ignore invalid characters
        $cleaned = @iconv('UTF-8', 'UTF-8//TRANSLIT//IGNORE', $text);
        
        if ($cleaned === false) {
            // Last resort: manually filter out non-UTF8 characters
            $cleaned = mb_convert_encoding($text, 'UTF-8', 'UTF-8');
        }

        // Final check: ensure json_encode will work
        if (json_encode($cleaned) === false) {
            // Strip all non-printable and non-ASCII characters as fallback
            $cleaned = preg_replace('/[^\x20-\x7E\n\r\t]/', '', $text);
        }

        return $cleaned ?: '';
    }
}
