<template>
    <div>
        <h1>Deriv Tick Data</h1>
        <p>Symbol: {{ symbol }}</p>
        <div v-if="tickData">
            <p>Latest Tick: {{ tickData }}</p>
        </div>
        <div v-else>
            <p>Loading...</p>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useDeriv } from "@/composables/useDeriv";

const symbol = "cryBTCUSD"; // The symbol to subscribe to
const { subscribeToTicks } = useDeriv();
const tickData = ref(null);

onMounted(() => {
    subscribeToTicks(symbol, (tick) => {
        tickData.value = tick;
    });
});
</script>
