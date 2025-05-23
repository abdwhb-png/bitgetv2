<template>
    <div>
        <h1>Binance Exchange Info</h1>
        <button @click="exchangeInfo">Fetch Excanhge Info</button>

        <div v-if="isLoading">Loading...</div>
        <div v-if="error" class="text-danger">{{ error }}</div>

        <!-- Liste des cryptos -->
        <ul class="list-group">
            <li
                class="list-group-item d-flex justify-content-between"
                v-for="(crypto, index) in paginatedCryptoList"
                :key="index"
            >
                <h3 class="text-secondary">
                    {{ crypto.symbol }}
                    <button
                        class="btn-sm"
                        :class="crypto.streaming ? 'btn-danger' : 'btn-primary'"
                        @click="stream(index)"
                    >
                        {{ crypto.streaming ? "stop stream" : "start stream" }}
                    </button>
                </h3>
                <p>
                    Base: {{ crypto.baseAsset }} | Quote:
                    {{ crypto.quoteAsset }}
                </p>
                <p>Status: {{ crypto.status }}</p>
                <!-- <p v-if="priceStreams[crypto.symbol]">
                    Price: {{ priceStreams[crypto.symbol].price || "N/A" }}
                </p> -->
            </li>
        </ul>

        <!-- Bouton Load More -->
        <button @click="loadMore" v-if="!isLoading && hasMoreItems">
            Load More
        </button>
    </div>
</template>

<script setup>
import { ref, computed } from "vue";
import { useBinance } from "@/composables/useBinance";

const {
    isLoading,
    errors,
    fetchExchangeInfo,
    data,
    streamTrade,
    unstreamTrade,
    priceStreams,
} = useBinance();

const cryptoList = ref([]);
const error = ref(null);

const pageSize = 10;
const currentPage = ref(1);

async function exchangeInfo() {
    try {
        const {} = useBinance();
        await fetchExchangeInfo();
        cryptoList.value = data.exchangeInfo ?? [];
    } catch (err) {
        error.value = errors.exchangeInfo ?? err.message;
    }
}

function stream(index) {
    const crypto = cryptoList.value[index];
    if (!crypto.streaming) streamTrade(crypto.symbol);
    else unstreamTrade(crypto.symbol);
    crypto.streaming = !crypto.streaming;
}

const paginatedCryptoList = computed(() => {
    const startIndex = 0;
    const endIndex = currentPage.value * pageSize;
    const list = cryptoList.value?.slice(startIndex, endIndex);
    list.forEach((crypto) => {
        // streamTrade(crypto.symbol);
    });
    console.log(priceStreams);
    return list;
});

const hasMoreItems = computed(() => {
    return cryptoList.value.length > currentPage.value * pageSize;
});

function loadMore() {
    if (hasMoreItems.value) {
        currentPage.value++;
    }
}
</script>
