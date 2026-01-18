{{--
    Note/Task Widget - Floating Button
    
    ISOLATED: This partial controls ONLY the floating toggle button.
    Changes to widget functionality will NOT affect this button's position.
    
    Fixed Position:
    - bottom: 96px
    - right: 24px
    - z-index: 99999
    
    The button position is INDEPENDENT of all widget logic.
--}}

<button @click="isOpen = !isOpen" 
        class="widget-floating-button"
        aria-label="Toggle Command Center">
    
    {{-- Home Icon (when closed) --}}
    <svg x-show="!isOpen" 
         xmlns="http://www.w3.org/2000/svg" 
         width="24" height="24" 
         viewBox="0 0 24 24" 
         fill="none" 
         stroke="currentColor" 
         stroke-width="2" 
         stroke-linecap="round" 
         stroke-linejoin="round" 
         class="w-6 h-6">
        <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
        <polyline points="9 22 9 12 15 12 15 22"/>
    </svg>
    
    {{-- Chevron Icon (when open) --}}
    <svg x-show="isOpen" 
         x-cloak 
         xmlns="http://www.w3.org/2000/svg" 
         width="24" height="24" 
         viewBox="0 0 24 24" 
         fill="none" 
         stroke="currentColor" 
         stroke-width="2" 
         stroke-linecap="round" 
         stroke-linejoin="round" 
         class="w-6 h-6">
        <path d="m18 8-6 6-6-6"/>
    </svg>

    {{-- Notification Badge --}}
    <template x-if="pendingCount > 0 && !isOpen">
        <span class="absolute top-3 right-3 flex h-3 w-3">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500 border-2 border-indigo-500"></span>
        </span>
    </template>
</button>

<style>
/* Floating Button - FIXED POSITION (Never changes) */
.widget-floating-button {
    position: fixed;
    bottom: 96px;
    right: 24px;
    z-index: 99999;
    
    /* Appearance */
    width: 56px;
    height: 56px;
    background-color: #6366f1;
    border-radius: 9999px;
    box-shadow: 0 8px 24px rgba(99, 102, 241, 0.4);
    
    /* Layout */
    display: flex;
    align-items: center;
    justify-content: center;
    
    /* Colors */
    color: white;
    
    /* Interactions */
    transition: transform 0.15s ease;
    overflow: hidden;
    border: none;
    cursor: pointer;
}

.widget-floating-button:hover {
    transform: scale(1.1);
}

.widget-floating-button:active {
    transform: scale(0.95);
}
</style>
