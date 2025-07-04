<script setup>
import { Head, Link, usePage } from "@inertiajs/vue3";
import VerificationBadge from "@/Components/Application/VerificationBadge.vue";
import { useUserStore } from "@/stores/user";
import { copyToClipboard } from "@/utils/helpers";
import { showSuccessToast } from "vant";

const userStore = useUserStore();

const page = usePage();
const user = page.props.auth.user;

async function changeMailNotif() {
    await userStore.setMailNotif().then(() => {
        showSuccessToast({
            message: "Mail Notification Updated",
            forbidClick: true,
        });
    });
}
</script>

<template>
    <Head title="My Account" />
    <AppLayout
        :header-back="route($routePrefix + 'my.index')"
        header-title="My Account"
    >
        <div class="pt-45 pb-16">
            <!--Mail Notif -->
            <ul class="bg-menuDark tf-container">
                <li
                    class="pt-12 pb-12 mt-4 d-flex justify-content-between align-items-center"
                >
                    <h5>Mail Notification</h5>
                    <input
                        class="tf-switch-check"
                        type="checkbox"
                        value="mail_notif"
                        name="mail_notif"
                        :checked="
                            userStore.user?.account.mail == 1 ? true : false
                        "
                        @change="changeMailNotif"
                    />
                </li>
            </ul>
            <div class="tf-container">
                <a
                    href="javascript:void(0);"
                    class="mt-16 d-flex justify-content-between align-items-center"
                >
                    <p class="text-small">Invitation Code</p>
                    <span class="text-secondary d-flex gap-8 align-items-center"
                        >{{ user.account.account_no }}
                        <i
                            @click="copyToClipboard(user.account.account_no)"
                            class="icon-copy fs-16 text-secondary"
                        ></i
                    ></span>
                </a>
                <a
                    :href="
                        user.isVerified.email
                            ? '#completed'
                            : route('verification.notice')
                    "
                    class="mt-16 d-flex justify-content-between"
                >
                    <div class="box-left d-flex gap-3">
                        <h6 class="mb-10">Primary Verification</h6>

                        <VerificationBadge
                            :status="user.isVerified.email"
                            :label="user.isVerified.email ? 'Completed' : ''"
                        />
                    </div>
                    <span
                        v-show="!user.isVerified.email"
                        class="icon-arr-right text-secondary fs-12"
                    ></span>
                </a>

                <Link
                    :href="
                        user.isVerified.kyc == 1
                            ? '#verified'
                            : route($routePrefix + 'my.verification', 'kyc')
                    "
                    class="mt-16 d-flex justify-content-between"
                >
                    <div class="box-left d-flex gap-3">
                        <h6 class="mb-10">KYC Verification</h6>
                        <VerificationBadge
                            :status="user.isVerified.kyc"
                            :label="
                                user.isVerified.kyc == 0 ? 'Processing' : ''
                            "
                        />
                    </div>
                    <span
                        v-show="user.isVerified.kyc != 1"
                        class="icon-arr-right text-secondary fs-12"
                    ></span>
                </Link>
                <a
                    href="#"
                    data-bs-toggle="modal"
                    data-bs-target="#filterPicture"
                    class="mt-16 pb-12 line-bt d-flex justify-content-between align-items-center"
                >
                    <p class="text-small">Profile Picture</p>
                    <span class="text-secondary d-flex gap-8 align-items-center"
                        ><i class="icon-arr-right fs-12 text-secondary"></i
                    ></span>
                </a>

                <!-- Personal Information -->
                <ul class="mt-16 pb-12 line-bt">
                    <li>
                        <Link
                            href="#personalInformation"
                            data-bs-toggle="modal"
                            class="d-flex justify-content-between align-items-center"
                        >
                            <h4>Personal Information</h4>
                            <span
                                class="icon-arr-right text-secondary fs-12"
                            ></span>
                        </Link>
                    </li>
                    <ul style="max-height: 250px; overflow-y: scroll">
                        <li>
                            <div
                                class="mt-16 d-flex justify-content-between align-items-center"
                            >
                                <p class="text-small text-white">Email</p>
                                <span
                                    class="text-secondary d-flex gap-8 align-items-center"
                                    >{{ user.email }}</span
                                >
                            </div>
                        </li>
                        <li v-for="(item, index) in user.info" :key="index">
                            <div
                                href="javascript:void(0);"
                                class="mt-16 d-flex justify-content-between align-items-center"
                            >
                                <p
                                    class="text-small text-capitalize text-white"
                                >
                                    {{ index.replace("_", " ") }}
                                </p>
                                <span class="text-secondary text-nowrap">{{
                                    item
                                }}</span>
                            </div>
                        </li>
                    </ul>
                </ul>
                <ul class="mt-16 pb-16 line-bt">
                    <li>
                        <h5>Security Center</h5>
                    </li>
                    <li>
                        <a
                            href="#changePassword"
                            data-bs-toggle="modal"
                            class="mt-16 d-flex justify-content-between align-items-center"
                        >
                            <p class="text-small">Password</p>
                            <span
                                class="icon-arr-right text-secondary fs-12"
                            ></span>
                        </a>
                    </li>
                    <li>
                        <a
                            href="#browserSessions"
                            data-bs-toggle="modal"
                            @click.prevent="userStore.fetchBrowserSessions()"
                            class="mt-16 d-flex justify-content-between align-items-center"
                        >
                            <p class="text-small">Browser Sessions</p>
                            <span
                                class="icon-arr-right text-secondary fs-12"
                            ></span>
                        </a>
                    </li>
                </ul>
            </div>
            <div style="min-height: 100px"></div>
        </div>
    </AppLayout>
</template>
