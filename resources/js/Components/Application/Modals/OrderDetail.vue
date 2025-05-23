<script setup>
import OrderTag from "@/Components/Application/Order/OrderTag.vue";
import OrderProfit from "@/Components/Application/Order/OrderProfit.vue";

defineProps({
    order: Object | null,
});
</script>

<template>
    <div
        class="modal fade modalCenter"
        id="orderDetail"
        aria-hidden="true"
        role="dialog"
        style=""
    >
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content success_box">
                <div class="icon-1 ani3">
                    <span
                        class="circle-box lg bg-circle check-icon bg-primary"
                    ></span>
                </div>
                <div class="icon-2 ani5">
                    <span class="circle-box md bg-primary"></span>
                </div>
                <div class="icon-3 ani8">
                    <span class="circle-box md bg-primary"></span>
                </div>
                <div class="icon-4 ani2">
                    <span class="circle-box sm bg-primary"></span>
                </div>
                <div class="text-center" v-if="order !== null">
                    <h2 class="text-surface">
                        {{ order.symbol }}
                    </h2>
                    <p class="text-small mt-8">
                        Expiry settlement
                        {{ order.closed_at ? "completed" : "processing" }}
                    </p>
                    <h5 class="mt-16 text-surface">
                        <OrderTag :order-type="order.type" size="large" />
                    </h5>
                    <h1 class="mt-8">
                        <OrderProfit :profit="order.profit" />
                    </h1>
                    <p class="mt-16 text-button">
                        <span class="text-muted">Open Price: </span>
                        <span class="text-surface">{{ order.open_price }}</span>
                    </p>
                    <p class="mt-4 text-button">
                        <span class="">Quantity: </span>
                        <span class="text-surface">{{ order.quantity }}</span>
                    </p>
                    <p class="mt-4 text-button d-none">
                        <span class="">Close Price: </span>
                        <span class="text-surface">{{ order.open_price }}</span>
                    </p>
                </div>

                <button
                    type="button"
                    data-bs-dismiss="modal"
                    data-bs-target="#orderDetail"
                    aria-label="Close"
                    class="tf-btn dark border-dark mt-40"
                >
                    Close
                </button>
            </div>
        </div>
    </div>
</template>
