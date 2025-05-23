import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";
import { fileURLToPath, URL } from "node:url";
import { nodePolyfills } from "vite-plugin-node-polyfills";
import Components from "unplugin-vue-components/vite";
import { PrimeVueResolver } from "@primevue/auto-import-resolver";

const publicDir = fileURLToPath(new URL("public_html", import.meta.url));

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/admin.css",
                "resources/css/app.css",
                "resources/js/app.js",
                "resources/js/admin.js",
            ],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        // nodePolyfills(),
        Components({ resolvers: [PrimeVueResolver()] }),
    ],
    resolve: {
        alias: {
            css: "/resources/css",
            assets: "/resources/assets",
            appAssets: publicDir + "/app_assets",
        },
    },
});
