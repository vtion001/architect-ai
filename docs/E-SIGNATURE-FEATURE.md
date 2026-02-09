# E-Signature Feature Documentation

## Overview
The E-Signature feature allows users to request electronic signatures on documents via email. Recipients receive a secure link to review and sign documents electronically.

## Architecture

### Database Schema

**Table: `signature_requests`**
- `id` - Primary key
- `document_id` - UUID reference to documents table
- `user_id` - UUID of user requesting signature
- `signer_name` - Full name of the person signing
- `signer_email` - Email address of the signer
- `subject` - Email subject line (optional)
- `message` - Custom message to include (optional)
- `hellosign_signature_request_id` - External API reference (optional)
- `status` - Current status: pending, sent, viewed, signed, declined
- `signature_token` - Unique 64-character token for secure access
- `sent_at` - Timestamp when email was sent
- `viewed_at` - Timestamp when signer viewed the document
- `signed_at` - Timestamp when document was signed
- `signature_data` - JSON field storing signature image/text and metadata
- `timestamps` - Created/updated timestamps

### Models

**SignatureRequest** (`app/Models/SignatureRequest.php`)
- Relationships: `belongsTo` Document and User
- Helper methods:
  - `markAsSent()` - Updates status to sent
  - `markAsViewed()` - Updates status to viewed (first time only)
  - `markAsSigned()` - Updates status to signed and updates document metadata
  - `isPending()` - Check if pending
  - `isSigned()` - Check if signed

### Services

**SignatureService** (`app/Services/SignatureService.php`)

Methods:
- `createSignatureRequest()` - Create new signature request and send email
- `sendViaHelloSign()` - Optional: Send via HelloSign API
- `sendSignatureEmail()` - Send email using Laravel Mail
- `getByToken()` - Retrieve signature request by token
- `markAsViewed()` - Mark request as viewed
- `processSignature()` - Process submitted signature
- `getDocumentSignatures()` - Get all signatures for a document

### Controllers

**SignatureRequestController** (`app/Http/Controllers/SignatureRequestController.php`)

Routes:
- `POST /documents/{document}/request-signature` - Send signature request
- `GET /documents/{document}/signatures` - List all signatures for document
- `GET /signatures/{token}/sign` - Show signature form (public)
- `POST /signatures/{token}/submit` - Submit signature (public)

### Mail

**SignatureRequested** (`app/Mail/SignatureRequested.php`)
- Mailable class for signature request emails
- Template: `resources/views/emails/signature-requested.blade.php`

## API Integration Options

### Option 1: Laravel Mail (Current Implementation)
Uses Laravel's built-in mail system. Configure in `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=mailhog  # For local testing
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

For production, use services like:
- **SendGrid**: `MAIL_MAILER=sendgrid`
- **Mailgun**: `MAIL_MAILER=mailgun`
- **AWS SES**: `MAIL_MAILER=ses`

### Option 2: HelloSign/Dropbox Sign API (Optional)

To enable HelloSign integration:

1. Sign up at https://www.hellosign.com/
2. Get API key from dashboard
3. Add to `.env`:

```env
HELLOSIGN_API_KEY=your_api_key_here
HELLOSIGN_CLIENT_ID=your_client_id
HELLOSIGN_TEST_MODE=true  # Set to false in production
```

HelloSign provides:
- Advanced PDF annotation
- Legally binding signatures with audit trails
- Built-in reminder emails
- Mobile-optimized signing experience
- Template management

## Usage

### 1. Request Signature (UI)

From document viewer, click "Request Signature" button:
1. Opens modal with form
2. Enter signer name and email
3. Add optional subject and message
4. Click "Send Request"

### 2. Request Signature (API)

```javascript
POST /documents/{documentId}/request-signature

Body:
{
  "signer_name": "John Doe",
  "signer_email": "john@example.com",
  "subject": "Please sign this document",
  "message": "Please review and sign at your earliest convenience"
}

Response:
{
  "success": true,
  "message": "Signature request sent successfully",
  "data": {
    "signature_request_id": 123,
    "status": "sent"
  }
}
```

### 3. Signing Flow

1. Recipient receives email with secure link
2. Clicks "Review & Sign Document"
3. Views document preview
4. Chooses signature method:
   - **Draw**: Use mouse/touchscreen to draw signature
   - **Type**: Type full name in script font
