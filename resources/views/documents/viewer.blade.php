{{--
    Document Viewer Page
    
    View and edit archived documents with A4 preview.
    
    Required variables:
    - $document: Document model with content
    
    Features:
    - A4 sized document preview
    - Inline editing with designMode
    - Zoom controls
    - PDF export
    - Integrity hash verification
--}}

@extends('layouts.app')

@section('content')
<div class="p-10 max-w-[1200px] mx-auto animate-in fade-in duration-700" 
     x-data="{ 
        isEditing: false, 
        content: @js($document->content), 
        isSaving: false,
        zoomLevel: 0.8,
        
        init() {
            this.$watch('isEditing', (value) => {
                const frame = this.$refs.previewFrame;
                if (frame && frame.contentDocument) {
                    frame.contentDocument.designMode = value ? 'on' : 'off';
                    if (value) {
                        frame.contentDocument.body.style.transition = 'background-color 0.3s';
                        frame.contentDocument.body.style.backgroundColor = '#fcfcfc';
                    } else {
                        frame.contentDocument.body.style.backgroundColor = '';
                    }
                }
            });
        },

        saveDocument() {
            this.isSaving = true;
            const frame = this.$refs.previewFrame;
            if (frame && frame.contentDocument) {
                const updatedContent = '<!DOCTYPE html>\n' + frame.contentDocument.documentElement.outerHTML;
                this.content = updatedContent;
            }

            fetch('{{ route('documents.update', $document) }}', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ content: this.content })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    this.isEditing = false;
                    window.location.reload();
                } else {
                    alert('Failed to save.');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Error saving document.');
            })
            .finally(() => { this.isSaving = false; });
        },

        downloadPdf() {
            const printWindow = window.open('', '_blank');
            printWindow.document.write(this.content);
            printWindow.document.close();
            setTimeout(() => {
                printWindow.print();
            }, 500);
        }
     }">
    {{-- Archive Header --}}
    @include('documents.partials.header')

    {{-- Document Content Node --}}
    @include('documents.partials.preview')

    {{-- Footer Watermark --}}
    @include('documents.partials.footer')
</div>

{{-- Styles --}}
@include('documents.partials.styles')
@endsection
