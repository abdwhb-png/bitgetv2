<style scoped>
.box-noti::after {
    content: var(--notification-count, "0");
    background-color: var(--red, #f00);
}
</style>

<script setup>
import { Link } from "@inertiajs/vue3";
import NotifIconBadge from "./NotifIconBadge.vue";

const props = defineProps({
    user: Object,
});
</script>

<template>
    <section>
        <div class="header-style2 fixed-top bg-menuDark">
            <div class="d-flex justify-content-between align-items-center">
                <Link
                    class="box-account"
                    :href="route($routePrefix + 'my.index')"
                >
                    <img
                        :src="$page.props.auth.user.profile_photo_url"
                        alt="img"
                        class="avt"
                    />
                    <div class="info">
                        <p class="text-xsmall text-secondary">Welcome back!</p>
                        <h5 class="mt-4">{{ user.full_name }}</h5>
                    </div>
                </Link>
                <div class="d-flex align-items-center gap-8">
                    <a
                        href="#coinsSearch"
                        class="icon-search"
                        data-bs-toggle="modal"
                    ></a>
                    <NotifIconBadge />
                </div>
            </div>
        </div>
    </section>
</template>
