<template>
    <UiButton
        variant="outline"
        class="ml-3"
        type="button"
        @click="emits('reset')"
    >
        <i class="pi pi-times"></i>
        <span v-bind="$attrs">Reset</span>
    </UiButton>
</template>

<script setup>
defineOptions({
    inheritAttrs: false,
});

const emits = defineEmits(["reset"]);
</script>
