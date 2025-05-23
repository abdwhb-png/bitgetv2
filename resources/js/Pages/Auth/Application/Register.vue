<script setup>
import { ref } from "vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import AuthLayout from "@/Layouts/AuthLayout.vue";
import TextInputGroup from "@/Components/Application/Form/TextInputGroup.vue";
import PasswordInputGroup from "@/Components/Application/Form/PasswordInputGroup.vue";
import InputError from "@/Components/Application/Form/InputError.vue";
import Button from "primevue/button";
import { showFailToast } from "vant";

const form = useForm({
    first_name: "",
    last_name: "",
    email: "",
    password: "",
    password_confirmation: "",
    invitation_code: "",
    terms: false,
});

const submit = async () => {
    await form.post(route("register"), {
        onError: () => {
            showFailToast({
                message: "Something went wrong, please check the fileds.",
                wordBreak: "break-word",
            });
        },
        onFinish: () => form.reset("password", "password_confirmation"),
    });
};
</script>

<template>
    <Head title="Sign Up" />

    <AuthLayout title="Create your account in seconds">
        <form action="" @submit.prevent="submit">
            <fieldset class="mt-16">
                <TextInputGroup
                    label="First Name"
                    v-model="form.first_name"
                    :show-required="false"
                    :autofocus="true"
                />
                <InputError class="mt-2" :message="form.errors.first_name" />
            </fieldset>
            <fieldset class="mt-16 mb-12">
                <TextInputGroup
                    label="Last Name"
                    v-model="form.last_name"
                    :show-required="false"
                />
                <InputError class="mt-2" :message="form.errors.last_name" />
            </fieldset>
            <fieldset class="mt-16 mb-12">
                <TextInputGroup
                    label="Email"
                    v-model="form.email"
                    type="email"
                />
                <InputError
                    class="mt-2"
                    :message="form.errors.email"
                    :show-required="false"
                />
            </fieldset>
            <fieldset class="mt-16 mb-12">
                <PasswordInputGroup
                    label="Password"
                    v-model="form.password"
                    :show-required="false"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </fieldset>
            <fieldset class="mt-16 mb-12">
                <PasswordInputGroup
                    label="Confirm Password"
                    v-model="form.password_confirmation"
                    :show-required="false"
                    placeholder="Please confirm password"
                />
                <InputError
                    class="mt-2"
                    :message="form.errors.password_confirmation"
                />
            </fieldset>

            <fieldset class="mt-16 mb-12">
                <TextInputGroup
                    label="Invitation Code"
                    :show-required="false"
                    v-model="form.invitation_code"
                />
                <InputError
                    class="mt-2"
                    :message="form.errors.invitation_code"
                />
            </fieldset>

            <fieldset class="group-cb cb-signup mt-12">
                <input
                    type="checkbox"
                    v-model="form.terms"
                    class="tf-checkbox"
                    id="agree"
                    required
                />
                <label for="agree"
                    >I confirm that I agree to the
                    <a
                        href="#tcs"
                        data-bs-toggle="modal"
                        data-bs-target="#tcsModal"
                        class="text-secondary underline"
                        >Terms and conditions</a
                    ></label
                >
            </fieldset>

            <div class="row mt-20" v-if="form.processing">
                <ProgressBar
                    mode="indeterminate"
                    style="height: 6px"
                ></ProgressBar>
            </div>

            <Button
                type="submit"
                label="Register"
                :loading="form.processing"
                unstyled
                class="mt-10 primary-btn"
            />
        </form>

        <template #bottom-text>
            Already have an Account? &ensp;
            <Link :href="route('login')">Sign In Now</Link>
        </template>
    </AuthLayout>
</template>
