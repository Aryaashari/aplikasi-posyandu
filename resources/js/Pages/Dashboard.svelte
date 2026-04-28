<script module>
    import MainLayout from '../Components/MainLayout.svelte';
    export const layout = MainLayout;
</script>

<script>
    import { Link } from '@inertiajs/svelte';

    let { stats = { total_balita: 0, hadir_bulan_ini: 0 }, recent_attendance = [] } = $props();

    const statCards = $derived([
        { 
            label: 'Total Balita Terdaftar', 
            value: stats.total_balita, 
            change: 'Data aktif bulan ini', 
            color: 'bg-posyandu-secondary',
            icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z'
        },
        { 
            label: 'Kehadiran Bulan Ini', 
            value: stats.hadir_bulan_ini, 
            change: `${stats.total_balita > 0 ? Math.round((stats.hadir_bulan_ini / stats.total_balita) * 100) : 0}% Tingkat Partisipasi`, 
            color: 'bg-posyandu-primary',
            icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'
        },
        { 
            label: 'Tugas Cepat', 
            value: 'Laporan', 
            change: 'Cetak data bulan berjalan', 
            color: 'bg-orange-500',
            icon: 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'
        },
    ]);
</script>

<div class="space-y-8">
    <!-- Dashboard Header -->
    <div class="flex flex-col gap-2">
        <h2 class="text-3xl font-extrabold text-posyandu-dark">Ikhtisar Dashboard</h2>
        <p class="text-lg text-gray-500 font-medium">Pantau ringkasan data kepesertaan dan kehadiran Posyandu Anda.</p>
    </div>

    <!-- Stat Cards -->
    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
        {#each statCards as stat, idx}
            <div class="p-8 transition-all bg-white shadow-sm rounded-3xl border-b-4 border-gray-100 hover:shadow-md hover:border-posyandu-primary/30 group">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-4 rounded-2xl {stat.color} text-white shadow-lg">
                         <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d={stat.icon} />
                         </svg>
                    </div>
                </div>
                <p class="text-lg font-bold text-gray-400 tracking-widest">{stat.label}</p>
                <h3 class="mt-1 text-5xl font-black text-posyandu-dark">{stat.value}</h3>
                <p class="mt-4 text-base font-bold text-gray-400 group-hover:text-posyandu-primary transition-colors">
                    {#if idx === 2}
                        <Link href="/laporan" class="text-posyandu-primary hover:underline">{stat.change}</Link>
                    {:else}
                        {stat.change}
                    {/if}
                </p>
            </div>
        {/each}
    </div>

    <!-- Dynamic Content Checkin -->
    <div class="p-8 bg-white shadow-lg rounded-[2rem] border border-gray-100 overflow-hidden">
        <div class="flex items-center justify-between mb-8">
            <h3 class="text-2xl font-bold text-posyandu-dark">Daftar Kehadiran Terbaru</h3>
            <Link href="/kehadiran/check-in" class="px-6 py-3 font-bold text-white bg-posyandu-primary rounded-xl shadow-lg shadow-green-200 hover:scale-105 transition-all">
                Mulai Check-In
            </Link>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-gray-400 border-b border-gray-100">
                        <th class="pb-4 text-sm font-bold uppercase tracking-wider">Nama Balita</th>
                        <th class="pb-4 text-sm font-bold uppercase tracking-wider">Tanggal & Waktu</th>
                        <th class="pb-4 text-sm font-bold uppercase tracking-wider">Status</th>
                        <th class="pb-4 text-sm font-bold uppercase tracking-wider text-right">Profil</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    {#if recent_attendance && recent_attendance.length > 0}
                        {#each recent_attendance as entry}
                            <tr class="group hover:bg-gray-50 transition-colors">
                                <td class="py-6 min-w-[250px]">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-500 font-bold text-xl">
                                            {entry.anak?.nama?.charAt(0) || '-'}
                                        </div>
                                        <div>
                                            <p class="text-lg font-bold text-posyandu-dark">{entry.anak?.nama || 'Unknown'}</p>
                                            <p class="text-sm text-gray-400 font-mono">NIK: {entry.anak?.nik || '-'}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-6 text-lg font-semibold text-gray-600">
                                    {entry.tanggal} <br>
                                    <span class="text-sm text-gray-400">{entry.waktu_checkin || ''}</span>
                                </td>
                                <td class="py-6">
                                    <span class="px-4 py-2 text-sm font-bold {entry.status === 'Hadir' ? 'text-green-700 bg-green-100' : 'text-orange-700 bg-orange-100'} rounded-lg">
                                        {entry.status}
                                    </span>
                                </td>
                                <td class="py-6 text-right">
                                    <Link 
                                        href={`/anak/${entry.id_anak}`}
                                        class="inline-block p-3 text-posyandu-secondary hover:bg-posyandu-secondary/10 hover:shadow shadow-blue-100 rounded-xl transition-all"
                                        aria-label="Lihat Detail"
                                    >
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </Link>
                                </td>
                            </tr>
                        {/each}
                    {:else}
                        <tr>
                            <td colspan="4" class="py-12 text-center text-gray-400 font-bold text-lg">
                                Belum ada data kehadiran yang tercatat.
                            </td>
                        </tr>
                    {/if}
                </tbody>
            </table>
        </div>
    </div>
</div>
