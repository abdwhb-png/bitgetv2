import "./bootstrap";
import "css/admin.css";

import { base } from "./base";
import { definePreset } from "@primevue/themes";
import { adminPreset } from "@/utils/primePreset";
import ConfirmationService from "primevue/confirmationservice";
import ToastError from "@/Components/Admin/Status/ToastError.vue";
import ShowKeyValue from "@/Components/Admin/Common/ShowKeyValue.vue";
import CopyBtn from "@/Components/Admin/Common/CopyBtn.vue";
import { Button } from "@/Components/ui/button";

const {
    createApp,
    h,
    ref,
    createInertiaApp,
    resolvePageComponent,
    ZiggyVue,
    pinia,
    PrimeVue,
    ToastService,
    Aura,
    Vant,
    appName,
    AdminLayout,
} = base();

createInertiaApp({
    title: (title) => `${title} - ${appName} | ADMIN`,

    progress: {
        color: "#10b981",
        showSpinner: true,
    },

    resolve: (name) => {
        if (name.startsWith("Auth/")) {
            return resolvePageComponent(
                `./Pages/${name}.vue`,
                import.meta.glob("./Pages/**/*.vue")
            );
        } else {
            return resolvePageComponent(
                `./Pages/Admin/${name}.vue`,
                import.meta.glob("./Pages/Admin/**/*.vue")
            );
        }
    },

    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });

        app.component("AdminLayout", AdminLayout);
        app.component("ToastError", ToastError);
        app.component("UiButton", Button);
        app.component("ShowKeyValue", ShowKeyValue);
        app.component("CopyBtn", CopyBtn);

        const routePrefix = "admin.";
        app.config.globalProperties.$routePrefix = routePrefix;
        localStorage.setItem("route_prefix", routePrefix);

        const MyPreset = definePreset(Aura, adminPreset());

        return app
            .use(plugin)
            .use(ZiggyVue)
            .use(pinia)
            .use(PrimeVue, {
                // Default theme configuration
                theme: {
                    preset: MyPreset,
                    options: {
                        darkModeSelector: ".system-dark",
                        cssLayer: false,
                    },
                },
            })
            .use(ToastService)
            .use(ConfirmationService)
            .mount(el);
    },
});
