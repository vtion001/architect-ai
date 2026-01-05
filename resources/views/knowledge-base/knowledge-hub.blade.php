@extends('layouts.app')

@section('content')
<div class="p-8 max-w-7xl mx-auto" x-data="{
    showUploadModal: false,
    showViewModal: false,
    viewingAsset: null,
    assetTitle: '',
    assetType: 'text',
    assetContent: '',
    assetCategory: 'General',
    assetUrl: '',
    isSaving: false,

    viewAsset(asset) {
        this.viewingAsset = asset;
        this.showViewModal = true;
    },

    saveAsset() {
        if (!this.assetTitle || !this.assetContent) {
            alert('Please fill in both title and content.');
            return;
        }
        this.isSaving = true;
        fetch('{{ route('knowledge-base.store') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                title: this.assetTitle,
                type: this.assetType,
                content: this.assetContent,
                category: this.assetCategory,
                source_url: this.assetUrl
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert('Failed to save asset.');
                this.isSaving = false;
            }
        })
        .catch(err => {
            console.error(err);
            this.isSaving = false;
        });
    },

    deleteAsset(id) {
        if (confirm('Are you sure you want to delete this asset?')) {
            fetch(`/knowledge-base/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(() => window.location.reload());
        }
    }
}">
    <div class="mb-8">
        <h1 class="text-3xl font-bold mb-2">Global Knowledge Base (RAG)</h1>
        <p class="text-muted-foreground">Centralized repository for business documents and AI sources.</p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-muted-foreground">Total Assets</p>
                        <p class="text-2xl font-bold">{{ number_format($stats['total_docs']) }}</p>
                    </div>
                    <i data-lucide="database" class="w-8 h-8 text-blue-500"></i>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-muted-foreground">Categories</p>
                        <p class="text-2xl font-bold">{{ $stats['categories'] }}</p>
                    </div>
                    <i data-lucide="folder" class="w-8 h-8 text-purple-500"></i>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-muted-foreground">Vector Status</p>
                        <p class="text-2xl font-bold">Synced</p>
                    </div>
                    <i data-lucide="zap" class="w-8 h-8 text-green-500"></i>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-muted-foreground">Recent Updates</p>
                        <p class="text-2xl font-bold">{{ $stats['recent_updates'] }}</p>
                    </div>
                    <i data-lucide="clock" class="w-8 h-8 text-amber-500"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="flex gap-4 mb-8">
        <div class="relative flex-1">
            <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground"></i>
            <input type="search" placeholder="Search knowledge assets..." class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 pl-9" />
        </div>
        <button @click="showUploadModal = true" class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2 uppercase font-black tracking-widest text-[10px]">
            <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
            Add Source Asset
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Asset List -->
        <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm lg:col-span-2">
            <div class="flex flex-col space-y-1.5 p-6 border-b border-border">
                <h3 class="text-lg font-black uppercase tracking-widest flex items-center gap-2">
                    <i data-lucide="layers" class="w-5 h-5 text-primary"></i>
                    Stored Intelligence
                </h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @forelse($assets as $asset)
                    <div class="p-4 border border-border rounded-xl hover:bg-muted/30 transition-colors group">
                        <div class="flex items-start justify-between">
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center shrink-0 border border-primary/20">
                                    @if($asset->type === 'website') <i data-lucide="globe" class="w-5 h-5 text-primary"></i>
                                    @elseif($asset->type === 'youtube') <i data-lucide="youtube" class="w-5 h-5 text-primary"></i>
                                    @elseif($asset->type === 'file') <i data-lucide="file-text" class="w-5 h-5 text-primary"></i>
                                    @else <i data-lucide="align-left" class="w-5 h-5 text-primary"></i>
                                    @endif
                                </div>
                                <div>
                                    <h4 class="font-bold text-sm text-foreground">{{ $asset->title }}</h4>
                                    <div class="flex items-center gap-3 mt-1">
                                        <span class="text-[10px] font-black uppercase tracking-widest text-muted-foreground">{{ $asset->category }}</span>
                                        <span class="text-[10px] text-slate-400 italic">Added {{ $asset->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="mt-2 text-xs text-muted-foreground line-clamp-2 italic">
                                        {{ Str::limit(strip_tags($asset->content), 150) }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button @click="viewAsset({{ $asset }})" class="p-2 text-primary hover:bg-primary/5 rounded-lg">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </button>
                                <button @click="deleteAsset('{{ $asset->id }}')" class="p-2 text-destructive hover:bg-red-50 rounded-lg">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-12 text-muted-foreground">
                        <i data-lucide="inbox" class="w-12 h-12 mx-auto mb-4 opacity-20"></i>
                        <p>No knowledge assets found. Add sources to power your RAG system.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Recent Events / Info -->
        <div class="space-y-6">
            <div class="rounded-xl border border-border bg-primary/5 p-6">
                <h3 class="text-sm font-black uppercase tracking-widest text-primary mb-4">RAG System Protocol</h3>
                <p class="text-xs text-muted-foreground leading-relaxed italic mb-4">
                    Assets stored in the Global Knowledge Base are automatically indexed into the vector database. AI modules (Research, Content Creator) will prioritize these sources for context-aware generation.
                </p>
                <div class="space-y-3">
                    <div class="flex items-center gap-3">
                        <i data-lucide="check-circle" class="w-4 h-4 text-green-500"></i>
                        <span class="text-[10px] font-bold">Auto-Contextualization Enabled</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <i data-lucide="check-circle" class="w-4 h-4 text-green-500"></i>
                        <span class="text-[10px] font-bold">Source Grounding Priority: 1</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Modal -->
    <div x-show="showUploadModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
        <div @click.away="showUploadModal = false" class="bg-card w-full max-w-lg rounded-2xl shadow-2xl border border-border p-8 animate-in zoom-in-95 duration-200">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-black uppercase tracking-tighter">Add Source Asset</h2>
                <button @click="showUploadModal = false"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>

            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground italic">Source Type</label>
                        <select x-model="assetType" class="w-full h-10 bg-muted/20 border border-border rounded-lg px-3 text-xs font-bold">
                            <option value="text">Raw Text</option>
                            <option value="website">Website URL</option>
                            <option value="file">Local File</option>
                            <option value="youtube">YouTube Transcript</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground italic">Category</label>
                        <input type="text" x-model="assetCategory" class="w-full h-10 bg-muted/20 border border-border rounded-lg px-3 text-xs font-bold">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground italic">Asset Title</label>
                    <input type="text" x-model="assetTitle" placeholder="e.g., Brand Guidelines 2026" class="w-full h-10 bg-muted/20 border border-border rounded-lg px-3 text-xs font-bold">
                </div>

                <div x-show="assetType === 'website' || assetType === 'youtube'" class="space-y-2" x-transition>
                    <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground italic">Source URL</label>
                    <input type="url" x-model="assetUrl" placeholder="https://..." class="w-full h-10 bg-muted/20 border border-border rounded-lg px-3 text-xs font-bold">
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground italic">Context Content</label>
                    <textarea x-model="assetContent" rows="6" placeholder="Paste the content or notes here..." class="w-full bg-muted/20 border border-border rounded-lg px-3 py-3 text-xs font-medium"></textarea>
                </div>

                <button @click="saveAsset" :disabled="isSaving" class="w-full h-12 bg-primary text-primary-foreground rounded-xl font-black uppercase tracking-widest text-xs shadow-lg shadow-primary/20 flex items-center justify-center gap-2 hover:scale-[1.02] transition-all">
                    <template x-if="!isSaving">
                        <div class="flex items-center gap-2">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                            <span>Index into Knowledge Base</span>
                        </div>
                    </template>
                    <template x-if="isSaving">
                        <i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i>
                    </template>
                </button>
            </div>
        </div>
    <!-- View Asset Modal -->
    <div x-show="showViewModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
        <div @click.away="showViewModal = false" class="bg-card w-full max-w-2xl rounded-2xl shadow-2xl border border-border p-10 animate-in zoom-in-95 duration-200">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-2xl font-black uppercase tracking-tighter text-foreground" x-text="viewingAsset?.title"></h2>
                    <p class="text-[10px] text-primary font-black uppercase tracking-widest mt-1" x-text="viewingAsset?.category"></p>
                </div>
                <button @click="showViewModal = false" class="p-2 hover:bg-muted rounded-full"><i data-lucide="x" class="w-6 h-6"></i></button>
            </div>

            <div class="max-h-[500px] overflow-y-auto pr-2 custom-scrollbar">
                <div class="prose prose-sm max-w-none text-muted-foreground whitespace-pre-wrap leading-relaxed font-medium" x-html="viewingAsset?.content"></div>
            </div>

            <div class="mt-8 pt-6 border-t border-border flex justify-end gap-3">
                <button @click="showViewModal = false" class="px-6 py-2 rounded-xl bg-muted font-black uppercase text-[10px] tracking-widest hover:bg-muted/80 transition-all">Close Viewer</button>
                <button @click="deleteAsset(viewingAsset.id)" class="px-6 py-2 rounded-xl bg-red-50 text-red-600 font-black uppercase text-[10px] tracking-widest hover:bg-red-100 transition-all">Purge Asset</button>
            </div>
        </div>
    </div>
</div>
@endsection