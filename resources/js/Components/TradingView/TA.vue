<style scoped>
.tradingview-widget-container {
    position: relative;
    display: flex;
    flex-direction: column;
    height: 100%;
    width: 100%;
}
.tradingview-widget-container__widget {
    flex-grow: 1;
}
</style>

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
    props: {
        symbol: String,
        required: true,
    },
    setup() {
        const page = usePage();
        const source = page.props.tv.widgets_src.ta;
        const data_source = page.props.tv.data_source;
        return {
            source,
            data_source,
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
                interval: "5m",
                width: "100%",
                isTransparent: false,
                height: "100%",
                symbol: this.data_source + this.symbol,
                showIntervalTabs: true,
                displayMode: "multiple",
                locale: "en",
                colorTheme: "dark",
            };
        },
    },
};
</script>
