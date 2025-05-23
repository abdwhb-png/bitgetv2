<script setup>
defineProps({
    message: String,
});
</script>

<template>
    <div v-show="message" class="input-error">
        <p class="text-" style="color: #ff4a4a">
            {{ message }}
        </p>
    </div>
</template>
