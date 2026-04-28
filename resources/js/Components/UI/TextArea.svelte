<script>
    let { 
        value = $bindable(), 
        id, 
        placeholder = '', 
        required = false, 
        rows = 4,
        class: className = '',
        ...rest
    } = $props();
</script>

<textarea
    bind:value
    {id}
    {placeholder}
    {required}
    {rows}
    {...rest}
    class="w-full px-4 py-3 sm:px-5 sm:py-4 text-base sm:text-lg bg-gray-50 border-2 border-gray-100 rounded-2xl focus:bg-white focus:border-posyandu-primary focus:ring-4 focus:ring-posyandu-primary/10 outline-none transition-all placeholder:text-gray-400 text-posyandu-dark min-h-[120px] {className}"
></textarea>
