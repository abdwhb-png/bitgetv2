<style scoped>
.admin-layout {
    min-height: 100vh;
    background-color: #f3f4f6;
}

.admin-layout.dark {
    background-color: #000;
}

.sticky-nav {
    position: sticky;
    top: 0;
    z-index: 999;
}

.nav-container {
    --tw-bg-opacity: 1;
    background-color: rgba(255, 255, 255, var(--tw-bg-opacity));
    border-bottom: 1px solid rgba(229, 231, 235, var(--tw-border-opacity));
}

.dark .nav-container {
    --tw-bg-opacity: 1;
    background-color: rgba(38, 38, 38, var(--tw-bg-opacity));
    --tw-border-opacity: 1;
    border-color: rgba(75, 85, 99, var(--tw-border-opacity));
}
</style>

<script setup>
import { ref, onBeforeMount, computed } from "vue";
import { Head, Link, usePage } from "@inertiajs/vue3";
import { useUserStore } from "@/stores/user";
import { useAdminStore } from "@/stores/admin";
import { usePusher } from "@/composables/usePusher";
import { useToast } from "primevue/usetoast";

// Components
import ApplicationMark from "@/Components/Admin/ApplicationMark.vue";
import Banner from "@/Components/Admin/Banner.vue";
import SpeedDial from "@/Components/Admin/SpeedDial.vue";
import ResponsiveNavLink from "@/Components/Admin/ResponsiveNavLink.vue";
import AdminNavigation from "@/Components/Admin/Layout/Navigation.vue";
import AdminResponsiveNavigation from "@/Components/Admin/Layout/ResponsiveNavigation.vue";
import AdminHeader from "@/Components/Admin/Layout/Header.vue";
import AdminUserDropdown from "@/Components/Admin/Layout/UserDropdown.vue";

const props = defineProps({
    title: {
        type: String,
        default: "Admin",
    },
});

// Page and store setup
const page = usePage();
const userStore = useUserStore();
const adminStore = useAdminStore();
const toast = useToast();

// Reactive state
const showingNavigationDropdown = ref(false);

// Computed properties for user info
const user = computed(() => page.props.auth.user);
const jetstream = computed(() => page.props.jetstream);

// Navigation links configuration
const navLinks = [
    {
        title: "Dashboard",
        icon: "pi pi-th-large",
        name: "dashboard",
        route: page.props.routePrefix + "dashboard",
    },
    {
        name: "users.index",
        title: "Users",
        icon: "pi pi-users",
        route: page.props.routePrefix + "users.index",
    },
    {
        name: "transactions.index",
        title: "Transactions",
        icon: "pi pi-arrow-right-arrow-left",
        route: page.props.routePrefix + "transactions.index",
    },
    {
        name: "orders.index",
        title: "Orders",
        icon: "pi pi-chart-bar",
        route: page.props.routePrefix + "orders.index",
    },
    {
        name: "verifications.index",
        title: "Verifications",
        icon: "pi pi-verified",
        route: page.props.routePrefix + "verifications.index",
    },
    {
        name: "notifications",
        title: "Notifications",
        icon: "pi pi-bell",
        route: page.props.routePrefix + "notifications",
    },
];

// Setup Pusher subscriptions
const {
    subscribeToUser,
    subscribeToAllTransactions,
    subscribeToAllOrders,
    subscribeToAllUsers,
} = usePusher(page.props.siteConfig.pusher);

// Initialize data
onBeforeMount(async () => {
    await userStore.fetchUser().then(() => {
        subscribeToUser(userStore);
    });

    subscribeToAllUsers(adminStore, toast);
    subscribeToAllTransactions(adminStore, toast);
    subscribeToAllOrders(adminStore, toast);
});
</script>

<template>
    <div>
        <Head :title="title" />

        <Banner />

        <ConfirmDialog></ConfirmDialog>
        <Toast />
        <SpeedDial />
        <ScrollTop />

        <div class="min-h-screen bg-gray-100 dark:bg-black">
            <nav class="sticky-nav nav-container">
                <!-- Primary Navigation Menu -->
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center">
                                <Link :href="route($routePrefix + 'dashboard')">
                                    <ApplicationMark class="block h-9 w-auto" />
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <AdminNavigation
                                :navLinks="navLinks"
                                :unreadCount="userStore.unreadCount"
                            />
                        </div>

                        <!-- Right side menu -->
                        <div class="hidden sm:flex sm:items-center sm:ms-6">
                            <!-- User dropdown -->
                            <div class="ms-3 relative">
                                <AdminUserDropdown
                                    :userName="user.full_name"
                                    :hasApiFeatures="jetstream.hasApiFeatures"
                                />
                            </div>
                        </div>

                        <!-- Hamburger Menu Button -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button
                                class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out"
                                @click="
                                    showingNavigationDropdown =
                                        !showingNavigationDropdown
                                "
                            >
                                <svg
                                    class="h-6 w-6"
                                    stroke="currentColor"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex':
                                                !showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex':
                                                showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <AdminResponsiveNavigation
                    :navLinks="navLinks"
                    :showingNavigationDropdown="showingNavigationDropdown"
                    :unreadCount="userStore.unreadCount"
                    :managesProfilePhotos="jetstream.managesProfilePhotos"
                    :profilePhotoUrl="user.profile_photo_url"
                    :userName="user.full_name"
                    :userEmail="user.email"
                >
                    <template #responsive-nav-links>
                        <ResponsiveNavLink
                            :href="route('profile.show')"
                            :active="route().current('profile.show')"
                        >
                            Profile
                        </ResponsiveNavLink>

                        <ResponsiveNavLink
                            v-if="jetstream.hasApiFeatures"
                            :href="route('api-tokens.index')"
                            :active="route().current('api-tokens.index')"
                        >
                            API Tokens
                        </ResponsiveNavLink>

                        <!-- Authentication -->
                        <form method="POST" @submit.prevent="logout">
                            <ResponsiveNavLink as="button">
                                Log Out
                            </ResponsiveNavLink>
                        </form>
                    </template>
                </AdminResponsiveNavigation>
            </nav>

            <!-- Page Header -->
            <slot name="header">
                <AdminHeader :title="title" />
            </slot>

            <!-- Page Content -->
            <main class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <slot />
                </div>
            </main>
        </div>
    </div>
</template>
