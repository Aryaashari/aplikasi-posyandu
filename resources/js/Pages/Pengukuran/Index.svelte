<script module>
    import MainLayout from '../../Components/MainLayout.svelte';
    export const layout = MainLayout;
</script>

<script>
    import Card from '../../Components/UI/Card.svelte';
    import { Link } from '@inertiajs/svelte';

    let { pengukuran = { data: [] } } = $props();
</script>

<div class="max-w-6xl mx-auto space-y-8">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-4xl font-black text-posyandu-dark tracking-tight">Riwayat Pengukuran</h1>
            <p class="text-gray-500 mt-1">Daftar hasil penimbangan dan pengukuran balita.</p>
        </div>
        <Link 
            href="/pengukuran/create" 
            class="px-6 py-4 bg-posyandu-primary text-white rounded-2xl font-bold text-lg hover:shadow-lg hover:shadow-posyandu-primary/20 transition-all flex items-center gap-2"
        >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
            </svg>
            Input Data Baru
        </Link>
    </div>

    <Card>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b-2 border-gray-50 text-gray-400 font-bold">
                        <th class="px-6 py-4">Anak</th>
                        <th class="px-6 py-4">Tanggal</th>
                        <th class="px-6 py-4">Berat (kg)</th>
                        <th class="px-6 py-4">Tinggi (cm)</th>
                        <th class="px-6 py-4">Status Gizi (BB/U)</th>
                        <th class="px-6 py-4">Status Tinggi (TB/U)</th>
                        <th class="px-6 py-4">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    {#each pengukuran.data as item}
                        <tr class="hover:bg-gray-50/50 transition-colors group">
                            <td class="px-6 py-5">
                                <div class="font-bold text-posyandu-dark text-lg">{item.anak.nama}</div>
                                <div class="text-sm text-gray-400">{item.anak.nik}</div>
                            </td>
                            <td class="px-6 py-5 text-gray-600">{item.tanggal_pengukuran}</td>
                            <td class="px-6 py-5 font-bold text-posyandu-primary">{item.berat_badan}</td>
                            <td class="px-6 py-5 text-gray-600">{item.tinggi_badan}</td>
                            <td class="px-6 py-5">
                                <span class="px-3 py-1 rounded-full text-sm font-bold 
                                    {item.status_gizi.includes('Buruk') ? 'bg-red-100 text-red-600' : 
                                     item.status_gizi.includes('Kurang') ? 'bg-yellow-100 text-yellow-600' : 
                                     'bg-green-100 text-green-600'}">
                                    {item.status_gizi}
                                </span>
                            </td>
                            <td class="px-6 py-5">
                                <span class="px-3 py-1 rounded-full text-sm font-bold 
                                    {item.status_stunting.includes('Stunted') ? 'bg-red-100 text-red-600' : 
                                     'bg-blue-100 text-blue-600'}">
                                    {item.status_stunting}
                                </span>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <Link 
                                        href="/pengukuran/{item.id}/edit"
                                        class="p-2 text-blue-500 hover:bg-blue-50 rounded-lg transition-all"
                                        title="Edit"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5M16.5 3.5a2.121 2.121 0 113 3L11.707 18.293a1 1 0 01-.707.293H8v-3a1 1 0 01.293-.707L16.5 3.5z" />
                                        </svg>
                                    </Link>
                                    <button 
                                        onclick={() => {
                                            if (confirm('Apakah Anda yakin ingin menghapus data pengukuran ini?')) {
                                                import('@inertiajs/svelte').then(({ router }) => {
                                                    router.delete(`/pengukuran/${item.id}`);
                                                });
                                            }
                                        }}
                                        class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-all"
                                        title="Hapus"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    {:else}
                        <tr>
                            <td colspan="7" class="px-6 py-20 text-center text-gray-400 font-medium">
                                Belum ada data pengukuran.
                            </td>
                        </tr>
                    {/each}
                </tbody>
            </table>
        </div>
    </Card>
</div>
