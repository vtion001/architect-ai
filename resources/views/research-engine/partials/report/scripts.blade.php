{{-- Research Report - Scripts (Mermaid & Source Extraction) --}}
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
