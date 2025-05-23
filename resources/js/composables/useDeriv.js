import { ref } from "vue";
import { useDerivApiStore } from "@/stores/derivApiStore";

/**
 * useDeriv composable to handle Deriv API interactions for ticks
 * @returns {Object} - Functions for working with Deriv tick data
 */
export function useDeriv() {
    const derivStore = useDerivApiStore();

    // Initialize the API if it's not already initialized
    if (!derivStore.api) {
        derivStore.init();
    }

    const api = derivStore.api;

    /**
     * Subscribe to live tick stream for a given symbol
     * @param {string} symbol - The symbol for live ticks
     * @returns {Subscription} - Observable subscription
     */
    const tickStream = (symbol) => api.subscribe({ ticks: symbol });

    /**
     * Get the latest ticks for a given symbol
     * @param {string} symbol - The symbol to fetch ticks for
     * @returns {Promise<Ticks>} - Latest ticks object
     */
    const getTicks = async (symbol) => {
        try {
            return await api.ticks(symbol);
        } catch (error) {
            console.error("Error fetching ticks(" + symbol + "):", error);
            throw error;
        }
    };

    /**
     * Subscribe to tick updates for a given symbol
     * @param {string} symbol - The symbol for tick subscription
     * @param {Function} onUpdateCallback - Callback function for tick updates
     * @returns {Promise<Subscription>} - Observable subscription
     */
    const subscribeToTicks = async (symbol, onUpdateCallback) => {
        try {
            const ticks = await getTicks(symbol);
            return ticks.onUpdate().subscribe(onUpdateCallback);
        } catch (error) {
            console.error(
                "Error subscribing to ticks (" + symbol + "):",
                error
            );
            throw error;
        }
    };

    /**
     * Fetch the tick history for a given symbol
     * @param {string} symbol - The symbol to fetch tick history for
     * @returns {Promise<Array>} - Array of historical ticks
     */
    const getTicksHistory = async (symbol) => {
        try {
            const ticks = await getTicks(symbol);
            return ticks.list;
        } catch (error) {
            console.error("Error fetching tick history:", error);
            throw error;
        }
    };

    /**
     * Fetch older tick history for a given symbol
     * @param {string} symbol - The symbol to fetch older tick history for
     * @param {number} count - Number of older ticks to fetch
     * @param {Date} end - End date for the tick history
     * @returns {Promise<Array>} - Array of older historical ticks
     */
    const getTicksOlderHistory = async (
        symbol,
        count = 100,
        end = new Date()
    ) => {
        try {
            const ticks = await getTicks(symbol);
            return await ticks.history({ count, end });
        } catch (error) {
            console.error("Error fetching older tick history:", error);
            throw error;
        }
    };

    /**
     * Fetch the tick history for a given symbol
     * @param {string} symbol - The symbol to fetch tick history for
     * @returns {Promise<Array>} - Array of historical ticks
     */
    const getActiveSymbols = async (symbol) => {
        try {
            const symbols = await api.activeSymbols();
            return symbols;
        } catch (error) {
            console.error("Error fetching active symbols:", error);
            throw error;
        }
    };

    return {
        tickStream,
        getTicks,
        subscribeToTicks,
        getTicksHistory,
        getTicksOlderHistory,
        getActiveSymbols,
    };
}
