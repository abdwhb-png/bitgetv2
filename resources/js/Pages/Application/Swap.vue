<style scoped>
.round-swap {
    cursor: pointer;
}
</style>

<script setup>
import { ref, onMounted, watch, computed } from "vue";
import { Head, usePage, useForm } from "@inertiajs/vue3";
import { useUserStore } from "@/stores/user";
import { useCoinsStore } from "@/stores/coins";
import { showToast } from "vant";
import { getAccountAsset } from "@/utils/helpers";
import CoinImg from "@/Components/Application/CoinImg.vue";
import InputError from "@/Components/Application/Form/InputError.vue";

const page = usePage();
const coinsStore = useCoinsStore();

const userStore = useUserStore();
const balances = ref(page.props.auth.user.account.balances);
const from = ref(null);
const to = ref(null);
const loading = ref(false);

const step = computed(() => {
    return from.value?.asset.symbol.startsWith("USDT") ? 10 : 0.0001;
});

const selectType = ref(null);

const selectAsset = (item) => {
    if (!Object.keys(item).includes("symbol", "id")) return;

    if (selectType.value == "from") {
        form.from = item.id;
        from.value = getAccountAsset(balances.value, item.symbol);
    }

    if (selectType.value == "to") {
        form.to = item.id;
        to.value = getAccountAsset(balances.value, item.symbol);
    }

    form.reset("amount");
};

const convertedAmount = ref(0);

const convertAmount = async () => {
    if (!from.value?.asset.symbol || !to.value?.asset.symbol) return 0;

    if (
        from.value.asset.symbol.startsWith("USDT") &&
        to.value.asset.symbol.startsWith("USDT")
    ) {
        convertedAmount.value = form.amount;
        return;
    }

    loading.value = true;

    const symbol = from.value.asset.symbol + to.value.asset.symbol;
    await coinsStore.convertAmount(form.amount, symbol).then((res) => {
        convertedAmount.value = res;
    });

    loading.value = false;
};

const interchange = () => {
    [form.from, form.to] = [form.to, form.from];
    [from.value, to.value] = [to.value, from.value];
    form.amount = 0;
};

function getAssets(theBalances) {
    from.value =
        getAccountAsset(theBalances, userStore.defaultCoinCurrency) ||
        page.props.from;

    to.value =
        getAccountAsset(
            theBalances,
            from.value.asset?.symbol || userStore.defaultCoinCurrency,
            "!="
        ) || page.props.to;
}

const form = useForm({
    _method: "PUT",
    from: null,
    to: null,
    amount: 0,
});

watch(
    () => form.amount,
    () => {
        convertAmount();
    }
);

onMounted(() => {
    getAssets(balances.value);
});

const submit = () => {
    form.transform((data) => ({
        ...data,
        from: from.value,
        to: to.value,
        from_amount: data.amount,
        to_amount: convertedAmount.value,
    })).post(route(page.props.routePrefix + "swap.store"), {
        only: ["from", "to", "balances"],
        preserveScroll: true,
        onProgress: () =>
            showToast({
                message: "Loading...",
                forbidClick: true,
                type: "loading",
            }),
        onSuccess: (page) => {
            showToast({
                message: "Successfully swapped.",
                forbidClick: true,
                type: "success",
            });
            from.value = page.props.from;
            to.value = page.props.to;
            balances.value = page.props.balances;
        },
        onError: (errors) => {
            console.log(errors);
            showToast({
                message: "Something went wrong, please check.",
                wordBreak: "break-word",
                type: "fail",
            });
        },
        onFinish: () => {
            form.amount = 0;
            convertedAmount.value = 0;
        },
    });
};
</script>

