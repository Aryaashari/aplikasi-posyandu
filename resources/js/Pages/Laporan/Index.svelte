<script module>
    import MainLayout from '../../Components/MainLayout.svelte';
    export const layout = MainLayout;
</script>

<script>
    import { router } from '@inertiajs/svelte';
    import Card from '../../Components/UI/Card.svelte';
    import Button from '../../Components/UI/Button.svelte';
    import { Doughnut } from 'svelte-chartjs';
    import { Chart as ChartJS, Title, Tooltip, Legend, ArcElement, CategoryScale } from 'chart.js';

    ChartJS.register(Title, Tooltip, Legend, ArcElement, CategoryScale);

    let { stats, filters } = $props();

    let month = $state(filters.month);
    let year = $state(filters.year);
    let weightTrendFilter = $state('all');

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
        router.get('/laporan', { month, year }, { preserveState: true });
    }

    // Prepare Chart Data
    const giziData = $derived({
        labels: Object.keys(stats.gizi),
        datasets: [{
            data: Object.values(stats.gizi),
            backgroundColor: [
                'rgba(239, 68, 68, 0.8)',   // Gizi Buruk (Red)
                'rgba(245, 158, 11, 0.8)',  // Gizi Kurang (Orange)
                'rgba(34, 197, 94, 0.8)',   // Gizi Baik (Green)
                'rgba(59, 130, 246, 0.8)'   // Gizi Lebih (Blue)
            ],
            borderWidth: 1
        }]
    });

    const stuntingData = $derived({
        labels: Object.keys(stats.stunting),
        datasets: [{
            data: Object.values(stats.stunting),
            backgroundColor: [
                'rgba(239, 68, 68, 0.8)',   // Sangat Pendek (Red)
                'rgba(245, 158, 11, 0.8)',  // Pendek (Orange)
                'rgba(34, 197, 94, 0.8)',   // Normal (Green)
                'rgba(168, 85, 247, 0.8)'   // Tinggi (Purple)
            ],
            borderWidth: 1
        }]
    });

    const chartOptions = {
        responsive: true,
        plugins: {
            legend: { position: 'bottom' }
        }
    };
</script>

