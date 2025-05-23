<script setup>
import { onBeforeMount, onMounted, onUnmounted, ref } from "vue";
import { Head, router, usePage } from "@inertiajs/vue3";
import { subscribeSymbol, unSubscribeSymbol } from "@/services/binance";
import { useCoinsStore } from "@/stores/coins";
import { useUserStore } from "@/stores/user";
import TechAnalysis from "@/Components/TradingView/TechAnalysis.vue";
import TA from "@/Components/TradingView/TA.vue";
import NewOrder from "@/Components/Application/NewOrder.vue";
import { showLoadingToast } from "vant";

const page = usePage();

const coinsStore = useCoinsStore();
const userStore = useUserStore();

const showOrder = ref(false);
const newOrder = ref(null);

const goPositions = () => {
    localStorage.setItem("historyActiveTab", "orders");
    router.visit(route(page.props.routePrefix + "history"));
};

const startOrder = (type) => {
    const ticker = coinsStore.getTickerById(coinsStore.getTradeCoin.symbol);

    newOrder.value = {
        type: type,
        ticker: ticker,
    };

    showOrder.value = true;
};

const orderCreated = () => {
    showOrder.value = false;
    newOrder.value = null;
};

onBeforeMount(async () => {
    // subscribe ticker for trade coin
    if (!coinsStore.getTickerById(coinsStore.getTradeCoin.symbol)) {
        subscribeSymbol(coinsStore.getTradeCoin.symbol);
    }
    // subscribe ticker for user account base currency
    let BC =
        coinsStore.getTradeCoin.base + page.props.auth.user.account.currency;
    if (!coinsStore.getTickerById(BC)) {
        subscribeSymbol(BC);
    }
});

onMounted(() => {
    showLoadingToast();
});

onUnmounted(() => {
    if (coinsStore.getTickerById(coinsStore.getTradeCoin.symbol)) {
        unSubscribeSymbol(coinsStore.getTradeCoin.symbol);
    }
});
</script>

<template>
    <Head title="Trade" />
    <AppLayout>
        <div class="pt-68 pb-80">
            <!-- Symbols Selector -->
            <div class="tf-container mb-2">
                <div class="row">
                    <div class="col">
                        <Button
                            severity="contrast"
                            icon="pi pi-caret-down"
                            :label="coinsStore.getTradeCoin.symbol"
                            type="button"
                            data-bs-toggle="modal"
                            data-bs-target="#coinsSearch"
                        />
                    </div>

                    <div class="col">
                        <Button
                            @click="goPositions"
                            severity="secondary"
                            icon="pi pi-list"
                            label="Positions"
                        />
                    </div>
                </div>
            </div>

            <!-- Technical Analysis -->
            <div class="tf-container my-1 px-0" style="height: 550px">
                <TechAnalysis :symbol="coinsStore.getTradeCoin.symbol" />
            </div>

            <!-- Order Buttons -->
            <div class="tf-container my-3">
                <div class="d-flex gap-8">
                    <Button
                        :disabled="userStore.user?.account.can_trade == 0"
                        severity="primary"
                        size="large"
                        label="Buy Up"
                        @click="startOrder('BUY UP')"
                    />
                    <Button
                        :disabled="userStore.user?.account.can_trade == 0"
                        severity="danger"
                        size="large"
                        label="Buy Fall"
                        @click="startOrder('BUY FALL')"
                    />

                    <van-popup
                        v-model:show="showOrder"
                        round
                        position="bottom"
                        @close="newOrder = null"
                        closeable
                        :style="{ 'padding-top': '40px' }"
                    >
                        <h5 class="mb-8 text-center">Order Confirmation</h5>
                        <NewOrder
                            v-if="newOrder"
                            :order="newOrder"
                            @done="orderCreated"
                        />
                    </van-popup>
                </div>
            </div>

            <!-- TA -->
            <div class="tf-container my-1 px-0" style="height: 950px">
                <TA :symbol="coinsStore.getTradeCoin.symbol" />
            </div>
        </div>
    </AppLayout>
</template>
