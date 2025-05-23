/**
 * Récupère la dernière date de modification d'un fichier.
 * @param {string} src - Chemin vers le fichier dont on veut connaître la date de modification.
 * @returns {Promise<string>} - Une promesse qui résout la date de modification ou une chaîne vide en cas d'erreur.
 */
async function getLastModifiedDate(src) {
    try {
        const response = await fetch(src, { method: "HEAD" });

        if (!response.ok) {
            return ""; // Retourne une chaîne vide si la réponse n'est pas OK
        }

        // Récupérer la date de modification
        const lastModified = response.headers.get("Last-Modified");

        return lastModified ? lastModified : ""; // Retourne la date ou une chaîne vide si elle n'existe pas
    } catch (error) {
        console.error(
            "Erreur lors de la récupération de la date de modification:",
            error
        );
        return ""; // Retourne une chaîne vide en cas d'erreur
    }
}

export function loadScriptDynamically(src) {
    return new Promise(async (resolve, reject) => {
        // Récupérer la date de modification
        const lastModifiedDate = await getLastModifiedDate(src);

        // Ajouter un paramètre de version si la date de modification est récupérée
        const versionedSrc = lastModifiedDate
            ? `${src}?v=${new Date(lastModifiedDate).getTime()}`
            : src;

        const script = document.createElement("script");
        script.src = versionedSrc;

        if (src.includes("admin.js")) {
            script.type = "module";
        }

        if (src.includes("jquery") || src.includes("admin.js")) {
            script.async = false;
        } else {
            script.async = true;
        }

        script.onload = () => resolve(src);
        script.onerror = () =>
            reject(new Error(`Errror while loading the script ${src}`));
        document.body.appendChild(script);
    });
}

export async function loadAdminScripts() {
    try {
        const scripts = [
            "/admin/assets/js/core/popper.min.js",
            "/admin/assets/js/core/bootstrap.min.js",
            "/admin/assets/js/plugins/perfect-scrollbar.min.js",
            "/admin/assets/js/plugins/smooth-scrollbar.min.js",
            "/admin/assets/js/soft-ui-dashboard.min.js",
            "/js/jquery-3.7.1.min.js",
            "/js/admin.js",
            "https://buttons.github.io/buttons.js",
        ];
        const loadedScripts = await Promise.all(
            scripts.map(loadScriptDynamically)
        );
        console.log(
            "All scripts have been loaded successfully :",
            loadedScripts
        );
    } catch (error) {
        console.error("Error while loading scripts :", error);
    }
}
