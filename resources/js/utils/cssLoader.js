export async function toggleAppCss(enable, url = "") {
    const existingLink = document.querySelector("link[data-app-css]");
    if (enable) {
        if (!existingLink) {
            const link = document.createElement("link");
            link.rel = "stylesheet";
            link.href = url;
            link.setAttribute("data-app-css", "true");
            document.head.appendChild(link);
        }
    } else {
        if (existingLink) {
            document.head.removeChild(existingLink);
        }
    }
}
