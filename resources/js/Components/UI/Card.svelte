<script>
    let { class: className = '', children } = $props();
</script>

<div class="bg-white p-5 sm:p-8 md:p-12 shadow-2xl rounded-[2.5rem] border border-gray-100 {className}">
    {@render children()}
</div>
