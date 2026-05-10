<script module>
    import MainLayout from "../../Components/MainLayout.svelte";
    export const layout = MainLayout;
</script>

<script>
    import { router } from "@inertiajs/svelte";
    import Card from "../../Components/UI/Card.svelte";
    import Button from "../../Components/UI/Button.svelte";
    import { Doughnut } from "svelte-chartjs";
    import {
        Chart as ChartJS,
        Title,
        Tooltip,
        Legend,
        ArcElement,
        CategoryScale,
    } from "chart.js";

    ChartJS.register(Title, Tooltip, Legend, ArcElement, CategoryScale);

    let { stats, filters } = $props();

    let month = $state(filters.month);
    let year = $state(filters.year);
    let weightTrendFilter = $state("all");
    let showAttendanceDetail = $state(false);
    let showGiziDetail = $state(false);
    let showStuntingDetail = $state(false);

    const months = [
        { value: 1, label: "Januari" },
        { value: 2, label: "Februari" },
        { value: 3, label: "Maret" },
        { value: 4, label: "April" },
        { value: 5, label: "Mei" },
        { value: 6, label: "Juni" },
        { value: 7, label: "Juli" },
        { value: 8, label: "Agustus" },
        { value: 9, label: "September" },
        { value: 10, label: "Oktober" },
        { value: 11, label: "November" },
        { value: 12, label: "Desember" },
    ];

    const years = [2024, 2025, 2026];

    function handleFilter() {
        router.get("/laporan", { month, year }, { preserveState: true });
    }

    // Prepare Chart Data
    const giziData = $derived({
        labels: Object.keys(stats.gizi),
        datasets: [
            {
                data: Object.values(stats.gizi),
                backgroundColor: [
                    "rgba(239, 68, 68, 0.8)", // Gizi Buruk (Red)
                    "rgba(245, 158, 11, 0.8)", // Gizi Kurang (Orange)
                    "rgba(34, 197, 94, 0.8)", // Gizi Baik (Green)
                    "rgba(59, 130, 246, 0.8)", // Gizi Lebih (Blue)
                ],
                borderWidth: 1,
            },
        ],
    });

    const stuntingData = $derived({
        labels: Object.keys(stats.stunting),
        datasets: [
            {
                data: Object.values(stats.stunting),
                backgroundColor: [
                    "rgba(239, 68, 68, 0.8)", // Sangat Pendek (Red)
                    "rgba(245, 158, 11, 0.8)", // Pendek (Orange)
                    "rgba(34, 197, 94, 0.8)", // Normal (Green)
                    "rgba(168, 85, 247, 0.8)", // Tinggi (Purple)
                ],
                borderWidth: 1,
            },
        ],
    });

    const chartOptions = {
        responsive: true,
        plugins: {
            legend: { position: "bottom" },
        },
    };
</script>

