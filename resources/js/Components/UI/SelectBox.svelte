<script>
    let { 
        value = $bindable(), 
        options = [], 
        id, 
        placeholder = 'Pilih opsi...',
        required = false, 
        class: className = '',
        ...rest
    } = $props();
</script>

<select
    bind:value
    {id}
    {required}
    {...rest}
    class="w-full px-4 py-3 sm:px-5 sm:py-4 text-base sm:text-lg bg-gray-50 border-2 border-gray-100 rounded-2xl focus:bg-white focus:border-posyandu-primary focus:ring-4 focus:ring-posyandu-primary/10 outline-none transition-all text-posyandu-dark {className}"
>
    {#if placeholder}
        <option value="" disabled selected>{placeholder}</option>
    {/if}
    {#each options as option}
        <option value={option.value}>{option.label}</option>
    {/each}
</select>
