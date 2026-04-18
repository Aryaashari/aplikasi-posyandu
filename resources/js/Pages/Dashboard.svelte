<script>
    import MainLayout from '../Components/MainLayout.svelte';

    const statCards = [
        { label: 'Total Balita', value: '124', change: '+5% bulan ini', color: 'bg-posyandu-secondary' },
        { label: 'Hadir Bulan Ini', value: '98', change: '85% tingkat kehadiran', color: 'bg-posyandu-primary' },
        { label: 'Jadwal Terdekat', value: '25 Apr', change: 'Imunisasi Polio', color: 'bg-orange-500' },
    ];
</script>

<MainLayout>
    <div class="space-y-8">
        <!-- Dashboard Header -->
        <div class="flex flex-col gap-2">
            <h2 class="text-3xl font-extrabold text-posyandu-dark">Ikhtisar Dashboard</h2>
            <p class="text-lg text-gray-500 font-medium">Pantau data kesehatan balita dan kegiatan Posyandu hari ini.</p>
        </div>

        <!-- Stat Cards -->
        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
            {#each statCards as stat}
                <div class="p-8 transition-all bg-white shadow-sm rounded-3xl border-b-4 border-gray-100 hover:shadow-md hover:border-posyandu-primary/30 group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-4 rounded-2xl {stat.color} text-white shadow-lg shadow-{stat.color.split('-')[1]}-200">
                             <!-- Placeholder icon -->
                             <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                             </svg>
                        </div>
                    </div>
                    <p class="text-lg font-bold text-gray-400 uppercase tracking-widest">{stat.label}</p>
                    <h3 class="mt-1 text-5xl font-black text-posyandu-dark">{stat.value}</h3>
                    <p class="mt-4 text-base font-bold text-gray-400 group-hover:text-posyandu-primary transition-colors">
                        {stat.change}
                    </p>
                </div>
            {/each}
        </div>

        <!-- Dynamic Content Example (Table) -->
        <div class="p-8 bg-white shadow-lg rounded-[2rem] border border-gray-100">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-2xl font-bold text-posyandu-dark">Daftar Kehadiran Terbaru</h3>
                <button class="px-6 py-3 font-bold text-white bg-posyandu-primary rounded-xl shadow-lg shadow-green-200 hover:scale-105 transition-all">
                    Tambah Data
                </button>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-gray-400 border-b border-gray-100">
                            <th class="pb-4 text-base font-bold uppercase tracking-wider">Nama Balita</th>
                            <th class="pb-4 text-base font-bold uppercase tracking-wider">Umur</th>
                            <th class="pb-4 text-base font-bold uppercase tracking-wider">Status</th>
                            <th class="pb-4 text-base font-bold uppercase tracking-wider text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        {#each [1, 2, 3] as i}
                            <tr class="group hover:bg-gray-50 transition-colors">
                                <td class="py-6">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center text-posyandu-primary font-bold">
                                            A{i}
                                        </div>
                                        <div>
                                            <p class="text-lg font-bold text-posyandu-dark">Ananda Budi Santoso {i}</p>
                                            <p class="text-sm text-gray-400">RW 0{i}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-6 text-lg font-semibold text-gray-600">24 Bulan</td>
                                <td class="py-6">
                                    <span class="px-4 py-2 text-sm font-bold text-green-700 bg-green-100 rounded-lg">Sehat</span>
                                </td>
                                <td class="py-6 text-right">
                                    <button 
                                        class="p-3 text-posyandu-secondary hover:bg-posyandu-secondary/10 rounded-xl transition-all"
                                        aria-label="Lihat Detail"
                                    >
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        {/each}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</MainLayout>
