<script>
    import { useForm } from '@inertiajs/svelte';
    import Card from '../../Components/UI/Card.svelte';
    import Input from '../../Components/UI/Input.svelte';
    import InputLabel from '../../Components/UI/InputLabel.svelte';
    import InputError from '../../Components/UI/InputError.svelte';
    import Button from '../../Components/UI/Button.svelte';

    let form = useForm({
        email: '',
        password: '',
    });

    function submit(e) {
        e.preventDefault();
        form.post('/login', {
            onFinish: () => form.reset('password'),
        });
    }
</script>

<!-- SEO Title -->
<svelte:head>
    <title>Masuk - Sistem Posyandu</title>
</svelte:head>

<div class="min-h-screen bg-posyandu-accent flex flex-col justify-center items-center p-6 selection:bg-posyandu-primary/30 font-sans">
    
    <!-- Logo & Header -->
    <div class="text-center mb-10">
        <img 
            src="/assets/img/mainlogo-full.png" 
            alt="Logo Sistem Posyandu" 
            class="h-32 w-auto mx-auto mb-6 object-contain animate-in fade-in slide-in-from-top-4 duration-700" 
        />
        <p class="text-xl text-gray-400 mt-2 font-bold">Silakan masuk untuk akses dashboard admin</p>
    </div>

    <!-- Login Form Card -->
    <div class="w-full max-w-xl">
        <Card>
            <form onsubmit={submit} class="space-y-8">
                <div>
                    <InputLabel for="email" value="Alamat Email" />
                    <Input 
                        id="email"
                        type="email"
                        bind:value={form.email}
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="nama@posyandu.id"
                    />
                    <InputError message={form.errors.email} />
                </div>

                <div>
                    <InputLabel for="password" value="Kata Sandi" />
                    <Input 
                        id="password"
                        type="password"
                        bind:value={form.password}
                        required
                        autocomplete="current-password"
                        placeholder="••••••••"
                    />
                    <InputError message={form.errors.password} />
                </div>

                <div class="pt-2">
                    <Button processing={form.processing}>
                        Masuk Sekarang
                    </Button>
                </div>
                
                <div class="text-center pt-4">
                    <p class="text-gray-400 font-bold">
                        Lupa kata sandi? 
                        <span class="text-posyandu-secondary hover:underline cursor-pointer">Hubungi Superadmin</span>
                    </p>
                </div>
            </form>
        </Card>
        
        <!-- Footer Version -->
        <p class="text-center mt-8 text-gray-400 font-bold text-sm">
            &copy; 2026 Aplikasi Posyandu v1.0 • Kota/Kabupaten Posyandu
        </p>
    </div>
</div>
