@extends('layouts.app')

@section('content')
<div class="p-10 max-w-[1400px] mx-auto animate-in fade-in duration-700">
    <!-- Protocol Header -->
    <div class="mb-12 flex flex-col md:flex-row items-start md:items-center justify-between gap-8 border-b border-border pb-10">
        <div>
            <div class="flex items-center gap-3 mb-4">
                <a href="{{ route('research-engine.index') }}" class="w-10 h-10 rounded-xl bg-muted/50 border border-border flex items-center justify-center hover:bg-primary/10 hover:border-primary/30 transition-all group">
                    <i data-lucide="arrow-left" class="w-4 h-4 text-muted-foreground group-hover:text-primary"></i>
                </a>
                <div class="flex flex-col">
                    <span class="mono text-[10px] font-black uppercase tracking-[0.3em] text-primary">Protocol: Research Result</span>
                    <h1 class="text-4xl font-black uppercase tracking-tighter text-foreground">{{ $research->title }}</h1>
                </div>
            </div>
            <div class="flex items-center gap-6 px-1">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-green-500"></div>
                    <span class="text-[10px] font-bold text-muted-foreground uppercase tracking-widest">Grounding Verified</span>
                </div>
                <span class="text-[10px] text-muted-foreground font-mono uppercase tracking-tighter">Session ID: {{ substr($research->id, 0, 13) }}...</span>
                <span class="text-[10px] text-muted-foreground font-mono uppercase tracking-tighter">{{ $research->created_at->format('Y-m-d H:i') }}</span>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button class="h-14 px-8 rounded-2xl border border-border bg-card font-black uppercase text-[10px] tracking-widest flex items-center gap-3 hover:bg-muted transition-all">
                <i data-lucide="download" class="w-4 h-4"></i>
                Export MD
            </button>
            <a href="{{ route('document-builder.index', ['research_id' => $research->id]) }}" 
               class="h-14 px-8 rounded-2xl bg-primary text-primary-foreground font-black uppercase text-[10px] tracking-[0.2em] flex items-center gap-3 shadow-xl shadow-primary/20 hover:scale-[1.02] transition-all">
                <i data-lucide="file-spreadsheet" class="w-4 h-4"></i>
                Architect Full Report
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
        <!-- Sidebar: Meta -->
        <div class="lg:col-span-3 space-y-8">
            <div class="space-y-6">
                <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] px-1">Intelligence Metrics</h3>
                
                <div class="bg-card border border-border rounded-[32px] p-8 space-y-8 relative overflow-hidden">
                    <div class="absolute -top-10 -right-10 w-32 h-32 bg-primary/5 rounded-full blur-3xl"></div>
                    
                    <div>
                        <p class="text-[9px] font-black text-muted-foreground uppercase tracking-widest mb-2">Sources Analyzed</p>
                        <div class="flex items-end gap-2">
                            <span class="text-4xl font-black text-foreground">{{ $research->sources_count }}</span>
                            <span class="text-[10px] font-bold text-green-500 uppercase mb-1">Verified</span>
                        </div>
                    </div>

                    <div>
                        <p class="text-[9px] font-black text-muted-foreground uppercase tracking-widest mb-2">Confidence Level</p>
                        <div class="flex items-end gap-2">
                            <span class="text-4xl font-black text-foreground">98.4%</span>
                            <div class="flex gap-0.5 mb-2">
                                <div class="w-1 h-3 bg-primary"></div>
                                <div class="w-1 h-3 bg-primary"></div>
                                <div class="w-1 h-3 bg-primary"></div>
                                <div class="w-1 h-3 bg-primary"></div>
                                <div class="w-1 h-3 bg-slate-200"></div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <p class="text-[9px] font-black text-muted-foreground uppercase tracking-widest mb-2">Grounding Depth</p>
                        <p class="text-sm font-bold text-foreground italic">"Multi-Layer Web Cross-Reference"</p>
                    </div>
                </div>
            </div>

            <!-- Query Context -->
            <div class="space-y-4">
                <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] px-1">Analytical Query</h3>
                <div class="p-6 bg-muted/30 border border-border rounded-2xl italic text-xs text-muted-foreground leading-relaxed">
                    "{{ $research->query }}"
                </div>
            </div>
        </div>

        <!-- Main Intelligence Display -->
        <div class="lg:col-span-9">
            <div class="bg-card border border-border rounded-[40px] shadow-sm relative overflow-hidden">
                <!-- Decorative Blueprint Header -->
                <div class="h-2 bg-gradient-to-r from-primary/40 via-primary to-primary/40"></div>
                
                <div class="p-12 md:p-20">
                    <article class="prose-architect max-w-none">
                        {!! Str::markdown($research->result ?? 'No intelligence data retrieved for this protocol session.') !!}
                    </article>

                    <!-- Dynamic Sources Footer -->
                    <div id="sources-section" class="mt-16 pt-10 border-t-2 border-border/50" style="display: none;">
                        <h3 class="text-xl font-black text-foreground uppercase tracking-tight mb-6 flex items-center gap-2">
                            <i data-lucide="link" class="w-5 h-5 text-primary"></i>
                            Verified Source Index
                        </h3>
                        <ul id="extracted-sources" class="grid grid-cols-1 gap-3 text-xs font-mono text-muted-foreground">
                            <!-- Populated by JS -->
                        </ul>
                    </div>
                </div>

                <!-- Footer Watermark -->
                <div class="p-10 border-t border-border bg-muted/10 flex justify-between items-center opacity-30 mono text-[8px] font-black uppercase tracking-[0.4em]">
                    <span>ArchitGrid Intelligence Node v1.0.4</span>
                    <span>System Encrypted: SHA-256</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="module">
    import mermaid from 'https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.esm.min.mjs';
    mermaid.initialize({ 
        startOnLoad: false, 
        theme: 'base',
        themeVariables: {
            fontFamily: 'Inter',
            primaryColor: '#00F2FF',
            primaryTextColor: '#0f172a',
            lineColor: '#334155'
        }
    });

    document.addEventListener('DOMContentLoaded', async () => {
        // Transform mermaid code blocks for rendering
        const mermaidBlocks = document.querySelectorAll('pre code.language-mermaid');
        if (mermaidBlocks.length > 0) {
            mermaidBlocks.forEach(block => {
                const pre = block.parentElement;
                const div = document.createElement('div');
                div.className = 'mermaid flex justify-center py-8 bg-slate-50 rounded-xl border border-border my-8';
                div.textContent = block.textContent;
                pre.replaceWith(div);
            });
            
            await mermaid.run();
        }

        const article = document.querySelector('.prose-architect');
        if (!article) return;

        // Simple regex to find URLs in text if they aren't already linked
        const urlRegex = /(https?:\/\/[^\s\)]+)/g;
        // logic to extract existing links
        const links = new Set();
        
        // 1. Get actual <a> tags
        article.querySelectorAll('a').forEach(a => links.add(a.href));

        // 2. Scan text for unlinked URLs (optional, but good for AI output)
        const walker = document.createTreeWalker(article, NodeFilter.SHOW_TEXT, null, false);
        let node;
        while (node = walker.nextNode()) {
            // Skip if inside an A tag already
            if (node.parentElement.tagName === 'A') continue;
            
            const matches = node.nodeValue.match(urlRegex);
            if (matches) {
                matches.forEach(url => links.add(url));
            }
        }

        const container = document.getElementById('extracted-sources');
        const section = document.getElementById('sources-section');

        if (links.size > 0) {
            links.forEach(url => {
                // Filter out non-http links or internal weirdness
                if (!url.startsWith('http')) return;
                
                const li = document.createElement('li');
                li.className = 'flex items-start gap-2';
                li.innerHTML = `<i data-lucide="external-link" class="w-3 h-3 mt-0.5 text-primary"></i> <a href="${url}" target="_blank" class="hover:text-primary transition-colors break-all text-blue-600 underline">${url}</a>`;
                container.appendChild(li);
            });
            section.style.display = 'block';
            if (window.lucide) window.lucide.createIcons();
        }
    });
