{{--
    Media Registry Index Page
    
    Digital asset management interface for all media files.
    Supports images, audio, and AI-generated assets.
    
    Required variables:
    - $assets: Paginated collection of media assets
    - $stats: Array with total_assets, ai_generated, uploads counts
    
    Features:
    - Grid view with hover previews
    - Upload functionality via hidden input
    - Asset preview modal with identity context
    - Pagination support
--}}

@extends('layouts.app')

@section('content')
<div class="p-8 max-w-[1600px] mx-auto animate-in fade-in duration-700" x-data="{ 
    showPreviewModal: false, 
    selectedAsset: null,
    isUploading: false,
    
    triggerUpload() { 
        this.$refs.fileInput.click() 
    },
    
    handleUpload(e) {
        const file = e.target.files[0];
        if (!file) return;
        this.isUploading = true;
        const formData = new FormData();
        formData.append('file', file);
        
        fetch('{{ route('media-registry.store') }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                window.location.reload();
            } else {
                alert('Upload failed: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(err => {
            console.error(err);
            alert('Error uploading file.');
        })
        .finally(() => this.isUploading = false);
    },
    
    purgeAsset(id) {
        if(!confirm('Purge this visual from the grid registry?')) return;
        fetch(`/media-registry/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
        }).then(() => window.location.reload());
    }
}">
    {{-- Hidden File Input --}}
    <input type="file" x-ref="fileInput" class="hidden" accept="image/*" @change="handleUpload">

    {{-- Registry Header --}}
    @include('media-registry.partials.header')

    {{-- Media Telemetry Stats --}}
    @include('media-registry.partials.stats')

    {{-- Assets Grid Matrix --}}
    @include('media-registry.partials.grid')

    {{-- Asset Preview Modal --}}
    @include('media-registry.partials.preview-modal')
</div>
@endsection
