<template>
    <div class="flex items-center gap-1 cursor-pointer" @click="copy">
        <i
            v-if="text"
            class="pi pi-copy"
            :class="copied ? 'text-primary' : ''"
        ></i>
        <slot>
            {{ text }}
        </slot>
    </div>
</template>

<script setup>
import { ref } from "vue";

const emits = defineEmits(["copied", "error"]);

const props = defineProps({
    text: String,
});

const copied = ref(false);

const copy = async () => {
    if (!props.text || copied.value) return; // Empêche le spam

    if (navigator?.clipboard?.writeText) {
        try {
            await navigator.clipboard.writeText(props.text);
            copied.value = true;
            emits("copied");

            setTimeout(() => (copied.value = false), 2000);
        } catch (error) {
            console.error("Copy error :", error);
            emits("error", error);
        }
    }

    // Fallback si Clipboard API non supportée
    const textArea = document.createElement("textarea");
    textArea.value = props.text;
    document.body.appendChild(textArea);
    textArea.select();
    try {
        document.execCommand("copy");
        copied.value = true;
        emits("copied");
    } catch (error) {
        console.error("Fallback copy failed:", error);
        emits("error", error);
    }
    document.body.removeChild(textArea);
    setTimeout(() => (copied.value = false), 2000);
};
</script>
