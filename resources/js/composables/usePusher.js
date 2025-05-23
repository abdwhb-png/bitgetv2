import { ref, onUnmounted } from "vue";
import { useToast } from "primevue/usetoast";

const pusherKey = import.meta.env.VITE_PUSHER_APP_KEY;
const pusherCulster = import.meta.env.VITE_PUSHER_APP_CLUSTER;

export function usePusher(config) {
    const pusher = new window.Pusher(pusherKey, {
        cluster: pusherCulster,
        channelAuthorization: {
            endpoint: "/broadcasting/auth",
            headers: {
                Accept: "application/json",
            },
        },
    });

    pusher.connection.bind("error", (err) => {
        console.error("Pusher connection error:", err);
    });

    pusher.connection.bind("disconnected", function () {
        console.log("Disconnected from Pusher.");
    });

    pusher.connection.bind("connected", function () {
        console.log("Connected to Pusher!");
    });

    const toast = useToast();

    const subscribeToUser = (userStore) => {
        if (userStore.userId) {
            const subsNotif = pusher.subscribe(
                config.notification.channel + "." + userStore.userId
            );

            subsNotif.bind(config.notification.event, (payload) => {
                userStore.unreadCount++;
                toast.add({
                    severity: "info",
                    summary: payload.title,
                    detail: payload.body,
                    life: 3000,
                });
                userStore.fetchNotifications();
            });
        } else {
            console.error("Unable to subscribe to User : id not found");
        }
    };

    const subscribeToUserInfo = (userStore) => {
        if (userStore.infoId) {
            const subscriber = pusher.subscribe(
                config.user_info.channel + "." + userStore.infoId
            );
            subscriber.bind(config.user_info.event, (data) => {
                if (!data.isCompleted) {
                    toast.add({
                        severity: "error",
                        summary: "Action required !",
                        detail: "Please complete your profile information.",
                        life: 3000,
                    });
                    userStore.showMustCompleteInfo();
                }
            });
        } else {
            console.error("Unable to subscribe to User Info : id not found");
        }
    };

    const subscribeToUserAccount = (userStore) => {
        if (userStore.accountId) {
            const subscriber = pusher.subscribe(
                config.user_account.channel + "." + userStore.accountId
            );
            subscriber.bind(config.user_account.event, (data) => {
                if (data.state == "updated") {
                    userStore.fetchUser();
                }
            });
        } else {
            console.error("Unable to subscribe to User Account : id not found");
        }
    };

    const subscribeToTransaction = (userStore) => {
        if (userStore.accountId) {
            const subscriber = pusher.subscribe(
                config.transaction.channel + "." + userStore.accountId
            );
            subscriber.bind(config.transaction.event, async (data) => {
                if (data.state == "updated") {
                    await userStore.fetchTransactions();
                    await userStore.fetchUser();
                }
            });
        } else {
            console.error(
                "Unable to subscribe to Transaction : user account id not found"
            );
        }
    };

    const subscribeToOrder = (userStore, showNotify) => {
        if (userStore.accountId) {
            const subscriber = pusher.subscribe(
                config.order.channel + "." + userStore.accountId
            );
            subscriber.bind(config.order.event, async (data) => {
                if (data.state == "closed" && data.order.closed_at !== null) {
                    await userStore.fetchOrders();
                    await userStore.fetchUser();
                }
            });
        } else {
            console.error(
                "Unable to subscribe to Order : user account id not found"
            );
        }
    };

    const subscribeToAllUsers = (adminStore, toast) => {
        const subscriber = pusher.subscribe(config.all_users.channel);
        subscriber.bind(config.all_users.event, async (data) => {
            if (data.state.startsWith("kyc-")) {
                await adminStore.fetchVerifications(toast);
            } else {
                await adminStore.fetchUsers(toast);
            }
        });
    };

    const subscribeToAllTransactions = (adminStore, toast) => {
        const subscriber = pusher.subscribe(config.all_transactions.channel);
        subscriber.bind(config.all_transactions.event, async (data) => {
            await adminStore.fetchTransactions(toast);
        });
    };

    const subscribeToAllOrders = (adminStore, toast) => {
        const subscriber = pusher.subscribe(config.all_orders.channel);
        subscriber.bind(config.all_orders.event, async (data) => {
            await adminStore.fetchOrders(toast);
        });
    };

    // Cleanup on unmount
    onUnmounted(() => {
        if (pusher) pusher.disconnect();
    });

    return {
        pusher,
        subscribeToUser,
        subscribeToUserInfo,
        subscribeToUserAccount,
        subscribeToTransaction,
        subscribeToOrder,
        subscribeToAllUsers,
        subscribeToAllTransactions,
        subscribeToAllOrders,
    };
}