<div class="space-y-8">
    <!-- Header Section -->
    <div
        class="flex flex-col md:flex-row md:items-center justify-between gap-4"
    >
        <div>
            <h1 class="text-4xl font-black text-posyandu-dark tracking-tight">
                Laporan Terpadu
            </h1>
            <p class="text-xl text-gray-400 mt-2 font-bold">
                Ringkasan kesehatan dan kehadiran bulanan
            </p>
        </div>
        <div class="flex gap-3">
            <a
                href="/laporan/export/excel?month={month}&year={year}"
                target="_blank"
            >
                <Button
                    variant="secondary"
                    outline
                    class="px-6 py-3 flex items-center gap-2"
                >
                    <svg
                        class="w-5 h-5 text-green-600"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                        />
                    </svg>
                    Unduh Excel
                </Button>
            </a>
            <a
                href="/laporan/export/pdf?month={month}&year={year}"
                target="_blank"
            >
                <Button class="px-6 py-3 flex items-center gap-2">
                    <svg
                        class="w-5 h-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"
                        />
                    </svg>
                    Cetak PDF
                </Button>
            </a>
        </div>
    </div>

    <!-- Filters -->
    <Card class="!p-6">
        <div class="flex items-end gap-4">
            <div class="flex-2 space-y-2">
                <label
                    for="month"
                    class="text-sm font-black text-gray-400 uppercase tracking-widest"
                    >Pilih Bulan</label
                >
                <select
                    id="month"
                    bind:value={month}
                    class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl px-5 py-4 font-bold text-posyandu-dark focus:border-posyandu-primary outline-none"
                >
                    {#each months as m}
                        <option value={m.value}>{m.label}</option>
                    {/each}
                </select>
            </div>
            <div class="flex-2 space-y-2">
                <label
                    for="year"
                    class="text-sm font-black text-gray-400 uppercase tracking-widest"
                    >Pilih Tahun</label
                >
                <select
                    id="year"
                    bind:value={year}
                    class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl px-5 py-4 font-bold text-posyandu-dark focus:border-posyandu-primary outline-none"
                >
                    {#each years as y}
                        <option value={y}>{y}</option>
                    {/each}
                </select>
            </div>
            <Button
                onclick={handleFilter}
                class="w-40 bg-posyandu-secondary py-4">Tampilkan</Button
            >
        </div>
    </Card>

    <!-- Top Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <Card class="flex items-center gap-6 !p-6">
            <div
                class="w-16 h-16 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center"
            >
                <svg
                    class="w-8 h-8"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                    ><path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                    /></svg
                >
            </div>
            <div>
                <p class="text-gray-400 font-bold mb-1">
                    Total Balita Terdaftar
                </p>
                <p class="text-4xl font-black text-posyandu-dark">
                    {stats.total_anak}
                </p>
            </div>
        </Card>

        <Card class="flex items-center gap-6 !p-6">
            <div
                class="w-16 h-16 rounded-2xl bg-green-100 text-green-600 flex items-center justify-center"
            >
                <svg
                    class="w-8 h-8"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                    ><path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                    /></svg
                >
            </div>
            <div>
                <p class="text-gray-400 font-bold mb-1">
                    Total Kehadiran Bulan Ini
                </p>
                <div class="flex items-end gap-3">
                    <p class="text-4xl font-black text-posyandu-dark">
                        {stats.total_hadir}
                    </p>
                    <p class="text-lg font-bold text-gray-400 mb-1">
                        / {stats.total_anak} Anak
                    </p>
                    <button 
                        onclick={() => showAttendanceDetail = true}
                        class="ml-auto p-2.5 text-posyandu-primary hover:bg-posyandu-primary/10 rounded-xl transition-all border-2 border-transparent hover:border-posyandu-primary/20"
                        title="Lihat Detail"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </button>
                </div>
            </div>
        </Card>
    </div>

    <!-- Weight Trends -->
    <Card class="!p-8">
        <div
            class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4"
        >
            <div>
                <h3 class="text-xl font-black text-posyandu-dark">
                    Tren Berat Badan
                </h3>
                <p class="text-sm font-bold text-gray-400 mt-1">
                    Dibandingkan dengan pengukuran bulan sebelumnya
                </p>
            </div>
            <div class="flex bg-gray-100 rounded-xl p-1 gap-1 w-full md:w-auto">
                <button
                    onclick={() => (weightTrendFilter = "all")}
                    class="flex-1 md:flex-none px-4 py-2 rounded-lg text-sm font-bold transition-all {weightTrendFilter ===
                    'all'
                        ? 'bg-white text-posyandu-dark shadow-sm'
                        : 'text-gray-400 hover:text-gray-600'}"
                >
                    Semua
                </button>
                <button
                    onclick={() => (weightTrendFilter = "L")}
                    class="flex-1 md:flex-none px-4 py-2 rounded-lg text-sm font-bold transition-all {weightTrendFilter ===
                    'L'
                        ? 'bg-white text-posyandu-dark shadow-sm'
                        : 'text-gray-400 hover:text-gray-600'}"
                >
                    Laki-laki
                </button>
                <button
                    onclick={() => (weightTrendFilter = "P")}
                    class="flex-1 md:flex-none px-4 py-2 rounded-lg text-sm font-bold transition-all {weightTrendFilter ===
                    'P'
                        ? 'bg-white text-posyandu-dark shadow-sm'
                        : 'text-gray-400 hover:text-gray-600'}"
                >
                    Perempuan
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div
                class="flex items-center gap-4 bg-green-50 rounded-2xl p-5 border border-green-100 hover:shadow-md transition-all"
            >
                <div
                    class="w-14 h-14 rounded-xl bg-green-100 text-green-600 flex items-center justify-center"
                >
                    <svg
                        class="w-8 h-8"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        ><path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2.5"
                            d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"
                        /></svg
                    >
                </div>
                <div>
                    <p
                        class="text-green-600/80 font-black text-sm mb-0.5 uppercase tracking-wide"
                    >
                        Naik
                    </p>
                    <p class="text-3xl font-black text-green-700">
                        {stats.weight_trends[weightTrendFilter]?.naik || 0}
                        <span class="text-sm font-bold text-green-600/60"
                            >Anak</span
                        >
                    </p>
                </div>
            </div>

            <div
                class="flex items-center gap-4 bg-red-50 rounded-2xl p-5 border border-red-100 hover:shadow-md transition-all"
            >
                <div
                    class="w-14 h-14 rounded-xl bg-red-100 text-red-600 flex items-center justify-center"
                >
                    <svg
                        class="w-8 h-8"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        ><path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2.5"
                            d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"
                        /></svg
                    >
                </div>
                <div>
                    <p
                        class="text-red-600/80 font-black text-sm mb-0.5 uppercase tracking-wide"
                    >
                        Turun
                    </p>
                    <p class="text-3xl font-black text-red-700">
                        {stats.weight_trends[weightTrendFilter]?.turun || 0}
                        <span class="text-sm font-bold text-red-600/60"
                            >Anak</span
                        >
                    </p>
                </div>
            </div>

            <div
                class="flex items-center gap-4 bg-blue-50 rounded-2xl p-5 border border-blue-100 hover:shadow-md transition-all"
            >
                <div
                    class="w-14 h-14 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center"
                >
                    <svg
                        class="w-8 h-8"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        ><path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2.5"
                            d="M5 12h14"
                        /></svg
                    >
                </div>
                <div>
                    <p
                        class="text-blue-600/80 font-black text-sm mb-0.5 uppercase tracking-wide"
                    >
                        Tetap
                    </p>
                    <p class="text-3xl font-black text-blue-700">
                        {stats.weight_trends[weightTrendFilter]?.tetap || 0}
                        <span class="text-sm font-bold text-blue-600/60"
                            >Anak</span
                        >
                    </p>
                </div>
            </div>
        </div>
    </Card>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <Card class="!p-8">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-black text-posyandu-dark">
                    Status Gizi (BB/U)
                </h3>
                <button 
                    onclick={() => showGiziDetail = true}
                    class="p-2 text-posyandu-primary hover:bg-posyandu-primary/5 rounded-xl transition-all"
                    title="Lihat Detail"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </button>
            </div>
            <div class="h-64 flex justify-center">
                <Doughnut data={giziData} options={chartOptions} />
            </div>
        </Card>

        <Card class="!p-8">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-black text-posyandu-dark">
                    Status Pertumbuhan (TB/U)
                </h3>
                <button 
                    onclick={() => showStuntingDetail = true}
                    class="p-2 text-posyandu-primary hover:bg-posyandu-primary/5 rounded-xl transition-all"
                    title="Lihat Detail"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </button>
            </div>
            <div class="h-64 flex justify-center">
                <Doughnut data={stuntingData} options={chartOptions} />
            </div>
        </Card>
    </div>