5. Agrees to terms
6. Clicks "Sign Document"
7. Receives confirmation

### 4. View Signatures

```javascript
GET /documents/{documentId}/signatures

Response:
{
  "success": true,
  "data": [
    {
      "id": 123,
      "signer_name": "John Doe",
      "signer_email": "john@example.com",
      "status": "signed",
      "signed_at": "2024-01-15T10:30:00Z",
      "signature_data": {...}
    }
  ]
}
```

## Security Features

1. **Unique Tokens**: Each signature request has a unique 64-character token
2. **One-Time Use**: Tokens become invalid after signing
3. **Audit Trail**: Full tracking of sent/viewed/signed timestamps
4. **IP Logging**: Stores IP address and user agent on signature
5. **Metadata**: Encrypted signature data with tamper detection

## Document Status Updates

When a signature request is created or completed, the document's metadata is automatically updated:

```json
{
  "signature_status": "pending|signed",
  "signature_requested_at": "2024-01-15T10:00:00Z",
  "signed_at": "2024-01-15T10:30:00Z",
  "signed_by": "john@example.com"
}
```

## UI Components

### Signature Status Badges

Three states displayed throughout the UI:
- **Unsigned** (slate): Default state, shows "Request Signature" button
- **Pending** (yellow): Shows "View Status" button and clock icon
- **Signed** (green): Shows "View Signatures" button and check icon

### Modal Component
- Alpine.js powered
- Form validation
- Loading states
- CSRF protection
- Responsive design

### Signature Form
- Canvas-based drawing (signature_pad library)
- Typed signature with script font
- Real-time preview
- Consent checkbox
- Mobile-friendly

## Customization

### Email Template

Edit `resources/views/emails/signature-requested.blade.php` to customize:
- Logo and branding
- Colors and styling
- Message content
- Footer information

### Signature Form

Edit `resources/views/signatures/sign.blade.php` to:
- Add file upload option
- Customize signature styles
- Add additional fields (title, company, etc.)
- Change layout and design

### Status Colors

Update badge colors in `resources/views/documents/partials/header.blade.php`:

```php
$statusConfig = [
    'unsigned' => ['color' => 'slate', 'icon' => 'file-text', 'text' => 'Unsigned'],
    'pending' => ['color' => 'yellow', 'icon' => 'clock', 'text' => 'Pending Signature'],
    'signed' => ['color' => 'green', 'icon' => 'check-circle', 'text' => 'Signed']
];
```

## Testing

### Local Testing with Mailhog

Mailhog captures all outgoing emails for testing:

1. Access Mailhog at http://localhost:8025
2. Request a signature
3. Check Mailhog inbox for email
4. Click signature link to test signing flow

### Manual Testing Checklist

- [ ] Create signature request from document viewer
- [ ] Verify email sent to correct recipient
- [ ] Click signature link and verify document loads
- [ ] Test drawing signature with mouse
- [ ] Test typing signature
- [ ] Verify consent checkbox required
- [ ] Submit signature successfully
- [ ] Verify "already signed" page on second visit
- [ ] Check document status updates to "signed"
- [ ] Verify signature data stored correctly

## Troubleshooting

### Email Not Sending

1. Check `.env` mail configuration
2. Verify SMTP credentials
3. Check Laravel logs: `storage/logs/laravel.log`
4. Test mail config: `php artisan tinker` → `Mail::raw('Test', fn($m) => $m->to('test@example.com')->subject('Test'))`

### Signature Form Not Loading

1. Clear cache: `php artisan optimize:clear`
2. Check route exists: `php artisan route:list | grep signatures`
3. Verify token is valid in database
4. Check browser console for JavaScript errors

### Status Not Updating

1. Verify document metadata column exists
2. Check SignatureRequest model relationships
3. Clear opcache: `docker-compose restart app`

## Future Enhancements

- [ ] Multiple signers per document
- [ ] Sequential signing workflow
- [ ] Signature templates
- [ ] Bulk signature requests
- [ ] Document expiration dates
- [ ] Reminder emails
- [ ] Signature certificate generation
- [ ] Custom signature fields/placement
- [ ] Integration with Azure AD for authentication
- [ ] Webhook notifications

## Support

For issues or questions:
- Check Laravel logs: `storage/logs/laravel.log`
- Review queue logs if using background jobs
- Consult HelloSign API docs: https://developers.hellosign.com/
- Email support: admin@archit-ai.io
