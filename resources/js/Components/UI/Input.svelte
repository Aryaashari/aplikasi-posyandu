<script>
    import { onMount } from 'svelte';

    let { 
        value = $bindable(), 
        type = 'text', 
        id, 
        placeholder = '', 
        required = false, 
        autocomplete = '',
        autofocus = false,
        class: className = '',
        ...rest
    } = $props();

    let inputElement;

    onMount(() => {
        if (autofocus) {
            inputElement.focus();
        }
    });

    /**
     * @public
     */
    export function focus() {
        inputElement.focus();
    }
</script>

<input
    bind:this={inputElement}
    bind:value
    {type}
    {id}
    {placeholder}
    {required}
    {autocomplete}
    {...rest}
    class="w-full px-4 py-3 sm:px-5 sm:py-4 text-base sm:text-lg bg-gray-50 border-2 border-gray-100 rounded-2xl focus:bg-white focus:border-posyandu-primary focus:ring-4 focus:ring-posyandu-primary/10 outline-none transition-all placeholder:text-gray-400 text-posyandu-dark {className}"
/>
