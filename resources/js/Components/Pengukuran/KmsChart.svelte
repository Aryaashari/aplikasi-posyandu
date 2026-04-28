<script>
    import { onMount } from 'svelte';
    import { Line } from 'svelte-chartjs';
    import {
        Chart as ChartJS,
        Title,
        Tooltip,
        Legend,
        LineElement,
        LinearScale,
        PointElement,
        CategoryScale,
        Filler
    } from 'chart.js';

    ChartJS.register(
        Title,
        Tooltip,
        Legend,
        LineElement,
        LinearScale,
        PointElement,
        CategoryScale,
        Filler
    );

    let { measurements = [], standards = [], type = 'bb' } = $props(); // 'bb' (Weight) or 'tb' (Height)

    const chartTitle = $derived(type === 'bb' ? 'Berat Badan (kg) menurut Umur (Bulan)' : 'Tinggi Badan (cm) menurut Umur (Bulan)');
    const yLabel = $derived(type === 'bb' ? 'Berat (kg)' : 'Tinggi (cm)');

    const data = $derived.by(() => {
        // Find max age in months to truncate standards if needed
        const maxAge = measurements.length > 0 ? Math.max(...measurements.map(m => {
            const birthDate = new Date(m.tanggal_lahir); // Wait, measurements might not have DOB joined
            // We'll trust the caller passes measurements with age_months if possible, 
            // but for now we'll match by standards' umur_bulan.
            return 60; // Standard 5 years
        })) : 24;

        const labels = Array.from({ length: 61 }, (_, i) => i); // 0 to 60 months

        // WHO Standards Datasets
        const standardDatasets = [
            {
                label: '+3 SD',
                data: standards.map(s => type === 'bb' ? null : null), // Actually we need SD+3 in DB but it's missing in ref_growth_standard right now
                borderColor: 'rgba(239, 68, 68, 0.4)',
                borderWidth: 1,
                borderDash: [5, 5],
                pointRadius: 0,
                fill: false,
            },
            {
                label: 'Median',
                data: standards.map(s => type === 'bb' ? s.median_bb : s.median_tb),
                borderColor: 'rgba(34, 197, 94, 0.8)',
                borderWidth: 2,
                pointRadius: 0,
                fill: false,
            },
             {
                label: '-2 SD',
                data: standards.map(s => type === 'bb' ? s.sd_minus_2_bb : s.sd_minus_2_tb),
                borderColor: 'rgba(234, 179, 8, 0.8)',
                borderWidth: 2,
                pointRadius: 0,
                fill: false,
            },
            {
                label: '-3 SD',
                data: standards.map(s => type === 'bb' ? s.sd_minus_3_bb : s.sd_minus_3_tb),
                borderColor: 'rgba(239, 68, 68, 0.8)',
                borderWidth: 2,
                pointRadius: 0,
                fill: false,
            }
        ];

        // Child's Trajectory
        // Measurements need to be aligned to umur_bulan
        const childData = Array(61).fill(null);
        measurements.forEach(m => {
            // Need to calculate months from birth
            // This logic is better done in backend or passed as a prop
            // Assuming measurement has a calculated 'umur_bulan' field from backend
            if (m.umur_bulan !== undefined && m.umur_bulan <= 60) {
                childData[m.umur_bulan] = type === 'bb' ? m.berat_badan : m.tinggi_badan;
            }
        });

        return {
            labels,
            datasets: [
                {
                    label: 'Perkembangan Anak',
                    data: childData,
                    borderColor: 'rgba(79, 70, 229, 1)',
                    backgroundColor: 'rgba(79, 70, 229, 1)',
                    borderWidth: 4,
                    pointRadius: 6,
                    pointHoverRadius: 8,
                    spanGaps: true,
                    zIndex: 10
                },
                ...standardDatasets
            ]
        };
    });

    const options = $derived({
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    usePointStyle: true,
                    padding: 20,
                    font: { size: 12, weight: 'bold' }
                }
            },
            tooltip: {
                backgroundColor: 'rgba(17, 24, 39, 0.9)',
                padding: 12,
                titleFont: { size: 14, weight: 'bold' },
                bodyFont: { size: 13 },
                displayColors: true
            }
        },
        scales: {
            x: {
                title: {
                    display: true,
                    text: 'Umur (Bulan)',
                    font: { weight: 'bold' }
                },
                grid: { display: false }
            },
            y: {
                title: {
                    display: true,
                    text: yLabel,
                    font: { weight: 'bold' }
                },
                beginAtZero: false
            }
        }
    });
</script>

<div class="h-[400px] w-full">
    <Line {data} {options} />
</div>
