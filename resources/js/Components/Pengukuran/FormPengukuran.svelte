<script>
    import { useForm } from '@inertiajs/svelte';
    import Card from '../UI/Card.svelte';
    import Button from '../UI/Button.svelte';
    import Input from '../UI/Input.svelte';
    import SelectBox from '../UI/SelectBox.svelte';
    import TextArea from '../UI/TextArea.svelte';
    import FormFieldGroup from '../UI/FormFieldGroup.svelte';

    let { children_list = [], pengukuran = null } = $props();
    const isEdit = $derived(!!pengukuran);

    const form = useForm({
        id_anak: (() => pengukuran?.id_anak || '')(),
        tanggal_pengukuran: (() => pengukuran?.tanggal_pengukuran || new Date().toISOString().split('T')[0])(),
        berat_badan: (() => pengukuran?.berat_badan || '')(),
        tinggi_badan: (() => pengukuran?.tinggi_badan || '')(),
        lingkar_kepala: (() => pengukuran?.lingkar_kepala || '')(),
        lingkar_lengan: (() => pengukuran?.lingkar_lengan || '')(),
        cara_ukur: (() => pengukuran?.cara_ukur || '')(),
        catatan: (() => pengukuran?.catatan || '')()
    });

    const childOptions = $derived(children_list.map(child => ({
        value: child.id,
        label: `${child.nama} (${child.nik})`
    })));

    const caraUkurOptions = [
        { value: 'Berdiri', label: 'Berdiri' },
        { value: 'Telentang', label: 'Telentang' }
    ];

    function submit(e) {
        e.preventDefault();
        if (isEdit) {
            form.put(`/pengukuran/${pengukuran.id}`);
        } else {
            form.post('/pengukuran', {
                onSuccess: () => form.reset()
            });
        }
    }
</script>

<Card>
    <form onsubmit={submit} class="space-y-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Child Selection -->
            <div class="md:col-span-2">
                <FormFieldGroup label="Pilih Balita" forId="id_anak" error={form.errors.id_anak}>
                    <SelectBox 
                        id="id_anak" 
                        bind:value={form.id_anak} 
                        options={childOptions} 
                        placeholder="Cari dan pilih nama balita..."
                        required
                    />
                </FormFieldGroup>
            </div>

            <!-- Date -->
            <div>
                <FormFieldGroup label="Tanggal Pengukuran" forId="tanggal_pengukuran" error={form.errors.tanggal_pengukuran}>
                    <Input 
                        type="date" 
                        id="tanggal_pengukuran" 
                        bind:value={form.tanggal_pengukuran} 
                        required 
                    />
                </FormFieldGroup>
            </div>

            <!-- Cara Ukur -->
            <div>
                <FormFieldGroup label="Cara Ukur (Mandatory)" forId="cara_ukur" error={form.errors.cara_ukur}>
                    <SelectBox 
                        id="cara_ukur" 
                        bind:value={form.cara_ukur} 
                        options={caraUkurOptions} 
                        placeholder="Pilih cara ukur..."
                        required
                    />
                </FormFieldGroup>
            </div>

            <!-- Weight -->
            <div class="md:col-span-1">
                <FormFieldGroup label="Berat Badan (kg)" forId="berat_badan" error={form.errors.berat_badan}>
                    <Input 
                        type="number" 
                        step="0.1" 
                        id="berat_badan" 
                        bind:value={form.berat_badan} 
                        placeholder="Contoh: 8.5"
                        required 
                    />
                </FormFieldGroup>
            </div>

            <!-- Height -->
            <div>
                <FormFieldGroup label="Tinggi Badan (cm)" forId="tinggi_badan" error={form.errors.tinggi_badan}>
                    <Input 
                        type="number" 
                        step="0.1" 
                        id="tinggi_badan" 
                        bind:value={form.tinggi_badan} 
                        placeholder="Contoh: 75.2"
                        required 
                    />
                </FormFieldGroup>
            </div>

            <!-- Head Circumference -->
            <div>
                <FormFieldGroup label="Lingkar Kepala (cm)" forId="lingkar_kepala" error={form.errors.lingkar_kepala}>
                    <Input 
                        type="number" 
                        step="0.1" 
                        id="lingkar_kepala" 
                        bind:value={form.lingkar_kepala} 
                        placeholder="Contoh: 42.0"
                        required 
                    />
                </FormFieldGroup>
            </div>

            <!-- Arm Circumference -->
            <div>
                <FormFieldGroup label="Lingkar Lengan (cm)" forId="lingkar_lengan" error={form.errors.lingkar_lengan}>
                    <Input 
                        type="number" 
                        step="0.1" 
                        id="lingkar_lengan" 
                        bind:value={form.lingkar_lengan} 
                        placeholder="Contoh: 12.5"
                    />
                </FormFieldGroup>
            </div>

            <!-- Notes -->
            <div class="md:col-span-2">
                <FormFieldGroup label="Catatan / Keterangan" forId="catatan" error={form.errors.catatan}>
                    <TextArea 
                        id="catatan" 
                        bind:value={form.catatan} 
                        placeholder="Tambahkan catatan jika ada..."
                    />
                </FormFieldGroup>
            </div>
        </div>

        <div class="pt-6 border-t border-gray-100 flex gap-4">
            <a href="/pengukuran" class="flex-1 text-center">
                <button type="button" class="w-full px-6 py-4 text-xl font-bold text-gray-400 hover:text-gray-600 transition-all">
                    Batalkan
                </button>
            </a>
            <div class="flex-[2]">
                <Button processing={form.processing}>
                    Simpan Hasil Pengukuran
                </Button>
            </div>
        </div>
    </form>
</Card>
