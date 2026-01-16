{{-- Research Report - Prose Styles --}}
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
