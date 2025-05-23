<script setup>
import { onBeforeMount, reactive, ref } from "vue";
import { usePage } from "@inertiajs/vue3";
import { useBinanceStream } from "@/composables/useBinanceStream";

defineProps({
    title: {
        type: String,
        default: "Market",
    },
});

const page = usePage();
const datas = page.props.symbols_swiper;

const symbolsInfo = ref([]);

const coinDetail = (symbol) => {
    const { tickData, connect, disconnect } = useBinanceStream();

    connect(symbol);

    return tickData;
};

onBeforeMount(() => {
    if (!datas || !datas.length) return;
    Object.keys(datas).forEach((key) => {
        const { tickData, connect, isConnected } = useBinanceStream();

        var d = datas[key];
        connect(d.symbol.toLowerCase());

        symbolsInfo.value.push({
            symbol: d.symbol,
            name: d.name,
            icon: d.logo,
            tickData: reactive({ isConnected: isConnected, value: tickData }),
        });
    });
});
</script>

<template>
    <div>
        <h5>{{ title }}</h5>

        <div
            class="swiper tf-swiper swiper-wrapper-r mt-16"
            data-space-between="16"
            data-preview="2.8"
            data-tablet="2.8"
            data-desktop="3"
        >
            <div class="swiper-wrapper">
                <div
                    v-for="(item, index) in symbolsInfo"
                    class="swiper-slide"
                    :class="'swiper-slide-' + index == 0 ? 'active' : 'next'"
                    style="width: 325.333px; margin-right: 16px"
                    role="group"
                    :aria-label="
                        parseInt(index) + 1 + ' / ' + symbolsInfo.length
                    "
                >
                    <a href="#" class="coin-box d-block">
                        <div class="coin-logo">
                            <img :src="item.icon" alt="img" class="logo" />
                            <div class="title">
                                <p class="text-capitalize">
                                    {{ item.name }}
                                </p>
                                <span>{{ item.symbol }}</span>
                            </div>
                        </div>
                        <div class="mt-8 mb-8 coin-chart">
                            <div
                                :id="'line-chart-' + parseInt(index + 1)"
                            ></div>
                        </div>
                        <div
                            v-if="item.tickData.isConnected"
                            class="coin-price d-flex justify-content-between"
                        >
                            <span>$&nbsp;{{ item.tickData.value.price }}</span>

                            <span
                                class="d-flex align-items-center gap-2"
                                :class="
                                    item.tickData.value.side == 'buy'
                                        ? 'text-success'
                                        : 'text-danger'
                                "
                            >
                                <i
                                    :class="
                                        item.tickData.value.side == 'buy'
                                            ? 'text-primary icon-select-up'
                                            : 'text-danger icon-select-down'
                                    "
                                ></i>
                                {{ item.tickData.value.side }}
                            </span>
                        </div>
                        <div v-else>
                            <p>Getting prices...</p>
                        </div>
                        <div :class="'blur bg' + parseInt(index + 1)"></div>
                    </a>
                </div>

                <!-- <div class="swiper-slide">
                            <a href="choose-payment.html" class="coin-box d-block">
                                <div class="coin-logo">
                                    <img src="images/coin/market-1.jpg" alt="img" class="logo">
                                    <div class="title">
                                        <p>Bitcoin</p>
                                        <span>BTC</span>
                                    </div>
                                </div>
                                <div class="mt-8 mb-8 coin-chart">
                                    <div id="line-chart-4"></div>
                                </div>
                                <div class="coin-price d-flex justify-content-between">
                                    <span>$30780</span>
                                    <span class="text-primary d-flex align-items-center gap-2"><i class="icon-select-up"></i> 11,75%</span>
                                </div>
                                <div class="blur bg1">
                                </div>
                            </a>
                        </div>
                        <div class="swiper-slide">
                            <a href="choose-payment.html" class="coin-box d-block">
                                <div class="coin-logo">
                                    <img src="images/coin/market-3.jpg" alt="img" class="logo">
                                    <div class="title">
                                        <p>Bitcoin</p>
                                        <span>BTC</span>
                                    </div>
                                </div>
                                <div class="mt-8 mb-8 coin-chart">
                                    <div id="line-chart-5"></div>
                                </div>
                                <div class="coin-price d-flex justify-content-between">
                                    <span>$30780</span>
                                    <span class="text-primary d-flex align-items-center gap-2"><i class="icon-select-up"></i> 11,75%</span>
                                </div>
                                <div class="blur bg2">
                                </div>
                            </a>
                        </div> -->
            </div>
        </div>
    </div>
</template>
