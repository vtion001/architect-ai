<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Services\CloudinaryService;
use App\Services\PdfToTextService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

/**
 * Brand Kit Controller.
 * 
 * Refactored to use CloudinaryService for file uploads (Service Layer Pattern).
 */
class BrandController extends Controller
{
    public function __construct(
        protected CloudinaryService $cloudinaryService,
        protected PdfToTextService $pdfToTextService
    ) {}

    public function analyzeBlueprint(Request $request)
    {
        $request->validate([
            'document' => 'required|file|mimes:pdf,txt,md|max:10240', // 10MB max
            'type' => 'required|string|in:proposal,contract,executive-summary'
        ]);

        $file = $request->file('document');
        $text = '';

        try {
            if ($file->getClientOriginalExtension() === 'pdf') {
                $text = $this->pdfToTextService->extract($file->getPathname());
            } else {
                $rawText = file_get_contents($file->getPathname());
                $text = $this->sanitizeUtf8($rawText);
            }

            if (empty(trim($text))) {
                return response()->json(['success' => false, 'message' => 'Could not extract text from the document.'], 422);
            }

            // AI Analysis
            $apiKey = config('services.openai.key');
            if (!$apiKey) {
                return response()->json(['success' => false, 'message' => 'AI service not configured.'], 500);
            }

            $response = Http::withToken($apiKey)->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o', // Use a smart model for extraction
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => "You are a Legal & Compliance Extraction AI. 
                        Your job is to read a raw business document (Proposal, Contract, etc.) and extract strict structural templates for a Brand Kit.
                        
                        Extract the following 4 fields into a JSON object:
                        1. `boilerplate_intro`: The standard opening greeting, company pride statement, or mission (e.g., 'We would like to thank you...').
                        2. `scope_of_work_template`: The static definitions of services (e.g., 'A. SOIL TREATMENT...'). Keep the headers and descriptions verbatim.
                        3. `legal_terms`: Any terms of payment, legal disclaimers, or 'Notes' (e.g., 'Terms of Payment: 50% down...').
                        4. `structure_instruction`: A short instruction on how the document is laid out (e.g., 'Intro -> Scope -> Pricing Table -> Terms').
                        
                        Return ONLY valid JSON."
                    ],
                    [
                        'role' => 'user',
                        'content' => "Extract the blueprint from this document text:\n\n" . substr($text, 0, 15000) // Truncate to avoid context limits
                    ]
                ],
                'response_format' => ['type' => 'json_object']
            ]);

            if ($response->successful()) {
                $data = $response->json('choices.0.message.content');
                $decodedData = json_decode($data);
                
                // If decoding failed, try sanitizing the response
                if ($decodedData === null && json_last_error() !== JSON_ERROR_NONE) {
                    $sanitizedData = $this->sanitizeUtf8($data);
                    $decodedData = json_decode($sanitizedData);
                }
                
                return response()->json(['success' => true, 'data' => $decodedData]);
            }

            return response()->json(['success' => false, 'message' => 'AI analysis failed.'], 500);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function index()
    {
        $brands = Auth::user()->tenant->brands()->orderBy('is_default', 'desc')->get();
        return view('brands.index', compact('brands'));
    }

    public function store(Request $request)
    {
        // Decode JSON fields from FormData (frontend sends them as strings)
        foreach (['colors', 'typography', 'voice_profile', 'contact_info', 'social_handles', 'blueprints'] as $field) {
            if ($request->has($field) && is_string($request->input($field))) {
                $decoded = json_decode($request->input($field), true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $request->merge([$field => $decoded]);
                }
            }
        }


        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'tagline' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:2000',
            'industry' => 'nullable|string|max:100',
            'logo' => 'nullable|image|mimes:jpeg,png,gif,webp,svg|max:5120',
            'logo_url' => 'nullable|url',
            'colors' => 'nullable|array',
            'typography' => 'nullable|array',
            'voice_profile' => 'nullable|array',
            'contact_info' => 'nullable|array',
            'social_handles' => 'nullable|array',
            'blueprints' => 'nullable|array',
        ]);

        $tenant = Auth::user()->tenant;
        
        // Handle logo upload to Cloudinary via service
        if ($request->hasFile('logo')) {
            $uploadResult = $this->cloudinaryService->upload($request->file('logo'), 'brands');
            if ($uploadResult) {
                $validated['logo_url'] = $uploadResult['url'];
                $validated['logo_public_id'] = $uploadResult['public_id'];
            }
        }
        
        // Remove the file from validated data (we only store the URL)
        unset($validated['logo']);

        // If this is the first brand, make it default
        if ($tenant->brands()->count() === 0) {
            $validated['is_default'] = true;
        }

        $tenant->brands()->create($validated);

        return response()->json(['success' => true, 'message' => 'Brand kit created successfully.']);
    }

    public function update(Request $request, Brand $brand)
    {
        // Decode JSON fields from FormData
        foreach (['colors', 'typography', 'voice_profile', 'contact_info', 'social_handles', 'blueprints'] as $field) {
            if ($request->has($field) && is_string($request->input($field))) {
                $decoded = json_decode($request->input($field), true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $request->merge([$field => $decoded]);
                }
            }
        }


        // Policy-based authorization
        $this->authorize('update', $brand);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'tagline' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:2000',
            'industry' => 'nullable|string|max:100',
            'logo' => 'nullable|image|mimes:jpeg,png,gif,webp,svg|max:5120',
            'logo_url' => 'nullable|string', // Allow keeping existing URL
            'colors' => 'nullable|array',
            'typography' => 'nullable|array',
            'voice_profile' => 'nullable|array',
            'contact_info' => 'nullable|array',
            'social_handles' => 'nullable|array',
            'blueprints' => 'nullable|array',
        ]);

        // Handle logo upload to Cloudinary via service
        if ($request->hasFile('logo')) {
            // Delete old logo from Cloudinary if exists
            if ($brand->logo_public_id) {
                $this->cloudinaryService->delete($brand->logo_public_id);
            }
            
            $uploadResult = $this->cloudinaryService->upload($request->file('logo'), 'brands');
            if ($uploadResult) {
                $validated['logo_url'] = $uploadResult['url'];
                $validated['logo_public_id'] = $uploadResult['public_id'];
            }
        }
        
        // Remove the file from validated data
        unset($validated['logo']);

        $brand->update($validated);

        return response()->json(['success' => true, 'message' => 'Brand kit updated successfully.']);
    }

    public function destroy(Brand $brand)
    {
        $this->authorize('delete', $brand);

        // Delete logo from Cloudinary via service
        if ($brand->logo_public_id) {
            $this->cloudinaryService->delete($brand->logo_public_id);
        }

        $brand->delete();

        return redirect()->back()->with('success', 'Brand kit deleted successfully.');
    }

    public function setDefault(Brand $brand)
    {
        $this->authorize('setDefault', $brand);

        $brand->tenant->brands()->update(['is_default' => false]);
        $brand->update(['is_default' => true]);

        return redirect()->back()->with('success', 'Default brand updated.');
    }

    /**
     * Sanitize text to ensure valid UTF-8 encoding for JSON responses.
     */
    private function sanitizeUtf8(string $text): string
    {
        // Detect encoding and convert to UTF-8
        $encoding = mb_detect_encoding($text, ['UTF-8', 'ISO-8859-1', 'Windows-1252', 'ASCII'], true);
        
        if ($encoding && $encoding !== 'UTF-8') {
            $text = mb_convert_encoding($text, 'UTF-8', $encoding);
        }

        // Remove control characters except newlines and tabs
        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $text);
        
        // Use iconv to handle any remaining invalid sequences
        $cleaned = @iconv('UTF-8', 'UTF-8//TRANSLIT//IGNORE', $text);
        
        if ($cleaned === false) {
            $cleaned = mb_convert_encoding($text, 'UTF-8', 'UTF-8');
        }

        // Final safety check
        if (json_encode($cleaned) === false) {
            $cleaned = preg_replace('/[^\x20-\x7E\n\r\t]/', '', $text);
        }

        return $cleaned ?: '';
    }
}
