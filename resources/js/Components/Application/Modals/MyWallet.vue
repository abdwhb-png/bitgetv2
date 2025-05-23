<script setup>
import { useUserStore } from "@/stores/user";
import CoinImg from "@/Components/Application/CoinImg.vue";

const userStore = useUserStore();
</script>

<template>
    <div class="modal fade action-sheet" id="accountWallet">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <span>Wallet</span>
                    <span class="icon-cancel" data-bs-dismiss="modal"></span>
                </div>
                <ul class="mt-20 pb-16">
                    <li
                        v-for="(item, index) in $page.props.siteConfig
                            .paymentMethods"
                        :key="index"
                    >
                        <a
                            href="javascript:void(0);"
                            @click="
                                userStore.setDefaultCoinCurrency(item.symbol)
                            "
                            data-bs-dismiss="modal"
                            class="d-flex justify-content-between gap-8 text-large item-check dom-value"
                            :class="
                                item.symbol == userStore.defaultCoinCurrency
                                    ? 'align-items-center active'
                                    : 'text-muted'
                            "
                        >
                            <div class="d-flex gap-10 align-items-center">
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
</template>
