<template>
    <header
        class="bg-white dark:bg-neutral-800 shadow dark:shadow-gray-900 border border-t-gray-100"
    >
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-2">
                <UiButton variant="outline" size="icon" @click="reload">
                    <i class="pi pi-refresh"></i>
                </UiButton>
                <slot>
                    <h2
                        class="font-semibold text-xl text-gray-800 dark:text-gray-400 leading-tight"
                    >
                        {{ title }}
                    </h2>
                </slot>
            </div>
        </div>
    </header>
</template>

<script setup>
import { ref } from "vue";
import { router } from "@inertiajs/vue3";
import { useToast } from "primevue/usetoast";

defineProps({
    title: {
        type: String,
        default: "",
    },
});

const toast = useToast();
const reloading = ref(false);

const reload = () => {
    reloading.value = true;
    router.reload({
        onSuccess: () =>
            toast.add({
                summary: "Page reloaded",
                detail: "The page has been reloaded",
                severity: "info",
                life: 2000,
            }),
        onFinish: () => (reloading.value = false),
    });
};
</script>

<style scoped>
header {
    margin-bottom: 1rem;
}
</style>
