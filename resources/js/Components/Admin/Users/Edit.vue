<script setup>
import { ref, watch, onBeforeMount, reactive } from "vue";
import { useForm, usePage } from "@inertiajs/vue3";
import { useToast } from "primevue/usetoast";

// Définir les props avec leur type
const props = defineProps({
    user: {
        type: Object, // On garde le type Object
        required: false, // Permettre que ce soit non requis
        default: null, // Spécifiez une valeur par défaut
    },
    show: {
        type: Boolean,
        default: false,
    },
});

// Définir les événements émis
const emits = defineEmits(["hide"]);

const page = usePage();
const toast = useToast();

const profitTypes = ref([]);
const profitType = ref(null);
const orderProfitStep = 1;

const canTradeOptions = ref([
    { name: "Yes", value: 1 },
    { name: "No", value: 0 },
]);
const canTrade = ref(null);

// Utiliser une référence pour suivre la visibilité
const visible = ref(props.show);

const pwdForm = reactive({
    password: null,
    password_confirmation: null,
});

const form = useForm({
    instance: null,
});

const submit = (section, item, instance) => {
    var newFormData = {};
    var url = null;
    form.instance = instance;

    if (section == "account") {
        url = route(page.props.routePrefix + "users.update", item.account_no);
        newFormData = {
            can_trade: canTrade.value.value,
            profit_type: profitType.value.value,
            profit_min: item.profit_min,
            profit_max: item.profit_max,
        };
    }

    if (section == "balance") {
        url = route(page.props.routePrefix + "users.update", item.id);
        newFormData = {
            amount: item.amount,
        };
    }

    if (section == "wallet") {
        url = route(page.props.routePrefix + "users.update", item.id);
        newFormData = {
            address: item.address,
        };
    }

    if (section == "password") {
        newFormData = pwdForm;
        url = route("user.password.update", item.email);
    }

    if (!url) return;

    form.transform((data) => ({
        section: section,
        ...data,
        ...newFormData,
    }));

    form.put(url, {
        only: ["flash"],
        onSuccess: (page) => {
            toast.add({
                severity: "success",
                summary:
                    "Action performed successfully for " + props.user.full_name,
                detail:
                    page.props.flash.status ||
                    "The " + section + " has been upated",
                life: 5000,
            });
        },
        onFinish: () => {
            form.reset();
            pwdForm.password = null;
            pwdForm.password_confirmation = null;
        },
    });
};

// Surveiller le changement de la prop `user`
watch(
    () => props.show,
    (newShow) => {
        visible.value = newShow;
        profitType.value = props.user?.account.profit_type
            ? {
                  value: props.user.account.profit_type,
                  name: page.props.siteConfig.dataTypes.profit[
                      props.user?.account.profit_type
                  ],
              }
            : null;
        canTrade.value =
            props.user?.account.can_trade == 1
                ? {
                      name: "Yes",
                      value: 1,
                  }
                : {
                      name: "No",
                      value: 0,
                  };
    },
    { immediate: true } // Appliquer immédiatement au montage
);

onBeforeMount(() => {
    if (page.props.siteConfig.dataTypes.profit)
        profitTypes.value = Object.keys(
            page.props.siteConfig.dataTypes.profit
        ).map((key) => {
            return {
                value: key,
                name: page.props.siteConfig.dataTypes.profit[key],
            };
        });
});
</script>

