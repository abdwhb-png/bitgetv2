<script setup>
import { Head, Link, router } from "@inertiajs/vue3";
import WalletOverview from "@/Components/Application/WalletOverview.vue";
import Ticker from "@/Components/TradingView/Ticker.vue";
import SymbolInfo from "@/Components/TradingView/SymbolInfo.vue";
import CoinsList from "@/Components/Application/CoinsList.vue";
</script>

<template>
    <Head title="Home" />
    <AppLayout v-slot="slotProps">
        <div class="pt-68 pb-80">
            <!-- Wallet Overview -->
            <div class="bg-menuDark tf-container">
                <WalletOverview
                    class="pt-12 pb-12 mt-10"
                    :user="slotProps.user"
                    :show-buttons="false"
                />
            </div>

            <!-- Quick -->
            <div class="bg-menuDark tf-container my-1 p-3">
                <div class="row align-items-center">
                    <div class="col-7 col-md-5 col-xl-4">
                        <div class="card text-bg-dark">
                            <Link :href="route($routePrefix + 'asset')">
                                <img
                                    src="/app_assets/images/quick-transaction.jpg"
                                    class="card-img"
                                    alt="QUICK TRANSACTION"
                                />
                            </Link>
                        </div>
                    </div>
                    <div class="col">
                        <SymbolInfo class="d-none d-md-block mb-2" />

                        <div class="">
                            <Button
                                label="Trade Now"
                                icon="pi pi-objects-column"
                                severity="primary"
                                class="mb-2"
                                @click="
                                    router.visit(route($routePrefix + 'trade'))
                                "
                            />
                            <Button
                                label="Help Center"
                                severity="contrast"
                                icon="pi pi-info-circle"
                                class=""
                                data-bs-toggle="modal"
                                data-bs-target="#supportModal"
                            />
                        </div>
                    </div>
                </div>
                <!-- <SymbolsSwiper /> -->
            </div>

            <!-- TradingView Ticker -->
            <div class="bg-menuDark tf-container px-0">
                <Ticker :ticker="true" />
            </div>

            <div class="bg-menuDark tf-container my-2 p-3">
                <Suspense>
                    <template #default>
                        <CoinsList />
                    </template>
                    <template #fallback>
                        <div>Loading...</div>
                    </template>
                </Suspense>
            </div>
        </div>
    </AppLayout>
</template>
