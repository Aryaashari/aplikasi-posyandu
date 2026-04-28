<script>
    import { onMount } from 'svelte';
    import Sidebar from './Sidebar.svelte';
    import Header from './Header.svelte';

    let { children } = $props();
    let sidebarOpen = $state(false);

    onMount(() => {
        // Otomatis buka sidebar hanya jika di layar besar (Desktop)
        if (window.innerWidth >= 1024) {
            sidebarOpen = true;
        }
    });

    function toggleSidebar() {
        sidebarOpen = !sidebarOpen;
    }
</script>

<div class="min-h-screen bg-posyandu-accent font-sans selection:bg-posyandu-primary/30">
    <!-- Component Sidebar -->
    <Sidebar isOpen={sidebarOpen} />

    <!-- Overlay for mobile -->
    {#if sidebarOpen}
        <button 
            onclick={toggleSidebar}
            class="fixed inset-0 z-40 bg-black/20 lg:hidden backdrop-blur-sm"
            aria-label="Close sidebar"
        ></button>
    {/if}

    <!-- Main Content wrapper -->
    <div class="transition-all duration-300 {sidebarOpen ? 'lg:pl-72' : 'lg:pl-20'}">
        <!-- Component Header -->
        <Header onToggleSidebar={toggleSidebar} sidebarOpen={sidebarOpen} />

        <!-- Main Page Viewport -->
        <main class="p-6 pt-28 md:p-10 md:pt-32">
            <div class="max-w-7xl mx-auto">
                {@render children()}
            </div>
        </main>
    </div>
</div>

<style>
    /* Styling khusus scrollbar agar tetap rapi */
    :global(body::-webkit-scrollbar) {
        width: 8px;
    }
    :global(body::-webkit-scrollbar-track) {
        background: #f1f5f9;
    }
    :global(body::-webkit-scrollbar-thumb) {
        background: #cbd5e1;
        border-radius: 10px;
    }
    :global(body::-webkit-scrollbar-thumb:hover) {
        background: #94a3b8;
    }
</style>
