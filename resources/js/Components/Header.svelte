<script>
    import { router } from "@inertiajs/svelte";

    let { onToggleSidebar, sidebarOpen } = $props();

    function logout() {
        router.post("/logout");
    }
</script>

<header
    class="fixed top-0 right-0 z-40 flex items-center h-20 transition-all duration-300 bg-white border-b border-gray-200 {sidebarOpen
        ? 'left-72'
        : 'left-0 lg:left-20'}"
>
    <div class="flex items-center justify-between w-full px-8">
        <!-- Dashboard Title / Breadcrumb area -->
        <div class="flex items-center gap-4">
            <button
                onclick={onToggleSidebar}
                class="p-2 text-gray-600 transition-colors rounded-lg hover:bg-gray-100 hover:text-posyandu-primary"
                aria-label="Toggle Sidebar"
            >
                <svg
                    class="w-8 h-8"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M4 6h16M4 12h16M4 18h16"
                    />
                </svg>
            </button>
            <h1 class="hidden text-2xl font-bold md:block text-posyandu-dark">
                Hai, Selamat Pagi!
            </h1>
        </div>

        <!-- Search / Actions -->
        <div class="flex items-center gap-6">
            <div class="relative hidden lg:block">
                <input
                    type="text"
                    placeholder="Cari data balita..."
                    class="py-3 pl-12 pr-4 text-base bg-gray-100 border-transparent rounded-xl focus:bg-white focus:ring-2 focus:ring-posyandu-primary focus:border-transparent w-80 transition-all"
                />
                <div
                    class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none"
                >
                    <svg
                        class="w-6 h-6 text-gray-400"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                        />
                    </svg>
                </div>
            </div>

            <!-- Quick Settings -->
            <button
                onclick={logout}
                class="flex items-center gap-2 p-2 text-gray-500 transition-all rounded-xl hover:bg-gray-100"
                aria-label="Keluar"
            >
                <div
                    class="p-1 px-3 text-sm font-bold text-white rounded-lg bg-posyandu-secondary"
                >
                    Keluar
                </div>
            </button>
        </div>
    </div>
</header>
