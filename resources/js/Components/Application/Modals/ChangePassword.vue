<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import PasswordInputGroup from "@/Components/Application/Form/PasswordInputGroup.vue";
import InputError from "@/Components/Application/Form/InputError.vue";
import { showSuccessToast, showFailToast } from "vant";
import Button from "primevue/button";

const loading = ref(false);

const form = useForm({
    current_password: "",
    password: "",
    password_confirmation: "",
});

const updatePassword = () => {
    form.put(route("user-password.update"), {
        errorBag: "updatePassword",
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            showSuccessToast({
                message: "Your password has been updated",
                wordBreak: "break-word",
            });
        },
        onError: () => {
            showFailToast({
                message: "Something went wrong, please check.",
                wordBreak: "break-word",
            });
            if (form.errors.password) {
                form.reset("password", "password_confirmation");
            }

            if (form.errors.current_password) {
                form.reset("current_password");
            }
        },
        onFinish: () => {
            loading.value = false;
        },
    });
};
</script>

<template>
    <div class="modal fade modalRight" id="changePassword">
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
                    <h3>Change Password</h3>
                </div>
                <div class="overflow-auto pt-45 pb-16">
                    <div class="tf-container mt-20">
                        <p class="mb-20">
                            Ensure your account is using a long, random password
                            to stay secure.
                        </p>
                        <form action="" @submit.prevent="updatePassword">
                            <div class="form-group">
                                <PasswordInputGroup
                                    label="Current Password"
                                    v-model="form.current_password"
                                    type="password"
                                    :autofocus="true"
                                />
                                <InputError
                                    class="mt-2"
                                    :message="form.errors.current_password"
                                />
                            </div>
                            <div class="form-group mt-16">
                                <PasswordInputGroup
                                    label="New Password"
                                    v-model="form.password"
                                    type="password"
                                />
                                <InputError
                                    class="mt-2"
                                    :message="form.errors.password"
                                />
                            </div>
                            <div class="form-group mt-16">
                                <PasswordInputGroup
                                    label="Confirm New Password"
                                    v-model="form.password_confirmation"
                                    type="password"
                                />
                                <InputError
                                    class="mt-2"
                                    :message="form.errors.password_confirmation"
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
