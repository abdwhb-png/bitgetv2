<template>
    <a
        href="javascript:void(0);"
        @click="goTrade"
        class="coin-item style-2 gap-12"
    >
        <div v-lazy-container="{ selector: 'img', error: iconbase }">
            <img
                :data-src="
                    info && info.base ? getIconbase(info.base) : iconbase
                "
                alt="img"
                class="img"
            />
        </div>
        <div class="content">
            <div class="title">
                <p v-if="info" class="mb-4 text-button">
                    {{ info.name }}
                    <span class="text-small">
                        <b>{{ info.base }}</b
                        >/{{ info.quote }}
                    </span>
                </p>

                <span class="coin-vol text-secondary" v-if="ticker?.price">
                    Vol: {{ ticker.vol || "N/A" }}
                </span>
                <van-loading v-else type="spinner" size="24px" />
            </div>
            <div class="d-flex align-items-center gap-12" v-if="ticker?.price">
                <div class="coin-price text-small">
                    {{ ticker.price || "" }}
                    <span
                        style="
                            font-size: x-small;
                            font-weight: 700;
                            padding-left: 3px;
                        "
                        >{{ info.quote }}</span
                    >
                </div>

                <span
                    class="coin-btn coin-percentage"
                    :class="ticker.percent < 0 ? 'decrease' : 'increase'"
                    >{{ ticker.percent }}%</span
                >
            </div>
            <van-loading v-else type="spinner" size="24px" />
        </div>
    </a>
</template>

<script>
import { router } from "@inertiajs/vue3";
import { unSubscribeSymbol } from "@/services/binance";
import { showLoadingToast } from "vant";
import { useCoinsStore } from "@/stores/coins";

export default {
    props: ["ticker", "info"],

    setup() {
        return {
            coinsStore: useCoinsStore(),
        };
    },

    data() {
        return {
            showDropDown: false,
            iconbase: "app_assets/images/coin/default_.png",
        };
    },

    unmounted() {
        // if (this.info) this.removeCard();
    },

    methods: {
        getIconbase(base) {
            const url =
                "/app_assets/images/icons/" + base.toLowerCase() + "_.png";

            return url;
        },

        goTrade() {
            if (!this.info) return;

            showLoadingToast();
            localStorage.setItem("trade_coin", JSON.stringify(this.info));

            window.location.replace(route(this.$routePrefix + "trade"));

            // router.visit(route(this.$routePrefix + "trade"));
        },

        removeCard() {
            this.showDropDown = false;
            unSubscribeSymbol(this.info.symbol);
        },
    },
};
</script>
