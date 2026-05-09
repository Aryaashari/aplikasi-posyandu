<script module>
    import MainLayout from '../../Components/MainLayout.svelte';
    export const layout = MainLayout;
</script>

<script>
    import { router, usePage } from '@inertiajs/svelte';
    import Card from '../../Components/UI/Card.svelte';
    import Button from '../../Components/UI/Button.svelte';

    const page = usePage();

    let file = $state(null);
    let fileName = $state('');
    let fileSize = $state('');
    let isDragOver = $state(false);
    let isUploading = $state(false);
    let fileInputRef = $state(null);
    
    // Modal state for errors
    let showModal = $state(false);

    // Derived states
    let importIssues = $derived(page.props.importIssues || []);
    let issueCount = $derived(page.props.issueCount || 0);
    let successMessage = $derived(page.props.flash?.success || null);
    let serverError = $derived(page.props.flash?.error || null);
    
    $effect(() => {
        if (importIssues.length > 0) {
            showModal = true;
        }
    });

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 B';
        const k = 1024;
        const sizes = ['B', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    function handleFileSelect(event) {
        const selected = event.target.files[0];
        if (selected) {
            setFile(selected);
        }
    }

    function setFile(selected) {
        const allowedExtensions = ['.xlsx', '.xls', '.csv'];
        const ext = '.' + selected.name.split('.').pop().toLowerCase();
        
        if (!allowedExtensions.includes(ext)) {
            alert('Format file tidak didukung. Gunakan file .xlsx, .xls, atau .csv');
            return;
        }
        
        if (selected.size > 10 * 1024 * 1024) {
            alert('Ukuran file maksimal 10MB');
            return;
        }

        file = selected;
        fileName = selected.name;
        fileSize = formatFileSize(selected.size);
    }

    function handleDrop(event) {
        event.preventDefault();
        isDragOver = false;
        const dropped = event.dataTransfer.files[0];
        if (dropped) {
            setFile(dropped);
        }
    }

    function handleDragOver(event) {
        event.preventDefault();
        isDragOver = true;
    }

    function handleDragLeave() {
        isDragOver = false;
    }

    function clearFile() {
        file = null;
        fileName = '';
        fileSize = '';
        if (fileInputRef) fileInputRef.value = '';
    }

    function handleSubmit(force = false) {
        if (!file) return;
        
        isUploading = true;
        // Reset errors before new upload
        page.props.importIssues = [];
        page.props.issueCount = 0;
        showModal = false;
        
        router.post('/import', { file, force: force ? 1 : 0 }, {
            forceFormData: true,
            preserveState: true,
            onFinish: () => {
                isUploading = false;
                clearFile();
            },
        });
    }

    function closeModal() {
        showModal = false;
        page.props.importIssues = [];
        page.props.issueCount = 0;
    }
</script>

<div class="space-y-6 sm:space-y-8">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 md:gap-6">
        <div>
            <h1 class="text-3xl sm:text-4xl font-black text-posyandu-dark tracking-tight leading-tight">Import Data Excel</h1>
            <p class="text-lg sm:text-xl text-gray-400 mt-1 sm:mt-2 font-bold">Unggah data posyandu dari file Excel</p>
        </div>
        <a href="/import/template" class="w-full md:w-auto">
            <button class="w-full md:w-auto flex items-center justify-center gap-2 px-6 sm:px-8 py-3 sm:py-4 text-base sm:text-lg font-black text-posyandu-secondary bg-blue-50 border-2 border-posyandu-secondary/20 rounded-2xl hover:bg-blue-100 transition-all">
                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Download Template
            </button>
        </a>
    </div>

    <!-- Success Message -->
    {#if successMessage}
        <div class="flex items-start gap-4 p-5 sm:p-6 bg-emerald-50 border-2 border-emerald-200 rounded-2xl">
            <div class="flex-shrink-0 w-10 h-10 sm:w-12 sm:h-12 bg-emerald-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 sm:w-7 sm:h-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <div>
                <p class="text-lg sm:text-xl font-bold text-emerald-800">Import Berhasil!</p>
                <p class="text-base text-emerald-600 font-semibold mt-1">{successMessage}</p>
            </div>
        </div>
    {/if}

    <!-- General Error Message -->
    {#if serverError}
        <div class="flex items-start gap-4 p-5 sm:p-6 bg-red-50 border-2 border-red-200 rounded-2xl">
            <div class="flex-shrink-0 w-10 h-10 sm:w-12 sm:h-12 bg-red-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 sm:w-7 sm:h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </div>
            <div>
                <p class="text-lg sm:text-xl font-bold text-red-800">Import Gagal!</p>
                <p class="text-base text-red-600 font-semibold mt-1">{serverError}</p>
            </div>
        </div>
    {/if}

    <!-- Upload Card -->
    <Card class="!p-6 sm:!p-10">
        <div class="space-y-6 sm:space-y-8">
            <div
                role="button"
                tabindex="0"
                ondragover={handleDragOver}
                ondragleave={handleDragLeave}
                ondrop={handleDrop}
                onclick={() => fileInputRef?.click()}
                onkeydown={(e) => e.key === 'Enter' && fileInputRef?.click()}
                class="relative border-3 border-dashed rounded-3xl p-8 sm:p-16 text-center cursor-pointer transition-all duration-300
                       {isDragOver ? 'border-posyandu-primary bg-emerald-50/80' : file ? 'border-emerald-300 bg-emerald-50/40' : 'border-gray-200 bg-gray-50/50 hover:border-posyandu-primary/40 hover:bg-emerald-50/30'}"
            >
                <input
                    bind:this={fileInputRef}
                    type="file"
                    accept=".xlsx,.xls,.csv"
                    onchange={handleFileSelect}
                    class="hidden"
                />

                {#if file}
                    <div class="flex flex-col items-center gap-4">
                        <div class="w-20 h-20 sm:w-24 sm:h-24 bg-gradient-to-br from-emerald-400 to-green-600 rounded-3xl flex items-center justify-center shadow-lg shadow-emerald-200">
                            <svg class="w-10 h-10 sm:w-12 sm:h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xl sm:text-2xl font-black text-posyandu-dark">{fileName}</p>
                            <p class="text-base sm:text-lg font-bold text-gray-400 mt-1">{fileSize}</p>
                        </div>
                        <button
                            onclick={(e) => { e.stopPropagation(); clearFile(); }}
                            class="px-5 py-2.5 text-sm font-bold text-red-500 bg-red-50 hover:bg-red-100 rounded-xl transition-colors"
                        >
                            Hapus File
                        </button>
                    </div>
                {:else}
                    <div class="flex flex-col items-center gap-4 sm:gap-6">
                        <div class="w-20 h-20 sm:w-28 sm:h-28 bg-gradient-to-br from-gray-100 to-gray-200 rounded-3xl flex items-center justify-center">
                            <svg class="w-10 h-10 sm:w-14 sm:h-14 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xl sm:text-2xl font-black text-posyandu-dark">
                                Drag & drop file Excel di sini
                            </p>
                            <p class="text-base sm:text-lg font-bold text-gray-400 mt-2">
                                atau <span class="text-posyandu-primary underline underline-offset-4">pilih file</span> dari perangkat Anda
                            </p>
                        </div>
                    </div>
                {/if}
            </div>

            {#if file}
                <div class="flex flex-col sm:flex-row gap-3 sm:gap-4">
                    <Button 
                        onclick={handleSubmit}
                        disabled={isUploading}
                        processing={isUploading}
                        class="flex-1 !py-4 sm:!py-5 !text-xl"
                    >
                        {#if isUploading}
                            Sedang Mengimport...
                        {:else}
                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                            Import Data
                        {/if}
                    </Button>
                </div>
            {/if}
        </div>
    </Card>
</div>

<!-- Modal Error Validation -->
{#if showModal}
    <div class="fixed inset-0 z-[100] flex items-center justify-center px-4 sm:px-6">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" onclick={closeModal} role="button" tabindex="0" onkeydown={(e) => e.key === 'Escape' && closeModal()}></div>
        
        <!-- Modal Content -->
        <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-5xl max-h-[90vh] flex flex-col overflow-hidden animate-fade-in-up">
            <!-- Modal Header -->
            <div class="px-6 py-5 sm:px-8 sm:py-6 border-b border-gray-100 flex items-center justify-between bg-amber-50/50">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-7 h-7 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl sm:text-2xl font-black text-amber-900">Perhatian! Validasi Import</h2>
                        <p class="text-sm sm:text-base font-bold text-amber-700 mt-0.5">Ditemukan {issueCount} isu (Peringatan / Error) pada data Excel Anda. Silakan review sebelum melanjutkan.</p>
                    </div>
                </div>
                <button onclick={closeModal} class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-2 rounded-xl transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <!-- Modal Body (Table) -->
            <div class="overflow-auto flex-1 p-0">
                <table class="w-full text-left border-collapse min-w-[800px]">
                    <thead class="sticky top-0 bg-white shadow-sm z-10">
                        <tr class="border-b border-gray-200">
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider w-20 text-center bg-gray-50">Tipe</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider w-20 text-center bg-gray-50">Baris</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider bg-gray-50 w-48">NIK & Nama Anak</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider bg-gray-50 w-32">Kolom</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider bg-gray-50">Pesan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        {#each importIssues as issue}
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 text-center">
                                    {#if issue.tipe === 'error'}
                                        <span class="inline-flex items-center justify-center px-2 py-1 rounded-lg bg-red-100 text-red-700 font-bold text-xs uppercase">Error</span>
                                    {:else}
                                        <span class="inline-flex items-center justify-center px-2 py-1 rounded-lg bg-amber-100 text-amber-700 font-bold text-xs uppercase">Warning</span>
                                    {/if}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-100 text-gray-600 font-bold text-sm">
                                        {issue.baris}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="font-bold text-posyandu-dark text-sm sm:text-base truncate max-w-[200px]">{issue.nama}</p>
                                    <p class="font-mono text-xs text-gray-500 mt-1">{issue.nik}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <code class="px-2 py-1 {issue.tipe === 'error' ? 'bg-red-50 text-red-700 border-red-200' : 'bg-amber-50 text-amber-700 border-amber-200'} font-mono text-xs sm:text-sm font-bold rounded-lg border">
                                        {issue.kolom}
                                    </code>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm sm:text-base {issue.tipe === 'error' ? 'text-red-700' : 'text-amber-700'} font-semibold">{issue.pesan}</p>
                                </td>
                            </tr>
                        {/each}
                    </tbody>
                </table>
            </div>
            
            <!-- Modal Footer -->
            <div class="px-6 py-4 sm:px-8 border-t border-gray-100 bg-gray-50 flex flex-col sm:flex-row gap-4 justify-between items-center">
                <div class="text-sm font-semibold text-gray-500">
                    <span class="text-red-600 font-bold">*Error</span> akan dilewati. <span class="text-amber-600 font-bold">*Warning</span> akan ditimpa.
                </div>
                <div class="flex gap-3 w-full sm:w-auto">
                    <Button onclick={closeModal} class="flex-1 sm:flex-none !w-auto px-6 py-3 text-base !bg-gray-200 !text-gray-700 hover:!bg-gray-300">
                        Batal
                    </Button>
                    <Button onclick={() => handleSubmit(true)} disabled={isUploading} class="flex-1 sm:flex-none !w-auto px-6 py-3 text-base !bg-emerald-600 hover:!bg-emerald-700">
                        {#if isUploading}
                            Memproses...
                        {:else}
                            Lanjutkan Import
                        {/if}
                    </Button>
                </div>
            </div>
        </div>
    </div>
{/if}

<style>
    @keyframes fade-in-up {
        from { opacity: 0; transform: translateY(20px) scale(0.95); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }
    
    .animate-fade-in-up {
        animation: fade-in-up 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }

    .border-3 {
        border-width: 3px;
    }
</style>
