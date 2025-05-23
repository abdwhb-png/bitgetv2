<script setup>
defineProps({
    orderType: String,
    size: {
        type: String,
        default: "",
    },
});
</script>
<template>
    <van-tag v-if="orderType == 'BUY UP'" type="success" :size>{{
        orderType
    }}</van-tag>
    <van-tag v-else-if="orderType == 'BUY FALL'" type="danger" :size>{{
        orderType
    }}</van-tag>
    <van-tag v-else type="primary">{{ orderType }}</van-tag>
</template>
