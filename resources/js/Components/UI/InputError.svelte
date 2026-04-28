<script>
    let { message, class: className = '' } = $props();
</script>

{#if message}
    <p class="mt-2 text-base font-bold text-red-600 {className}" role="alert">
        {message}
    </p>
{/if}
