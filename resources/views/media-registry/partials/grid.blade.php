{{-- Media Registry - Assets Grid --}}
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-6">
    @forelse($assets as $asset)
        @include('media-registry.partials.asset-card', ['asset' => $asset])
    @empty
        <div class="col-span-full py-40 text-center opacity-30 italic">
            <i data-lucide="image" class="w-16 h-16 mx-auto mb-6"></i>
            <p class="text-sm font-bold uppercase tracking-[0.2em]">Visual registry node empty</p>
        </div>
    @endforelse
</div>

<div class="mt-12">
    {{ $assets->links() }}
</div>
