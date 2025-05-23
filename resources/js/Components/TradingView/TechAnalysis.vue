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
    <div class="tradingview-widget-container" ref="widgetContainer">
        <div class="tradingview-widget-container__widget"></div>
    </div>
</template>

<script>
import { usePage } from "@inertiajs/vue3";

export default {
    name: "TradingViewWidget",
    props: {
        symbol: {
            type: String,
            requierd: true, // Default symbol for the widget
        },
        interval: {
            type: String,
            default: "D", // Default interval (D = Daily)
        },
        theme: {
            type: String,
            default: "dark", // Theme: 'light' or 'dark'
        },
        locale: {
            type: String,
            default: "en", // Default language
        },
        autosize: {
            type: Boolean,
            default: true, // Auto-resize the widget
        },
        timezone: {
            type: String,
            default: "Etc/UTC", // Default timezone
        },
    },

    setup() {
        const page = usePage();
        const source = page.props.tv.widgets_src.tech_analysis;
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
                autosize: this.autosize,
                symbol: this.data_source + this.symbol,
                interval: this.interval,
                timezone: this.timezone,
                theme: this.theme,
                style: "1",
                locale: this.locale,
                withdateranges: true,
                hide_side_toolbar: false,
                allow_symbol_change: false,
                details: true,
                calendar: true,
            };
        },
    },
};
</script>