</div>

{#if showAttendanceDetail}
    <!-- svelte-ignore a11y_click_events_have_key_events -->
    <!-- svelte-ignore a11y_no_static_element_interactions -->
    <div 
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-posyandu-dark/60 backdrop-blur-sm transition-all"
        onclick={() => showAttendanceDetail = false}
    >
        <div 
            class="bg-white rounded-[2rem] shadow-2xl w-full max-w-lg overflow-hidden animate-in fade-in zoom-in duration-200"
            onclick={(e) => e.stopPropagation()}
        >
            <div class="p-8 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <div>
                    <h3 class="text-2xl font-black text-posyandu-dark leading-tight">Detail Kehadiran</h3>
                    <p class="text-posyandu-primary font-black uppercase text-xs tracking-[0.2em] mt-1">
                        {months.find(m => m.value == month).label} {year}
                    </p>
                </div>
                <button 
                    onclick={() => showAttendanceDetail = false}
                    class="p-3 hover:bg-gray-200 rounded-2xl transition-all text-gray-400 hover:text-gray-600"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <div class="p-8 space-y-10">
                <!-- Gender Breakdown -->
                <div>
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-1.5 h-6 bg-posyandu-primary rounded-full"></div>
                        <p class="text-sm font-black text-gray-400 uppercase tracking-widest">Jenis Kelamin</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-6 bg-blue-50 rounded-3xl border-2 border-blue-100 flex flex-col items-center">
                            <p class="text-blue-600 font-black text-4xl">{stats.hadir_detail.gender.L}</p>
                            <p class="text-blue-400 font-black text-xs uppercase tracking-widest mt-2">Laki-laki</p>
                        </div>
                        <div class="p-6 bg-pink-50 rounded-3xl border-2 border-pink-100 flex flex-col items-center">
                            <p class="text-pink-600 font-black text-4xl">{stats.hadir_detail.gender.P}</p>
                            <p class="text-pink-400 font-black text-xs uppercase tracking-widest mt-2">Perempuan</p>
                        </div>
                    </div>
                </div>

                <!-- Age Category Breakdown -->
                <div>
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-1.5 h-6 bg-posyandu-secondary rounded-full"></div>
                        <p class="text-sm font-black text-gray-400 uppercase tracking-widest">Kategori Umur</p>
                    </div>
                    <div class="space-y-3">
                        {#each Object.entries(stats.hadir_detail.category) as [cat, count]}
                            <div class="flex items-center justify-between p-5 bg-gray-50 rounded-2xl border-2 border-gray-100/50 hover:border-gray-200 transition-all">
                                <div class="flex items-center gap-3">
                                    <span class="w-2 h-2 rounded-full bg-posyandu-primary/30"></span>
                                    <span class="font-black text-posyandu-dark text-lg">
                                        {cat} <span class="text-gray-400 font-bold ml-0.5">bln</span>
                                    </span>
                                </div>
                                <span class="px-5 py-1.5 bg-white shadow-sm border border-gray-100 rounded-xl font-black text-posyandu-primary text-2xl">
                                    {count}
                                </span>
                            </div>
                        {/each}
                    </div>
                </div>
            </div>

            <div class="p-8 bg-gray-50 flex">
                <Button onclick={() => showAttendanceDetail = false} class="w-full py-4 text-lg">
                    Tutup Detail
                </Button>
            </div>
        </div>
    </div>
{/if}

{#if showGiziDetail}
    <!-- svelte-ignore a11y_click_events_have_key_events -->
    <!-- svelte-ignore a11y_no_static_element_interactions -->
    <div 
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-posyandu-dark/60 backdrop-blur-sm transition-all"
        onclick={() => showGiziDetail = false}
    >
        <div 
            class="bg-white rounded-[2rem] shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden animate-in fade-in zoom-in duration-200 flex flex-col"
            onclick={(e) => e.stopPropagation()}
        >
            <div class="p-8 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <div>
                    <h3 class="text-2xl font-black text-posyandu-dark leading-tight">Detail Status Gizi</h3>
                    <p class="text-posyandu-primary font-black uppercase text-xs tracking-[0.2em] mt-1">
                        {months.find(m => m.value == month).label} {year}
                    </p>
                </div>
                <button 
                    onclick={() => showGiziDetail = false}
                    class="p-3 hover:bg-gray-200 rounded-2xl transition-all text-gray-400 hover:text-gray-600"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <div class="p-8 overflow-y-auto space-y-8 flex-1">
                {#each Object.entries(stats.gizi_detail) as [status, list]}
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-3 h-3 rounded-full 
                                    {status === 'Gizi Buruk' ? 'bg-red-500' : 
                                     status === 'Gizi Kurang' ? 'bg-amber-500' : 
                                     status === 'Gizi Baik' ? 'bg-green-500' : 'bg-blue-500'}">
                                </div>
                                <p class="font-black text-posyandu-dark uppercase tracking-wider">{status}</p>
                            </div>
                            <span class="px-3 py-1 bg-gray-100 rounded-lg text-sm font-black text-gray-500">
                                {list.length} Anak
                            </span>
                        </div>
                        
                        {#if list.length > 0}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                {#each list as anak}
                                    <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100 flex items-center justify-between">
                                        <div>
                                            <p class="font-bold text-posyandu-dark">{anak.nama}</p>
                                            <p class="text-xs text-gray-400 font-bold uppercase">{anak.jk === 'L' ? 'Laki-laki' : 'Perempuan'}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-black text-posyandu-primary">{anak.bb} kg</p>
                                            <p class="text-[10px] text-gray-400 font-bold uppercase">{anak.tanggal}</p>
                                        </div>
                                    </div>
                                {/each}
                            </div>
                        {:else}
                            <div class="p-6 text-center text-gray-400 italic bg-gray-50/50 rounded-2xl border border-dashed border-gray-200">
                                Tidak ada anak dalam kategori ini.
                            </div>
                        {/if}
                    </div>
                {/each}
            </div>

            <div class="p-8 bg-gray-50 flex">
                <Button onclick={() => showGiziDetail = false} class="w-full py-4 text-lg">
                    Tutup Detail
                </Button>
            </div>
        </div>
    </div>
{/if}

{#if showStuntingDetail}
    <!-- svelte-ignore a11y_click_events_have_key_events -->
    <!-- svelte-ignore a11y_no_static_element_interactions -->
    <div 
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-posyandu-dark/60 backdrop-blur-sm transition-all"
        onclick={() => showStuntingDetail = false}
    >
        <div 
            class="bg-white rounded-[2rem] shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden animate-in fade-in zoom-in duration-200 flex flex-col"
            onclick={(e) => e.stopPropagation()}
        >
            <div class="p-8 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <div>
                    <h3 class="text-2xl font-black text-posyandu-dark leading-tight">Detail Status Pertumbuhan</h3>
                    <p class="text-posyandu-primary font-black uppercase text-xs tracking-[0.2em] mt-1">
                        {months.find(m => m.value == month).label} {year}
                    </p>
                </div>
                <button 
                    onclick={() => showStuntingDetail = false}
                    class="p-3 hover:bg-gray-200 rounded-2xl transition-all text-gray-400 hover:text-gray-600"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <div class="p-8 overflow-y-auto space-y-8 flex-1">
                {#each Object.entries(stats.stunting_detail) as [status, list]}
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-3 h-3 rounded-full 
                                    {status.includes('Sangat Pendek') ? 'bg-red-500' : 
                                     status.includes('Pendek') ? 'bg-amber-500' : 
                                     status.includes('Normal') ? 'bg-green-500' : 'bg-purple-500'}">
                                </div>
                                <p class="font-black text-posyandu-dark uppercase tracking-wider">{status}</p>
                            </div>
                            <span class="px-3 py-1 bg-gray-100 rounded-lg text-sm font-black text-gray-500">
                                {list.length} Anak
                            </span>
                        </div>
                        
                        {#if list.length > 0}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                {#each list as anak}
                                    <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100 flex items-center justify-between">
                                        <div>
                                            <p class="font-bold text-posyandu-dark">{anak.nama}</p>
                                            <p class="text-xs text-gray-400 font-bold uppercase">{anak.jk === 'L' ? 'Laki-laki' : 'Perempuan'}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-black text-posyandu-secondary">{anak.tb} cm</p>
                                            <p class="text-[10px] text-gray-400 font-bold uppercase">{anak.tanggal}</p>
                                        </div>
                                    </div>
                                {/each}
                            </div>
                        {:else}
                            <div class="p-6 text-center text-gray-400 italic bg-gray-50/50 rounded-2xl border border-dashed border-gray-200">
                                Tidak ada anak dalam kategori ini.
                            </div>
                        {/if}
                    </div>
                {/each}
            </div>

            <div class="p-8 bg-gray-50 flex">
                <Button onclick={() => showStuntingDetail = false} class="w-full py-4 text-lg">
                    Tutup Detail
                </Button>
            </div>
        </div>
    </div>
{/if}
