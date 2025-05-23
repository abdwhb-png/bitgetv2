<template>
    <div>
        <h1>Binance Live Ticks</h1>

        <div
            v-if="isConnected && tickData"
            class="d-flex justify-content-around align-items-center"
        >
            <h5 class="text-primary">{{ symbol.toUpperCase() }}</h5>
            <div>
                <p><strong>Price:</strong> {{ tickData.price }}</p>
                <p><strong>Quantity:</strong> {{ tickData.quantity }}</p>
                <p><strong>Side:</strong> {{ tickData.side }}</p>
                <p><strong>Time:</strong> {{ formatTime(tickData.time) }}</p>
            </div>

            <div>
                RawData:
                <p v-for="(item, index) in rawData" :key="index">
                    <strong>{{ index }}</strong> {{ item }}
                </p>
            </div>
        </div>
        <div v-else>
            <p>Connecting...</p>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useBinanceStream } from "@/composables/useBinanceStream";

const symbol = ref("btcusdt");
const { tickData, rawData, isConnected, connect, disconnect } =
    useBinanceStream();

onMounted(async () => {
    connect(symbol.value);
});

function formatTime(timestamp) {
    return new Date(timestamp).toLocaleTimeString();
}
</script>
