<script module>
    import MainLayout from '../../Components/MainLayout.svelte';
    export const layout = MainLayout;
</script>

<script>
    import { router } from '@inertiajs/svelte';
    import Card from '../../Components/UI/Card.svelte';
    import Button from '../../Components/UI/Button.svelte';

    let { recap, filters } = $props();

    let month = $state(filters.month);
    let year = $state(filters.year);

    const months = [
        { value: 1, label: 'Januari' }, { value: 2, label: 'Februari' },
        { value: 3, label: 'Maret' }, { value: 4, label: 'April' },
        { value: 5, label: 'Mei' }, { value: 6, label: 'Juni' },
        { value: 7, label: 'Juli' }, { value: 8, label: 'Agustus' },
        { value: 9, label: 'September' }, { value: 10, label: 'Oktober' },
        { value: 11, label: 'November' }, { value: 12, label: 'Desember' }
    ];

    const years = [2024, 2025, 2026];

    function handleFilter() {
        router.get('/kehadiran', { month, year }, { preserveState: true });
    }
</script>

<div class="space-y-8">
     <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-4xl font-black text-posyandu-dark tracking-tight">Rekap Kehadiran</h1>
            <p class="text-xl text-gray-400 mt-2 font-bold">Ringkasan partisipasi balita bulanan</p>
        </div>
        <a href="/kehadiran/check-in">
            <Button class="px-8 py-4">
                Lakukan Check-In Hari Ini
            </Button>
        </a>
    </div>

    <!-- Filters -->
    <Card class="!p-6">
        <div class="flex flex-col sm:flex-row items-end gap-4">
            <div class="flex-1 space-y-2 w-full">
                <label for="month" class="text-sm font-black text-gray-400 uppercase tracking-widest">Bulan</label>
                <select 
                    id="month"
                    bind:value={month}
                    class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl px-5 py-4 font-bold text-posyandu-dark focus:border-posyandu-primary focus:ring-4 focus:ring-posyandu-primary/10 transition-all outline-none"
                >
                    {#each months as m}
                        <option value={m.value}>{m.label}</option>
                    {/each}
                </select>
            </div>
            <div class="flex-1 space-y-2 w-full">
                <label for="year" class="text-sm font-black text-gray-400 uppercase tracking-widest">Tahun</label>
                <select 
                    id="year"
                    bind:value={year}
                    class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl px-5 py-4 font-bold text-posyandu-dark focus:border-posyandu-primary focus:ring-4 focus:ring-posyandu-primary/10 transition-all outline-none"
                >
                    {#each years as y}
                        <option value={y}>{y}</option>
                    {/each}
                </select>
            </div>
            <Button onclick={handleFilter} class="w-full sm:w-40 bg-posyandu-secondary py-4">
                Terapkan
            </Button>
        </div>
    </Card>

    <!-- Table Recap -->
    <Card class="!p-0 overflow-hidden border-2 border-gray-100">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-gray-50/80 border-b-2 border-gray-100">
                    <th class="px-8 py-6 text-sm font-black text-gray-400 uppercase tracking-widest">Nama Balita</th>
                    <th class="px-8 py-6 text-sm font-black text-gray-400 uppercase tracking-widest">NIK</th>
                    <th class="px-8 py-6 text-sm font-black text-gray-400 uppercase tracking-widest text-center">Total Hadir</th>
                    <th class="px-8 py-6 text-sm font-black text-gray-400 uppercase tracking-widest text-right">Status Keaktifan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                {#each recap as item}
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-8 py-6">
                            <p class="text-xl font-bold text-posyandu-dark">{item.nama}</p>
                        </td>
                        <td class="px-8 py-6 text-gray-500 font-mono font-bold">
                            {item.nik}
                        </td>
                        <td class="px-8 py-6 text-center">
                            <span class="text-3xl font-black text-posyandu-primary">{item.kehadiran_count}</span>
                            <span class="text-gray-400 font-bold ml-1">kali</span>
                        </td>
                        <td class="px-8 py-6 text-right">
                            <span class="px-4 py-2 rounded-xl text-sm font-black 
                                {item.kehadiran_count > 0 ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600'}">
                                {item.kehadiran_count > 0 ? 'AKTIF' : 'TIDAK HADIR'}
                            </span>
                        </td>
                    </tr>
                {:else}
                    <tr>
                        <td colspan="4" class="px-8 py-20 text-center text-gray-400 font-bold">
                            Belum ada data balita.
                        </td>
                    </tr>
                {/each}
            </tbody>
        </table>
    </Card>
</div>
