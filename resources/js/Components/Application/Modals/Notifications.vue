<script setup>
import { computed, ref } from "vue";
import { useUserStore } from "@/stores/user";
import Button from "primevue/button";

const userStore = useUserStore();

const [pageSize, step] = [ref(10), 10];

const notifications = computed(() => {
    return userStore.notifications.slice(0, pageSize.value);
});
</script>

<template>
    <div class="modal fade modalRight" id="notifications">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div
                    class="header fixed-top bg-surface d-flex justify-content-center align-items-center"
                >
                    <span
                        class="left"
                        data-bs-dismiss="modal"
                        aria-hidden="true"
                        ><i class="icon-left-btn"></i
                    ></span>
                    <h3>Notifications</h3>
                    <a
                        v-show="userStore.unreadCount"
                        href="#readAll"
                        @click="userStore.readNotifications()"
                        class="right"
                        :class="userStore.unreadCount > 0 ? 'text-primary' : ''"
                        ><i class="pi pi-eye me-1"></i
                        ><span class="d-none d-md-block"
                            >Mark As Readed</span
                        ></a
                    >
                </div>
                <div class="overflow-auto pt-45 pb-16">
                    <div class="tf-container">
                        <h3
                            class="text-muted text-center mt-12"
                            v-if="
                                !userStore.hasNotifications &&
                                !userStore.loading
                            "
                        >
                            You do not have any notifications
                        </h3>
                        <ul class="mt-12">
                            <li
                                v-for="(item, index) in notifications"
                                :key="index"
                                class="mb-3"
                            >
                                <a
                                    href="#"
                                    @click="userStore.readNotification(item.id)"
                                    class="noti-item bg-menuDark"
                                >
                                    <div
                                        class="line-bt"
                                        :class="
                                            item.status == 'readed'
                                                ? 'text-muted'
                                                : ''
                                        "
                                    >
                                        <div class="pb-8 d-flex">
                                            <i
                                                v-show="item.status == 'unread'"
                                                class="dot-lg bg-primary mx-2"
                                            ></i>
                                            <div class="notif-content">
                                                <p
                                                    class="text-button fw-6 pb-1"
                                                    v-html="item.title"
                                                ></p>
                                                <p
                                                    class="text-extrasmall"
                                                    v-html="item.body"
                                                ></p>
                                            </div>
                                        </div>
                                    </div>
                                    <span
                                        class="d-block mt-8"
                                        :class="
                                            item.status == 'readed'
                                                ? 'text-muted'
                                                : ''
                                        "
                                        >{{
                                            item.created || item.created_at
                                        }}</span
                                    >
                                </a>
                            </li>
                            <li class="">
                                <!-- Bouton Load More -->
                                <Button
                                    @click="pageSize = pageSize + step"
                                    v-show="
                                        userStore.notifications.length >
                                        pageSize
                                    "
                                    label="Show More"
                                    unstyled
                                    class="btn btn-secondary"
                                />
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
