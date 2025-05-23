import { defineStore, acceptHMRUpdate } from "pinia";
import axios from "axios";

const createEntityState = (entities) => {
    const state = {};
    const loadingState = {};

    entities.forEach((entity) => {
        state[entity] = entity == "verifications" ? ["emails", "kycs"] : [];
        loadingState[entity] = false;
    });

    return { data: state, loadings: loadingState };
};

export const useAdminStore = defineStore("adminStore", {
    state: () => ({
        ...createEntityState([
            "users",
            "transactions",
            "orders",
            "verifications",
        ]),
        routePrefix: localStorage.getItem("route_prefix") || "admin.",
    }),

    getters: {
        loading: (state) => (key) => state.loadings[key],

        getData: (state) => (key) => state.data[key],
    },

    actions: {
        async fetchData(endpoint, stateKey, toast) {
            this.loadings[stateKey] = true; // Indiquer que le chargement est en cours
            try {
                const response = await axios.get(
                    route(this.routePrefix + endpoint)
                );
                this.data[stateKey] = response.data; // Mettre à jour l'état correspondant
            } catch (error) {
                const summary = `Error while fetching ${stateKey}`;
                console.error(summary, error);
                this.data[stateKey] =
                    stateKey == "verifications" ? ["emails", "kycs"] : []; // Réinitialiser les données en cas d'erreur

                // Afficher un message toast pour l'erreur
                toast.add({
                    severity: "error",
                    summary: summary,
                    detail: error.response?.data?.msg || error.message,
                });
            } finally {
                this.loadings[stateKey] = false; // Indiquer que le chargement est terminé
            }
        },

        fetchUsers(toast) {
            return this.fetchData("users.get", "users", toast);
        },

        fetchTransactions(toast) {
            return this.fetchData("transactions.get", "transactions", toast);
        },

        fetchOrders(toast) {
            return this.fetchData("orders.get", "orders", toast);
        },

        fetchVerifications(toast) {
            return this.fetchData("verifications.get", "verifications", toast);
        },
    },
});

// Gestion du HMR (Hot Module Replacement)
if (import.meta.hot) {
    import.meta.hot.accept(acceptHMRUpdate(useAdminStore, import.meta.hot));
}
