<script module>
    import MainLayout from '../../Components/MainLayout.svelte';
    export const layout = MainLayout;
</script>

<script>
    import FormPengukuran from '../../Components/Pengukuran/FormPengukuran.svelte';
    
    let { children = [] } = $props();
</script>

<div class="max-w-4xl mx-auto space-y-8">
    <!-- Header Section -->
    <div class="flex items-center gap-4">
        <a href="/pengukuran" class="p-2 text-gray-400 hover:text-posyandu-primary transition-colors">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </a>
        <div>
            <h1 class="text-4xl font-black text-posyandu-dark tracking-tight">Input Hasil Pengukuran</h1>
            <p class="text-gray-500 mt-1">Catat data berat, tinggi, dan lingkar kepala balita secara rutin.</p>
        </div>
    </div>

    <!-- Form Organism -->
    <FormPengukuran children_list={children} />
</div>
