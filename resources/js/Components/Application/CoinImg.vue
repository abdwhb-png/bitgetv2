<script setup>
defineProps({
    symbol: String,
});
</script>

<template>
    <div
        v-lazy-container="{
            selector: 'img',
            error: $page.props.siteConfig.default_coin_img,
        }"
    >
        <img
            :data-src="'/app_assets/images/coin/' + symbol + '.jpg'"
            :alt="symbol"
            class="img"
        />
    </div>
</template>
