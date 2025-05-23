<style>
.input-error {
    margin-top: 5px;
    margin-left: 15px;
}
</style>

<script setup>
import { onMounted, watch, computed, ref } from "vue";
import { Link, useForm, usePage } from "@inertiajs/vue3";
import { useCoinsStore } from "@/stores/coins";
import { useUserStore } from "@/stores/user";
import { showToast } from "vant";
import InputError from "./Form/InputError.vue";

const props = defineProps({
    order: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(["done"]);

const page = usePage();

const coinsStore = useCoinsStore();
const userStore = useUserStore();

const quote = computed(() => page.props.auth.user.account.currency || "USDT");
const base = computed(() => coinsStore.getTradeCoin.base);

const ticker = computed(
    () => coinsStore.getTickerById(coinsStore.getTradeCoin.symbol) || {}
);

const availableBalance = ref(0);

const expirations = [
    { text: "30S", value: 30, active: false },
    { text: "60S", value: 60, active: false },
    { text: "3M", value: 60 * 3, active: false },
    { text: "5M", value: 60 * 5, active: false },
];

const selectExpiration = (index) => {
    if (expirations[index]) {
        const item = expirations[index];

        form.expiration = item.value;

        expirations.forEach((elem) => (elem.active = item.value == elem.value));
    }
};

const form = useForm({
    type: props.order.type,
    symbol: coinsStore.getTradeCoin.symbol,
    quote: quote.value,
    base: base.value,
    asset: coinsStore.getTradeCoin.quote,
    quantity: null,
    price: ticker.value.price || null,
    expiration: null,
});

const submit = (values) => {
    try {
        if (!availableBalance.value) {
            showToast({
                message: "Something went wrong, please try again.",
                wordBreak: "break-word",
                type: "fail",
            });
        }
        if (values.quantity <= 0 || values.quantity > availableBalance.value) {
            form.errors.quantity =
                "The quantity field is less or greater than the available balance.";
            return;
        }

        form.transform((data) => ({
            ...data,
            price: values.exec_price,
            time: values.time,
        })).put(route(page.props.routePrefix + "order.store"), {
            onSuccess: () => {
                showToast({
                    message: "Order created",
                    wordBreak: "break-word",
                    type: "success",
                });

                userStore.fetchOrders();

                emit("done");
            },
            onError: (errors) => {
                showToast({
                    message:
                        "Something went wrong, please check and try again or contact support.",
                    wordBreak: "break-word",
                    type: "fail",
                });
                console.log(errors);
            },
            onFinish: () => {
                form.reset();
            },
        });
    } catch (error) {
        console.log(error);
    }
};

const convertAmount = () => {
    availableBalance.value = userStore.getBalance(
        userStore.user.account.currency
    );
    return;
};

onMounted(async () => {
    if (expirations[1]) {
        selectExpiration(1);
    }

    const price = await coinsStore.getPrice(coinsStore.getTradeCoin.symbol);

    if (!ticker.value.price) {
        form.price = price;
    }

    convertAmount();
});

watch(
    () => props.order,
    (newOrder) => {
        form.type = newOrder.type;
    }
);

watch(
    () => userStore.balances,
    (newBalances) => {
        convertAmount();
    }
);
</script>

<template>
    <div>
        <van-form @submit="submit">
            <van-cell-group inset>
                <div class="form-group">
                    <van-field
                        v-model="form.symbol"
                        name="symbol"
                        label="Symbol"
                        placeholder="Select a symbol"
                        readonly
                        input-align="right"
                        label-align="center"
                    />
                    <InputError :message="form.errors.symbol" />
                </div>

                <div class="form-group">
                    <van-field
                        v-model="form.type"
                        name="type"
                        label="Order Type"
                        readonly
                        input-align="right"
                        label-align="center"
                    />
                    <InputError :message="form.errors.type" />
                </div>

                <div class="form-group">
                    <van-field
                        v-model="ticker.time"
                        name="time"
                        label="Open Time"
                        readonly
                        input-align="right"
                        label-align="center"
                        class="d-none"
                    />
                    <InputError :message="form.errors.time" />
                </div>

                <div class="form-group">
                    <van-field
                        v-model="form.price"
                        name="exec_price"
                        label="Open Price"
                        readonly
                        input-align="right"
                        label-align="center"
                    />
                    <InputError :message="form.errors.price" />
                </div>

                <div class="form-group mt-16">
                    <FloatLabel variant="in">
                        <InputNumber
                            v-model="form.quantity"
                            inputId="minmaxfraction"
                            showButtons
                            :step="10"
                            :min="5"
                            :max="parseFloat(availableBalance)"
                            :minFractionDigits="2"
                            :maxFractionDigits="5"
                            class="ip-style2"
                            fluid
                        />
                        <label for="in_label">{{
                            form.type + " Quantity"
                        }}</label>
                    </FloatLabel>

                    <InputError :message="form.errors.quantity" />

                    <div class="d-flex justify-content-around mt-16">
                        <p class="">Available Balance</p>
                        <div class="d-flex align-items-center gap-4">
                            <span
                                :class="
                                    userStore.getBalance(quote) > 0
                                        ? 'text-info'
                                        : 'text-danger'
                                "
                            >
                                {{ userStore.getBalance(quote) + " " + quote }}
                                <!-- &asymp;
                                {{ availableBalance + " " + base }} -->
                            </span>
                            <Link
                                :href="
                                    route(
                                        $routePrefix + 'transaction',
                                        'deposit'
                                    ) +
                                    '#' +
                                    quote
                                "
                                ><i class="pi pi-plus-circle"></i
                            ></Link>
                        </div>
                    </div>
                </div>

                <div class="mt-10 mx-4">
                    <h6>Duration</h6>
                    <ul class="grid-2 grid-md-4 gap-12 mt-16 mb-5">
                        <li
                            v-for="(item, index) in expirations"
                            :key="item.value"
                        >
                            <button
                                type="button"
                                @click="selectExpiration(index)"
                                class="tag-money text-small btn btn-outline-secondary"
                                :class="
                                    item.value === form.expiration &&
                                    item.active === true
                                        ? 'active'
                                        : ''
                                "
                            >
                                {{ item.text }}
                            </button>
                        </li>
                    </ul>
                    <InputError :message="form.errors.duration" />
                </div>
            </van-cell-group>

            <div class="mx-4 mb-3">
                <InputError :message="form.errors.error" />
                <Button
                    label="Confirm Order"
                    severity="info"
                    size="large"
                    class="rounded-pill w-100 w-md-50"
                    :disabled="
                        form.quantity <= 0 ||
                        !(availableBalance > 0) ||
                        form.quantity > availableBalance
                    "
                    :loading="form.processing"
                    type="submit"
                />
            </div>
        </van-form>
    </div>
</template>