<template>
    <div>
        <Dialog
            @hide="emits('hide')"
            v-model:visible="visible"
            modal
            :dismissable-mask="true"
            :style="{ width: '50rem' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
        >
            <template #header>
                <h3 class="text-lg font-medium text-sky-900 dark:text-sky-700">
                    Edit
                    <span class="underline text-sky-600 dark:text-sky-500">{{
                        user.full_name
                    }}</span>
                </h3>
            </template>
            <form action="" @submit="updateUser">
                <Accordion value="">
                    <!-- account -->
                    <AccordionPanel value="account">
                        <AccordionHeader>
                            <span><i class="pi pi-user"></i> Account</span>
                        </AccordionHeader>
                        <AccordionContent>
                            <div class="flex-none md:flex gap-2">
                                <FloatLabel variant="on" class="mb-4">
                                    <Select
                                        v-model="canTrade"
                                        id="profit_type"
                                        :options="canTradeOptions"
                                        optionLabel="name"
                                        placeholder="Choose if user can trade"
                                        fluid
                                    />

                                    <label for="profit_type"
                                        >User can trade ?</label
                                    >
                                </FloatLabel>

                                <FloatLabel variant="on" class="mb-4">
                                    <Select
                                        v-model="profitType"
                                        id="profit_type"
                                        :options="profitTypes"
                                        optionLabel="name"
                                        placeholder="Select a type"
                                        fluid
                                    />

                                    <label for="profit_type"
                                        >Order profit type</label
                                    >
                                </FloatLabel>

                                <FloatLabel variant="on" class="mb-4 w-full">
                                    <InputNumber
                                        v-model="user.account.profit_min"
                                        id="profit_min"
                                        :min="1"
                                        :max="100"
                                        :step="orderProfitStep"
                                        suffix="%"
                                        showButtons
                                        fluid
                                    />
                                    <label for="profit_min"
                                        >Order profit min</label
                                    >
                                </FloatLabel>

                                <FloatLabel variant="on" class="mb-4 w-full">
                                    <InputNumber
                                        v-model="user.account.profit_max"
                                        id="profit_max"
                                        :min="user.account.profit_min"
                                        :max="100"
                                        :step="orderProfitStep"
                                        suffix="%"
                                        showButtons
                                        fluid
                                    />
                                    <label for="profit_max"
                                        >Order profit max</label
                                    >
                                </FloatLabel>
                            </div>

                            <div class="flex justify-end">
                                <Button
                                    label="Save"
                                    icon="pi pi-save"
                                    size="small"
                                    :loading="
                                        form.instance ==
                                            user.account.account_no &&
                                        form.processing
                                    "
                                    @click="
                                        submit(
                                            'account',
                                            user.account,
                                            user.account.account_no
                                        )
                                    "
                                />
                            </div>
                        </AccordionContent>
                    </AccordionPanel>

                    <!-- balances -->
                    <AccordionPanel value="balances">
                        <AccordionHeader>
                            <span><i class="pi pi-dollar"></i> Balances</span>
                        </AccordionHeader>
                        <AccordionContent>
                            <InputGroup
                                class="mb-5"
                                v-for="(balance, index) in user.balances"
                            >
                                <FloatLabel :key="index" variant="on">
                                    <InputNumber
                                        v-model="balance.amount"
                                        mode="currency"
                                        currency="USD"
                                        locale="en-US"
                                        :maxFractionDigits="8"
                                        :id="'balance_' + balance.id"
                                        fluid=""
                                    />
                                    <label :for="'balance_' + balance.id">{{
                                        balance.name + " Balance"
                                    }}</label>
                                </FloatLabel>

                                <Button
                                    icon="pi pi-save"
                                    :loading="
                                        form.instance == balance.name &&
                                        form.processing
                                    "
                                    @click="
                                        submit('balance', balance, balance.name)
                                    "
                                />
                            </InputGroup>
                        </AccordionContent>
                    </AccordionPanel>

                    <!-- wallet addresses -->
                    <AccordionPanel value="wallet_addresses">
                        <AccordionHeader>
                            <span
                                ><i class="pi pi-wallet"></i> Wallet
                                addresses</span
                            >
                        </AccordionHeader>

                        <AccordionContent>
                            <InputGroup
                                class="mb-5"
                                v-for="(wallet, index) in user.wallet_addresses"
                                :key="index"
                            >
                                <FloatLabel variant="on">
                                    <InputText
                                        v-model="wallet.address"
                                        :id="'wallet_' + wallet.id"
                                        fluid
                                    />
                                    <label :for="'wallet_' + wallet.id">{{
                                        wallet.name + " address"
                                    }}</label>
                                </FloatLabel>

                                <Button
                                    icon="pi pi-save"
                                    :loading="
                                        form.instance == wallet.name &&
                                        form.processing
                                    "
                                    @click="
                                        submit('wallet', wallet, wallet.name)
                                    "
                                />
                            </InputGroup>
                        </AccordionContent>
                    </AccordionPanel>

                    <!-- password -->
                    <AccordionPanel value="password">
                        <AccordionHeader>
                            <span><i class="pi pi-key"></i> Password</span>
                        </AccordionHeader>

                        <AccordionContent>
                            <div class="md:flex justify-around gap-4">
                                <FloatLabel variant="on" class="mb-4 md:w-1/2">
                                    <Password
                                        id="password"
                                        v-model="pwdForm.password"
                                        toggleMask
                                        fluid
                                    />
                                    <label for="password">New Password</label>
                                </FloatLabel>

                                <FloatLabel variant="on" class="mb-4 md:w-1/2">
                                    <Password
                                        fluid
                                        id="password_confirmation"
                                        v-model="pwdForm.password_confirmation"
                                        toggleMask
                                    />
                                    <label for="password_confirmation"
                                        >Confirm New Password</label
                                    >
                                </FloatLabel>
                            </div>
                            <div class="flex justify-end">
                                <Button
                                    label="Save"
                                    icon="pi pi-save"
                                    size="small"
                                    :loading="
                                        form.instance == user.email &&
                                        form.processing
                                    "
                                    @click="
                                        submit('password', user, user.email)
                                    "
                                />
                            </div>
                        </AccordionContent>
                    </AccordionPanel>
                </Accordion>
            </form>
        </Dialog>
    </div>
</template>
