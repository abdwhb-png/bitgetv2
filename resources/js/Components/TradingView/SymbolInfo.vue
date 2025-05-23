<template>
    <div class="tradingview-widget-container">
        <div
            class="tradingview-widget-container__widget"
            ref="widgetContainer"
            style="max-height: 170px"
        ></div>
    </div>
</template>

<script>
import { usePage } from "@inertiajs/vue3";
export default {
    props: {
        symbol: String,
        default: "BINANCE:BTCUSDT",
    },
    setup() {
        const page = usePage();
        const source = page.props.tv.widgets_src.symbol_info;
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
                symbol: "BINANCE:BTCUSDT",
                width: "100%",
                locale: "en",
                colorTheme: "dark",
                isTransparent: false,
            };
        },
    },
};
</script>
