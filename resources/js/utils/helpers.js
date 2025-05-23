/**
 * Helpers module.
 *
 * @module utils/helpers
 */

/**
 * Sleeps for the given amount of milliseconds.
 *
 * @param {number} ms - The amount of milliseconds to sleep.
 * @returns {Promise} A promise that resolves after the specified amount of time.
 */
export function sleep(ms) {
    return new Promise((resolve) => setTimeout(resolve, ms));
}

/**
 * Capitalizes the first letter of a string.
 *
 * @param {string} string - The string to capitalize.
 * @returns {string} The capitalized string.
 */
export function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

/**
 * Copies the given text to the clipboard.
 *
 * @param {string} text - The text to copy.
 */
export function copyToClipboard(text) {
    navigator.clipboard.writeText(text);
    alert("copied to clipboard: " + text);
}

/**
 * Formats a number with commas.
 *
 * @param {number} num - The number to format.
 * @returns {string} The formatted number.
 */
export function formatNumber(num) {
    return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,");
}

/**
 * Gets an asset from an account balance object.
 *
 * @param {object|array} balances - The account balance object.
 * @param {string} search - The asset to search for.
 * @param {string} operator - The operatore to use.
 * @returns {object|null} The asset if found, or null.
 */
export function getAccountAsset(balances, search, operator = "==") {
    // Vérification que balances est un objet ou un tableau
    if (
        !balances ||
        (typeof balances !== "object" && !Array.isArray(balances))
    ) {
        return null;
    }

    // Convertir balances en tableau si c'est un objet
    const values = Array.isArray(balances) ? balances : Object.values(balances);

    // Définition de la condition de recherche
    const matchCondition = (item) => {
        if (!item || !item.asset) return false; // Vérification que les champs existent

        const isMatch =
            item.asset.name === search || item.asset.symbol === search;
        return operator === "!=" ? !isMatch : isMatch;
    };

    // Recherche dans les balances
    const asset = values.find(matchCondition);

    return asset || null;
}

/**
 * Formats a notification object.
 *
 * @param {object} value - The notification object.
 * @returns {object} The formatted notification object.
 */
export function formatNotification(value) {
    return {
        id: value.id,
        status: value.read_at === null ? "unread" : "readed",
        title: value.data.title,
        body: value.data.body,
        created: value.created,
        created_at: formatDateTime(value.created_at),
        updated_at: formatDateTime(value.updated_at),
    };
}

/**
 * Formats a countries array for use in a select field.
 *
 * @param {array} data - The countries array.
 * @returns {array} The formatted countries array.
 */
export function formatCountriesForSelect(data) {
    if (!Array.isArray(data)) {
        console.error("Invalid data: Expected an array.");
        return [];
    }

    return data
        .filter((country) => country && typeof country.name === "string") // Filtrer les entrées invalides
        .map((country) => country.name);
}

/**
 * Formats a date string.
 *
 * @param {string} value - The date string.
 * @returns {string} The formatted date string.
 */
export function formatDate(value) {
    return value.replace("T", " ");
}

/**
 * Formats a date-time string.
 *
 * @param {string} value - The date-time string.
 * @returns {string} The formatted date-time string.
 */
export function formatDateTime(value) {
    return value.replace("T", " ").replace(".000000Z", "");
}

/**
 * Pretty-prints a JSON object.
 *
 * @param {object} json - The JSON object to pretty-print.
 * @returns {string} The pretty-printed JSON string.
 */
export function prettyPrintJson(json) {
    return JSON.stringify(json, null, 2)
        .replace(/"([^"]+)":/g, '<span class="json-key">"$1":</span>') // Key
        .replace(/: "(.*?)"/g, ': <span class="json-string">"$1"</span>') // String value
        .replace(/: (\d+)/g, ': <span class="json-value">$1</span>'); // Numeric value
}
