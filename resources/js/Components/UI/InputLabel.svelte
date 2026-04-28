<script>
    let { value, for: htmlFor, class: className = '' } = $props();
</script>

<label for={htmlFor} class="block text-base sm:text-lg font-bold text-posyandu-dark mb-2 {className}">
    {#if value}
        {value}
    {:else}
        {@render children()}
    {/if}
</label>
