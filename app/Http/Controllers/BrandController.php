<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Brand Kit Controller.
 * 
 * Refactored to use CloudinaryService for file uploads (Service Layer Pattern).
 */
class BrandController extends Controller
{
    public function __construct(
        protected CloudinaryService $cloudinaryService
    ) {}

    public function index()
    {
        $brands = Auth::user()->tenant->brands()->orderBy('is_default', 'desc')->get();
        return view('brands.index', compact('brands'));
    }

    public function store(Request $request)
    {
        // Decode JSON fields from FormData (frontend sends them as strings)
        foreach (['colors', 'typography', 'voice_profile', 'contact_info', 'social_handles'] as $field) {
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
        foreach (['colors', 'typography', 'voice_profile', 'contact_info', 'social_handles'] as $field) {
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
}
