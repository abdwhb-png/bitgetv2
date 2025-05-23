<script setup>
import { onBeforeMount, ref } from "vue";
import { Head } from "@inertiajs/vue3";
import { useUserStore } from "@/stores/user";
import { showLoadingToast } from "vant";
import TransactionList from "@/Components/Application/History/TransactionList.vue";
import OrderList from "@/Components/Application/History/OrderList.vue";

const userStore = useUserStore();

const tabs = ["orders", "transactions"];

const activeTab = ref(localStorage.getItem("historyActiveTab") ?? "orders");

const changeTab = async (tab) => {
    const loading = showLoadingToast({
        message: "Fetching " + tab,
        duration: 5000,
        forbidClick: true,
    });

    localStorage.setItem("historyActiveTab", tab);
    activeTab.value = tab;

    if (tab === "orders") {
        await userStore.fetchOrders();
    }
    if (tab === "transactions") {
        await userStore.fetchTransactions();
    }

    loading.close();
};

onBeforeMount(async () => {
    changeTab(activeTab.value);
});
</script>

<template>
    <Head title="History" />
    <AppLayout header-title="History">
        <div class="pt-68 pb-80">
            <div class="bg-menuDark tf-container">
                <div class="tf-tab pt-12 mt-4">
                    <div class="tab-slide">
                        <ul class="nav nav-tabs wallet-tabs" role="tablist">
                            <li class="item-slide-effect"></li>
                            <li
                                v-for="(item, index) in tabs"
                                :key="index"
                                class="nav-item"
                                :class="activeTab === item ? 'active' : ''"
                                role="presentation"
                            >
                                <button
                                    @click="changeTab(item)"
                                    class="nav-link text-capitalize"
                                    :class="activeTab === item ? 'active' : ''"
                                    data-bs-toggle="tab"
                                    :data-bs-target="'#' + item"
                                    :aria-selected="
                                        activeTab === item ? 'true' : 'false'
                                    "
                                    role="tab"
                                >
                                    {{ item }}
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="tab-content pt-16 pb-16">
                        <div
                            class="tab-pane fade"
                            :class="activeTab === 'orders' ? 'show active' : ''"
                            id="orders"
                            role="tabpanel"
                        >
                            <van-empty
                                v-if="!userStore.hasOrders"
                                description="No Orders"
                            />
                            <OrderList v-else :data="userStore.orders" />
                        </div>
                        <div
                            class="tab-pane fade"
                            :class="
                                activeTab === 'transactions'
                                    ? 'show active'
                                    : ''
                            "
                            id="transactions"
                            role="tabpanel"
                        >
                            <van-empty
                                v-if="!userStore.hasTransactions"
                                description="No Transactions"
                            />
                            <TransactionList
                                v-else
                                :data="userStore.transactions"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
