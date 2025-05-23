<script setup>
import { ref } from "vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import AuthLayout from "@/Layouts/AuthLayout.vue";
import TextInputGroup from "@/Components/Application/Form/TextInputGroup.vue";
import PasswordInputGroup from "@/Components/Application/Form/PasswordInputGroup.vue";
import InputError from "@/Components/Application/Form/InputError.vue";
import Button from "primevue/button";

const props = defineProps({
    defaultEmail: {
        type: String,
        default: "",
    },
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: props.defaultEmail || "",
    password: props.defaultEmail ? "password" : "",
    remember: false,
});

const submit = async () => {
    form.transform((data) => ({
        ...data,
        remember: form.remember ? "on" : "",
    })).post(route("login"), {
        onFinish: () => {
            form.reset("password");
        },
    });
};
</script>

<template>
    <Head title="Sign In" />

    <AuthLayout title="Sign In to your account">
        <form action="" @submit.prevent="submit">
            <div
                v-if="props.status"
                class="mb-4 font-medium text-sm text-green-600"
            >
                {{ props.status }}
            </div>
            <fieldset class="mt-16">
                <TextInputGroup
                    label="Email"
                    v-model="form.email"
                    type="email"
                    :show-required="false"
                    autofocus
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </fieldset>
            <fieldset class="mt-16 mb-12">
                <PasswordInputGroup
                    label="Password"
                    v-model="form.password"
                    type="password"
                    :show-required="false"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </fieldset>

            <div class="d-flex justify-content-end">
                <Link
                    :href="route('password.request')"
                    class="text-secondary underline"
                    >Forgot Password?</Link
                >
            </div>

            <Button
                type="submit"
                label="Login"
                :loading="form.processing"
                unstyled
                class="mt-20 primary-btn"
            />
        </form>

        <template #bottom-text>
            Not registered yet? &ensp;<Link :href="route('register')"
                >Create An Account</Link
            >
        </template>
    </AuthLayout>
</template>
