<script setup>
import OrderDetail from "@/Components/Application/Modals/OrderDetail.vue";
import OrderTag from "@/Components/Application/Order/OrderTag.vue";
import OrderProfit from "@/Components/Application/Order/OrderProfit.vue";
import { computed, ref } from "vue";
import { formatDateTime } from "@/utils/helpers";

const props = defineProps({
    data: Object,
});

const [pageSize, step] = [ref(10), 10];

const list = computed(() => {
    return props.data.slice(0, pageSize.value);
});

const selectedOrder = ref(null);
</script>

<template>
    <!-- order detail modal -->
    <OrderDetail :order="selectedOrder" />
    <ul>
        <li v-for="(item, index) in list" :key="index" class="mb-8">
            <a
                href="#orderDetail"
                data-bs-toggle="modal"
                @click="selectedOrder = item"
                class="coin-item style-1 d-block bg-surface"
            >
                <div class="content line-bt mb-1 pb-1">
                    <div class="title">
                        <p class="mb-4 text-large">
                            <OrderTag :order-type="item.type" />

                            <span class="symbol mx-2">{{ item.symbol }}</span>

                            <span class="text-small qty">{{
                                item.quantity
                            }}</span>
                        </p>
                        <span class="text-secondary">{{
                            formatDateTime(item.created_at)
                        }}</span>
                    </div>

                    <div class="box-price">
                        <p class="text-small mb-4">
                            <OrderProfit :profit="item.profit" />
                        </p>
                        <p class="text-end">
                            <van-tag
                                plain
                                :type="
                                    item.status === 'opened'
                                        ? 'success'
                                        : 'warning'
                                "
                                >{{ item.status }}</van-tag
                            >
                        </p>
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <div class="text-center">
                        <p class="text-small text-muted">Open Price</p>
                        <p>{{ item.open_price }}</p>
                    </div>

                    <div class="text-center">
                        <p class="text-small text-muted">Close Price</p>
                        <p>
                            {{ item.close_price ?? "N/A" }}
                        </p>
                    </div>

                    <div class="text-center">
                        <p class="text-small text-muted">Duration</p>
                        <p>
                            {{
                                item.expiration > 60
                                    ? item.expiration / 60 + "M"
                                    : item.expiration + "S"
                            }}
                        </p>
                    </div>
                </div>
            </a>
        </li>

        <li class="">
            <!-- Bouton Load More -->
            <Button
                @click="pageSize = pageSize + step"
                v-show="props.data.length > pageSize"
                label="Show More"
                unstyled
                class="btn btn-secondary"
            />
        </li>
    </ul>
</template>
