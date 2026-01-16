{{--
    Knowledge Hub Index Page
    
    Central intelligence repository with folders and assets.
    Modularized - uses @include for partials.
    
    Required variables:
    - $assets: Collection of knowledge assets
    - $stats: Array with total_docs, categories, recent_updates
    - $currentFolder: Current folder or null
--}}

@extends('layouts.app')

@section('content')
<div class="p-8 max-w-7xl mx-auto" x-data="{
    showAddModal: false,
    showFolderModal: false,
    showViewModal: false,
    selectedAsset: null,
    currentFolder: @js($currentFolder ?? null),
    
    newAsset: {
        title: '',
        type: 'text',
        category: 'Market Intelligence',
        content: '',
        source_url: ''
    },
    newFolderTitle: '',
    isSaving: false,

    createFolder() {
        if (!this.newFolderTitle) return;
        this.isSaving = true;
        const formData = new FormData();
        formData.append('title', this.newFolderTitle);
        formData.append('type', 'folder');
        if (this.currentFolder) formData.append('parent_id', this.currentFolder.id);

        fetch('{{ route('knowledge-base.store') }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) window.location.reload();
            else this.isSaving = false;
        });
    },

    saveAsset() {
        if (!this.newAsset.title) {
            alert('Title is mandatory.');
            return;
        }
        this.isSaving = true;
        const formData = new FormData();
        formData.append('title', this.newAsset.title);
        formData.append('type', this.newAsset.type);
        formData.append('category', this.newAsset.category);
        formData.append('content', this.newAsset.content || '');
        if (this.newAsset.source_url) formData.append('source_url', this.newAsset.source_url);
        if (this.currentFolder) formData.append('parent_id', this.currentFolder.id);
        
        if (this.$refs.fileInput && this.$refs.fileInput.files.length) {
            formData.append('file', this.$refs.fileInput.files[0]);
        }

        fetch('{{ route('knowledge-base.store') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert('Save failed.');
                this.isSaving = false;
            }
        })
        .catch(err => {
            console.error(err);
            this.isSaving = false;
        });
    },

    deleteAsset(id) {
        if (!confirm('Are you sure you want to delete this asset? This cannot be undone.')) return;

        fetch(`/knowledge-base/${id}`, {
            method: 'DELETE',
            headers: { 
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) window.location.reload();
            else alert('Delete failed.');
        });
    }
}">
    {{-- Page Header & Breadcrumbs --}}
    @include('knowledge-base.partials.header')

    {{-- Stats Grid --}}
    @include('knowledge-base.partials.stats-grid', ['stats' => $stats])

    {{-- Assets Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($assets as $asset)
            @if($asset->type === 'folder')
                @include('knowledge-base.partials.folder-card', ['asset' => $asset])
            @else
                @include('knowledge-base.partials.asset-card', ['asset' => $asset])
            @endif
        @empty
            <div class="col-span-full py-32 text-center space-y-6 opacity-50 italic border-2 border-dashed border-border rounded-[40px]">
                <i data-lucide="folder-open" class="w-16 h-16 mx-auto text-slate-300"></i>
                <p class="text-sm font-medium">This directory is currently empty.</p>
            </div>
        @endforelse
    </div>

    {{-- Asset Preview Modal --}}
    @include('knowledge-base.partials.modals.preview-modal')

    {{-- Create Folder Modal --}}
    @include('knowledge-base.partials.modals.folder-modal')

    {{-- Index New Asset Modal --}}
    @include('knowledge-base.partials.modals.create-asset-modal')
</div>
@endsection
