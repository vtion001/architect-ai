<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sign Document - {{ $signatureRequest->document->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
</head>
<body class="bg-gradient-to-br from-slate-50 to-slate-100 min-h-screen">
    <div class="container mx-auto px-4 py-12 max-w-4xl">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl mx-auto mb-4 flex items-center justify-center text-4xl">
                ✍️
            </div>
            <h1 class="text-4xl font-bold text-slate-800 mb-2">Electronic Signature</h1>
            <p class="text-slate-600">Review and sign the document below</p>
        </div>

        <!-- Document Info Card -->
        <div class="bg-white rounded-2xl shadow-xl p-8 mb-6">
            <div class="flex items-start gap-4 mb-6 pb-6 border-b border-slate-200">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i data-lucide="file-text" class="w-6 h-6 text-blue-600"></i>
                </div>
                <div class="flex-1">
                    <h2 class="text-2xl font-bold text-slate-800 mb-1">{{ $signatureRequest->document->name }}</h2>
                    <p class="text-slate-600">Requested by {{ $signatureRequest->user->name ?? 'System' }}</p>
                </div>
                <div class="text-right">
                    <span class="inline-flex items-center gap-2 px-3 py-1 bg-yellow-100 text-yellow-700 rounded-lg text-sm font-semibold">
                        <i data-lucide="clock" class="w-4 h-4"></i>
                        Pending
                    </span>
                </div>
            </div>

            @if($signatureRequest->message)
            <div class="bg-amber-50 border-l-4 border-amber-400 p-4 mb-6 rounded">
                <p class="text-sm font-semibold text-amber-800 mb-1">Message from sender:</p>
                <p class="text-slate-700">{{ $signatureRequest->message }}</p>
            </div>
            @endif

            <!-- Document Preview -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-slate-800 mb-3">Document Preview</h3>
                <div class="bg-slate-50 border-2 border-slate-200 rounded-xl p-6 max-h-96 overflow-y-auto">
                    <iframe 
                        src="{{ route('documents.show', $signatureRequest->document) }}" 
                        class="w-full h-[400px] border-0 rounded-lg"
                        sandbox="allow-same-origin">
                    </iframe>
                </div>
            </div>
        </div>

        <!-- Signature Form -->
        <div class="bg-white rounded-2xl shadow-xl p-8" 
             x-data="{
                 signatureType: 'drawn',
                 typedSignature: '',
                 signaturePad: null,
                 loading: false,
                 
                 init() {
                     const canvas = this.$refs.signatureCanvas;
                     this.signaturePad = new SignaturePad(canvas, {
                         backgroundColor: 'rgb(255, 255, 255)',
                         penColor: 'rgb(0, 0, 0)'
                     });
                     this.resizeCanvas();
                     window.addEventListener('resize', () => this.resizeCanvas());
                 },
                 
                 resizeCanvas() {
                     const canvas = this.$refs.signatureCanvas;
                     const ratio = Math.max(window.devicePixelRatio || 1, 1);
                     canvas.width = canvas.offsetWidth * ratio;
                     canvas.height = canvas.offsetHeight * ratio;
                     canvas.getContext('2d').scale(ratio, ratio);
                     this.signaturePad.clear();
                 },
                 
                 clearSignature() {
                     this.signaturePad.clear();
                 },
                 
                 async submitSignature() {
                     let signatureData = '';
                     
                     if (this.signatureType === 'drawn') {
                         if (this.signaturePad.isEmpty()) {
                             alert('Please provide your signature');
                             return;
                         }
                         signatureData = this.signaturePad.toDataURL();
                     } else if (this.signatureType === 'typed') {
                         if (!this.typedSignature.trim()) {
                             alert('Please type your name');
                             return;
                         }
                         signatureData = this.typedSignature;
                     }
                     
                     this.loading = true;
                     
                     try {
                         const response = await fetch('{{ route('signatures.submit', $signatureRequest->signature_token) }}', {
                             method: 'POST',
                             headers: {
                                 'Content-Type': 'application/json',
                                 'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').content
                             },
                             body: JSON.stringify({
                                 signature_type: this.signatureType,
                                 signature_data: signatureData
                             })
                         });
                         
                         const data = await response.json();
                         
                         if (data.success) {
                             window.location.href = '{{ route('signatures.sign', $signatureRequest->signature_token) }}';
                         } else {
                             alert('Error: ' + (data.message || 'Failed to submit signature'));
                         }
                     } catch (error) {
                         alert('Error: ' + error.message);
                     } finally {
                         this.loading = false;
                     }
                 }
             }">
            
            <h3 class="text-2xl font-bold text-slate-800 mb-6">Your Signature</h3>

            <!-- Signature Type Selector -->
            <div class="flex gap-3 mb-6">
                <button @click="signatureType = 'drawn'" 
                        :class="signatureType === 'drawn' ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-700'"
                        class="flex-1 py-3 px-6 rounded-xl font-semibold transition-all flex items-center justify-center gap-2">
                    <i data-lucide="pen-tool" class="w-5 h-5"></i>
                    Draw
                </button>
                <button @click="signatureType = 'typed'" 
                        :class="signatureType === 'typed' ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-700'"
                        class="flex-1 py-3 px-6 rounded-xl font-semibold transition-all flex items-center justify-center gap-2">
                    <i data-lucide="type" class="w-5 h-5"></i>
                    Type
                </button>
            </div>

            <!-- Draw Signature -->
            <div x-show="signatureType === 'drawn'" class="mb-6">
                <div class="border-2 border-slate-300 rounded-xl bg-white relative">
                    <canvas x-ref="signatureCanvas" 
                            class="w-full h-48 rounded-xl cursor-crosshair"></canvas>
                    <button @click="clearSignature" 
                            class="absolute top-2 right-2 bg-white border border-slate-300 rounded-lg px-3 py-1 text-sm text-slate-600 hover:bg-slate-50">
                        Clear
                    </button>
                </div>
                <p class="text-sm text-slate-500 mt-2">Sign above using your mouse or touchscreen</p>
            </div>

            <!-- Type Signature -->
            <div x-show="signatureType === 'typed'" class="mb-6">
                <input type="text" 
                       x-model="typedSignature"
                       placeholder="Type your full name"
                       class="w-full px-6 py-4 border-2 border-slate-300 rounded-xl text-2xl font-script focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none"
                       style="font-family: 'Brush Script MT', cursive;">
                <p class="text-sm text-slate-500 mt-2">Type your full legal name as it appears on official documents</p>
            </div>

            <!-- Consent -->
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 mb-6">
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" required class="mt-1 w-5 h-5 text-blue-600 rounded">
                    <span class="text-sm text-slate-700">
                        I agree that by clicking "Sign Document" below, I am electronically signing this document. 
                        I understand that my electronic signature is the legal equivalent of my manual signature.
                    </span>
                </label>
            </div>

            <!-- Submit Button -->
            <button @click="submitSignature" 
                    :disabled="loading"
                    class="w-full py-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-bold text-lg rounded-xl shadow-lg hover:shadow-xl transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-3">
                <i x-show="!loading" data-lucide="check-circle" class="w-6 h-6"></i>
                <i x-show="loading" data-lucide="loader-2" class="w-6 h-6 animate-spin"></i>
                <span x-text="loading ? 'Submitting...' : 'Sign Document'"></span>
            </button>
        </div>

        <!-- Footer -->
        <div class="text-center mt-8 text-slate-500 text-sm">
            <p>🔒 Secured by ArchitGrid E-Signature System</p>
            <p class="mt-2">This signature request was sent to {{ $signatureRequest->signer_email }}</p>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
