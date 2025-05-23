<script setup>
import { computed } from "vue";
import { Link, Head, useForm, usePage } from "@inertiajs/vue3";
import AuthenticationCard from "@/Components/Admin/AuthenticationCard.vue";
import AuthenticationCardLogo from "@/Components/Admin/AuthenticationCardLogo.vue";
import InputError from "@/Components/Admin/InputError.vue";
import InputLabel from "@/Components/Admin/InputLabel.vue";
import PrimaryButton from "@/Components/Admin/PrimaryButton.vue";
import TextInput from "@/Components/Admin/TextInput.vue";
import { goTo } from "@/composables/useAuth";

defineProps({
    status: String,
});

const page = usePage();
const texts = computed(() => page.props.siteConfig.appTexts.forgot_password);

const form = useForm({
    email: "",
});

const submit = () => {
    form.post(route("password.email"));
};
</script>

<template>
    <Head title="Forgot Password" />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <div class="mb-4 text-sm text-gray-600">
            {{ texts.info }}
        </div>

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Email" />
                <TextInput
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-1 block w-full"
                    required
                    autofocus
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="flex items-center justify-end my-2">
                <PrimaryButton
                    class="w-auto"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Email Password Reset Link
                </PrimaryButton>
            </div>
        </form>
        <div class="flex items-center justify-end mt-4">
            <Link :href="route('login')" class="link"> Sign In </Link>
        </div>
    </AuthenticationCard>
</template>
