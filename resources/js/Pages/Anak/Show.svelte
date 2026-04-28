<script module>
    import MainLayout from '../../Components/MainLayout.svelte';
    export const layout = MainLayout;
</script>

<script>
    import Card from '../../Components/UI/Card.svelte';
    import KmsChart from '../../Components/Pengukuran/KmsChart.svelte';
    import { Link } from '@inertiajs/svelte';

    let { anak, measurements = [], standards = [], attendance = [], imunisasi = [] } = $props();

    let activeTab = $state('bb'); // 'bb', 'tb', or 'imunisasi'

    const immunizationSchedule = [
        { name: 'HB-0', month: 0, type: 'Dasar' },
        { name: 'BCG', month: 1, type: 'Dasar' },
        { name: 'Polio 1', month: 1, type: 'Dasar' },
        { name: 'DPT-HB-Hib 1', month: 2, type: 'Dasar' },
        { name: 'Polio 2', month: 2, type: 'Dasar' },
        { name: 'PCV 1', month: 2, type: 'Dasar' },
        { name: 'DPT-HB-Hib 2', month: 3, type: 'Dasar' },
        { name: 'Polio 3', month: 3, type: 'Dasar' },
        { name: 'PCV 2', month: 3, type: 'Dasar' },
        { name: 'DPT-HB-Hib 3', month: 4, type: 'Dasar' },
        { name: 'Polio 4', month: 4, type: 'Dasar' },
        { name: 'IPV 1', month: 4, type: 'Dasar' },
        { name: 'Campak-Rubella (MR) 1', month: 9, type: 'Dasar' },
        { name: 'DPT-HB-Hib Lanjutan', month: 18, type: 'Lanjutan' },
        { name: 'Campak-Rubella (MR) Lanjutan', month: 18, type: 'Lanjutan' },
    ];

    // Logic for "Weight didn't increase" (T)
    const weightWarning = $derived.by(() => {
        if (measurements.length < 2) return null;
        const last = measurements[measurements.length - 1];
        const prev = measurements[measurements.length - 2];
        
        if (parseFloat(last.berat_badan) <= parseFloat(prev.berat_badan)) {
            return {
                type: 'danger',
                message: 'Berat badan tidak naik (T)! Perlu perhatian khusus.'
            };
        }
        return null;
    });

    // Logic for Risk Zone (Stunting/Underweight)
    const riskWarning = $derived.by(() => {
        if (measurements.length === 0) return null;
        const last = measurements[measurements.length - 1];
        
        if (last.status_gizi.includes('Buruk') || last.status_stunting.includes('Sangat Pendek')) {
            return {
                type: 'danger',
                message: 'Anak dalam zona risiko tinggi! Segera konsultasikan ke tenaga kesehatan.'
            };
        } else if (last.status_gizi.includes('Kurang') || last.status_stunting.includes('Pendek')) {
            return {
                type: 'warning',
                message: 'Anak dalam zona risiko sedang. Pantau asupan gizi.'
            };
        }
        return null;
    });
    
    // Logic for Next Immunization Alert
    const nextVaccine = $derived.by(() => {
        const birthday = new Date(anak.tanggal_lahir);
        const today = new Date();
        const diffMonths = (today.getFullYear() - birthday.getFullYear()) * 12 + (today.getMonth() - birthday.getMonth());
        
        // Find first vaccine in schedule not in immunization history
        return immunizationSchedule.find(v => {
            return !imunisasi.find(i => i.jenis_imunisasi === v.name);
        });
    });
</script>

