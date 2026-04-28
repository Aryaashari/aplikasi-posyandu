<script>
    import InputLabel from './InputLabel.svelte';
    import InputError from './InputError.svelte';

    let { 
        label, 
        forId, 
        error = null, 
        required = false,
        children 
    } = $props();
</script>

<div class="space-y-2">
    {#if label}
        <InputLabel for={forId} value={label} />
    {/if}
    
    {@render children()}

    {#if error}
        <InputError message={error} />
    {/if}
</div>
