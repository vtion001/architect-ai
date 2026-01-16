{{-- Content Creator Header Info & Generator Toggles --}}
<div class="mb-8">
    <h1 class="text-3xl font-bold mb-2">Knowledge-Base Driven Content Creator</h1>
    <p class="text-muted-foreground">Generate high-quality content powered by your knowledge base</p>
</div>

<div class="mb-8 rounded-xl border border-primary/20 bg-primary/5 p-6 border-l-4 border-l-primary flex flex-col md:flex-row justify-between gap-6">
    <div class="flex gap-4 flex-1">
        <div class="rounded-full bg-primary/10 p-2 h-fit">
            <i data-lucide="help-circle" class="w-6 h-6 text-primary"></i>
        </div>
        <div>
            <h3 class="font-semibold text-lg mb-2">How to Use This Page</h3>
            <ol class="text-sm text-muted-foreground space-y-2 list-decimal list-inside">
                <li>Enter your topic and generate posts using the form below</li>
                <li>Review and edit your generated posts on the right</li>
                <li>Select posts using checkboxes, then use "Bulk Images" or "Bulk Schedule"</li>
                <li>For individual posts, click "Post Now" or "Schedule" to choose platforms</li>
                <li>Platform selection happens when posting/scheduling, not during generation</li>
            </ol>
        </div>
    </div>
    
    {{-- Generator Toggles --}}
    <div class="flex flex-col gap-2 min-w-[200px]">
        <button @click="generator = 'video'; type = 'video'" 
                :class="generator === 'video' ? 'bg-slate-800 text-white shadow-xl shadow-black/20 font-bold border-white/10 ring-1 ring-white/20' : 'bg-slate-900 text-white/70 hover:text-white border-white/5 font-medium'" 
                class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all text-sm border">
            <i data-lucide="video" class="w-4 h-4"></i>
            Video Generator
        </button>
        <button @click="generator = 'post'; type = 'social-media'" 
                :class="generator === 'post' ? 'bg-slate-800 text-white shadow-xl shadow-black/20 font-bold border-white/10 ring-1 ring-white/20' : 'bg-slate-900 text-white/70 hover:text-white border-white/5 font-medium'" 
                class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all text-sm border">
            <i data-lucide="edit-3" class="w-4 h-4"></i>
            Post Generator
        </button>
        <button @click="generator = 'blog'; type = 'blog-post'" 
                :class="generator === 'blog' ? 'bg-slate-800 text-white shadow-xl shadow-black/20 font-bold border-white/10 ring-1 ring-white/20' : 'bg-slate-900 text-white/70 hover:text-white border-white/5 font-medium'" 
                class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all text-sm border">
            <i data-lucide="book" class="w-4 h-4"></i>
            Blog Generator
        </button>
        <button @click="generator = 'framework'; type = 'framework_calendar'" 
                :class="generator === 'framework' ? 'bg-slate-800 text-white shadow-xl shadow-black/20 font-bold border-white/10 ring-1 ring-white/20' : 'bg-slate-900 text-white/70 hover:text-white border-white/5 font-medium'" 
                class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all text-sm border">
            <i data-lucide="calendar" class="w-4 h-4"></i>
            1-Click Calendar
        </button>
    </div>
</div>
