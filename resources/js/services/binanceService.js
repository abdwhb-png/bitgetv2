import {
    MainClient,
    USDMClient,
    CoinMClient,
    WebsocketClient,
    DefaultLogger,
} from "binance";
import { ref, reactive } from "vue";

export class BinanceService {
    constructor(client = "main", websocket = false, auth = false) {
        const logger = {
            ...DefaultLogger,
            silly: (...params) => {
                console.log(new Date(), "sillyLog", ...params);
            },
        };
        // Reactive state for managing errors and loading state
        this.state = {
            clientType: client,
            isAuth: auth,
            client: null,
            websocket: reactive({
                client: null,
                key: null,
                url: null,
            }),
        };

        this.createClientInstance(logger);

        if (websocket) {
            this.createWsClientInstance(logger);
            this.listenToWs();
        }
    }

    /**
     * Get the current reactive state
     * @returns {Object} - Reactive state object
     */
    getState() {
        return this.state;
    }

    /**
     * Create a client instance based on the specified client type and authentication state
     * @param {Object} logger - Logger object for logging purposes
     * @returns {Object} - Binance client instance
     */
    createClientInstance(logger) {
        // Parameters for authenticated requests
        const params = this.state.isAuth
            ? {
                  api_key:
                      "nRRjJtfznriOqhiYt4TtkElIyjOXcAgQ8Tmm19lQCd3LJU967WjJuUWAR5SRAKI7",
                  api_secret:
                      "zP8EOnJINgWYf7bIyj3A2DzPUEOQIBZmujPILTUru2lOrM6fli3buv5kKuNukSKu",
              }
            : {};

        // Configuration object for the client instance
        const config = {
            ...params,
            logger,
        };

        // Default to MainClient for other APIs (e.g., spot, margin, mining, etc.)
        var client = new MainClient(config);

        // Create and return a USDMClient for USD-M futures APIs if clientType is 'usdm'
        if (this.state.clientType === "usdm") client = new USDMClient(config);

        // Create and return a CoinMClient for COIN-M futures APIs if clientType is 'coinm'
        if (this.state.clientType === "coinm") client = new CoinMClient(config);

        this.state.client = client;
    }

    createWsClientInstance(logger) {
        this.state.websocket.client = new WebsocketClient({
            beautify: true,
            wsUrl: "wss://ws-api.binance.com:9443",
            logger,
        });
    }

    listenToWs() {
        if (this.state.websocket.client) {
            // receive raw events
            this.state.websocket.client.on("message", (data) => {
                console.log(
                    "raw message received ",
                    JSON.stringify(data, null, 2)
                );
            });

            // notification when a connection is opened
            this.state.websocket.client.on("open", (data) => {
                this.state.websocket.key = data.wsKey;
                this.state.websocket.url = data.ws.target.url;
                console.log(
                    "BWS: connection opened open:",
                    data.wsKey,
                    data.ws.target.url
                );
            });

            // receive formatted events with beautified keys. Any "known" floats stored in strings as parsed as floats.
            this.state.websocket.client.on("formattedMessage", (data) => {
                console.log("formattedMessage: ", data);
            });

            // read response to command sent via WS stream (e.g LIST_SUBSCRIPTIONS)
            this.state.websocket.client.on("reply", (data) => {
                console.log("log reply: ", JSON.stringify(data, null, 2));
            });

            // receive notification when a ws connection is reconnecting automatically
            this.state.websocket.client.on("reconnecting", (data) => {
                console.log("ws automatically reconnecting.... ", data?.wsKey);
            });

            // receive notification that a reconnection completed successfully (e.g use REST to check for missing data)
            this.state.websocket.client.on("reconnected", (data) => {
                console.log("ws has reconnected ", data?.wsKey);
            });

            // Recommended: receive error events (e.g. first reconnection failed)
            this.state.websocket.client.on("error", (data) => {
                console.error("ws saw error ", data?.wsKey);
            });

            // this.state.websocket.client.on("trade", (data) => {
            //     if (data.s.toLowerCase() === this.symbol.toLowerCase()) {
            //         if (this.state.priceStreams[data.s]) this.price = data.p; // Update the price
            //         this.error = null; // Clear any previous errors
            //     }
            // });
        }
    }
}
