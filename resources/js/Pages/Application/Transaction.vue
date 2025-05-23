<style>
:root:root {
    --van-dialog-has-title-message-text-color: #f00;
}
</style>

<script setup>
import {
    ref,
    computed,
    onUnmounted,
    watch,
    onBeforeMount,
    onMounted,
    watchEffect,
} from "vue";
import { Head, router, useForm, usePage } from "@inertiajs/vue3";
import { useUserStore } from "@/stores/user";
import { useCoinsStore } from "@/stores/coins";
import { showFailToast } from "vant";
import { copyToClipboard } from "@/utils/helpers";
import SelectGroup from "@/Components/Application/Form/SelectGroup.vue";
import InputError from "@/Components/Application/Form/InputError.vue";
import Confirmation from "@/Components/Application/Confirmation.vue";

// consts
const page = usePage();
const userStore = useUserStore();
const coinsStore = useCoinsStore();

const paymentMethods = ref([]);
const transaction = page.props.transactionType;
const userWallets = page.props.auth.user.wallet_addresses;

const needWallet = ref(false);
const depositAddress = ref(null);
const showDepositDialog = ref(false);
const showSuccess = ref(false);

const convertedAmount = ref(0);

// amount selection
const amountMoney = [
    { value: 50, active: false },
    { value: 100, active: false },
    { value: 200, active: false },
    { value: 500, active: false },
    { value: 1000, active: false },
    { value: 2000, active: false },
];

// form & form fns
const form = useForm({
    _method: "PUT",
    amount: null,
    type: transaction,
    pay_method: userStore.getDefaultCoinCurrency,
    binded_address: null,
});

// timer
const initialTime = 15 * 60; // Temps initial (15 minutes en secondes)
const remainingTime = ref(initialTime);
const timer = ref(null);
const isRunning = ref(false);

// Propriété calculée pour formater le temps en MM:SS
const formattedTime = computed(() => {
    const minutes = Math.floor(remainingTime.value / 60)
        .toString()
        .padStart(2, "0");
    const seconds = (remainingTime.value % 60).toString().padStart(2, "0");
    return `${minutes}:${seconds}`;
});

const selectAmount = (item) => {
    if (transaction == "withdrawal" && item.value > convertedAmount.value) {
        return;
    }

    form.amount = item.value;

    amountMoney.forEach((elem) => (elem.active = item.value == elem.value));
};

// setup fns
watch(remainingTime, () => {
    if (remainingTime.value <= 0) {
        window.location.reload();
    }
});

onBeforeMount(() => {
    page.props.siteConfig.paymentMethods?.forEach((method) => {
        paymentMethods.value.push({
            value: method.symbol,
            label: method.symbol,
        });
    });
});

onMounted(() => {
    const anchor = window.location.hash;
    if (anchor) {
        form.pay_method = anchor.replace("#", "");
    }
});

onUnmounted(() => {
    stopTimer();
});

function checkWallet(data) {
    var wallet = data?.filter(
        (item) =>
            item.name == form.pay_method ||
            item.method?.name == form.pay_method ||
            item.symbol == form.pay_method ||
            item.method?.symbol == form.pay_method
    );

    return wallet?.length ? wallet[0].address : null;
}

const confirm = () => {
    if (transaction == "withdrawal") {
        const address = checkWallet(userWallets);

        if (!address) {
            needWallet.value = true;
            return;
        }

        form.binded_address = address;

        submit();

        return;
    }

    if (transaction == "deposit") {
        const address = checkWallet(page.props.siteConfig.paymentMethods);

        if (!address) {
            showFailToast({
                message:
                    "Something went wrong, please contact customer support.",
                wordBreak: "break-word",
                duration: 5000,
            });

            const errorForm = useForm({
                error: "Deposit wallet not foun for : " + form.pay_method,
            });
            errorForm.post(route("fatal-error"));

            return;
        }

        form.binded_address = address;
        depositAddress.value = address;
        showDepositDialog.value = true;
        startTimer();

        return;
    }
};

function submit() {
    form.transform((data) => ({
        ...data,
        converted_amount: convertedAmount.value,
    })).post(route(page.props.routePrefix + "transaction.store"), {
        onSuccess: () => {
            showSuccess.value = true;
        },

        onFinish: () => {
            form.reset();
            selectAmount({});
        },
    });
}

// Démarrer le timer
const startTimer = () => {
    if (isRunning.value) return;

    isRunning.value = true;
    timer.value = setInterval(() => {
        if (remainingTime.value > 0) {
            remainingTime.value--;
        } else {
            stopTimer();
            alert("Le temps est écoulé !");
        }
    }, 1000);
};

// Arrêter le timer
const stopTimer = () => {
    clearInterval(timer.value);
    timer.value = null;
    isRunning.value = false;
};

// Réinitialiser le timer
const resetTimer = () => {
    stopTimer();
    remainingTime.value = initialTime;
};

