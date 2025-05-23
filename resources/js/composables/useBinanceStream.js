import { ref, onUnmounted } from "vue";

export function useBinanceStream() {
    const ws = ref(null);
    const tickData = ref({
        symbol: "",
        price: 0,
        quantity: 0,
        time: null,
        side: "",
    });
    const rawData = ref(null);
    const isConnected = ref(false);
    const currentUrl = ref("");

    const urls = [
        "wss://stream.binance.com:9443/ws",
        "wss://stream.binance.com:443/ws",
        "wss://stream.binance.com:443/stream",
    ];
    let activeUrlIndex = 0;

    const connect = (symbol) => {
        if (ws.value && isConnected.value && ws.value.url.includes(symbol)) {
            console.warn(`BStream: Already connected to ${symbol}`);
            return;
        }
        disconnect();

        currentUrl.value = urls[activeUrlIndex];
        const url = `${currentUrl.value}/${symbol}@trade`;
        console.log(`BStream: Connecting to ${url}`);

        ws.value = new WebSocket(url);

        ws.value.onopen = () => {
            console.log(`BStream: Connected to ${symbol}`);
            isConnected.value = true;
        };

        ws.value.onmessage = (event) => {
            try {
                const data = JSON.parse(event.data);
                rawData.value = data;
                tickData.value = {
                    symbol: symbol.toUpperCase(),
                    price: parseFloat(data.p) || 0,
                    quantity: parseFloat(data.q) || 0,
                    time: data.T || Date.now(),
                    side: data.m ? "sell" : "buy",
                };
            } catch (error) {
                console.error("BStream: Error parsing message:", error);
            }
        };

        ws.value.onerror = (error) => {
            console.error(`BStream: WebSocket error:`, error);
            reconnect(symbol);
        };

        ws.value.onclose = () => {
            console.log("BStream: Connection closed");
            isConnected.value = false;
            ws.value = null;
        };
    };

    const disconnect = () => {
        if (ws.value) {
            ws.value.close();
            ws.value = null;
        }
    };

    const reconnect = (symbol) => {
        if (ws.value) disconnect();
        activeUrlIndex = (activeUrlIndex + 1) % urls.length;
        console.log(`BStream: Reconnecting to ${urls[activeUrlIndex]}`);
        connect(symbol);
    };

    onUnmounted(() => {
        disconnect();
    });

    return {
        tickData,
        rawData,
        isConnected,
        connect,
        disconnect,
    };
}
