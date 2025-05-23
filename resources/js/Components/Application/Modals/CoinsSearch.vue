<script setup>
import { onBeforeMount, ref } from "vue";
import { useCoinsStore } from "@/stores/coins";
import { sleep } from "@/utils/helpers";
import CoinsList from "@/Components/Application/CoinsList.vue";

const coinsStore = useCoinsStore();
const filteredData = ref([]);
const searchInput = ref(null);
const loading = ref(false);

const search = async (event) => {
    let value = event.target.value.toLowerCase();

    try {
        loading.value = true;

        await sleep(200);

        if (!value) {
            reset();
            return;
        }

        if (!coinsStore.isCacheValid) {
            await coinsStore.loadExchangeInfo();
        }

        filteredData.value = coinsStore.getExchangeInfo.filter((item) => {
            return (
                item?.symbol.toLowerCase().includes(value) ||
                item?.name.toLowerCase().includes(value)
            );
        });
    } finally {
        loading.value = false;
    }
};

const reset = () => {
    searchInput.value = null;
    filteredData.value = coinsStore.exchangeInfo;
};

onBeforeMount(async () => {
    // loading.value = coinsStore.isLoading;
    // await coinsStore.loadExchangeInfo();
    // filteredData.value = coinsStore.getExchangeInfo;
});
</script>

<template>
    <div
        class="modal fade modalRight"
        id="coinsSearch"
        style="display: none"
        aria-hidden="true"
    >
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
                    <h3>Market Data</h3>
                </div>
                <div class="overflow-auto pt-45 pb-16">
                    <div class="tf-container">
                        <div class="mt-8 search-box box-input-field">
                            <i class="icon-search"></i>
                            <input
                                v-model="searchInput"
                                type="text"
                                placeholder="Search cryptocurrency"
                                required
                                class="clear-ip"
                                @input="search"
                            />
                            <i class="icon-close" @click="reset"></i>
                        </div>
                        <h5 class="mt-12">Popular search</h5>

                        <button
                            type="button"
                            id="closeCoinsSearch"
                            class="d-none"
                            data-bs-dimiss="modal"
                            data-bs-target="#coinsSearch"
                        ></button>

                        <!-- coins list -->
                        <CoinsList
                            data-type="exchangeInfo"
                            :data-list="filteredData"
                            :is-loading="loading"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
