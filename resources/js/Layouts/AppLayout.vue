<template>
    <div class="system">
        <Toast />
        <!-- headerNav -->
        <slot name="header">
            <Header v-if="hasHeader()" :user="$page.props.auth.user" />
            <SimpleHeader v-else :back="headerBack" :name="headerTitle" />
        </slot>
        <!-- /headerNav -->

        <!-- menuBar -->
        <BottomNavBar :navs="bottomNavBarLinks" />
        <!-- /menuBar -->

        <van-config-provider theme="dark">
            <!-- pageContent -->
            <slot :user="$page.props.auth.user" />
            <!-- /pageContent -->

            <!-- must complete info -->
            <van-popup
                :show="
                    !startCompleting &&
                    (userStore.mustCompleteInfo ||
                        !page.props.auth.user.isInfoCompleted)
                "
                position="bottom"
                round
                :style="{ height: '30%', padding: '40px' }"
                :close-on-click-overlay="false"
            >
                <h4 class="text-center text-primary my-4 mt-3">
                    You must complete your profile information to continue
                </h4>

                <button
                    href="#personalInformation"
                    data-bs-toggle="modal"
                    class="primary-btn"
                    @click="startCompleting = true"
                >
                    <i class="icon icon-user"></i>
                    Complete
                </button>
            </van-popup>
        </van-config-provider>

        <!-- modals -->
        <Modals />
        <!-- /modals -->
    </div>
</template>

<script>
import { usePusher } from "@/composables/usePusher";
import { usePage } from "@inertiajs/vue3";
import { useUserStore } from "@/stores/user";
import { useCoinsStore } from "@/stores/coins";
import { showNotify } from "vant";
import Header from "@/Components/Application/Header.vue";
import SimpleHeader from "@/Components/Application/SimpleHeader.vue";
import BottomNavBar from "@/Components/Application/BottomNavBar.vue";
import Toast from "primevue/toast";

export default {
    components: {
        Header,
        SimpleHeader,
        BottomNavBar,
        Toast,
    },

    props: {
        headerTitle: String,
        headerBack: String,
    },

    setup() {
        const page = usePage();
        const {
            pusher,
            subscribeToUser,
            subscribeToUserInfo,
            subscribeToUserAccount,
            subscribeToTransaction,
            subscribeToOrder,
        } = usePusher(page.props.siteConfig.pusher);

        const bottomNavBarLinks = [
            {
                name: "home",
                icon: "icon-home2",
            },
            {
                name: "markets",
                icon: "icon-exchange",
            },
            {
                name: "news",
                icon: "icon-earn",
            },
            {
                name: "asset",
                icon: "icon-wallet",
            },
        ];

        return {
            pusher,
            subscribeToUser,
            subscribeToUserInfo,
            subscribeToUserAccount,
            subscribeToTransaction,
            subscribeToOrder,
            page,
            userStore: useUserStore(),
            coinsStore: useCoinsStore(),
            bottomNavBarLinks,
        };
    },

    data() {
        return {
            startCompleting: false,
            interval: null,
            orderInterval: null,
            awaitingOrders: [],
        };
    },

    watch: {
        awaitingOrders() {
            this.awaitingOrders.forEach(async (order) => {
                await this.processOrder(order);
            });
        },
    },

    // on component created
    created() {},

    async beforeMount() {
        await this.userStore.fetchUser().then(() => {
            this.subscribeToUser(this.userStore);
            this.subscribeToUserInfo(this.userStore);
            this.subscribeToUserAccount(this.userStore);
            this.subscribeToTransaction(this.userStore);
            this.subscribeToOrder(this.userStore, showNotify);
        });

        await this.userStore.fetchOrders().then(() => {
            this.orderInterval = setInterval(() => {
                if (this.userStore.hasOrders) {
                    this.awaitingOrders = this.userStore.orders.filter(
                        (order) => {
                            let validity = Date.now() - order.open_time;
                            return (
                                order.closed_at == null &&
                                validity >= order.expiration
                            );
                        }
                    );
                }
            }, 30000);
        });
    },

    // on component mounted
    mounted() {
        this.pusher.connection.bind("error", (err) => {
            if (err.data.code === 4004) {
                log("Over limit!");
                this.interval = setInterval(() => {
                    this.userStore.fetchUser().then(() => {
                        if (this.userStore.user.info.isInfoCompleted) {
                            this.userStore.showMustCompleteInfo();
                        }
                    });
                });
            }
        });

        if (window.Tawk_API) {
            window.Tawk_API.visitor = {
                name: this.page.props.auth.user.full_name,
                email: this.page.props.auth.user.email,
            };
        }
    },

    unmounted() {
        this.hideAllModals();
        clearInterval(this.interval);
        clearInterval(this.orderInterval);
    },

    methods: {
        // cheeck if page need header
        hasHeader() {
            return this.bottomNavBarLinks.some((nav) => {
                return route().current(this.$routePrefix + nav.name);
            });
        },

        async processOrder(order) {
            let validity = Date.now() - order.open_time;

            if (order.status == "opened" && validity >= order.expiration) {
                const price = await this.coinsStore.getPrice(order.symbol);

                try {
                    const closeOrderUrl = route(
                        `${this.$routePrefix}order.close`,
                        order.ref_id
                    );

                    const res = await axios.post(closeOrderUrl, {
                        price: price || null,
                    });

                    if (res.status == 202) {
                        showNotify({
                            message:
                                "Order expiry settlement completed. Check your orders history.",
                            type: "success",
                            duration: 5000,
                        });

                        this.awaitingOrders = this.userStore.orders.filter(
                            (o) => o.ref_id != order.ref_id
                        );
                    }
                } catch (error) {
                    console.error("Failed to close order:", error);
                }
            }
        },

        hideAllModals() {
            const modals = document.querySelectorAll(".modal.show");
            modals.forEach((modal) => {
                const instance = bootstrap.Modal.getInstance(modal);
                const bsModal = new bootstrap.Modal(
                    document.getElementById(modal.id)
                ); // Get the Bootstrap modal instance

                if (bsModal) {
                    bsModal.hide(); // Hide the modal if it is currently shown
                    // bsModal.dispose();
                }
            });

            const backdrops = document.querySelectorAll(
                ".modal-backdrop.fade.show"
            );
            backdrops.forEach((backdrop) => {
                backdrop.remove();
            });
        },
    },
};
</script>
