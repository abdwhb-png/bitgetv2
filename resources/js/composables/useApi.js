import { setupCache } from "axios-cache-interceptor";

const baseURL = import.meta.env.VITE_APP_URL + "/api";

const api = setupCache(
    axios.create({
        baseURL: "/api",
    }),
    {
        ttl: 30 * 60 * 1000, // Durée de vie du cache (30 minutes)
    }
);

export default api;
