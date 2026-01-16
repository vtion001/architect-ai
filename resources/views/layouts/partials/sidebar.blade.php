{{-- Sidebar Navigation --}}
<aside class="w-64 bg-sidebar text-sidebar-foreground flex flex-col" x-data="{ openSwitcher: false }">
    {{-- Sidebar Header --}}
    <div class="p-6 border-b border-sidebar-border">
        <a href="/dashboard" class="flex items-center gap-3">
            <div class="w-10 h-10 shrink-0">
                @php
                    $sidebarLogo = app(\App\Models\Tenant::class)->metadata['logo_url'] ?? 'https://res.cloudinary.com/dbviya1rj/image/upload/v1767554289/xe54y8zsvhursjrpbnvm.png';
                @endphp
                <img src="{{ $sidebarLogo }}" class="w-full h-full object-contain" alt="Identity Logo">
            </div>
            <div>
                <h1 class="text-sm font-black tracking-tight text-white uppercase truncate max-w-[120px]">{{ app(\App\Models\Tenant::class)->name }}</h1>
                <p class="text-[10px] font-bold text-primary uppercase tracking-widest">Workspace Node</p>
            </div>
        </a>
    </div>

    {{-- Workspace Switcher --}}
    @include('layouts.partials.sidebar.workspace-switcher')

    {{-- Navigation Menu --}}
    @include('layouts.partials.sidebar.navigation')

    {{-- Sidebar Footer --}}
    @include('layouts.partials.sidebar.footer')
</aside>
