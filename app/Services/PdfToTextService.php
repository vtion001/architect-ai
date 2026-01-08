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
                return $result->output();
            } else {
                Log::error("PdfToText Failed: " . $result->errorOutput());
                return '';
            }
        } catch (Exception $e) {
            Log::error("PdfToText Exception: " . $e->getMessage());
            return '';
        }
    }
}
