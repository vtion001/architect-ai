<?php

namespace App\Mail;

use App\Models\SignatureRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SignatureRequested extends Mailable
{
    use Queueable, SerializesModels;

    public SignatureRequest $signatureRequest;

    public string $signUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(SignatureRequest $signatureRequest)
    {
        $this->signatureRequest = $signatureRequest;
        $this->signUrl = route('signatures.sign', ['token' => $signatureRequest->signature_token]);
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject($this->signatureRequest->subject ?? 'Please sign this document')
            ->view('emails.signature-requested')
            ->with([
                'signerName' => $this->signatureRequest->signer_name,
                'documentName' => $this->signatureRequest->document->name,
                'message' => $this->signatureRequest->message,
                'signUrl' => $this->signUrl,
            ]);
    }
}
