import { defineStore, acceptHMRUpdate } from "pinia";
import { usePage } from "@inertiajs/vue3";
import { useBinance } from "@/composables/useBinance";
import defaultPair from "assets/defaultpair.json";
import api from "@/composables/useApi";

const localStorageKey = "bybit-coins-list";

export const useCoinsStore = defineStore("coinsStore", {
    state: () => ({
        defaultPair: defaultPair, // Paires par défaut
        exchangeInfo: [], // Informations sur les paires d'échange disponibles
        localStorageKey: localStorageKey, // Clé du localStorage
        tradeCoin: JSON.stringify(defaultPair[0]),
        currencies: localStorage.getItem(localStorageKey)
            ? JSON.parse(localStorage.getItem(localStorageKey))
            : defaultPair, // Liste des devises (chargée depuis localStorage ou par défaut)
        tickers: {}, // Données des tickers (symboles, prix, etc.)
        chartData: [], // Données du graphique
        error: null, // Message d'erreur
        isLoading: false, // État de chargement
        cache: {
            data: null,
            lastFetched: null,
            ttl: 60 * 60 * 1000, // Cache TTL (60 minutes)
        },
    }),

    getters: {
        getTradeCoin: (state) =>
            JSON.parse(localStorage.getItem("trade_coin") || state.tradeCoin),

        isCacheValid: (state) =>
            state.cache?.data &&
            Date.now() - state.cache.lastFetched < state.cache.ttl,

        /**
         * Récupère une devise par son symbole
         * @param {string} symbol
         * @returns {Object|undefined}
         */
        getSymbolById: (state) => (symbol) =>
            state.currencies.find((s) => s.symbol === symbol),

        /**
         * Récupère un ticker par son symbole
         * @param {string} symbol
         * @returns {Object|undefined}
         */
        getTickerById: (state) => (symbol) => state.tickers[symbol],

        /**
         * Retourne les informations d'échange (ou les paires par défaut si vide)
         */
        getExchangeInfo: (state) =>
            state.exchangeInfo?.length ? state.exchangeInfo : state.defaultPair,
    },

    actions: {
        setTradeCoin(coin) {
            this.tradeCoin = coin;
            localStorage.setItem("trade_coin", coin);
        },

        goodSmbol(symbol, goodSymbols) {
            if (Object.keys(goodSymbols).includes(symbol)) {
                return goodSymbols[symbol];
            }
            return symbol;
        },

        async getPrice(symbol) {
            try {
                const response = await api.get(
                    "/ticker-price?symbol=" + symbol
                );
                const price = response.data.price;
                return price;
            } catch (err) {
                console.error(err);

                const ticker = this.getTickerById(symbol);

                return ticker?.price || 0;
            }
        },

        async convertAmount(amount, symbol, goodSymbol = null) {
            try {
                if (!goodSymbol) {
                    const response = await api.get("/config");

                    const goodSymbols = response.data.goodSymbols;
                    goodSymbol = this.goodSmbol(symbol, goodSymbols);
                }

                const price = await this.getPrice(goodSymbol);

                if (price) {
                    if (symbol == goodSymbol) {
                        return (amount * price).toFixed(8);
                    } else {
                        return (amount / price).toFixed(8);
                    }
                } else {
                    return 0;
                }
            } catch (err) {
                console.error(err);
                return 0;
            }
        },

        /**
         * Met à jour les informations d'échange dans l'état
         * @param {Array} data
         */
        setExchangeInfo(data) {
            this.exchangeInfo = data || [];
        },

        /**
         * Charge les informations sur les échanges depuis Binance
         */
        async loadExchangeInfo() {
            if (this.isLoading) return; // Empêche les appels simultanés

            this.isLoading = true;
            let data;
            try {
                if (this.isCacheValid) {
                    data = this.cache.data;
                } else {
                    const response = await api.get("/exchange-info");
                    data = Object.entries(response.data).map(([key, item]) => ({
                        symbol: item.symbol,
                        status: item.status,
                        base: item.baseAsset,
                        quote: item.quoteAsset,
                        name: item.name || "",
                    }));
                    this.cache.data = data;
                    this.cache.lastFetched = Date.now();
                }

                this.exchangeInfo = data;
            } catch (err) {
                err.response?.data?.message ||
                    err.message ||
                    "An error occurred while fetching exchange information.";
            } finally {
                this.isLoading = false;
            }
        },

        async loadExchangeInfo2() {
            const page = usePage(); // Charger dynamiquement usePage
            const { isLoading, errors, fetchExchangeInfo, data } = useBinance();

            this.isLoading = isLoading; // Synchroniser l'état de chargement

            try {
                await fetchExchangeInfo(); // Appeler la fonction pour charger les données

                if (data?.exchangeInfo?.length) {
                    const exchangeInfo = data.exchangeInfo
                        .filter((item) => {
                            const check1 =
                                page.props.siteConfig.paymentMethods.some(
                                    (method) =>
                                        method.symbol?.toLowerCase() ===
                                        item.quoteAsset.toLowerCase()
                                );
                            const check2 = item.status === "TRADING";

                            return check1 && check2;
                        })
                        .map((item) => ({
                            symbol: item.symbol,
                            status: item.status,
                            base: item.baseAsset,
                            quote: item.quoteAsset,
                            name: item.name || "",
                        }));

                    this.exchangeInfo = exchangeInfo;
                } else {
                    this.exchangeInfo = [];
                }
            } catch (err) {
                this.error = errors?.exchangeInfo || err.message;
            } finally {
                this.isLoading = false;
            }
        },

        /**
         * Met à jour un ticker dans l'état
         * @param {Object} payload
         */
        UPDATE_TICKER(payload) {
            const tick = this.tickers[payload.symbol];

            // Calculer le changement de prix (pchg)
            payload.pchg = tick ? (payload.price > tick.price ? 1 : -1) : 1;

            // Mettre à jour ou ajouter le ticker
            this.tickers[payload.symbol] = payload;
        },

        /**
         * Ajoute une nouvelle paire de devises dans l'état
         * @param {Object} payload
         */
        ADD_COIN_PAIR(payload) {
            if (!this.tickers[payload.symbol]) {
                // Ajouter à la liste des devises
                this.currencies.push(payload);

                // Sauvegarder dans le localStorage
                localStorage.setItem(
                    this.localStorageKey,
                    JSON.stringify(this.currencies)
                );
            }

            // Ajouter le ticker avec une valeur initiale
            this.tickers[payload.symbol] = { pchg: 1 };
        },

        /**
         * Supprime une paire de devises de l'état
         * @param {string} symbol
         */
        REMOVE_COIN_PAIR(symbol) {
            // Supprimer le ticker
            delete this.tickers[symbol];

            // Supprimer de la liste des devises
            const index = this.currencies.findIndex((s) => s.symbol === symbol);
            if (index !== -1) {
                this.currencies.splice(index, 1);
            }

            // Sauvegarder dans le localStorage
            localStorage.setItem(
                this.localStorageKey,
                JSON.stringify(this.currencies)
            );
        },
    },
});

// Gestion du HMR (Hot Module Replacement)
if (import.meta.hot) {
    import.meta.hot.accept(acceptHMRUpdate(useCoinsStore, import.meta.hot));
}
