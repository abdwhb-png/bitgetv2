<template>
    <span class="text-gray-500"> {{ index }}: </span>
    <slot>
        <span class="ml-2">
            <pre>{{ value }}</pre>
        </span>
    </slot>
</template>

<script setup>
defineProps(["index", "value"]);
</script>
