<script setup>
import { useForm, usePage } from "@inertiajs/vue3";
import { useToast } from "primevue/usetoast";

const page = usePage();
const toast = useToast();

const props = defineProps({
    datas: Object,
});

const form = useForm(props.datas);

const submit = () => {
    form.put(route(page.props.routePrefix + "setting.update", props.datas.id), {
        onSuccess: () => {
            toast.add({
                severity: "success",
                summary: "Settings have been updated",
                life: 5000,
            });
        },
    });
};
</script>

<template>
    <div class="grid grid-cols-3 gap-4 m-3">
        <div class=""></div>
        <div class="col-span-3">
            <div class="mb-4">
                <h5 class="fw-bold">Handling Fee</h5>
                <InputNumber
                    v-model="form.handling_fee"
                    inputId="locale-us"
                    locale="en-US"
                    :minFractionDigits="2"
                />
            </div>
            <div class="mb-4">
                <h5 class="fw-bold">Terms And Conditions</h5>
                <Editor v-model="form.tcs" editorStyle="height: 200px" />
            </div>
            <div class="mb-4">
                <h5 class="fw-bold">About Us</h5>
                <Editor v-model="form.about_us" editorStyle="height: 200px" />
            </div>
            <div v-if="form.faq !== null" class="mb-4">
                <h5 class="fw-bold">FAQ</h5>
                <Editor v-model="form.faq" editorStyle="height: 200px" />
            </div>
            <div v-if="form.reg_agree !== null" class="mb-4">
                <h5 class="fw-bold">Registration Agreement</h5>
                <Editor v-model="form.reg_agree" editorStyle="height: 200px" />
            </div>

            <div class="flex justify-center">
                <Button
                    label="Update"
                    :loading="form.processing"
                    @click="submit"
                    icon="pi pi-check"
                    type="button"
                    class="w-2/3"
                />
            </div>
        </div>
    </div>
</template>
