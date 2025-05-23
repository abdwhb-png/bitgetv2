<style scoped>
label[for="on_label"] {
    font-size: large;
}
</style>

<script setup>
import { useForm, usePage } from "@inertiajs/vue3";
import { showToast } from "vant";

const page = usePage();
const userWallets = page.props.auth.user.wallet_addresses;

const paymentMethods = page.props.siteConfig.paymentMethods.map(
    (item, index) => {
        return {
            id: item.id,
            name: item.name,
            address: userWallets?.length
                ? userWallets.filter(
                      (wallet) => wallet.method.name == item.name
                  )?.[0]?.address
                : "",
        };
    }
);

const form = useForm({
    id: null,
    wallet_address: null,
});

const submit = (item) => {
    form.id = item.id;
    form.wallet_address = item.address;

    form.put(route(page.props.routePrefix + "wallet-address.update"), {
        onSuccess: () => {
            showToast({
                message: item.name + " wallet address updated",
                wordBreak: "break-word",
                type: "success",
            });
        },
        onError: (errors) => {
            showToast({
                message: errors.wallet_address,
                wordBreak: "break-word",
                type: "fail",
            });
        },
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <div class="modal fade modalRight" id="walletAddress">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div
                    class="header fixed-top bg-surface d-flex justify-content-center align-items-center"
                >
                    <span
                        class="left"
                        data-bs-dismiss="modal"
                        aria-hidden="true"
                        ><i class="icon-left-btn"></i
                    ></span>
                    <h3>Wallet Addresses</h3>
                </div>
                <div class="overflow-auto pt-45 pb-16">
                    <div class="tf-container">
                        <h4 class="mt-40">
                            Complete your wallet address for the payment methods
                            you prefer
                        </h4>

                        <div class="mt-12">
                            <fieldset>
                                <div
                                    class="form-group mt-20"
                                    v-for="(item, index) in paymentMethods"
                                    :key="index"
                                >
                                    <InputGroup>
                                        <FloatLabel variant="on">
                                            <InputText
                                                :id="'on_label' + index"
                                                v-model="item.address"
                                                :invalid="item.address === ''"
                                            />
                                            <label :for="'on_label' + index">{{
                                                item.name + " Wallet Address"
                                            }}</label>
                                        </FloatLabel>

                                        <Button
                                            @click="submit(item)"
                                            :disabled="item.address === ''"
                                            :loading="form.processing"
                                            label=""
                                            icon="pi pi-save"
                                        />
                                    </InputGroup>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
