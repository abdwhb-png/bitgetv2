import { router } from "@inertiajs/vue3";
import { showLoadingToast } from "vant";

export async function logout(reload = false) {
    router.post(
        route("logout"),
        {},
        {
            onStart: () => {
                showLoadingToast({
                    message: "Logging out...",
                    forbidClick: true,
                });
            },
            onFinish: () => {
                if (reload) {
                    window.location.reload();
                }
            },
        }
    );
}

export async function goTo(routeName, reload = true) {
    router.visit(route(routeName), {
        onStart: () => {
            showLoadingToast({
                forbidClick: true,
            });
        },
        onFinish: () => {
            if (reload) {
                window.location.reload();
            }
        },
    });
}