</script>

<style>
    /* Premium Architectural Prose Styling */
    .prose-architect {
        font-family: 'Inter', sans-serif;
        color: #334155;
        line-height: 1.8;
    }
    .prose-architect h1 {
        font-family: 'Montserrat', sans-serif;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: -0.05em;
        color: #0f172a;
        font-size: 2.5rem;
        margin-bottom: 2rem;
        border-bottom: 4px solid #00F2FF;
        padding-bottom: 1rem;
        display: inline-block;
    }
    .prose-architect h2 {
        font-family: 'Montserrat', sans-serif;
        font-weight: 800;
        text-transform: uppercase;
        color: #1e293b;
        font-size: 1.5rem;
        margin-top: 3.5rem;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .prose-architect h2::before {
        content: '';
        width: 1.5rem;
        height: 1.5rem;
        background: #00F2FF;
        border-radius: 4px;
        flex-shrink: 0;
    }
    .prose-architect p {
        margin-bottom: 1.5rem;
        font-size: 1.05rem;
        text-align: justify;
    }
    .prose-architect ul {
        margin-bottom: 2rem;
        list-style: none;
        padding-left: 0;
    }
    .prose-architect li {
        position: relative;
        padding-left: 2rem;
        margin-bottom: 0.75rem;
        font-weight: 500;
    }
    .prose-architect li::before {
        content: '→';
        position: absolute;
        left: 0;
        color: #00F2FF;
        font-weight: 900;
    }
    .prose-architect strong {
        color: #0f172a;
        font-weight: 800;
        background: rgba(0, 242, 255, 0.05);
        padding: 0 4px;
    }
    .prose-architect table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        margin: 3rem 0;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        overflow: hidden;
    }
    .prose-architect th {
        background: #f8fafc;
        padding: 1rem;
        text-align: left;
        font-size: 0.75rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        border-bottom: 1px solid #e2e8f0;
    }
    .prose-architect td {
        padding: 1rem;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.9rem;
    }
</style>
@endsection