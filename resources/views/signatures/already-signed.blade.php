<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Already Signed</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-gradient-to-br from-green-50 to-emerald-100 min-h-screen flex items-center justify-center px-4">
    <div class="max-w-2xl w-full">
        <div class="text-center mb-8">
            <div class="w-24 h-24 bg-gradient-to-br from-green-500 to-emerald-600 rounded-full mx-auto mb-6 flex items-center justify-center text-5xl shadow-2xl shadow-green-500/50 animate-bounce">
                ✓
            </div>
            <h1 class="text-5xl font-bold text-slate-800 mb-4">Document Signed!</h1>
            <p class="text-xl text-slate-600">This document has already been signed</p>
        </div>

        <div class="bg-white rounded-2xl shadow-2xl p-8 mb-6">
            <div class="flex items-start gap-4 mb-6 pb-6 border-b border-slate-200">
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i data-lucide="file-check" class="w-6 h-6 text-green-600"></i>
                </div>
                <div class="flex-1">
                    <h2 class="text-2xl font-bold text-slate-800 mb-1">{{ $signatureRequest->document->name }}</h2>
                    <p class="text-slate-600">Signed by {{ $signatureRequest->signer_name }}</p>
                </div>
            </div>

            <div class="space-y-4">
                <div class="flex items-center justify-between py-3 px-4 bg-slate-50 rounded-lg">
                    <span class="text-slate-600 font-medium">Status</span>
                    <span class="inline-flex items-center gap-2 px-3 py-1 bg-green-100 text-green-700 rounded-lg text-sm font-semibold">
                        <i data-lucide="check-circle" class="w-4 h-4"></i>
                        Signed
                    </span>
                </div>

                <div class="flex items-center justify-between py-3 px-4 bg-slate-50 rounded-lg">
                    <span class="text-slate-600 font-medium">Signed On</span>
                    <span class="text-slate-800 font-semibold">
                        {{ $signatureRequest->signed_at->format('F j, Y \a\t g:i A') }}
                    </span>
                </div>

                <div class="flex items-center justify-between py-3 px-4 bg-slate-50 rounded-lg">
                    <span class="text-slate-600 font-medium">Signer Email</span>
                    <span class="text-slate-800 font-semibold">{{ $signatureRequest->signer_email }}</span>
                </div>

                @if($signatureRequest->signature_data)
                <div class="mt-6 p-6 bg-slate-50 rounded-xl border-2 border-slate-200">
                    <h3 class="text-sm font-semibold text-slate-600 mb-3">Signature</h3>
                    @if($signatureRequest->signature_data['type'] === 'drawn')
                        <img src="{{ $signatureRequest->signature_data['data'] }}" 
                             alt="Signature" 
                             class="max-w-xs mx-auto bg-white border border-slate-300 rounded-lg p-2">
                    @else
                        <div class="text-4xl font-script text-center py-4" style="font-family: 'Brush Script MT', cursive;">
                            {{ $signatureRequest->signature_data['data'] }}
                        </div>
                    @endif
                </div>
                @endif
            </div>
        </div>

        <div class="text-center">
            <p class="text-slate-600 mb-4">
                This signature request has been completed. You may close this window.
            </p>
            <div class="inline-flex items-center gap-2 text-green-600 font-semibold">
                <i data-lucide="shield-check" class="w-5 h-5"></i>
                <span>Verified & Secured by ArchitGrid</span>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
