<style scoped>
.tf-list-item .box-round:hover {
    background-color: gray !important;
}
</style>

<script setup>
import { getCurrentInstance } from "vue";
import { Link, router } from "@inertiajs/vue3";
import { useUserStore } from "@/stores/user";
import ConfirmPassword from "@/Components/Application/ConfirmPassword.vue";
import { showLoadingToast } from "vant";

defineProps({
    user: Object,
    showButtons: {
        type: Boolean,
        default: true,
    },
});

const instance = getCurrentInstance();
const routePrefix = instance.appContext.config.globalProperties.$routePrefix;

const userStore = useUserStore();

const buttons = [
    {
        name: "deposit",
        icon: "icon-way2",
        url: route(routePrefix + "transaction", { type: "deposit" }),
    },
    {
        name: "withdraw",
        icon: "icon-way",
        url: route(routePrefix + "transaction", { type: "withdrawal" }),
    },
    {
        name: "swap",
        icon: "icon-swap",
    },
    {
        name: "history",
        icon: "icon-history",
    },
];

const goWithdraw = (url) => {
    showLoadingToast({
        message: "Loading...",
        forbidClick: true,
        duration: 500,
    });
    router.visit(url);
};
</script>
<template>
    <div class="">
        <div class="d-flex justify-content-between align-items-center">
            <h5>
                <span class="text-primary">My Wallet</span>
                &nbsp;
                <a
                    href="#"
                    class="choose-account"
                    data-bs-toggle="modal"
                    data-bs-target="#accountWallet"
                    ><span class="dom-text"
                        >{{ userStore.defaultCoinCurrency }}
                    </span>
                    &nbsp;<i class="icon-select-down"></i
                ></a>
            </h5>
            <h1>
                <a href="#" class="gap-2">
                    {{
                        "$" +
                        userStore.getBalance(userStore.defaultCoinCurrency)
                    }}
                    <span class="text-small d-none">
                        of
                        {{ "$" + userStore.getBalance("total") }}
                        &nbsp;
                        <span class="">{{
                            $page.props.auth.user.account.currency
                        }}</span>
                    </span>
                </a>
            </h1>
        </div>
        <ul class="mt-16 grid-4 m--16" v-if="showButtons">
            <li v-for="(item, index) in buttons" :key="index">
                <ConfirmPassword
                    v-if="item.name === 'withdraw'"
                    @confirmed="goWithdraw(item.url)"
                >
                    <a
                        href="javascript:void(0);"
                        class="tf-list-item d-flex flex-column gap-8 align-items-center"
                    >
                        <span
                            class="box-round bg-surface d-flex justify-content-center align-items-center"
                            ><i :class="'icon ' + item.icon"></i
                        ></span>
                        <span class="text-capitalize">{{ item.name }}</span>
                    </a>
                </ConfirmPassword>

                <Link
                    v-else
                    :href="item.url ?? route(routePrefix + item.name)"
                    class="tf-list-item d-flex flex-column gap-8 align-items-center"
                >
                    <span
                        class="box-round bg-surface d-flex justify-content-center align-items-center"
                        ><i :class="'icon ' + item.icon"></i
                    ></span>
                    <span class="text-capitalize">{{ item.name }}</span>
                </Link>
            </li>
        </ul>
    </div>
</template>
