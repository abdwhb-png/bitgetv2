import { defineStore, acceptHMRUpdate } from "pinia";
import DerivAPI from "@deriv/deriv-api/dist/DerivAPI";

export const useDerivApiStore = defineStore("derivApiStore", {
    state: () => ({
        api: null,
        basi: null,
        prefixes: {
            crypto: "cry",
        },
        coins: null,
    }),

    getters: {
        async getCoinsDetails(state) {
            const url =
                "https://api.coingecko.com/api/v3/coins/markets?vs_currency=usd;";
            await axios
                .get(url)
                .then((response) => {
                    state.coins = response.data;
                })
                .catch((error) => {
                    console.log(
                        "Error while fetching coins from coingeko",
                        error
                    );
                });
            return state.coins;
        },

        getActiveSymbols(state) {
            return state.api.activeSymbols();
        },
    },

    actions: {
        init() {
            const app_id = import.meta.env.VITE_DERIV_APP_ID;

            const connection = new WebSocket(
                `wss://ws.binaryws.com/websockets/v3?app_id=${app_id}`
            );

            const api = new DerivAPI({ connection });

            this.api = api;
            this.basic = api.basic;
        },
    },
});

if (import.meta.hot) {
    import.meta.hot.accept(acceptHMRUpdate(useDerivApiStore, import.meta.hot));
}
