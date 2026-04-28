<script module>
    import MainLayout from '../../Components/MainLayout.svelte';
    export const layout = MainLayout;
</script>

<script>
    import { router } from '@inertiajs/svelte';
    import Card from '../../Components/UI/Card.svelte';
    import Button from '../../Components/UI/Button.svelte';
    import Input from '../../Components/UI/Input.svelte';

    let { children, filters, today } = $props();

    let search = $state((() => filters.search || '')());
    
    function handleSearch() {
        router.get('/kehadiran/check-in', { search }, { preserveState: true, replace: true });
    }

    function checkIn(id) {
        router.post('/kehadiran', { id_anak: id }, {
            onSuccess: () => {
                // Flash message handled by Inertia
            }
        });
    }
</script>

<div class="max-w-4xl mx-auto space-y-8">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-4xl font-black text-posyandu-dark tracking-tight leading-tight">Presensi Hari Ini</h1>
            <p class="text-xl text-gray-400 mt-2 font-bold italic">{today}</p>
        </div>
        <a href="/kehadiran">
            <Button variant="secondary" outline class="px-6 py-3">
                Lihat Rekap Bulanan
            </Button>
        </a>
    </div>

    <!-- Search Card -->
    <Card class="!p-4 sm:!p-6">
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <Input 
                    placeholder="Cari Nama atau NIK Balita..." 
                    bind:value={search}
                    onkeydown={(e) => e.key === 'Enter' && handleSearch()}
                />
            </div>
            <Button onclick={handleSearch} class="w-full sm:w-32 bg-posyandu-primary py-4">
                Cari
            </Button>
        </div>
    </Card>

    <!-- Children List -->
    <Card class="!p-0 overflow-hidden border-2 border-gray-100">
        <div class="divide-y divide-gray-100">
            {#if children.length === 0}
                <div class="p-20 text-center text-gray-400 font-bold">
                    Tidak ada data balita ditemukan.
                </div>
            {:else}
                {#each children as child}
                    <div class="p-6 flex items-center justify-between hover:bg-gray-50 transition-colors group">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-2xl font-black">
                                {child.nama.charAt(0)}
                            </div>
                            <div>
                                <p class="text-xl font-bold text-posyandu-dark">{child.nama}</p>
                                <p class="text-gray-400 text-sm font-bold">NIK: {child.nik}</p>
                            </div>
                        </div>

                        <div>
                            {#if child.is_present}
                                <div class="px-6 py-3 bg-green-100 text-green-600 rounded-xl font-black flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Sudah Hadir
                                </div>
                            {:else}
                                <Button 
                                    onclick={() => checkIn(child.id)}
                                    class="px-8 py-3 bg-posyandu-primary"
                                >
                                    Hadir
                                </Button>
                            {/if}
                        </div>
                    </div>
                {/each}
            {/if}
        </div>
    </Card>
</div>