<template>
    <Head title="Swap" />
    <AppLayout header-title="Swap" :header-back="route($routePrefix + 'asset')">
        <div class="pt-45 pb-16">
            <div class="tf-container mt-20">
                <div class="trade-box">
                    <!-- From -->
                    <div class="accent-box bg-menuDark" v-if="from">
                        <div class="text-small d-flex justify-content-between">
                            <p class="text-white">From</p>
                            <p class="d-flex align-items-center gap-20">
                                <span class="d-flex align-items-center gap-4">
                                    <i class="icon-wallet fs-24"></i>
                                    {{ from?.amount }}
                                </span>
                                <a
                                    href="javascript:void(0);"
                                    class="text-primary"
                                    @click="form.amount = from?.amount"
                                    >Max</a
                                >
                            </p>
                        </div>
                        <div class="coin-item style-1 gap-8 mt-20">
                            <CoinImg :symbol="from?.asset?.symbol" />
                            <div class="content">
                                <div class="title">
                                    <h3 class="mb-4">
                                        <a
                                            href="#selectAssetModal"
                                            data-bs-toggle="modal"
                                            @click="selectType = 'from'"
                                            class="d-flex align-items-center"
                                        >
                                            {{ from?.asset?.symbol }}
                                            &nbsp;
                                            <i class="icon-select-down"></i
                                        ></a>
                                    </h3>
                                </div>
                                <div class="box-price text-end">
                                    <!-- this is useForm Form amount -->
                                    <h3 class="mb-4">
                                        {{ parseFloat(form.amount).toFixed(8) }}
                                    </h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Interchange -->
                    <div class="round-swap" @click="interchange">
                        <i class="icon icon-clockwise"></i>
                    </div>

                    <!-- To -->
                    <div v-if="to" class="accent-box bg-menuDark mt-8">
                        <div class="text-small d-flex justify-content-between">
                            <p class="text-white">To</p>
                            <span class="d-flex align-items-center gap-4">
                                <i class="icon-wallet fs-24"></i>
                                {{ to?.amount }}
                            </span>
                        </div>
                        <div class="coin-item style-1 gap-8 mt-20">
                            <CoinImg :symbol="to?.asset?.symbol" />
                            <div class="content">
                                <div class="title">
                                    <h3 class="mb-4">
                                        <a
                                            href="#selectAssetModal"
                                            data-bs-toggle="modal"
                                            @click="selectType = 'to'"
                                            class="d-flex align-items-center"
                                            >{{ to?.asset?.symbol }} &nbsp;<i
                                                class="icon-select-down"
                                            ></i
                                        ></a>
                                    </h3>
                                </div>
                                <div class="box-price text-end">
                                    <h3 class="mb-4">
                                        {{
                                            loading
                                                ? "..."
                                                : parseFloat(
                                                      convertedAmount
                                                  ).toFixed(8)
                                        }}
                                    </h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- select asset -->
                <div class="modal fade action-sheet" id="selectAssetModal">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <span>Select {{ selectType }} asset</span>
                                <span
                                    class="icon-cancel"
                                    data-bs-dismiss="modal"
                                ></span>
                            </div>
                            <ul class="mt-20 pb-16">
                                <li
                                    v-for="(item, index) in $page.props
                                        .siteConfig.paymentMethods"
                                    :key="index"
                                    class="mx-2"
                                >
                                    <a
                                        href="javascript:void(0);"
                                        v-if="
                                            (selectType == 'from' &&
                                                item.symbol !=
                                                    to?.asset?.symbol) ||
                                            (selectType == 'to' &&
                                                item.symbol !=
                                                    from?.asset?.symbol)
                                        "
                                        @click="selectAsset(item)"
                                        data-bs-dismiss="modal"
                                        class="d-flex justify-content-between gap-8 text-large item-check dom-value bg-surface mb-2 rounded-pill"
                                        :class="
                                            (selectType == 'from' &&
                                                item.symbol ==
                                                    from?.asset?.symbol) ||
                                            (selectType == 'to' &&
                                                item.symbol ==
                                                    to?.asset?.symbol)
                                                ? 'align-items-center active'
                                                : 'text-muted'
                                        "
                                    >
                                        <div
                                            class="d-flex gap-10 align-items-center"
                                        >
                                            <CoinImg
                                                :symbol="item.symbol"
                                                style="max-width: 28px"
                                            />
                                            <span>
                                                {{ item.name }}
                                            </span>
                                        </div>
                                        <i class="icon icon-check-circle"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="mt-4 accent-box bg-surface">
                    <FloatLabel variant="in">
                        <InputNumber
                            v-model="form.amount"
                            inputId="minmaxfraction"
                            showButtons
                            :step="step"
                            :min="0"
                            :max="from?.amount"
                            :minFractionDigits="2"
                            :maxFractionDigits="5"
                            class="ip-style2"
                            fluid
                        />
                        <label for="in_label">Amount</label>
                    </FloatLabel>
                    <InputError :message="form.errors.amount" />
                </div>

                <div class="mt-4">
                    <Button
                        label="Confirm"
                        :disabled="
                            loading ||
                            form.amount <= 0 ||
                            !convertedAmount ||
                            form.amount > from.amount
                        "
                        :loading="form.processing"
                        size="large"
                        @click="submit"
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
