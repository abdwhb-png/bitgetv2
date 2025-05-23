<script setup>
import { usePage, Link } from "@inertiajs/vue3";

defineProps({
    navs: Array,
});

const page = usePage();

const routePrefix = page.props.routePrefix;

const reload = () => {
    window.location.reload();
};
</script>
<template>
    <div class="menubar-footer footer-fixed">
        <div class="absolute left-[48%] bottom-[64px] z-40">
            <Link
                :href="route($routePrefix + 'trade')"
                class="bg-primary text-2xl p-3.5 rounded-full"
            >
                <i class="pi pi-chart-bar fs-4"></i>
                <div class="absolute -bottom-2 -left-5 -right-5 -z-10">
                    <img src="/app_assets/images/reload-bg.png" alt="" />
                </div>
            </Link>
        </div>

        <ul class="inner-bar">
            <li
                v-for="(item, index) in navs"
                :key="index"
                :class="
                    route().current(routePrefix + item.name) ? 'active' : ''
                "
            >
                <a
                    v-if="item.name === 'markets'"
                    href="javascript:void(0);"
                    data-bs-toggle="modal"
                    data-bs-target="#coinsSearch"
                >
                    <i :class="'icon ' + item.icon"></i>
                    <span class="text-capitalize">{{ item.name }}</span>
                </a>

                <Link v-else :href="route(routePrefix + item.name)">
                    <i :class="'icon ' + item.icon"></i>
                    <span class="text-capitalize">{{ item.name }}</span>
                </Link>
            </li>
        </ul>
    </div>
</template>
<style scoped>
.z-40 {
    z-index: 40;
}
.left-\[48\%\] {
    left: 48%;
}
.bottom-\[64px\] {
    bottom: 58px;
}
.absolute {
    position: absolute;
}

.text-2xl {
    font-size: 1.5rem;
    line-height: 2rem;
}
.p-3\.5 {
    padding: 0.75rem;
    padding-left: 0.9rem;
    padding-right: 0.9rem;
}
.rounded-full {
    border-radius: 9999px;
}

.-z-10 {
    z-index: -10;
}
.-right-5 {
    right: -1.9rem;
}
.-left-5 {
    left: -2rem;
}
.-bottom-2 {
    bottom: -1rem;
}
@media screen and (max-width: 512px) {
    .left-\[48\%\] {
        left: 45%;
    }
    .bottom-\[64px\] {
        bottom: 62px;
    }
    .text-2xl {
        font-size: 1.2rem;
        line-height: 1.5rem;
    }
    .p-3\.5 {
        /* padding: 0.75rem; */
        padding-left: 0.75rem;
        padding-right: 0.75rem;
    }

    .-right-5 {
        right: -1.4rem;
    }
    .-left-5 {
        left: -1.5rem;
    }
    .-bottom-2 {
        bottom: -0.8rem;
    }
}
</style>
