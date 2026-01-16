<style>
    :root {
        --bg: #030712;
        --card: rgba(255, 255, 255, 0.03);
        --border: rgba(255, 255, 255, 0.08);
        --accent: #22d3ee;
    }

    body { 
        font-family: 'Inter', sans-serif; 
        background-color: var(--bg);
        color: #F8FAFC; 
        overflow-x: hidden; 
    }

    h1, h2, h3, h4, .heading { font-family: 'Plus Jakarta Sans', sans-serif; }

    /* Loader */
    .loader-overlay {
        position: fixed;
        inset: 0;
        background: #000;
        z-index: 9999;
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
    }
    .loader-bar-bg {
        width: 200px;
        height: 2px;
        background: rgba(255,255,255,0.1);
        position: relative;
        overflow: hidden;
        border-radius: 2px;
    }
    .loader-progress {
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        background: var(--accent);
        width: 0%;
    }

    /* Marquee */
    .marquee-container {
        mask-image: linear-gradient(to right, transparent, black 10%, black 90%, transparent);
    }
    .marquee-content {
        display: flex;
        gap: 4rem;
        width: max-content;
    }

    /* Glass */
    .glass {
        background: var(--card);
        backdrop-filter: blur(12px);
        border: 1px solid var(--border);
    }
    .glass-hover:hover {
        background: rgba(255,255,255,0.05);
        border-color: rgba(34,211,238,0.2);
    }
    
    /* Smooth Gradient Text */
    .gradient-text {
        background: linear-gradient(to right, #22d3ee, #3b82f6, #818cf8);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-size: 200% auto;
        animation: gradient-move 5s linear infinite;
    }
    @keyframes gradient-move { 0% { background-position: 0% 50%; } 100% { background-position: 200% 50%; } }

    /* Grid Background */
    .bg-grid {
        background-image: linear-gradient(to right, #ffffff05 1px, transparent 1px),
                          linear-gradient(to bottom, #ffffff05 1px, transparent 1px);
        background-size: 50px 50px;
        mask-image: radial-gradient(circle at center, black, transparent 80%);
    }

    /* 3D Tilt */
    .tilt-card {
        transform-style: preserve-3d;
        perspective: 1000px;
    }

    /* Spotlight Effect */
    .bento-card, .glass {
        position: relative;
    }
    .bento-card::after, .glass::after {
        content: "";
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: radial-gradient(800px circle at var(--mouse-x) var(--mouse-y), rgba(34, 211, 238, 0.15), transparent 40%);
        opacity: 0;
        transition: opacity 0.5s;
        pointer-events: none;
        z-index: 50;
    }
    .bento-card:hover::after, .glass:hover::after {
        opacity: 1;
    }
</style>
