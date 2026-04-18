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
        class: className = ''
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
    {type}
    {id}
    {placeholder}
    {required}
    {autocomplete}
    bind:value
    class="w-full px-5 py-4 text-lg bg-gray-50 border-2 border-gray-100 rounded-2xl focus:bg-white focus:border-posyandu-primary focus:ring-4 focus:ring-posyandu-primary/10 outline-none transition-all placeholder:text-gray-400 text-posyandu-dark {className}"
/>
