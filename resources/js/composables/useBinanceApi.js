import { ref } from "vue";
import axios from "axios";

/**
 * useBinanceCryptoList composable to fetch the detailed list of cryptocurrencies with caching and failover URLs
 * @returns {Object} - Contains the crypto list, loading state, error message, and a fetch method
 */
export function useBinanceApi() {
    const cryptoList = ref([]); // Reactive list of cryptocurrencies
    const isLoading = ref(false); // Loading state
    const error = ref(null); // Error message

    const API_URLS = [
        "https://api.binance.com",
        "https://api-gcp.binance.com",
        "https://api1.binance.com",
        "https://api2.binance.com",
        "https://api3.binance.com",
        "https://api4.binance.com",
    ];
    const CACHE_DURATION = 5 * 60 * 1000; // Cache duration (5 minutes)
    let cache = {
        data: null,
        timestamp: null,
    };

    /**
     * Tries to fetch the crypto list from multiple API URLs
     * @returns {Array} - The detailed list of cryptocurrencies
     * @throws {Error} - Throws an error if all URLs fail
     */
    const fetchFromUrls = async () => {
        for (const url of API_URLS) {
            try {
                const response = await axios.get(`${url}/api/v3/exchangeInfo`);
                return response.data.symbols.map((symbol) => ({
                    symbol: symbol.symbol,
                    baseAsset: symbol.baseAsset,
                    quoteAsset: symbol.quoteAsset,
                    status: symbol.status,
                    isSpotTrading: symbol.isSpotTradingAllowed,
                    isMarginTrading: symbol.isMarginTradingAllowed,
                }));
            } catch (err) {
                console.warn(`Failed to fetch from ${url}: ${err.message}`);
            }
        }
        throw new Error("All API URLs failed to fetch data.");
    };

    /**
     * Fetch the detailed list of cryptocurrencies from Binance or cache
     */
    const fetchCryptoList = async () => {
        // Check if cache is still valid
        const now = Date.now();
        if (
            cache.data &&
            cache.timestamp &&
            now - cache.timestamp < CACHE_DURATION
        ) {
            cryptoList.value = cache.data;
            return;
        }

        isLoading.value = true;
        error.value = null;

        try {
            const data = await fetchFromUrls();
            // Update the reactive state and cache
            cryptoList.value = data;
            cache = {
                data,
                timestamp: now,
            };
        } catch (err) {
            error.value = `Failed to fetch crypto list: ${err.message}`;
            console.error(error.value);
        } finally {
            isLoading.value = false;
        }
    };

    return {
        cryptoList,
        isLoading,
        error,
        fetchCryptoList,
    };
}
