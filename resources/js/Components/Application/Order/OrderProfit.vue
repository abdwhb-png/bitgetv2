<script setup>
defineProps({
    profit: Number | String,
});
</script>

<template>
    <span v-if="profit < 0" class="text-red">{{
        profit.replace("-", "- ")
    }}</span>
    <span v-else class="text-primary">+ {{ profit }}</span>
</template>
