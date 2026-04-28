<script module>
    import MainLayout from '../../Components/MainLayout.svelte';
    export const layout = MainLayout;
</script>

<script>
    import { useForm } from '@inertiajs/svelte';
    import Card from '../../Components/UI/Card.svelte';
    import Button from '../../Components/UI/Button.svelte';
    import Input from '../../Components/UI/Input.svelte';
    import InputLabel from '../../Components/UI/InputLabel.svelte';
    import InputError from '../../Components/UI/InputError.svelte';
    let { anak = null } = $props();
    const isEdit = $derived(!!anak);

    const form = useForm({
        nama: (() => anak?.nama || '')(),
        nik: (() => anak?.nik || '')(),
        no_kk: (() => anak?.no_kk || '')(),
        tanggal_lahir: (() => anak?.tanggal_lahir || '')(),
        jenis_kelamin: (() => anak?.jenis_kelamin || 'L')(),
        nama_ayah: (() => anak?.nama_ayah || '')(),
        nama_ibu: (() => anak?.nama_ibu || '')(),
        no_telp_ortu: (() => anak?.no_telp_ortu || '')(),
        alamat: (() => anak?.alamat || '')(),
    });

    function submit(e) {
        e.preventDefault();
        if (isEdit) {
            form.put(`/anak/${anak.id}`);
        } else {
            form.post('/anak');
        }
    }
</script>

<div class="max-w-4xl mx-auto space-y-8">
    <!-- Header Section -->
    <div>
        <div class="flex items-center gap-3 mb-2">
            <a href="/anak" class="p-2 text-gray-400 hover:text-posyandu-primary transition-colors" aria-label="Kembali ke Daftar">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <h1 class="text-4xl font-black text-posyandu-dark tracking-tight">
                {isEdit ? 'Ubah Data Balita' : 'Tambah Balita Baru'}
            </h1>
    </div>

    <Card>
        <form onsubmit={submit} class="space-y-10">
            <!-- Identitas Section -->
                <div class="space-y-6">
                    <div class="flex items-center gap-3 border-b border-gray-100 pb-4">
                        <div class="w-10 h-10 bg-posyandu-primary/10 text-posyandu-primary rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <h2 class="text-2xl font-black text-posyandu-dark">Identitas Anak</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="md:col-span-2">
                            <InputLabel for="nama" value="Nama Lengkap Balita" />
                            <Input 
                                id="nama"
                                bind:value={form.nama}
                                placeholder="Masukkan nama sesuai akta lahir..."
                                required
                            />
                            <InputError message={form.errors.nama} />
                        </div>

                        <div>
                            <InputLabel for="nik" value="NIK Anak" />
                            <Input 
                                id="nik"
                                bind:value={form.nik}
                                placeholder="16 digit nomor induk kependudukan"
                                required
                            />
                            <InputError message={form.errors.nik} />
                        </div>

                        <div>
                            <InputLabel for="no_kk" value="Nomor Kartu Keluarga" />
                            <Input 
                                id="no_kk"
                                bind:value={form.no_kk}
                                placeholder="16 digit nomor KK"
                                required
                            />
                            <InputError message={form.errors.no_kk} />
                        </div>

                        <div>
                            <InputLabel for="tanggal_lahir" value="Tanggal Lahir" />
                            <Input 
                                id="tanggal_lahir"
                                type="date"
                                bind:value={form.tanggal_lahir}
                                required
                            />
                            <InputError message={form.errors.tanggal_lahir} />
                        </div>

                        <div>
                            <InputLabel for="jenis_kelamin" value="Jenis Kelamin" />
                            <div class="flex gap-4">
                                <button 
                                    type="button"
                                    onclick={() => form.jenis_kelamin = 'L'}
                                    class="flex-1 px-6 py-4 rounded-2xl border-2 transition-all font-bold text-lg
                                           {form.jenis_kelamin === 'L' ? 'bg-blue-50 border-blue-500 text-blue-600' : 'bg-gray-50 border-gray-100 text-gray-400'}"
                                >
                                    Laki-laki
                                </button>
                                <button 
                                    type="button"
                                    onclick={() => form.jenis_kelamin = 'P'}
                                    class="flex-1 px-6 py-4 rounded-2xl border-2 transition-all font-bold text-lg
                                           {form.jenis_kelamin === 'P' ? 'bg-pink-50 border-pink-500 text-pink-600' : 'bg-gray-50 border-gray-100 text-gray-400'}"
                                >
                                    Perempuan
                                </button>
                            </div>
                            <InputError message={form.errors.jenis_kelamin} />
                        </div>
                    </div>
                </div>

                <!-- Keluarga Section -->
                <div class="space-y-6">
                    <div class="flex items-center gap-3 border-b border-gray-100 pb-4">
                        <div class="w-10 h-10 bg-posyandu-secondary/10 text-posyandu-secondary rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <h2 class="text-2xl font-black text-posyandu-dark">Data Orang Tua</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <InputLabel for="nama_ibu" value="Nama Lengkap Ibu" />
                            <Input 
                                id="nama_ibu"
                                bind:value={form.nama_ibu}
                                placeholder="Nama ibu kandung..."
                                required
                            />
                            <InputError message={form.errors.nama_ibu} />
                        </div>

                        <div>
                            <InputLabel for="nama_ayah" value="Nama Lengkap Ayah" />
                            <Input 
                                id="nama_ayah"
                                bind:value={form.nama_ayah}
                                placeholder="Nama ayah kandung..."
                                required
                            />
                            <InputError message={form.errors.nama_ayah} />
                        </div>

                        <div class="md:col-span-2">
                            <InputLabel for="no_telp_ortu" value="Nomor Telepon Orang Tua" />
                            <Input 
                                id="no_telp_ortu"
                                bind:value={form.no_telp_ortu}
                                placeholder="Contoh: 0812XXXXXXXX"
                                required
                            />
                            <InputError message={form.errors.no_telp_ortu} />
                        </div>

                        <div class="md:col-span-2">
                            <InputLabel for="alamat" value="Alamat Tinggal Sekarang" />
                            <textarea 
                                id="alamat"
                                bind:value={form.alamat}
                                required
                                class="w-full px-5 py-4 text-lg bg-gray-50 border-2 border-gray-100 rounded-2xl focus:bg-white focus:border-posyandu-primary focus:ring-4 focus:ring-posyandu-primary/10 outline-none transition-all placeholder:text-gray-400 text-posyandu-dark min-h-[120px]"
                                placeholder="Masukkan alamat lengkap..."
                            ></textarea>
                            <InputError message={form.errors.alamat} />
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-6 border-t border-gray-100 flex gap-4">
                    <a href="/anak" class="flex-1">
                        <button type="button" class="w-full px-6 py-4 text-xl font-bold text-gray-400 hover:text-gray-600 transition-all">
                            Batalkan
                        </button>
                    </a>
                    <div class="flex-[2]">
                        <Button processing={form.processing}>
                            {isEdit ? 'Simpan Perubahan' : 'Simpan Data Balita'}
                        </Button>
                    </div>
                </div>
            </form>
        </Card>
    </div>
</div>
