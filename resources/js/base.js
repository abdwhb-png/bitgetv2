import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { ZiggyVue } from "../../vendor/tightenco/ziggy";
import { createPinia } from "pinia";
import PrimeVue from "primevue/config";
import ToastService from "primevue/toastservice";
import Aura from "@primevue/themes/aura";
import Vant from "vant";
import AppLayout from "@/Layouts/AppLayout.vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";

export function base() {
    const appName = import.meta.env.VITE_APP_NAME || "ByBit";

    const pinia = createPinia();

    return {
        createApp,
        h,
        createInertiaApp,
        resolvePageComponent,
        ZiggyVue,
        pinia,
        PrimeVue,
        ToastService,
        Aura,
        Vant,
        appName,
        AppLayout,
        AdminLayout,
    };
}
