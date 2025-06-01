<script setup>
import { Head, Link } from "@inertiajs/vue3";
import WalletOverview from "@/Components/Application/WalletOverview.vue";
import { useUserStore } from "@/stores/user";
import CoinImg from "@/Components/Application/CoinImg.vue";

const userStore = useUserStore();
</script>

<template>
    <Head title="Asset" />
    <AppLayout v-slot="slotProps">
        <div class="pt-68 pb-80">
            <!-- Wallet Overview -->
            <div class="bg-menuDark tf-container">
                <WalletOverview
                    class="pt-12 pb-12 mt-10"
                    :user="slotProps.user"
                />
            </div>

            <div class="tf-container">
                <a
                    href="#paymentProof"
                    data-bs-toggle="modal"
                    class="btn btn-dark w-100 my-2 mb-0"
                    >Add Payment Proof</a
                >
            </div>

            <div class="tf-container bg-menuDark my-2 py-2 pt-3">
                <ul>
                    <li
                        v-for="(balance, index) in userStore.user?.account
                            .balances"
                        :key="index"
                        class="mb-16"
                    >
                        <Link
                            :href="
                                route($routePrefix + 'transaction', 'deposit') +
                                '#' +
                                balance.asset.symbol
                            "
                            class="coin-item style-1 gap-12 bg-surface"
                        >
                            <CoinImg :symbol="balance.asset.symbol" />
                            <div class="content">
                                <div class="title">
                                    <span class="text-large"
                                        >{{ balance.asset.symbol }}
                                    </span>
                                    <p class="text-large">
                                        <span class="text-secondary"
                                            >Available Balance:
                                        </span>
                                        &nbsp;{{ balance.amount }}
                                    </p>
                                </div>
                                <div class="box-price">
                                    <span class="text-secondary"
                                        >In review
                                    </span>
                                    <p class="text-small mb-4">
                                        {{ balance.in_review || "--" }}
                                    </p>
                                </div>
                            </div>
                        </Link>
                    </li>
                </ul>
            </div>
        </div>
    </AppLayout>
</template>
