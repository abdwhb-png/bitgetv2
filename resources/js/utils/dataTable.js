import { FilterMatchMode } from "@primevue/core/api";

export const rowsPerPage = [10, 20, 30, 50, 100];
export const itemsPerPage = rowsPerPage;

export const scrollHeight = "700px";

export const pt = {
    table: { style: "min-width: 50rem" },
    column: {
        bodycell: ({ state }) => ({
            style:
                state["d_editing"] &&
                "padding-top: 0.75rem; padding-bottom: 0.75rem",
        }),
    },
};

export function getFilters(type = "", search = null) {
    switch (type) {
        case "all":
            return {};

        default:
            return {
                global: { value: search, matchMode: FilterMatchMode.CONTAINS },
                status: { value: null, matchMode: FilterMatchMode.EQUALS },
                type: { value: null, matchMode: FilterMatchMode.EQUALS },
            };
    }
}

export function parseStatuses(statuses) {
    return statuses?.map((status) => {
        return {
            value: status,
            label: status,
        };
    });
}

export function getColor(type) {
    switch (type) {
        case "BUY UP":
            return "text-blue-500";

        case "BUY FALL":
            return "text-red-500";

        case "deposit":
            return "text-blue-500";

        case "withdrawal":
            return "text-red-500";

        case "swap":
            return "text-sky-500";

        default:
            return null;
    }
}

export function getSeverity(status) {
    switch (status) {
        case "approved":
            return "success";

        case "verified":
            return "success";

        case "pending":
            return "warn";

        case "rejected":
            return "danger";

        case "unverified":
            return "danger";

        case "not provided":
            return "secondary";

        case "opened":
            return "success";

        case "closed":
            return "warn";

        case "BUY UP":
            return "info";

        case "BUY FALL":
            return "danger";

        case "deposit":
            return "info";

        case "withdrawal":
            return "danger";

        case "completed":
            return "info";

        case "uncompleted":
            return "danger";

        default:
            return null;
    }
}
