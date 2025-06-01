<style scoped>
.tf-container {
    margin-bottom: 16px;
}
</style>

<script setup>
import { Head, Link, usePage } from "@inertiajs/vue3";
import { useUserStore } from "@/stores/user";
import SiteConfig from "@/Components/Application/SiteConfig.vue";

const page = usePage();
const user = page.props.auth.user;
const userStore = useUserStore();
</script>

<template>
    <Head title="My" />
    <AppLayout header-back="/" header-title="On Boarding">
        <div class="pt-45 pb-16">
            <!-- Profile Overview -->
            <div class="bg-menuDark tf-container">
                <Link
                    :href="route($routePrefix + 'my.account')"
                    class="pt-12 pb-12 mt-4 d-flex justify-content-between align-items-center"
                >
                    <div class="box-account">
                        <img
                            :src="$page.props.auth.user.profile_photo_url"
                            alt="img"
                            class="avt"
                        />
                        <div class="info">
                            <h5>{{ user.full_name }}</h5>
                            <p class="text-small text-secondary mt-8 mb-8">
                                Profile and settings
                            </p>
                            <div class="d-flex gap-10">
                                <span
                                    v-if="!user.isVerified?.email"
                                    class="tag-xs style-2 round-2 red"
                                    >Unverified</span
                                >
                                <span
                                    v-if="!user.isVerified?.kyc"
                                    class="tag-xs style-2 round-2 red"
                                    >KYC Uncompleted</span
                                >
                            </div>
                        </div>
                    </div>
                    <span class="arr-right"
                        ><i class="icon-arr-right"></i
                    ></span>
                </Link>
            </div>

            <!--Exchange -->
            <div class="bg-menuDark tf-container">
                <div class="pt-12 pb-12 mt-4">
                    <h5>Exchange</h5>
                    <ul class="mt-16 grid-3 gap-12">
                        <li>
                            <a
                                href="#walletAddress"
                                data-bs-toggle="modal"
                                class="tf-list-item d-flex flex-column gap-8 align-items-center text-break text-center"
                            >
                                <i class="icon icon-currency"></i>
                                Wallet <br />
                                Addresses
                            </a>
                        </li>
                        <li>
                            <a
                                href="#paymentMethods"
                                data-bs-toggle="modal"
                                class="tf-list-item d-flex flex-column gap-8 align-items-center text-break text-center"
                            >
                                <i class="icon icon-bank"></i>
                                Payment <br />
                                Methods
                            </a>
                        </li>
                        <li>
                            <a
                                href="#paymentProof"
                                data-bs-toggle="modal"
                                class="tf-list-item d-flex flex-column gap-8 align-items-center text-break text-center"
                            >
                                <i class="icon icon-fileText"></i>
                                Payment <br />
                                Proof
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Help Center -->
            <div class="bg-menuDark tf-container">
                <div class="pt-12 pb-12 mt-4">
                    <h5>Help center</h5>
                    <ul class="mt-16 grid-3 gap-12">
                        <li>
                            <a
                                href="#support"
                                data-bs-toggle="modal"
                                data-bs-target="#supportModal"
                                class="tf-list-item d-flex flex-column gap-8 align-items-center"
                            >
                                <i class="icon icon-headset"></i>
                                Support
                            </a>
                        </li>
                        <li class="">
                            <a
                                href="#"
                                class="tf-list-item d-flex flex-column gap-8 align-items-center"
                            >
                                <i class="icon icon-globe"></i>
                                Community
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Site COnfig -->
            <div class="bg-menuDark tf-container">
                <SiteConfig />
            </div>

            <!-- Logout -->
            <div class="bg-menuDark tf-container mb-5">
                <a
                    href="#logout"
                    data-bs-toggle="modal"
                    data-bs-target="#logout"
                    class="pt-12 pb-12 mt-4 d-flex justify-content-between align-items-center"
                >
                    <h5 class="text-danger">Logout</h5>
                    <span class="arr-right"
                        ><i class="icon-arr-right"></i
                    ></span>
                </a>
            </div>
        </div>
    </AppLayout>
</template>
