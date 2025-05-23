<script setup>
import { Head } from "@inertiajs/vue3";
import { onMounted } from "vue";
import { showLoadingToast } from "vant";

onMounted(() => {
    showLoadingToast("Latest News");
});
</script>

<template>
    <Head title="News" />
    <AppLayout>
        <div class="pt-55 pb-80">
            <div class="tf-container px-0">
                <div class="mt-20">
                    <!-- TradingView Widget BEGIN -->
                    <iframe
                        id="market-news-iframe"
                        scrolling="no"
                        allowtransparency="true"
                        frameborder="0"
                        :src="'https://s.tradingview.com/embed-widget/timeline/?locale=en#%7B%22colorTheme%22%3A%22dark%22%2C%22isTransparent%22%3Afalse%2C%22displayMode%22%3A%22regular%22%2C%22width%22%3A480%2C%22height%22%3A830%2C%22utm_source%22%3A%22www.tradingview.com%22%2C%22utm_medium%22%3A%22widget_new%22%2C%22utm_campaign%22%3A%22timeline%22%7D'"
                        style="
                            box-sizing: border-box;
                            display: block;
                            height: calc(798px);
                            width: 100%;
                        "
                    ></iframe>
                    <!-- TradingView Widget END -->
                </div>
            </div>
        </div>
    </AppLayout>
</template>
