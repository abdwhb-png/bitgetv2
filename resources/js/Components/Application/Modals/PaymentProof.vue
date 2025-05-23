<script setup>
import { computed, ref } from "vue";
import { usePage, useForm } from "@inertiajs/vue3";
import { showNotify, showToast } from "vant";
import TextInputGroup from "@/Components/Application/Form/TextInputGroup.vue";
import SelectGroup from "@/Components/Application/Form/SelectGroup.vue";
import InputError from "@/Components/Application/Form/InputError.vue";

const page = usePage();

const pMethods = computed(() =>
    page.props.siteConfig.paymentMethods.map((item) => item.name)
);

const success = ref(null);

const form = useForm({
    _method: "PUT",
    reference_number: null,
    method: null,
    amount: null,
    photo: null,
});

const submit = () => {
    form.post(route(page.props.routePrefix + "payment-proof"), {
        preserveScroll: true,
        onProgress: () =>
            showToast({
                message: "Loading...",
                forbidClick: true,
                type: "loading",
            }),
        onSuccess: () => {
            success.value = "Payment proof uploaded successfully.";
            showNotify({
                message: success.value,
                type: "success",
            });
        },
        onError: () => {
            showNotify({
                message: "Something went wrong, please check.",
                type: "danger",
            });
        },
        onFinish: () => {
            form.reset();
            setTimeout(() => (success.value = null), 5000);
        },
    });
};
</script>

<template>
    <div class="modal fade action-sheet" id="paymentProof">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <span>Upload a payment proof</span>
                    <span
                        class="icon-cancel"
                        data-bs-dismiss="modal"
                        @click="form.reset()"
                    ></span>
                </div>
                <form
                    class="modal-body bg-surface"
                    action=""
                    @submit.prevent="submit"
                >
                    <div class="form-group mb-16">
                        <TextInputGroup
                            label="Reference number"
                            :required="false"
                            v-model="form.reference_number"
                        />
                        <InputError :message="form.errors.reference_number" />
                    </div>
                    <div class="form-group mb-16">
                        <SelectGroup
                            label="Method used"
                            :data="pMethods"
                            v-model="form.method"
                        />
                        <InputError :message="form.errors.method" />
                    </div>
                    <div class="form-group mb-16">
                        <TextInputGroup
                            label="Transaction amount"
                            v-model="form.amount"
                        />
                        <InputError :message="form.errors.amount" />
                    </div>
                    <div class="form-group mb-16">
                        <input
                            type="file"
                            @input="form.photo = $event.target.files[0]"
                            class="form-control"
                        />
                        <InputError :message="form.errors.photo" />
                    </div>
                    <div class="form-group mb-16">
                        <Button
                            label="Upload"
                            severity="info"
                            class="w-100"
                            :loading="form.processing"
                            type="submit"
                        />
                        <p class="text-success" v-show="success">
                            {{ success }}
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
