<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\SignatureRequest;
use App\Services\SignatureService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SignatureRequestController extends Controller
{
    public function __construct(
        protected SignatureService $signatureService
    ) {}

    /**
     * Send a signature request
     */
    public function send(Request $request, Document $document)
    {
        $validator = Validator::make($request->all(), [
            'signer_name' => 'required|string|max:255',
            'signer_email' => 'required|email|max:255',
            'subject' => 'nullable|string|max:255',
            'message' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $signatureRequest = $this->signatureService->createSignatureRequest(
                $document,
                $request->input('signer_name'),
                $request->input('signer_email'),
                $request->input('subject'),
                $request->input('message')
            );

            return response()->json([
                'success' => true,
                'message' => 'Signature request sent successfully',
                'data' => [
                    'signature_request_id' => $signatureRequest->id,
                    'status' => $signatureRequest->status,
                ],
            ]);
        } catch (\Exception $e) {
            \Log::error('Signature request failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to send signature request: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show signature form
     */
    public function show(string $token)
    {
        $signatureRequest = $this->signatureService->getByToken($token);

        if (!$signatureRequest) {
            abort(404, 'Signature request not found');
        }

        if ($signatureRequest->isSigned()) {
            return view('signatures.already-signed', compact('signatureRequest'));
        }

        // Mark as viewed
        $this->signatureService->markAsViewed($signatureRequest);

        return view('signatures.sign', compact('signatureRequest'));
    }

    /**
     * Submit signature
     */
    public function submit(Request $request, string $token)
    {
        $signatureRequest = $this->signatureService->getByToken($token);

        if (!$signatureRequest) {
            return response()->json([
                'success' => false,
                'message' => 'Signature request not found',
            ], 404);
        }

        if ($signatureRequest->isSigned()) {
            return response()->json([
                'success' => false,
                'message' => 'This document has already been signed',
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'signature_type' => 'required|in:drawn,typed,uploaded',
            'signature_data' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $this->signatureService->processSignature(
                $signatureRequest,
                [
                    'type' => $request->input('signature_type'),
                    'data' => $request->input('signature_data'),
                    'signed_at' => now()->toIso8601String(),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Document signed successfully',
            ]);
        } catch (\Exception $e) {
            \Log::error('Signature submission failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to process signature: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get document signatures
     */
    public function index(Document $document)
    {
        $signatures = $this->signatureService->getDocumentSignatures($document);

        return response()->json([
            'success' => true,
            'data' => $signatures,
        ]);
    }
}
