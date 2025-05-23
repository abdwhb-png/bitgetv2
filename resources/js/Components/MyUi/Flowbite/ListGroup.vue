<script setup>
defineProps({
    items: Array | Object,
});
</script>

<template>
    <ul
        class="w-48 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white"
    >
        <li
            v-for="(item, index) in items"
            :key="index"
            class="w-full px-4 py-2 border-b border-gray-200 rounded-t-lg dark:border-gray-600"
        >
            {{ item }}
        </li>
    </ul>
</template>
