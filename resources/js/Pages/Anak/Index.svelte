<script module>
    import MainLayout from '../../Components/MainLayout.svelte';
    export const layout = MainLayout;
</script>

<script>
    import { router } from '@inertiajs/svelte';
    import Card from '../../Components/UI/Card.svelte';
    import Button from '../../Components/UI/Button.svelte';
    import Input from '../../Components/UI/Input.svelte';

    let { anak, filters } = $props();

    let search = $state((() => filters.search || '')());
    
    $effect(() => {
        search = filters.search || '';
    });

    function handleSearch() {
        router.get('/anak', { search }, { preserveState: true, replace: true });
    }

    function handleDelete(id) {
        if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
            router.delete(`/anak/${id}`);
        }
    }
</script>

<div class="space-y-6 sm:space-y-8">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 md:gap-6">
        <div>
            <h1 class="text-3xl sm:text-4xl font-black text-posyandu-dark tracking-tight leading-tight">Data Balita</h1>
            <p class="text-lg sm:text-xl text-gray-400 mt-1 sm:mt-2 font-bold">Kelola informasi anak dan balita</p>
        </div>
        <a href="/anak/create" class="w-full md:w-auto">
            <Button class="w-full md:w-auto px-6 sm:px-8 py-3 sm:py-4 text-base sm:text-lg">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Anak Baru
            </Button>
        </a>
    </div>

    <!-- Filter & Search Card -->
    <Card class="!p-4 sm:!p-6">
        <div class="flex flex-col sm:flex-row gap-3 sm:gap-4">
            <div class="flex-1">
                <Input 
                    placeholder="Nama atau NIK..." 
                    bind:value={search}
                    onkeydown={(e) => e.key === 'Enter' && handleSearch()}
                />
            </div>
            <Button onclick={handleSearch} class="w-full sm:w-32 bg-posyandu-secondary py-3 sm:py-4">
                Cari
            </Button>
        </div>
    </Card>

    <!-- Table Card -->
    <Card class="!p-0 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-5 sm:px-8 py-4 sm:py-5 text-xs sm:text-sm font-bold text-gray-400 uppercase tracking-wider">Anak / Balita</th>
                        <th class="hidden md:table-cell px-8 py-5 text-sm font-bold text-gray-400 uppercase tracking-wider">NIK & KK</th>
                        <th class="hidden sm:table-cell px-5 sm:px-8 py-5 text-sm font-bold text-gray-400 uppercase tracking-wider">Jenis Kelamin</th>
                        <th class="hidden lg:table-cell px-8 py-5 text-sm font-bold text-gray-400 uppercase tracking-wider">Orang Tua</th>
                        <th class="px-5 sm:px-8 py-5 text-sm font-bold text-gray-400 uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    {#if anak.data.length === 0}
                        <tr>
                            <td colspan="5" class="px-5 sm:px-8 py-10 sm:py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="p-4 sm:p-6 bg-gray-100 rounded-full mb-4 text-gray-300">
                                        <svg class="w-12 h-12 sm:w-16 sm:h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                        </svg>
                                    </div>
                                    <p class="text-xl sm:text-2xl font-bold text-gray-400">Data Tidak Ditemukan</p>
                                </div>
                            </td>
                        </tr>
                    {:else}
                        {#each anak.data as item}
                            <tr class="hover:bg-gray-50/50 transition-colors group">
                                <td class="px-5 sm:px-8 py-4 sm:py-6">
                                    <div class="flex items-center gap-3 sm:gap-4">
                                        <div class="w-10 h-10 sm:w-14 sm:h-14 rounded-xl sm:rounded-2xl flex-shrink-0 flex items-center justify-center text-lg sm:text-2xl font-black {item.jenis_kelamin === 'L' ? 'bg-blue-100 text-blue-600' : 'bg-pink-100 text-pink-600'}">
                                            {item.nama.charAt(0)}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-lg sm:text-xl font-bold text-posyandu-dark truncate">{item.nama}</p>
                                            <p class="text-sm sm:text-base text-gray-400 font-bold">{item.tanggal_lahir}</p>
                                            <!-- Visible only on mobile -->
                                            <div class="md:hidden mt-1 text-xs font-bold text-gray-400 italic">
                                                NIK: {item.nik}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="hidden md:table-cell px-8 py-6 font-mono text-lg text-gray-500">
                                    <div>NIK: {item.nik}</div>
                                    <div class="text-sm opacity-60">KK: {item.no_kk}</div>
                                </td>
                                <td class="hidden sm:table-cell px-8 py-6">
                                    <span class="px-3 sm:px-4 py-1.5 sm:py-2 rounded-lg sm:rounded-xl text-xs sm:text-sm font-black {item.jenis_kelamin === 'L' ? 'bg-blue-50 text-blue-600 border border-blue-100' : 'bg-pink-50 text-pink-600 border border-pink-100'}">
                                        {item.jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan'}
                                    </span>
                                </td>
                                <td class="hidden lg:table-cell px-8 py-6">
                                    <p class="text-lg font-bold text-posyandu-dark">Ibu: {item.nama_ibu}</p>
                                    <p class="text-sm text-gray-400 font-bold">Ayah: {item.nama_ayah}</p>
                                </td>
                                <td class="px-5 sm:px-8 py-4 sm:py-6">
                                    <div class="flex items-center justify-end gap-1 sm:gap-3 transition-opacity">
                                    <a href="/anak/{item.id}" aria-label="Lihat KMS {item.nama}">
                                        <button class="p-2 sm:p-3 text-posyandu-secondary hover:bg-posyandu-secondary/10 rounded-lg sm:rounded-xl transition-all" aria-label="Lihat KMS">
                                            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                                            </svg>
                                        </button>
                                    </a>
                                    <a href="/anak/{item.id}/edit" aria-label="Ubah Data {item.nama}">
                                            <button class="p-2 sm:p-3 text-blue-500 hover:bg-blue-50 rounded-lg sm:rounded-xl transition-all" aria-label="Edit">
                                                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                        </a>
                                        <button 
                                            onclick={() => handleDelete(item.id)}
                                            class="p-2 sm:p-3 text-red-500 hover:bg-red-50 rounded-lg sm:rounded-xl transition-all" 
                                            aria-label="Hapus"
                                        >
                                            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        {/each}
                    {/if}
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        {#if anak.links && anak.data.length > 0}
            <div class="px-5 sm:px-8 py-4 sm:py-6 border-t border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-sm font-bold text-gray-400 order-2 sm:order-1">
                    Menampilkan <span class="text-posyandu-dark">{anak.from}</span> - <span class="text-posyandu-dark">{anak.to}</span> dari <span class="text-posyandu-dark">{anak.total}</span> balita
                </p>
                <div class="flex gap-2 order-1 sm:order-2 overflow-x-auto w-full sm:w-auto pb-2 sm:pb-0">
                    {#each anak.links as link}
                         <button
                            onclick={() => link.url && router.get(link.url)}
                            disabled={!link.url}
                            class="px-4 py-2 rounded-xl text-sm font-bold transition-all whitespace-nowrap
                                   {link.active ? 'bg-posyandu-primary text-white shadow-md' : 'bg-white text-gray-500 border border-gray-200 hover:bg-gray-50'}
                                   {!link.url ? 'opacity-50 cursor-not-allowed' : ''}"
                         >
                            {@html link.label}
                         </button>
                    {/each}
                </div>
            </div>
        {/if}
    </Card>
</div>
