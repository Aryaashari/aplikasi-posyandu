<script>
    import { usePage } from "@inertiajs/svelte";
    
    let { isOpen = true } = $props();
    const page = usePage();

    const menuItems = [
        { label: 'Dashboard', icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6', href: '/dashboard' },
        { label: 'Data Balita', icon: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z', href: '/anak' },
        { label: 'Pengukuran', icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01', href: '/pengukuran' },
        { label: 'Kehadiran', icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4', href: '/kehadiran' },
        { label: 'Laporan', icon: 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', href: '/laporan' },
        { label: 'Import Data', icon: 'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12', href: '/import' },
    ];
</script>

<aside 
    class="fixed inset-y-0 left-0 z-50 transition-all duration-300 transform bg-white border-r border-gray-200 shadow-lg overflow-hidden {isOpen ? 'w-72 translate-x-0' : 'w-0 -translate-x-full lg:w-20 lg:translate-x-0'}"
>
    <!-- Logo area -->
    <div class="flex items-center justify-center h-20 {isOpen ? 'px-6' : 'px-0'} border-b border-gray-100 bg-posyandu-primary/5 transition-all duration-300">
        <div class="flex items-center gap-3">
            <div class="flex-shrink-0 p-2 text-white rounded-xl bg-posyandu-primary shadow-sm">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            {#if isOpen}
                <span class="text-xl font-bold tracking-tight text-posyandu-dark whitespace-nowrap">
                    Sistem Posyandu
                </span>
            {/if}
        </div>
    </div>

    <!-- Navigation -->
    <nav class="p-4 mt-4 space-y-2">
        {#each menuItems as item}
            {@const isActive = page.url === item.href || (item.href !== '/dashboard' && page.url.startsWith(item.href))}
            <a 
                href={item.href}
                class="flex items-center gap-4 px-4 py-4 transition-all rounded-xl group
                       {isActive 
                        ? 'bg-posyandu-primary text-white shadow-md' 
                        : 'text-gray-600 hover:bg-posyandu-primary/10 hover:text-posyandu-dark'}"
            >
                <div class="flex-shrink-0">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d={item.icon} />
                    </svg>
                </div>
                {#if isOpen}
                    <span class="text-lg font-semibold">{item.label}</span>
                {/if}
            </a>
        {/each}
    </nav>

    <!-- Footer Sidebar -->
    {#if isOpen}
        <div class="absolute bottom-0 w-full p-6 border-t border-gray-100">
            <div class="flex items-center gap-4">
                <div class="flex-shrink-0 w-12 h-12 overflow-hidden bg-blue-100 rounded-full">
                    <img src="https://ui-avatars.com/api/?name=Kader+Posyandu&background=2D9CDB&color=fff" alt="User" />
                </div>
                <div class="overflow-hidden">
                    <p class="text-base font-bold text-posyandu-dark truncate">Ibu Kader Siti</p>
                    <p class="text-sm text-gray-500 truncate">Petugas Lapangan</p>
                </div>
            </div>
        </div>
    {/if}
</aside>
