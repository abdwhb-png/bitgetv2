<template>
    <div>
        <div class="d-flex justify-content-between">
            Coin/Volume
            <p class="d-flex gap-8">
                <span>Price</span>
                <span>24h change</span>
            </p>
        </div>

        <div class="mt-40 text-center">
            <div v-if="checkLoading()">
                <van-loading type="spinner" color="#1989fa" size="50px" />
            </div>

            <h6 v-if="coinsStore.error" class="text-danger">
                {{ coinsStore.error }}
            </h6>

            <div v-if="!list?.length && !checkLoading()">
                <van-empty description="No data found" />
            </div>
        </div>

        <!-- Liste des cryptos -->
        <ul class="mt-16">
            <li v-for="(item, index) in list" :key="index" class="mb-16">
                <CurrencyCard
                    :ticker="tickers[item?.symbol] || {}"
                    :info="item"
                />
            </li>

            <!-- Bouton Load More -->
            <li v-if="coins.length > pageSize">
                <button @click="loadMore()" class="btn btn-secondary">
                    Load More
                </button>
            </li>
        </ul>
    </div>
</template>

<script>
import { reactive } from "vue";
import { useCoinsStore } from "@/stores/coins";
import { subscribeSymbol, unSubscribeSymbol } from "@/services/binance";
import CurrencyCard from "@/Components/Application/CurrencyCard.vue";

export default {
    props: {
        dataList: {
            type: [Array, Object],
            required: false,
        },
        dataType: {
            type: String,
            required: false,
        },
        isLoading: {
            type: Boolean,
            default: false,
        },
    },

    components: {
        CurrencyCard,
    },

    setup() {
        const coinsStore = useCoinsStore();

        return {
            coinsStore,
            tickers: coinsStore.tickers,
        };
    },

    data() {
        return {
            coins: reactive([]),
            pageSize: 0,
            step: 15,
            loading: false,
        };
    },

    computed: {
        list() {
            return Array.isArray(this.coins)
                ? this.coins.slice(0, this.pageSize)
                : [];
        },
    },

    watch: {
        dataList(newData) {
            if (newData?.length) {
                this.coins = newData;
            }
        },

        isLoading(newVal) {
            if (typeof newVal === "boolean") this.loading = newVal;
        },
    },

    async created() {
        if (this.dataType == "exchangeInfo") {
            await this.coinsStore.loadExchangeInfo();
            this.coins = this.coinsStore.getExchangeInfo;
        } else {
            this.coins = this.data || this.coinsStore.defaultPair;
        }

        this.loadMore();
    },

    // mounted() {
    //     if (this.coins?.length) this.loadMore();
    // },

    unmounted() {
        this.coins?.forEach((item) => {
            if (item) unSubscribeSymbol(item.symbol);
        });
    },

    methods: {
        async loadMore() {
            this.loading = true;
            this.pageSize += this.step;

            this.list.slice(0, this.pageSize).forEach((item) => {
                if (item?.symbol) subscribeSymbol(item.symbol);
            });

            this.loading = false;
        },

        checkLoading() {
            return this.coinsStore.isLoading || this.loading;
        },
    },
};
</script>
