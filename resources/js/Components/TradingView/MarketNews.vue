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
    },
    setup() {
        const page = usePage();
        const source = page.props.tv.widgets_src.market_news;
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
                feedMode: "all_symbols",
                isTransparent: false,
                displayMode: "adaptive",
                width: "100%",
                height: "100%",
                colorTheme: "dark",
                locale: "en",
            };
        },
    },
};
</script>
