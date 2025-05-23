<template>
    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
        <NavLink
            v-for="(item, index) in navLinks"
            :key="index"
            :href="item.url || route(item.route)"
            :active="route().current(item.route)"
        >
            <OverlayBadge
                v-if="item.name === 'notifications'"
                :value="unreadCount"
                severity="danger"
            >
                <i class="pi pi-bell" />
            </OverlayBadge>
            <div v-else class="flex items-center">
                <i :class="item.icon" class="mr-2"></i>
                <span class="text-capitalize">{{ item.title }}</span>
            </div>
        </NavLink>
    </div>
</template>

<script setup>
import { inject } from "vue";
import NavLink from "@/Components/Admin/NavLink.vue";

defineProps({
    navLinks: {
        type: Array,
        required: true,
    },
    unreadCount: {
        type: Number,
        default: 0,
    },
});
</script>

<style scoped>
.flex {
    display: flex;
    align-items: center;
}

.mr-2 {
    margin-right: 0.5rem;
}
</style>
