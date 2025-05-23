import Api from "./binanceApi";
import { useCoinsStore } from "@/stores/coins";

// const store = useCoinsStore(pinia);
let store;

export function initBinanceStore(pinia) {
    store = useCoinsStore(pinia);
    store.setExchangeInfo();
}

const wsApi = new Api();

const subscribeSymbol = function (symbol) {
    if (!store) {
        throw new Error(
            "Binance store is not initialized. Call initBinanceStore first."
        );
    }

    wsApi.onTicker(symbol, (ticker) => {
        const tick = {
            price: parseFloat(ticker.c),
            vol: parseFloat(ticker.q).toFixed(2),
            percent: parseFloat(ticker.P).toFixed(2),
            chg: ticker.p,
            high: ticker.h,
            low: ticker.l,
            open: ticker.o,
            time: ticker.E,
            symbol: symbol,
        };
        store.UPDATE_TICKER(tick);
    });
};

const unSubscribeSymbol = function (symbol) {
    wsApi.closeSubscription("ticker", false, symbol);
};

const subscribeChart = function (symbol, interval) {
    wsApi.onKline(symbol, interval, () => {});
};

const unSubscribeChart = function (symbol, interval) {
    wsApi.closeSubscription("kline", false, symbol, interval);
};

export { subscribeSymbol, unSubscribeSymbol, subscribeChart, unSubscribeChart };
