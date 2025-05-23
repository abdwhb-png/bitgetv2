import { ref, reactive, onUnmounted, getCurrentInstance } from "vue";
import { BinanceService } from "@/services/BinanceService";
import { get } from "vant/lib/utils";

const binanceService = new BinanceService("main", true);

const state = binanceService.getState();
const client = state.client;
let wsClient = state.websocket.client;

const cache = {
    data: null,
    lastFetched: null,
    ttl: 5 * 60 * 1000, // Cache TTL (5 minutes)
};

/**
 * Composable to interact with Binance API and WebSocket
 */
export function useBinance() {
    const data = reactive({
        exchangeInfo: null,
        getPrice: null,
        priceStreams: {},
    });

    const isLoading = ref(false);
    const errors = reactive({});

    /**
     * Cleanup WebSocket on component unmount
     */
    function setupCleanup() {
        onUnmounted(() => {
            closeWebSocket();
        });
    }

    /**
     * Format an error object for better readability
     * @param {Object} error - The error object from the API or network
     * @returns {String} - A human-readable error message
     */
    function formatError(message, error) {
        console.error(message);

        if (error.response) {
            return `API Error: ${error.response.data.msg || error.message}`;
        } else if (error.request) {
            return "Network Error: Unable to reach the server.";
        } else {
            return `Unexpected Error: ${error.message}`;
        }
    }

    async function getPrice(symbol) {
        errors.getPrice = null;
        try {
            const response = await client.getSymbolPriceTicker({
                symbol: symbol,
            });
            data.getPrice = response.price;
        } catch (error) {
            errors.getPrice = formatError(
                `Error getting price for ${symbol}...`,
                error
            );
            throw error;
        }
    }

    /**
     * Fetch exchange info from Binance API
     * @returns {Promise<Object>} - The exchange info or error
     */
    async function fetchExchangeInfo(params = {}) {
        isLoading.value = true;
        errors.exchangeInfo = null;

        try {
            let response;
            // Use cached data if available and valid
            if (cache.data && Date.now() - cache.lastFetched < cache.ttl) {
                response = cache.data;
            } else {
                response = await client.getExchangeInfo(params);
                cache.data = response;
                cache.lastFetched = Date.now();
            }
            data.exchangeInfo = response?.symbols || [];
        } catch (error) {
            errors.exchangeInfo = formatError(
                "Error fetching exchange info...",
                error
            );
            throw error; // Re-throw if further handling is needed
        } finally {
            isLoading.value = false;
        }
    }

    /**
     * Subscribe to trade stream for a specific symbol
     * @param {String} symbol - The symbol to subscribe to
     */
    async function streamTrade(symbol) {
        errors.priceStream = null;
        try {
            const response = await wsClient.subscribeMarkPrice(
                symbol.toLowerCase()
            );
            if (!data.priceStreams[symbol]) {
                data.priceStreams[symbol] = response;
            }
        } catch (error) {
            errors.priceStream = formatError(
                `Error subscribing to trade stream for ${symbol}...`,
                error
            );
            throw error;
        }
    }

    /**
     * Unsubscribe from trade stream for a specific symbol
     * @param {String} symbol - The symbol to unsubscribe from
     */
    async function unstreamTrade(symbol) {
        if (data.priceStreams[symbol]) {
            try {
                await wsClient.unsubscribeMarkPrice(symbol.toLowerCase());
                delete data.priceStreams[symbol];
            } catch (error) {
                errors.priceStream = formatError(
                    `Error unsubscribing from trade stream for ${symbol}...`,
                    error
                );
                throw error;
            }
        }
    }

    /**
     * Close WebSocket connection
     */
    function closeWebSocket() {
        if (wsClient && state.websocket.key) {
            try {
                console.log("Closing WebSocket:", state.websocket.key);
                wsClient.close(state.websocket.key, false);
            } catch (error) {
                console.error("Error while closing WebSocket:", error);
            }
        } else {
            console.warn("WebSocket or key is already null. No action taken.");
        }

        wsClient = null;
    }

    // Verify the hook is used within a component instance
    if (!getCurrentInstance()) {
        setupCleanup();
        // throw new Error("useBinance must be called within a setup function.");
    }

    return {
        isLoading,
        errors,
        data,
        fetchExchangeInfo,
        getPrice,
        streamTrade,
        unstreamTrade,
        closeWebSocket,
    };
}
