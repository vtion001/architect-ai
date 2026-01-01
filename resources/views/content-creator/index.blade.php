@extends('layouts.app')

@section('content')
<div class="p-8 max-w-7xl mx-auto" x-data="{
    topic: '',
    type: '',
    context: '',
    isGenerating: false,
    generateContent() {
        if (!this.topic || !this.type) {
            alert('Please fill in both topic and content type.');
            return;
        }
        this.isGenerating = true;
        fetch('{{ route('content-creator.generate') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                topic: this.topic,
                type: this.type,
                context: this.context
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert('Generation failed: ' + (data.message || 'Unknown error'));
                this.isGenerating = false;
            }
        })
        .catch(err => {
            console.error(err);
            this.isGenerating = false;
        });
    }
}">
    <div class="mb-8">
        <h1 class="text-3xl font-bold mb-2">Knowledge-Base Driven Content Creator</h1>
        <p class="text-muted-foreground">Generate high-quality content powered by your knowledge base</p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <!-- Total Content -->
        <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-muted-foreground">Total Content</p>
                        <p class="text-2xl font-bold">{{ number_format($stats['total_content']) }}</p>
                    </div>
                    <i data-lucide="file-text" class="w-8 h-8 text-blue-500"></i>
                </div>
            </div>
        </div>
        <!-- This Month -->
        <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-muted-foreground">This Month</p>
                        <p class="text-2xl font-bold">{{ number_format($stats['this_month']) }}</p>
                    </div>
                    <i data-lucide="trending-up" class="w-8 h-8 text-green-500"></i>
                </div>
            </div>
        </div>
        <!-- In Draft -->
        <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-muted-foreground">In Draft</p>
                        <p class="text-2xl font-bold">{{ number_format($stats['in_draft']) }}</p>
                    </div>
                    <i data-lucide="pencil" class="w-8 h-8 text-amber-500"></i>
                </div>
            </div>
        </div>
        <!-- Published -->
        <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-muted-foreground">Published</p>
                        <p class="text-2xl font-bold">{{ number_format($stats['published']) }}</p>
                    </div>
                    <i data-lucide="sparkles" class="w-8 h-8 text-purple-500"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Content Generator -->
        <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm lg:col-span-2">
            <div class="flex flex-col space-y-1.5 p-6">
                <h3 class="text-2xl font-semibold leading-none tracking-tight flex items-center gap-2">
                    <i data-lucide="sparkles" class="w-5 h-5"></i>
                    Generate New Content
                </h3>
                <p class="text-sm text-muted-foreground">Create content using AI with your brand voice and knowledge base</p>
            </div>
            <div class="p-6 pt-0 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="content-topic" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Content Topic</label>
                        <input x-model="topic" type="text" id="content-topic" placeholder="What do you want to write about?" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 mt-1.5">
                    </div>
                    <div>
                        <label for="content-type" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Content Type</label>
                        <select x-model="type" id="content-type" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 mt-1.5">
                            <option value="">Select type</option>
                            <option value="blog-post">Blog Post</option>
                            <option value="social-media">Social Media Post</option>
                            <option value="email">Email Campaign</option>
                            <option value="case-study">Case Study</option>
                            <option value="product-description">Product Description</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="additional-context" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Additional Context (Optional)</label>
                    <textarea x-model="context" id="additional-context" placeholder="Add any specific instructions or context..." rows="3" class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 mt-1.5"></textarea>
                </div>

                <button @click="generateContent" :disabled="isGenerating" class="w-full inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2">
                    <template x-if="!isGenerating">
                        <i data-lucide="sparkles" class="w-4 h-4 mr-2"></i>
                    </template>
                    <template x-if="isGenerating">
                        <i data-lucide="loader-2" class="w-4 h-4 mr-2 animate-spin"></i>
                    </template>
                    <span x-text="isGenerating ? 'Architecting Content...' : 'Generate Content'"></span>
                </button>
            </div>
        </div>

        <!-- Recent Content -->
        <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm">
            <div class="flex flex-col space-y-1.5 p-6">
                <h3 class="text-2xl font-semibold leading-none tracking-tight">Recent Content</h3>
            </div>
            <div class="p-6 pt-0">
                <div class="space-y-4">
                    @forelse($recentContents as $item)
                    <a href="{{ route('content-creator.show', $item) }}" class="block p-3 border border-border rounded-lg hover:bg-muted/50">
                        <h3 class="font-semibold text-sm mb-2">{{ $item->title }}</h3>
                        <div class="flex items-center justify-between text-xs text-muted-foreground mb-2">
                            <span>{{ ucwords(str_replace('-', ' ', $item->type)) }}</span>
                            <span>{{ $item->word_count }} words</span>
                        </div>
                        @php
                            $statusClasses = [
                                'published' => 'bg-green-100 text-green-700',
                                'draft' => 'bg-amber-100 text-amber-700',
                                'generating' => 'bg-blue-100 text-blue-700',
                                'failed' => 'bg-red-100 text-red-700'
                            ];
                            $currentStatusClass = $statusClasses[$item->status] ?? 'bg-slate-100 text-slate-700';
                        @endphp
                        <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 {{ $currentStatusClass }}">
                            {{ ucfirst($item->status) }}
                        </span>
                    </a>
                    @empty
                    <div class="text-center py-8 text-muted-foreground">
                        <i data-lucide="pencil" class="w-8 h-8 mx-auto mb-2 opacity-20"></i>
                        <p>No content generated yet.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