<div class="space-y-8">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-4xl font-black text-posyandu-dark tracking-tight">Laporan Terpadu</h1>
            <p class="text-xl text-gray-400 mt-2 font-bold">Ringkasan kesehatan dan kehadiran bulanan</p>
        </div>
        <div class="flex gap-3">
            <a href="/laporan/export/excel?month={month}&year={year}" target="_blank">
                <Button variant="secondary" outline class="px-6 py-3 flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Unduh Excel
                </Button>
            </a>
            <a href="/laporan/export/pdf?month={month}&year={year}" target="_blank">
                <Button class="px-6 py-3 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Cetak PDF
                </Button>
            </a>
        </div>
    </div>

    <!-- Filters -->
    <Card class="!p-6">
        <div class="flex items-end gap-4">
            <div class="flex-1 space-y-2">
                <label for="month" class="text-sm font-black text-gray-400 uppercase tracking-widest">Pilih Bulan</label>
                <select id="month" bind:value={month} class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl px-5 py-4 font-bold text-posyandu-dark focus:border-posyandu-primary outline-none">
                    {#each months as m} <option value={m.value}>{m.label}</option> {/each}
                </select>
            </div>
            <div class="flex-1 space-y-2">
                <label for="year" class="text-sm font-black text-gray-400 uppercase tracking-widest">Pilih Tahun</label>
                <select id="year" bind:value={year} class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl px-5 py-4 font-bold text-posyandu-dark focus:border-posyandu-primary outline-none">
                    {#each years as y} <option value={y}>{y}</option> {/each}
                </select>
            </div>
            <Button onclick={handleFilter} class="w-40 bg-posyandu-secondary py-4">Tampilkan</Button>
        </div>
    </Card>

    <!-- Top Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <Card class="flex items-center gap-6 !p-6">
            <div class="w-16 h-16 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
            </div>
            <div>
                <p class="text-gray-400 font-bold mb-1">Total Balita Terdaftar</p>
                <p class="text-4xl font-black text-posyandu-dark">{stats.total_anak}</p>
            </div>
        </Card>
        
        <Card class="flex items-center gap-6 !p-6">
            <div class="w-16 h-16 rounded-2xl bg-green-100 text-green-600 flex items-center justify-center">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <div>
                <p class="text-gray-400 font-bold mb-1">Total Kehadiran Bulan Ini</p>
                <div class="flex items-end gap-3">
                    <p class="text-4xl font-black text-posyandu-dark">{stats.total_hadir}</p>
                    <p class="text-lg font-bold text-gray-400 mb-1">/ {stats.total_anak} Anak</p>
                </div>
            </div>
        </Card>
    </div>

    <!-- Weight Trends -->
    <Card class="!p-8">
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            <div>
                <h3 class="text-xl font-black text-posyandu-dark">Tren Berat Badan</h3>
                <p class="text-sm font-bold text-gray-400 mt-1">Dibandingkan dengan pengukuran bulan sebelumnya</p>
            </div>
            <div class="flex bg-gray-100 rounded-xl p-1 gap-1 w-full md:w-auto">
                <button 
                    onclick={() => weightTrendFilter = 'all'} 
                    class="flex-1 md:flex-none px-4 py-2 rounded-lg text-sm font-bold transition-all {weightTrendFilter === 'all' ? 'bg-white text-posyandu-dark shadow-sm' : 'text-gray-400 hover:text-gray-600'}"
                >
                    Semua
                </button>
                <button 
                    onclick={() => weightTrendFilter = 'L'} 
                    class="flex-1 md:flex-none px-4 py-2 rounded-lg text-sm font-bold transition-all {weightTrendFilter === 'L' ? 'bg-white text-posyandu-dark shadow-sm' : 'text-gray-400 hover:text-gray-600'}"
                >
                    Laki-laki
                </button>
                <button 
                    onclick={() => weightTrendFilter = 'P'} 
                    class="flex-1 md:flex-none px-4 py-2 rounded-lg text-sm font-bold transition-all {weightTrendFilter === 'P' ? 'bg-white text-posyandu-dark shadow-sm' : 'text-gray-400 hover:text-gray-600'}"
                >
                    Perempuan
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="flex items-center gap-4 bg-green-50 rounded-2xl p-5 border border-green-100 hover:shadow-md transition-all">
                <div class="w-14 h-14 rounded-xl bg-green-100 text-green-600 flex items-center justify-center">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
                </div>
                <div>
                    <p class="text-green-600/80 font-black text-sm mb-0.5 uppercase tracking-wide">Naik</p>
                    <p class="text-3xl font-black text-green-700">{stats.weight_trends[weightTrendFilter]?.naik || 0} <span class="text-sm font-bold text-green-600/60">Anak</span></p>
                </div>
            </div>

            <div class="flex items-center gap-4 bg-red-50 rounded-2xl p-5 border border-red-100 hover:shadow-md transition-all">
                <div class="w-14 h-14 rounded-xl bg-red-100 text-red-600 flex items-center justify-center">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" /></svg>
                </div>
                <div>
                    <p class="text-red-600/80 font-black text-sm mb-0.5 uppercase tracking-wide">Turun</p>
                    <p class="text-3xl font-black text-red-700">{stats.weight_trends[weightTrendFilter]?.turun || 0} <span class="text-sm font-bold text-red-600/60">Anak</span></p>
                </div>
            </div>

            <div class="flex items-center gap-4 bg-blue-50 rounded-2xl p-5 border border-blue-100 hover:shadow-md transition-all">
                <div class="w-14 h-14 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 12h14" /></svg>
                </div>
                <div>
                    <p class="text-blue-600/80 font-black text-sm mb-0.5 uppercase tracking-wide">Tetap</p>
                    <p class="text-3xl font-black text-blue-700">{stats.weight_trends[weightTrendFilter]?.tetap || 0} <span class="text-sm font-bold text-blue-600/60">Anak</span></p>
                </div>
            </div>
        </div>
    </Card>

    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <Card class="!p-8">
            <h3 class="text-xl font-black text-posyandu-dark text-center mb-6">Status Gizi (BB/U)</h3>
            <div class="h-64 flex justify-center">
                <Doughnut data={giziData} options={chartOptions} />
            </div>
        </Card>
        
        <Card class="!p-8">
            <h3 class="text-xl font-black text-posyandu-dark text-center mb-6">Status Pertumbuhan (TB/U)</h3>
            <div class="h-64 flex justify-center">
                <Doughnut data={stuntingData} options={chartOptions} />
            </div>
        </Card>
    </div>
</div>
