import { defineStore, acceptHMRUpdate } from "pinia";
import axios from "axios";
import { formatNotification } from "@/utils/helpers";
import { showLoadingToast, showSuccessToast } from "vant";

export const useUserStore = defineStore("userStore", {
    state: () => ({
        user: null,
        account: null,
        ids: null,
        balances: [],
        transactions: [],
        orders: [],
        notifications: [],
        unreadCount: 0,
        mustCompleteInfo: false,
        defaultCoinCurrency:
            localStorage.getItem("default_coin_currency") || "USDT-TRC20",
        sessions: [],
        loading: false,
        routePrefix: localStorage.getItem("route_prefix") || "app.",
    }),

    getters: {
        hasNotifications: (state) => !!state.notifications?.length,
        hasTransactions: (state) => !!state.transactions?.length,
        hasOrders: (state) => !!state.orders?.length,
        isAuth: (state) => (state.user ? true : false),
        userId: (state) => state.ids?.user || null,
        accountId: (state) => state.ids?.account || null,
        getBalance:
            (state) =>
            (type = "total") => {
                if (type === "total") return state.user?.account.balance || 0;
                if (type === "USDT") {
                    return (
                        state.balances
                            ?.filter((b) => b.asset?.symbol.startsWith("USDT"))
                            .reduce((sum, b) => sum + b.amount, 0) || 0
                    );
                } else {
                    return (
                        state.balances?.find(
                            (b) =>
                                b.asset?.name === type ||
                                b.asset?.symbol === type
                        )?.amount || 0
                    );
                }
            },
        infoId: (state) => state.ids?.info || null,
        getUnreadCount: (state) => state.unreadCount || 0,
        getDefaultCoinCurrency: (state) => {
            return (
                localStorage.getItem("default_coin_currency") ||
                state.defaultCoinCurrency
            );
        },
    },

    actions: {
        async fetchUser() {
            try {
                const url = route("user.index");
                const response = await axios.get(url);
                this.user = response.data.resource;
                this.account = response.data.resource.account;
                this.ids = response.data.ids;
                this.unreadCount = response.data.unreadNotificationsCount;

                this.balances = Object.values(
                    response.data.resource.account.balances
                );
            } catch (error) {
                console.error("Error while fetching user", error);
                this.user = null;
                this.account = null;
                this.ids = null;
                this.unreadCount = 0;
                this.balances = [];
            }
        },

        async fetchBrowserSessions() {
            showLoadingToast({
                message: "Sessions...",
                duration: 500,
            });
            try {
                const url = route("session.index");
                const response = await axios.get(url);
                this.sessions = response.data ?? [];
            } catch (error) {
                console.error("Error while fetching sessions", error);
                this.sessions = [];
            }
        },

        async fetchTransactions() {
            try {
                const url = route(this.routePrefix + "transaction.index");
                const response = await axios.get(url);
                this.transactions = response.data;
            } catch (error) {
                console.error("Error while fetching transactions", error);
                this.transactions = [];
            }
        },

        async fetchOrders() {
            try {
                const url = route(this.routePrefix + "order.index");
                const response = await axios.get(url);
                this.orders = response.data;
            } catch (error) {
                console.error("Error while fetching orders", error);
                this.orders = []; // Éviter un état incohérent
            }
        },

        async fetchNotifications() {
            this.loading = true;
            this.notifications = [];
            if (this.routePrefix === "app.") {
                showLoadingToast({
                    message: "Notifications...",
                    duration: 500,
                });
            }
            try {
                const url = route("notification.index");
                const response = await axios.get(url);
                const data = response.data;
                if (data.unread?.length) {
                    this.unreadCount = data.unread.length || 0;
                    Object.values(data.unread).forEach((value) => {
                        this.notifications.push(formatNotification(value));
                    });
                }
                if (data.readed?.length) {
                    Object.values(data.readed).forEach((value) => {
                        this.notifications.push(formatNotification(value));
                    });
                }
            } catch (error) {
                console.error("Error while fetching notifications", error);
                this.notifications = []; // Éviter un état incohérent
            } finally {
                this.loading = false;
            }
        },

        async readNotifications() {
            if (this.unreadCount === 0) return;
            const url = route("notifications.read");
            axios.patch(url).then(() => {
                this.fetchNotifications();
                this.unreadCount = 0;

                showSuccessToast(
                    "All notifications have been marked as readed"
                );
            });
        },

        async readNotification(id) {
            if (this.unreadCount === 0) return;
            const url = route("notification.update", id);
            axios.put(url).then(() => {
                this.unreadCount--;
                this.fetchNotifications();

                showSuccessToast("Readed");
            });
        },

        async deleteNotifications() {
            if (!this.hasNotifications) return;
            const url = route("notifications.delete");
            axios.delete(url).then(() => {
                this.fetchNotifications();
                this.unreadCount = 0;

                showSuccessToast("All notifications have been deleted");
            });
        },

        async setMailNotif() {
            const url = route("user.mail-notif");
            axios.put(url).then(() => {
                this.fetchUser();
            });
        },

        showMustCompleteInfo() {
            this.mustCompleteInfo = true;
        },

        setDefaultCoinCurrency(value) {
            localStorage.setItem("default_coin_currency", value);
            this.defaultCoinCurrency = value;
        },
    },
});

// Gestion du HMR (Hot Module Replacement)
if (import.meta.hot) {
    import.meta.hot.accept(acceptHMRUpdate(useUserStore, import.meta.hot));
}
