@extends('layouts.app')

@section('content')
<div class="p-8 max-w-7xl mx-auto" x-data="{
    deleteDocument(id) {
        if (confirm('Are you sure you want to delete this document?')) {
            fetch(`/documents/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(() => window.location.reload());
        }
    }
}">
    <div class="mb-8">
        <h1 class="text-3xl font-bold mb-2">Documents</h1>
        <p class="text-muted-foreground">Manage and organize your generated reports and business documents</p>
    </div>

    <!-- Search and Actions -->
    <div class="flex gap-4 mb-6">
        <div class="relative flex-1">
            <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground"></i>
            <input type="search" placeholder="Search documents..." class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 pl-9" />
        </div>
        <button class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2">
            <i data-lucide="filter" class="w-4 h-4 mr-2"></i>
            Filter
        </button>
    </div>

    <!-- All Documents -->
    <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm">
        <div class="flex flex-col space-y-1.5 p-6">
            <h3 class="text-2xl font-semibold leading-none tracking-tight">All Documents</h3>
        </div>
        <div class="p-6 pt-0">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-border">
                            <th class="text-left py-3 px-4 text-xs font-black uppercase tracking-widest text-muted-foreground">Name</th>
                            <th class="text-left py-3 px-4 text-xs font-black uppercase tracking-widest text-muted-foreground">Type</th>
                            <th class="text-left py-3 px-4 text-xs font-black uppercase tracking-widest text-muted-foreground">Size</th>
                            <th class="text-left py-3 px-4 text-xs font-black uppercase tracking-widest text-muted-foreground">Category</th>
                            <th class="text-left py-3 px-4 text-xs font-black uppercase tracking-widest text-muted-foreground">Modified</th>
                            <th class="text-left py-3 px-4 text-xs font-black uppercase tracking-widest text-muted-foreground text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($documents as $doc)
                        <tr class="border-b border-border hover:bg-muted/50 transition-colors">
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-blue-100 rounded flex items-center justify-center">
                                        <i data-lucide="file-text" class="w-4 h-4 text-blue-600"></i>
                                    </div>
                                    <span class="text-sm font-bold">{{ $doc->name }}</span>
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-[10px] font-black uppercase tracking-wider bg-secondary text-secondary-foreground">
                                    {{ $doc->type }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-xs font-medium text-muted-foreground">{{ number_format($doc->size / 1024, 1) }} KB</td>
                            <td class="py-4 px-4 text-xs font-bold">{{ $doc->category }}</td>
                            <td class="py-4 px-4 text-xs text-muted-foreground">{{ $doc->updated_at->format('Y-m-d H:i') }}</td>
                            <td class="py-4 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('documents.show', $doc) }}" target="_blank" class="inline-flex items-center justify-center rounded-md text-sm font-medium border border-border bg-background hover:bg-accent h-9 w-9">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>
                                    <button @click="deleteDocument('{{ $doc->id }}')" class="inline-flex items-center justify-center rounded-md text-sm font-medium border border-border bg-background text-destructive hover:bg-destructive/10 h-9 w-9">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-muted-foreground italic">
                                <i data-lucide="file-question" class="w-12 h-12 mx-auto mb-4 opacity-20"></i>
                                <p>No documents architected yet. Head over to the Report Builder to start.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection