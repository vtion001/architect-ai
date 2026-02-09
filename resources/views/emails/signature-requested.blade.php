<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signature Request</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            border-radius: 8px;
            padding: 40px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            color: white;
        }
        h1 {
            color: #1a202c;
            font-size: 24px;
            margin: 0 0 10px 0;
        }
        .subtitle {
            color: #718096;
            font-size: 14px;
            margin: 0;
        }
        .content {
            margin: 30px 0;
        }
        .document-info {
            background: #f7fafc;
            border-left: 4px solid #667eea;
            padding: 15px 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .document-name {
            font-weight: 600;
            color: #2d3748;
            font-size: 16px;
        }
        .message-box {
            background: #fffbeb;
            border: 1px solid #fbbf24;
            padding: 15px 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .sign-button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            padding: 16px 40px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            text-align: center;
            margin: 20px 0;
            box-shadow: 0 4px 6px rgba(102, 126, 234, 0.4);
        }
        .sign-button:hover {
            box-shadow: 0 6px 8px rgba(102, 126, 234, 0.5);
        }
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        .alternative-link {
            color: #718096;
            font-size: 12px;
            margin-top: 15px;
            word-break: break-all;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            color: #a0aec0;
            font-size: 12px;
        }
        .security-note {
            background: #edf2f7;
            padding: 12px 16px;
            border-radius: 4px;
            font-size: 12px;
            color: #4a5568;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">✍️</div>
            <h1>Signature Request</h1>
            <p class="subtitle">You've been asked to sign a document</p>
        </div>

        <div class="content">
            <p>Hello {{ $signerName }},</p>
            
            <p>You have been requested to review and electronically sign the following document:</p>

            <div class="document-info">
                <div class="document-name">📄 {{ $documentName }}</div>
            </div>

            @if($message)
            <div class="message-box">
                <strong>Message from sender:</strong><br>
                {{ $message }}
            </div>
            @endif

            <div class="button-container">
                <a href="{{ $signUrl }}" class="sign-button">Review & Sign Document</a>
                
                <div class="alternative-link">
                    Or copy and paste this link in your browser:<br>
                    <a href="{{ $signUrl }}">{{ $signUrl }}</a>
                </div>
            </div>

            <div class="security-note">
                🔒 <strong>Security Note:</strong> This signature request link is unique to you and can only be used once. 
                Your signature will be encrypted and stored securely.
            </div>
        </div>

        <div class="footer">
            <p>This is an automated message from ArchitGrid Document Management System.</p>
            <p>If you believe you received this email in error, please disregard it.</p>
        </div>
    </div>
</body>
</html>
