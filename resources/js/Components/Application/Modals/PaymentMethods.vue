<script setup>
import { usePage } from "@inertiajs/vue3";

const page = usePage();

const userWallets = page.props.auth.user.wallet_addresses;
const payment_methods = page.props.siteConfig.paymentMethods;

function checkAddress(name) {
    var wallet = userWallets?.filter(
        (wallet) => wallet.method?.name == name || wallet.name == name
    );

    return wallet?.length ? wallet[0].address : null;
}
</script>

<template>
    <div class="modal fade action-sheet" id="paymentMethods">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <span>Available payment methods</span>
                    <span class="icon-cancel" data-bs-dismiss="modal"></span>
                </div>
                <ul class="mt-20 pb-16">
                    <li
                        v-for="(item, index) in payment_methods"
                        :key="index"
                        href="#walletAddress"
                        data-bs-toggle="modal"
                    >
                        <span
                            target="_blank"
                            class="d-flex justify-content-between align-items-center gap-8 text-large mb-1 item-check dom-value active"
                            :class="
                                checkAddress(item.name) !== null
                                    ? ''
                                    : 'text-danger'
                            "
                        >
                            {{ item.name }}
                            <span v-if="!checkAddress(item.name)">
                                (please add a wallet address)</span
                            >
                            <i
                                class="icon"
                                :class="
                                    checkAddress(item.name) !== null
                                        ? 'icon-check-circle text-success'
                                        : 'icon-close text-danger'
                                "
                            ></i>
                        </span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>
