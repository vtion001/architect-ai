<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            'logo_url' => 'nullable|url',
            'colors' => 'nullable|array',
            'typography' => 'nullable|array',
            'voice_profile' => 'nullable|array',
            'contact_info' => 'nullable|array',
        ]);

        $tenant = Auth::user()->tenant;
        
        // If this is the first brand, make it default
        if ($tenant->brands()->count() === 0) {
            $validated['is_default'] = true;
        }

        $tenant->brands()->create($validated);

        return redirect()->back()->with('success', 'Brand kit created successfully.');
    }

    public function update(Request $request, Brand $brand)
    {
        // Ensure brand belongs to tenant
        if ($brand->tenant_id !== Auth::user()->tenant_id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'logo_url' => 'nullable|url',
            'colors' => 'nullable|array',
            'typography' => 'nullable|array',
            'voice_profile' => 'nullable|array',
            'contact_info' => 'nullable|array',
        ]);

        $brand->update($validated);

        return redirect()->back()->with('success', 'Brand kit updated successfully.');
    }

    public function destroy(Brand $brand)
    {
        if ($brand->tenant_id !== Auth::user()->tenant_id) {
            abort(403);
        }

        $brand->delete();

        return redirect()->back()->with('success', 'Brand kit deleted successfully.');
    }

    public function setDefault(Brand $brand)
    {
        if ($brand->tenant_id !== Auth::user()->tenant_id) {
            abort(403);
        }

        $brand->tenant->brands()->update(['is_default' => false]);
        $brand->update(['is_default' => true]);

        return redirect()->back()->with('success', 'Default brand updated.');
    }
}
