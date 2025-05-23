<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import { useUserStore } from "@/stores/user";
import ConfirmPassword from "@/Components/Application/ConfirmPassword.vue";
import { showSuccessToast } from "vant";
import Button from "primevue/button";

const userStore = useUserStore();

const loading = ref(false);

const inputPassword = ref("");

const form = useForm({
    password: inputPassword.value,
});

const logoutOtherBrowserSessions = () => {
    loading.value = true;
    console.log("pwd", form.password, inputPassword);

    form.delete(route("other-browser-sessions.destroy"), {
        preserveScroll: true,
        onSuccess: () => showSuccessToast("Disconnected"),
        onFinish: () => {
            userStore.fetchBrowserSessions();
            loading.value = false;
        },
    });
};
</script>

<template>
    <div class="modal fade modalRight" id="browserSessions">
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
                    <h3>Browser Sessions</h3>
                </div>
                <div class="overflow-auto pt-45 pb-16">
                    <div class="tf-container">
                        <ul class="mt-12">
                            <li class="mb-3">
                                <ConfirmPassword
                                    v-model="inputPassword"
                                    :always-check="true"
                                    @confirmed="logoutOtherBrowserSessions"
                                >
                                    <Button
                                        type="button"
                                        unstyled
                                        :loading="loading"
                                        :disabled="
                                            userStore.sessions.length <= 1
                                        "
                                        class="btn btn-primary text-capitalize"
                                    >
                                        Log out from other devices
                                    </Button>
                                </ConfirmPassword>
                            </li>
                            <li
                                v-for="(session, i) in userStore.sessions"
                                :key="i"
                                class="mb-2"
                            >
                                <div
                                    class="accent-box-v5 bg-menuDark"
                                    :class="
                                        session.is_current_device
                                            ? 'active'
                                            : ''
                                    "
                                >
                                    <div
                                        class="d-flex gap-4 align-items-center"
                                    >
                                        <span
                                            v-if="session.agent.is_desktop"
                                            class="icon-box"
                                        >
                                            <svg
                                                class="w-8 h-8 text-gray-500"
                                                xmlns="http://www.w3.org/2000/svg"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke-width="1.5"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25"
                                                />
                                            </svg>
                                        </span>
                                        <span v-else class="icon-box">
                                            <svg
                                                class="w-8 h-8 text-gray-500"
                                                xmlns="http://www.w3.org/2000/svg"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke-width="1.5"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3"
                                                />
                                            </svg>
                                        </span>

                                        <div class="">
                                            <a href="#" class="text-small">
                                                {{
                                                    session.agent.platform
                                                        ? session.agent.platform
                                                        : "Unknown"
                                                }}
                                                -
                                                {{
                                                    session.agent.browser
                                                        ? session.agent.browser
                                                        : "Unknown"
                                                }}
                                            </a>
                                            <p class="mt-4">
                                                {{ session.ip_address }}
                                                <span
                                                    v-if="
                                                        session.is_current_device
                                                    "
                                                    class="text-success font-semibold"
                                                    >This device</span
                                                >
                                                <span v-else
                                                    >Last active
                                                    {{
                                                        session.last_active
                                                    }}</span
                                                >
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
