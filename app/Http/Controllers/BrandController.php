<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Auth::user()->tenant->brands()->orderBy('is_default', 'desc')->get();
        return view('brands.index', compact('brands'));
    }

    public function store(Request $request)
    {
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
        ]);

        $tenant = Auth::user()->tenant;
        
        // Handle logo upload to Cloudinary
        if ($request->hasFile('logo')) {
            $uploadResult = $this->uploadToCloudinary($request->file('logo'), 'brands');
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
        ]);

        // Handle logo upload to Cloudinary
        if ($request->hasFile('logo')) {
            // Delete old logo from Cloudinary if exists
            if ($brand->logo_public_id) {
                $this->deleteFromCloudinary($brand->logo_public_id);
            }
            
            $uploadResult = $this->uploadToCloudinary($request->file('logo'), 'brands');
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

        // Delete logo from Cloudinary if exists
        if ($brand->logo_public_id) {
            $this->deleteFromCloudinary($brand->logo_public_id);
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
     * Upload file to Cloudinary
     */
    private function uploadToCloudinary($file, string $folder): ?array
    {
        $cloudName = config('services.cloudinary.cloud_name');
        $apiKey = config('services.cloudinary.api_key');
        $apiSecret = config('services.cloudinary.api_secret');

        if (!$cloudName || !$apiKey || !$apiSecret) {
            Log::error('BrandController: Cloudinary credentials not configured.');
            return null;
        }

        try {
            $timestamp = time();
            $publicId = $folder . '/' . uniqid('brand_');
            
            // Build signature
            $signatureString = "folder={$folder}&public_id={$publicId}&timestamp={$timestamp}{$apiSecret}";
            $signature = sha1($signatureString);

            $response = Http::timeout(60)
                ->attach('file', file_get_contents($file->getPathname()), $file->getClientOriginalName())
                ->post("https://api.cloudinary.com/v1_1/{$cloudName}/image/upload", [
                    'api_key' => $apiKey,
                    'timestamp' => $timestamp,
                    'signature' => $signature,
                    'folder' => $folder,
                    'public_id' => $publicId,
                ]);

            if ($response->successful()) {
                Log::info("BrandController: Logo uploaded successfully to Cloudinary.");
                return [
                    'url' => $response->json('secure_url'),
                    'public_id' => $response->json('public_id'),
                ];
            }

            Log::error("BrandController: Cloudinary upload failed. " . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error("BrandController: Cloudinary upload exception. " . $e->getMessage());
            return null;
        }
    }

    /**
     * Delete file from Cloudinary
     */
    private function deleteFromCloudinary(string $publicId): bool
    {
        $cloudName = config('services.cloudinary.cloud_name');
        $apiKey = config('services.cloudinary.api_key');
        $apiSecret = config('services.cloudinary.api_secret');

        if (!$cloudName || !$apiKey || !$apiSecret) {
            return false;
        }

        try {
            $timestamp = time();
            $signatureString = "public_id={$publicId}&timestamp={$timestamp}{$apiSecret}";
            $signature = sha1($signatureString);

            $response = Http::asForm()->post("https://api.cloudinary.com/v1_1/{$cloudName}/image/destroy", [
                'api_key' => $apiKey,
                'timestamp' => $timestamp,
                'signature' => $signature,
                'public_id' => $publicId,
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error("BrandController: Cloudinary delete exception. " . $e->getMessage());
            return false;
        }
    }
}
