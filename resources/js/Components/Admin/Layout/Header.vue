<template>
    <header
        class="bg-white dark:bg-neutral-800 shadow dark:shadow-gray-900 border border-t-gray-100"
    >
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <slot>
                <h2
                    class="font-semibold text-xl text-gray-800 dark:text-gray-400 leading-tight"
                >
                    {{ title }}
                </h2>
            </slot>
        </div>
    </header>
</template>

<script setup>
defineProps({
    title: {
        type: String,
        default: "",
    },
});
</script>

<style scoped>
header {
    margin-bottom: 1rem;
}
</style>
