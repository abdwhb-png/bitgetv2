<template>
    <div class="tradingview-widget-container">
        <div
            class="tradingview-widget-container__widget"
            ref="widgetContainer"
        ></div>
    </div>
</template>

<script>
import { usePage } from "@inertiajs/vue3";
export default {
    setup() {
        const page = usePage();
        const source = page.props.tv.widgets_src.ticker_tape;
        return {
            source,
        };
    },
    mounted() {
        const script = document.createElement("script");
        script.src = this.source;
        script.async = true;
        script.innerHTML = JSON.stringify(this.getWidgetOptions());
        this.$refs.widgetContainer.appendChild(script);
    },
    methods: {
        getWidgetOptions() {
            return {
                symbols: [
                    { description: "Bitcoin", proName: "BINANCE:BTCUSDT" },
                    { description: "Ethereum", proName: "COINBASE:ETHUSD" },
                    { description: "USDT ERC20", proName: "BINANCE:ETHUSDT" },
                    { description: "Solana", proName: "COINBASE:SOLUSD" },
                    { description: "Dogecoin", proName: "COINBASE:DOGEUSD" },
                ],
                showSymbolLogo: true,
                isTransparent: false,
                displayMode: "adaptive",
                colorTheme: "dark",
                locale: "en",
            };
        },
    },
};
</script>
