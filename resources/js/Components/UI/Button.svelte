<script>
    let { 
        type = 'submit', 
        disabled = false, 
        processing = false, 
        class: className = '',
        children 
    } = $props();
</script>

<button
    {type}
    {disabled}
    class="w-full flex items-center justify-center gap-3 px-6 py-4 text-xl font-black text-white bg-posyandu-primary rounded-2xl shadow-lg shadow-green-200 hover:bg-green-600 hover:scale-[1.02] active:scale-[0.98] focus:ring-4 focus:ring-posyandu-primary/20 transition-all disabled:opacity-70 disabled:pointer-events-none {className}"
>
    {#if processing}
        <svg class="animate-spin h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    {/if}
    
    {@render children()}
</button>
