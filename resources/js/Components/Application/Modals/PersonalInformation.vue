<script setup>
import { ref } from "vue";
import { useForm, usePage } from "@inertiajs/vue3";
import TextInputGroup from "@/Components/Application/Form/TextInputGroup.vue";
import InputError from "@/Components/Application/Form/InputError.vue";
import SelectGroup from "@/Components/Application/Form/SelectGroup.vue";
import Button from "primevue/button";
import { formatCountriesForSelect } from "@/utils/helpers";
import { showSuccessToast, showFailToast } from "vant";

const props = defineProps({
    user: Object,
});

const page = usePage();

const loading = ref(false);

const countries = formatCountriesForSelect(
    page.props.countries || page.props.siteConfig.countries
);

const form = useForm({
    _method: "PUT",
    email: props.user.email,
    ...props.user.info,
});

const submit = async () => {
    loading.value = true;
    form.post(route("user.personal-info.update"), {
        preserveScroll: true,
        onSuccess: (page) => {
            showSuccessToast({
                message: "Personal information updated",
                wordBreak: "break-word",
            });
            if (page.url == "/email/verify") {
                window.location.reload();
            }
        },
        onError: (errors) => {
            showFailToast("Something went wrong, please check.");
        },
        onFinish: (visit) => {
            loading.value = false;
        },
    });
};
</script>

<template>
    <div class="modal fade modalRight" id="personalInformation">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div
                    class="header fixed-top bg-surface d-flex justify-content-center align-items-center"
                >
                    <span
                        class="left"
                        data-bs-dismiss="modal"
                        aria-hidden="true"
                        ><i class="icon-left-btn"></i
                    ></span>
                    <h3>Personal Information</h3>
                </div>
                <div class="overflow-auto pt-45 pb-16">
                    <div class="tf-container mt-20">
                        <p class="mb-20"></p>
                        <form action="" @submit.prevent="submit">
                            <div class="form-group mt-16">
                                <TextInputGroup
                                    label="Email"
                                    v-model="form.email"
                                    type="email"
                                    autofocus
                                />
                                <InputError
                                    class="mt-2"
                                    :message="form.errors.email"
                                />
                            </div>
                            <div
                                v-for="(item, index) in user.info"
                                :key="index"
                                class="mt-16 form-group"
                            >
                                <SelectGroup
                                    v-if="
                                        index == 'country' ||
                                        index == 'nationality'
                                    "
                                    :label="index"
                                    :data="countries"
                                    v-model="form[index]"
                                />
                                <SelectGroup
                                    v-else-if="index == 'gender'"
                                    :label="index"
                                    :data="['Male', 'Female', 'Other']"
                                    v-model="form[index]"
                                />
                                <TextInputGroup
                                    v-else
                                    :label="index"
                                    v-model="form[index]"
                                    :type="
                                        index == 'birth_date' ? 'date' : 'text'
                                    "
                                    :required="
                                        index != 'state' && index != 'zip_code'
                                    "
                                />
                                <InputError
                                    class="mt-2"
                                    :message="form.errors[index]"
                                />
                            </div>
                            <Button
                                type="submit"
                                label="Update"
                                :loading="loading"
                                unstyled
                                class="mt-20 primary-btn"
                            />
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
