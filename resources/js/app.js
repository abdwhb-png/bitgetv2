import "./bootstrap";
import "css/styles.css";
import "css/vant.css";
import "@vant/touch-emulator";

import { base } from "./base";
import { Lazyload } from "vant";
import { initBinanceStore } from "@/services/binance";
import { toggleAppCss } from "@/utils/cssLoader";
import { capitalizeFirstLetter } from "@/utils/helpers";

import Modals from "@/Components/Application/Modals.vue";

const {
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
} = base();

// Define an array of component names that require `app.css`
const inertiaComponents = [
    "Auth/ConfirmPassword",
    "Auth/ForgotPassword",
    "Auth/ResetPassword",
    "Auth/TwoFactorChallenge",
    "Auth/VerifyEmail",
];

createInertiaApp({
    title: (title) =>
        `${capitalizeFirstLetter(title)} - ${appName} | APPLICATION`,

    progress: {
        // The color of the progress bar...
        color: "#29d",

        // Whether the NProgress spinner will be shown...
        showSpinner: true,
    },

    resolve: (name) => {
        // Dynamically resolve the page and set the theme
        const resolvedPage = name.startsWith("Auth/")
            ? resolvePageComponent(
                  `./Pages/${name}.vue`,
                  import.meta.glob("./Pages/**/*.vue")
              )
            : resolvePageComponent(
                  `./Pages/Application/${name}.vue`,
                  import.meta.glob("./Pages/Application/**/*.vue")
              );

        resolvedPage.then((page) => {
            if (inertiaComponents.includes(name)) {
                const appCssUrl = "/app.css";
                toggleAppCss(true, appCssUrl);
            } else {
                toggleAppCss(false);
            }
        });

        return resolvedPage;
    },

    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });

        app.component("AppLayout", AppLayout);
        app.component("Modals", Modals);

        // create custom global app properties
        const routePrefix = "";
        app.config.globalProperties.$routePrefix = routePrefix;
        localStorage.setItem("route_prefix", routePrefix);

        return app
            .use(plugin)
            .use(ZiggyVue)
            .use(pinia)
            .use(PrimeVue, {
                theme: {
                    preset: Aura,
                    options: {
                        darkModeSelector: "system",
                        // cssLayer: false,
                    },
                },
            })
            .use(ToastService)
            .use(Vant)
            .use(Lazyload, {
                lazyComponent: true,
            })
            .mount(el);
    },
});

initBinanceStore(pinia);
