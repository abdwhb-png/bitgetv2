<style scoped>
.box-noti::after {
    content: var(--notification-count, "0");
    background-color: var(--red, #f00);
}
</style>

<script setup>
import { useUserStore } from "@/stores/user";

const userStore = useUserStore();
</script>

<template>
    <a
        href="#notifications"
        @click="userStore.fetchNotifications()"
        class="icon-noti box-noti"
        data-bs-toggle="modal"
        :style="{
            '--notification-count': `'${userStore.getUnreadCount}'`,
        }"
    ></a>
</template>