watchEffect(async () => {
    if (!form.pay_method) return;

    const amount = userStore.getBalance(
        form.pay_method ? form.pay_method : "total"
    );
    const symbol = form.pay_method + page.props.auth.user.account.currency;

    if (form.pay_method.startsWith("USDT")) {
        convertedAmount.value = amount;
    } else {
        convertedAmount.value = 0;
        const price = await coinsStore.getPrice(symbol);
        convertedAmount.value = (price * amount).toFixed(4);
    }
});
</script>

<template>
    <Head :title="transaction" />
    <AppLayout :header-title="'Make ' + transaction">
        <Confirmation
            v-if="showSuccess"
            status="success"
            :url="route($routePrefix + 'asset')"
            :content="
                $page.props.siteConfig.appTexts[transaction].confirm_success
            "
        />
        <div v-else class="pt-45 pb-16">
            <div class="tf-container">
                <van-dialog
                    @confirm="router.visit(route($routePrefix + 'my.index'))"
                    v-model:show="needWallet"
                    show-cancel-button
                    title="Wallet address not provided"
                    message="You need to add a wallet address for this method to make a withdrawal"
                    confirm-button-text="Add now"
                    cancel-button-text="Cancel"
                >
                </van-dialog>

                <van-dialog
                    @confirm="submit()"
                    v-model:show="showDepositDialog"
                    show-cancel-button
                    :message="'You need to send $ xxx' + form.pay_method"
                    confirm-button-text="Confirm Deposit"
                    cancel-button-text="Cancel"
                >
                    <template #title>
                        Send only
                        <span class="text-primary">{{ form.pay_method }}</span>
                        to this address
                        <div class="text-center m-2">
                            <van-loading color="#1989fa" />
                            <p class="">{{ formattedTime }}</p>
                        </div>
                    </template>
                    <div class="accent-box-v5 bg-menuDark active m-2">
                        <span class="icon-box bg-icon1"
                            ><i class="icon-wallet"></i
                        ></span>
                        <div class="mt-12">
                            <a
                                href="#"
                                class="text-small text-muted text-break"
                                >{{ depositAddress }}</a
                            >
                            <button
                                type="button"
                                @click="copyToClipboard(depositAddress)"
                                class="btn btn-outline-warning mt-12"
                            >
                                <i class="pi pi-copy"></i>
                                Copy Address
                            </button>
                        </div>
                    </div>
                    <p class="text-small m-3 text-justify">
                        Click on confirm once you have made the deposit.
                    </p>
                </van-dialog>

                <div class="mt-12">
                    <form action="" @submit.prevent="confirm">
                        <div class="form-group mt-4">
                            <SelectGroup
                                label="Method"
                                :data="paymentMethods"
                                v-model="form.pay_method"
                            />
                            <InputError :message="form.errors.pay_method" />
                        </div>
                        <div class="mt-16 accent-box-v2 bg-menuDark">
                            <div
                                v-if="transaction == 'withdrawal'"
                                class="d-flex justify-content-between align-items-center"
                            >
                                <span>Your Balance:</span>
                                <h6>
                                    <span class="text-info">
                                        {{
                                            userStore.getBalance(
                                                form.pay_method
                                            )
                                        }}
                                    </span>
                                    {{ form.pay_method }}
                                    <span
                                        v-if="
                                            convertedAmount &&
                                            !form.pay_method.startsWith('USDT')
                                        "
                                    >
                                        &asymp;
                                        <span class="text-info">
                                            {{ convertedAmount }}
                                        </span>
                                        {{
                                            page.props.auth.user.account
                                                .currency
                                        }}
                                    </span>
                                </h6>
                            </div>

                            <div class="form-group">
                                <div class="mt-12 box-input-field">
                                    <input
                                        type="text"
                                        v-model="form.amount"
                                        placeholder="Amount"
                                        required=""
                                        class="clear-ip value_input ip-style2"
                                    />
                                    <i
                                        class="icon-close"
                                        @click="form.amount = null"
                                    ></i>
                                </div>
                                <InputError :message="form.errors.amount" />
                            </div>
                        </div>
                        <h5 class="mt-20">Amount Money</h5>
                        <ul class="grid-3 gap-12 mt-16 mb-5">
                            <li
                                v-for="(item, index) in amountMoney"
                                :key="index"
                            >
                                <button
                                    type="button"
                                    :disabled="
                                        transaction == 'withdrawal' &&
                                        item.value > convertedAmount
                                    "
                                    @click="selectAmount(item)"
                                    class="tag-money text-small btn btn-outline-secondary"
                                    :class="item.active ? 'active' : ''"
                                >
                                    ${{ item.value }}
                                </button>
                            </li>
                        </ul>
                        <fieldset
                            v-if="transaction == 'deposit' && form.pay_method"
                            class="group-cb cb-signup mt-16 mb-3"
                        >
                            <input
                                type="checkbox"
                                v-model="form.terms"
                                class="tf-checkbox"
                                id="understand"
                                required
                            />
                            <label for="understand"
                                >I understand that I must send only
                                <span class="text-primary">{{
                                    form.pay_method
                                }}</span>
                                to the address that will be generated.
                                Otherwise, I’ll lose funds.</label
                            >
                        </fieldset>
                        <Button
                            type="submit"
                            class="primary-btn"
                            label="Confirm"
                            size="large"
                            :loading="form.processing"
                        />
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
