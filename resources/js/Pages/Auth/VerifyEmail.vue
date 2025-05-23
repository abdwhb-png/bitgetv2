<script setup>
import { computed } from "vue";
import { Head, Link, useForm, usePage } from "@inertiajs/vue3";
import { logout } from "@/composables/useAuth";
import AuthenticationCard from "@/Components/Admin/AuthenticationCard.vue";
import AuthenticationCardLogo from "@/Components/Admin/AuthenticationCardLogo.vue";
import PrimaryButton from "@/Components/Admin/PrimaryButton.vue";

const page = usePage();
const texts = computed(() => page.props.siteConfig.appTexts.email_verification);

const props = defineProps({
    status: String,
});

const form = useForm({});

const submit = () => {
    form.post(route("verification.send"));
};

const verificationLinkSent = computed(
    () => props.status === "verification-link-sent"
);
</script>

<template>
    <Head title="Email Verification" />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <div class="mb-4 text-sm text-gray-600">
            {{ texts.info }}
        </div>

        <div
            v-if="verificationLinkSent"
            class="mb-4 font-medium text-sm text-green-600"
        >
            {{ texts.success }}
        </div>

        <form @submit.prevent="submit">
            <div class="mt-4">
                <PrimaryButton
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Resend Verification Email
                </PrimaryButton>
            </div>

            <div class="flex items-center justify-between mt-4">
                <Link
                    v-if="$page.props.isAdminDomain"
                    :href="route('profile.show')"
                    class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                    Edit Profile</Link
                >

                <Link
                    as="button"
                    type="button"
                    method="post"
                    :href="route('logout')"
                    class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 ms-2"
                >
                    Log Out
                </Link>
            </div>
        </form>
    </AuthenticationCard>
</template>
