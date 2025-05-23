<style scoped>
.container {
    width: auto !important;
}
</style>

<template>
    <div class="card flex justify-center">
        <Toast
            position="bottom-center"
            group="toastError"
            @close="onClose"
            class="container"
        >
            <template #message="slotProps">
                <div class="flex flex-col items-start flex-auto">
                    <div class="flex items-center gap-2">
                        <i class="pi pi-exclamation-triangle" shape="circle" />
                        <span class="font-bold"
                            >Error : {{ slotProps.message.summary }}</span
                        >
                    </div>
                    <div class="font-medium text-lg my-4">
                        {{ props.errors }}
                    </div>
                    <Button
                        size="small"
                        label="Close"
                        class="align-self-end"
                        severity="contrast"
                        @click="closeToast()"
                    ></Button>
                </div>
            </template>
        </Toast>
    </div>
</template>

<script setup>
import { useToast } from "primevue/usetoast";
import { ref, watch } from "vue";

const props = defineProps({
    errors: Object,
    summary: {
        type: String,
        default: "Something went wrong",
    },
});

const toast = useToast();
const visible = ref(false);

const showToast = () => {
    if (!visible.value) {
        toast.add({
            severity: "error",
            summary: props.summary,
            group: "toastError",
            duration: 1000 * 60, // 1 minute
        });
        visible.value = true;
    }
};

const closeToast = () => {
    toast.removeGroup("toastError");
    visible.value = false;
};

const onClose = () => {
    visible.value = false;
};

watch(
    () => props.errors,
    (newErrors) => {
        if (newErrors && Object.keys(newErrors).length > 0) showToast();
        else closeToast();
    },
    { immediate: true }
);
</script>
