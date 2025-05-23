<script setup>
import { Link, router, usePage } from "@inertiajs/vue3";
import NotifIconBadge from "./NotifIconBadge.vue";

defineProps({
    back: String,
    home: {
        type: Boolean,
        default: false,
    },
    name: String,
});

const urlPrev = usePage().props.urlPrev;

const goBack = () => {
    if (urlPrev !== "empty") {
        router.visit(urlPrev);
    }
};
</script>

<template>
    <div
        class="header fixed-top bg-surface d-flex justify-content-center align-items-center"
    >
        <Link v-if="back" :href="back" class="left back-btn"
            ><i class="icon-left-btn"></i>
        </Link>
        <Link v-else href="#back" @click="goBack" class="left back-btn"
            ><i class="icon-left-btn"></i>
        </Link>

        <h3 v-show="name" class="text-capitalize" v-html="name"></h3>

        <div class="right">
            <NotifIconBadge />
            <Link v-show="home" :href="route($routePrefix + 'home')"
                ><i class="icon-home2 fs-20"></i>
            </Link>
        </div>
    </div>
</template>