<div class="max-w-6xl mx-auto space-y-8">
    <!-- Header/Back -->
    <div class="flex items-center gap-4">
        <Link href="/anak" class="p-2 text-gray-400 hover:text-posyandu-primary transition-colors">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </Link>
        <div>
            <h1 class="text-4xl font-black text-posyandu-dark tracking-tight">KMS Digital: {anak.nama}</h1>
            <p class="text-gray-500 mt-1">NIK: {anak.nik} • {anak.jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan'}</p>
        </div>
    </div>

    <!-- Alert Section -->
    {#if weightWarning || riskWarning}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {#if weightWarning}
                <div class="p-5 bg-red-50 border-2 border-red-100 rounded-2xl flex items-start gap-4">
                    <div class="p-2 bg-red-100 text-red-600 rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <p class="text-red-700 font-bold leading-tight">{weightWarning.message}</p>
                </div>
            {/if}
            {#if riskWarning}
                <div class="p-5 {riskWarning.type === 'danger' ? 'bg-red-50 border-red-100' : 'bg-yellow-50 border-yellow-100'} border-2 rounded-2xl flex items-start gap-4">
                    <div class="p-2 {riskWarning.type === 'danger' ? 'bg-red-100 text-red-600' : 'bg-yellow-100 text-yellow-600'} rounded-lg">
                         <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="{riskWarning.type === 'danger' ? 'text-red-700' : 'text-yellow-700'} font-bold leading-tight">{riskWarning.message}</p>
                </div>
            {/if}
        </div>
    {/if}

    <!-- Chart Section -->
    <Card class="overflow-hidden">
        <div class="border-b border-gray-100 p-2 flex gap-2">
            <button 
                onclick={() => activeTab = 'bb'}
                class="flex-1 py-4 text-center font-black transition-all rounded-xl
                       {activeTab === 'bb' ? 'bg-posyandu-primary text-white shadow-lg' : 'text-gray-400 hover:bg-gray-50'}"
            >
                Berat Badan (BB/U)
            </button>
            <button 
                onclick={() => activeTab = 'tb'}
                class="flex-1 py-4 text-center font-black transition-all rounded-xl
                       {activeTab === 'tb' ? 'bg-posyandu-primary text-white shadow-lg' : 'text-gray-400 hover:bg-gray-50'}"
            >
                Tinggi Badan (TB/U)
            </button>
            <button 
                onclick={() => activeTab = 'imunisasi'}
                class="flex-1 py-4 text-center font-black transition-all rounded-xl
                       {activeTab === 'imunisasi' ? 'bg-posyandu-primary text-white shadow-lg' : 'text-gray-400 hover:bg-gray-50'}"
            >
                Imunisasi
            </button>
        </div>

        <div class="p-6 sm:p-10">
            {#if activeTab === 'bb' || activeTab === 'tb'}
                <div class="mb-8 flex justify-between items-end">
                    <div>
                        <h2 class="text-2xl font-black text-posyandu-dark">Grafik Pertumbuhan</h2>
                        <p class="text-gray-400">Plotting berdasarkan standar WHO 2006 (0-60 Bulan)</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold text-gray-400">Update Terakhir</p>
                        <p class="text-lg font-black text-posyandu-dark">
                            {measurements.length > 0 ? measurements[measurements.length - 1].tanggal_pengukuran : '-'}
                        </p>
                    </div>
                </div>

                <KmsChart {measurements} {standards} type={activeTab} />
            {:else if activeTab === 'imunisasi'}
                <div class="space-y-8">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <div>
                            <h2 class="text-2xl font-black text-posyandu-dark">Status Imunisasi Dasar Lengkap (IDL)</h2>
                            <p class="text-gray-400 font-bold">Lacak kelengkapan vaksinasi balita rutin</p>
                        </div>
                        <div class="text-right">
                            <span class="px-5 py-2 rounded-full text-sm font-black 
                                {imunisasi.length >= immunizationSchedule.filter(v => v.type === 'Dasar').length 
                                    ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600'}">
                                {imunisasi.length >= immunizationSchedule.filter(v => v.type === 'Dasar').length 
                                    ? 'IDL LENGKAP' : 'BELUM LENGKAP'}
                            </span>
                        </div>
                    </div>

                    <!-- Next Vaccine Alert -->
                    {#if nextVaccine}
                        <div class="p-5 bg-blue-50 border-2 border-blue-100 rounded-2xl flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="p-3 bg-blue-100 text-blue-600 rounded-xl">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-blue-800 font-extrabold">Jadwal Selanjutnya</p>
                                    <p class="text-blue-600 font-bold text-sm">Vaksin {nextVaccine.name} (Anjuran: {nextVaccine.month} bln)</p>
                                </div>
                            </div>
                        </div>
                    {/if}

                    <!-- Progress Bar -->
                    <div class="relative pt-1">
                        <div class="overflow-hidden h-4 mb-4 text-xs flex rounded-full bg-gray-100">
                            <div 
                                style="width: {Math.min(100, (imunisasi.length / immunizationSchedule.filter(v => v.type === 'Dasar').length) * 100)}%" 
                                class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-posyandu-primary transition-all duration-500"
                            ></div>
                        </div>
                    </div>

                    <!-- Vaccine List -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {#each immunizationSchedule as vaccine}
                            {@const record = imunisasi.find(i => i.jenis_imunisasi === vaccine.name)}
                            <div class="p-5 border-2 {record ? 'border-green-100 bg-green-50/30' : 'border-gray-100 bg-white'} rounded-2xl flex items-center justify-between group transition-all">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 flex items-center justify-center rounded-xl font-black
                                        {record ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400'}">
                                        {vaccine.month}
                                    </div>
                                    <div>
                                        <p class="font-black text-posyandu-dark">{vaccine.name}</p>
                                        <p class="text-xs font-bold text-gray-400 capitalize">{vaccine.type} • {vaccine.month} bln</p>
                                    </div>
                                </div>

                                <div>
                                    {#if record}
                                        <div class="flex flex-col items-end">
                                            <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                            </svg>
                                            <span class="text-[10px] font-black text-green-600 uppercase mt-1">{record.tanggal_imunisasi}</span>
                                        </div>
                                    {:else}
                                        <button 
                                            onclick={() => {
                                                if(confirm(`Yakin sudah memberikan ${vaccine.name}?`)) {
                                                    import('@inertiajs/svelte').then(({ router }) => {
                                                        router.post('/imunisasi', {
                                                            id_anak: anak.id,
                                                            jenis_imunisasi: vaccine.name,
                                                            tanggal_imunisasi: new Date().toISOString().split('T')[0],
                                                            keterangan: 'Pemberian rutin'
                                                        }, { preserveScroll: true });
                                                    });
                                                }
                                            }}
                                            class="px-4 py-2 bg-white border-2 border-posyandu-primary text-posyandu-primary rounded-xl text-xs font-black hover:bg-posyandu-primary hover:text-white transition-all shadow-sm"
                                        >
                                            Beri Vaksin
                                        </button>
                                    {/if}
                                </div>
                            </div>
                        {/each}
                    </div>
                </div>
            {/if}
        </div>
    </Card>

    <!-- History & Attendance Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Measurement Short Log -->
        <Card class="!p-0 overflow-hidden">
            <div class="px-8 py-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <h3 class="text-xl font-black text-posyandu-dark">Riwayat Pengukuran</h3>
            </div>
            <div class="divide-y divide-gray-100">
                {#each measurements.slice().reverse() as m}
                    <div class="px-8 py-4 flex justify-between items-center">
                        <div>
                            <p class="font-bold text-posyandu-dark">{m.tanggal_pengukuran}</p>
                            <p class="text-sm text-gray-400 font-bold">{m.umur_bulan} Bulan</p>
                        </div>
                        <div class="text-right">
                            <p class="font-black text-posyandu-primary">{m.berat_badan} kg</p>
                            <p class="text-sm text-gray-500 font-bold">{m.tinggi_badan} cm</p>
                        </div>
                    </div>
                {:else}
                    <div class="p-10 text-center text-gray-400 font-bold">Belum ada riwayat.</div>
                {/each}
            </div>
        </Card>

        <!-- Attendance Log -->
        <Card class="!p-0 overflow-hidden">
            <div class="px-8 py-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <h3 class="text-xl font-black text-posyandu-dark">Riwayat Kehadiran</h3>
            </div>
            <div class="divide-y divide-gray-100">
                {#each attendance as entry}
                    <div class="px-8 py-4 flex justify-between items-center">
                        <div>
                            <p class="font-bold text-posyandu-dark">{entry.tanggal}</p>
                            <p class="text-sm text-gray-400 font-bold">{entry.waktu_checkin || '--:--'}</p>
                        </div>
                        <span class="px-3 py-1 bg-green-100 text-green-600 rounded-lg text-xs font-black">
                            HADIR
                        </span>
                    </div>
                {:else}
                    <div class="p-10 text-center text-gray-400 font-bold">Belum ada riwayat kehadiran.</div>
                {/each}
            </div>
        </Card>
    </div>
</div>
