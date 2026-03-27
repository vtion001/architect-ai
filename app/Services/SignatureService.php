<?php

namespace App\Services;

use App\Models\Document;
use App\Models\SignatureRequest;
use App\Mail\SignatureRequested;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Exception;

class SignatureService
{
    protected string $apiKey;
    protected string $baseUrl = 'https://api.hellosign.com/v3';
    protected bool $testMode;

    public function __construct()
    {
        $this->apiKey = config('services.hellosign.api_key') ?? '';
        $this->testMode = config('services.hellosign.test_mode', true);
    }

    /**
     * Create a signature request for a document
     */
    public function createSignatureRequest(
        Document $document,
        string $signerName,
        string $signerEmail,
        ?string $subject = null,
        ?string $message = null
    ): SignatureRequest {
        // Generate unique signature token
        $token = Str::random(64);

        // Create signature request record
        $signatureRequest = SignatureRequest::create([
            'document_id' => $document->id,
            'user_id' => auth()->id(),
            'signer_name' => $signerName,
            'signer_email' => $signerEmail,
            'subject' => $subject ?? "Please sign: {$document->name}",
            'message' => $message,
            'signature_token' => $token,
            'status' => 'pending',
        ]);

        // Update document metadata
        $metadata = $document->metadata ?? [];
        $metadata['signature_status'] = 'pending';
        $metadata['signature_requested_at'] = now()->toIso8601String();
        $document->update(['metadata' => $metadata]);

        // Send signature request email (bypass HelloSign API for now)
        $this->sendSignatureEmail($signatureRequest);

        return $signatureRequest;
    }

    /**
     * Send signature request via HelloSign API (optional - requires API key)
     */
    protected function sendViaHelloSign(SignatureRequest $signatureRequest): ?string
    {
        if (empty($this->apiKey)) {
            return null; // Skip if no API key configured
        }

        try {
            $document = $signatureRequest->document;
            
            // Prepare document file path
            $filePath = storage_path('app/public/' . $document->path);
            
            if (!file_exists($filePath)) {
                throw new Exception('Document file not found');
            }

            $response = Http::withBasicAuth($this->apiKey, '')
                ->attach('file[0]', file_get_contents($filePath), $document->name)
                ->post("{$this->baseUrl}/signature_request/send", [
                    'test_mode' => $this->testMode ? 1 : 0,
                    'title' => $signatureRequest->subject,
                    'subject' => $signatureRequest->subject,
                    'message' => $signatureRequest->message,
                    'signers[0][name]' => $signatureRequest->signer_name,
                    'signers[0][email_address]' => $signatureRequest->signer_email,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $signatureRequestId = $data['signature_request']['signature_request_id'] ?? null;
                
                $signatureRequest->update([
                    'hellosign_signature_request_id' => $signatureRequestId,
                ]);

                return $signatureRequestId;
            }

            throw new Exception('HelloSign API error: ' . $response->body());
        } catch (Exception $e) {
            \Log::error('HelloSign API error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Send signature request email using Laravel Mail
     */
    protected function sendSignatureEmail(SignatureRequest $signatureRequest): void
    {
        try {
            Mail::to($signatureRequest->signer_email)
                ->send(new SignatureRequested($signatureRequest));

            $signatureRequest->markAsSent();
        } catch (Exception $e) {
            \Log::error('Failed to send signature request email: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get signature request by token
     */
    public function getByToken(string $token): ?SignatureRequest
    {
        return SignatureRequest::where('signature_token', $token)
            ->with('document')
            ->first();
    }

    /**
     * Mark signature request as viewed
     */
    public function markAsViewed(SignatureRequest $signatureRequest): void
    {
        $signatureRequest->markAsViewed();
    }

    /**
     * Process signature submission
     */
    public function processSignature(
        SignatureRequest $signatureRequest,
        array $signatureData
    ): void {
        $signatureRequest->markAsSigned($signatureData);
    }

    /**
     * Get all signature requests for a document
     */
    public function getDocumentSignatures(Document $document)
    {
        return SignatureRequest::where('document_id', $document->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
