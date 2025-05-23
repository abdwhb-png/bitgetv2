<template>
    <Card>
        <template #header>
            <div class="flex justify-between p-3">
                <SearchFilter
                    v-if="showSearch"
                    class="mr-4 w-full max-w-md"
                    v-model="form.search"
                    :loading="loading"
                    :filter-key="filterKey"
                    :data-filters="dataFilters"
                    @reset="reset"
                >
                    <template #filterContent>
                        <DropdownMenuRadioGroup v-model="form[filterKey]">
                            <slot name="filterContent" />
                        </DropdownMenuRadioGroup>
                    </template>
                </SearchFilter>
            </div>
        </template>

        <template #title>
            {{ title }}
            <span class="text-sm font-normal">{{
                `${paginated?.total || "0"} records displayed ${
                    total ? `of ${total}` : ""
                }`
            }}</span>
        </template>

        <template #content>
            <DataTable
                class="w-full"
                v-bind="$attrs"
                ref="dt"
                :value="data"
                v-model:filters="filters"
                scrollable
                :scrollHeight="scrollHeight"
                showGridlines
                @sort="onSort"
                @update:filters="onFilterUpdate"
            >
                <template #empty>
                    <slot name="empty">
                        <div
                            class="bg-secondary-light flex grow items-center gap-4 rounded-xl border border-dashed border-primary p-4"
                        >
                            <Info />
                            <div class="flex flex-col gap-0.5">
                                <p class="text text-2sm font-normal">
                                    No records found
                                </p>
                            </div>
                        </div>
                    </slot>
                </template>

                <template #loading>
                    <slot name="loading">
                        <Loader2 class="animate-spin" />
                    </slot>
                </template>

                <!-- ID -->
                <Column v-if="showId" field="id" header="ID" sortable>
                    <template #body="{ data, field }">
                        {{ `#${data[field]}` }}
                    </template>
                </Column>

                <!-- Content -->
                <slot></slot>
                <!-- End Content -->

                <!-- Creation Date -->
                <Column
                    v-if="showCreationDate"
                    field="created_at"
                    header="Creation date"
                    sortable
                    style="max-width: 11rem"
                >
                    <template #body="{ data, field }">
                        {{ data[field] || "--" }}
                    </template>
                    <!-- Note: PrimeVue date filter inputs currently filter client-side only -->
                    <template #filter="{ filterModel, filterCallback }">
                        <InputText
                            style="min-width: 5rem; max-width: 7rem"
                            size="small"
                            v-model="filterModel.value"
                            @input="filterCallback()"
                            placeholder="YYYY-MM-DD"
                        />
                    </template>
                </Column>
                <!-- End Creation Date -->

                <!-- Update Date -->
                <Column
                    v-if="showUpdateDate"
                    field="updated_at"
                    header="Last update"
                    style="max-width: 11rem"
                    sortable
                >
                    <template #body="{ data, field }">
                        {{ data[field] || "--" }}
                    </template>
                    <!-- Note: PrimeVue date filter inputs currently filter client-side only -->
                    <template #filter="{ filterModel, filterCallback }">
                        <InputText
                            style="min-width: 5rem; max-width: 7rem"
                            size="small"
                            v-model="filterModel.value"
                            @input="filterCallback()"
                            placeholder="YYYY-MM-DD"
                        />
                    </template>
                </Column>
                <!-- End Update Date -->

                <template #footer>
                    <slot name="footer"> </slot>
                </template>
            </DataTable>
        </template>

        <template #footer>
            <div
                v-if="showPagination"
                class="card-footer justify-center md:justify-end"
            >
                <Pagination v-model="form.per_page" :paginated="paginated" />
            </div>
        </template>
    </Card>
</template>

<script setup lang="ts">
import { DropdownMenuRadioGroup } from "@/components/ui/dropdown-menu";
import { LaravelPagination } from "@/types";
import { scrollHeight } from "@/utils/dataTable";
import { router } from "@inertiajs/vue3";
import { FilterMatchMode } from "@primevue/core/api";
import dayjs from "dayjs";
import relativeTime from "dayjs/plugin/relativeTime";
import pickBy from "lodash/pickBy";
import throttle from "lodash/throttle";
import { Info, Loader2 } from "lucide-vue-next";
import { reactive, ref, watch } from "vue";
import Pagination from "./Pagination.vue";
import SearchFilter from "./SearchFilter.vue";

// Extend dayjs with the relativeTime plugin to enable fromNow() functionality
dayjs.extend(relativeTime);

// Define TableItem locally (or move to your @/types file)
interface TableItem {
    id: number | string;
    created_at?: string | null;
    updated_at?: string | null;
    [key: string]: any;
}

defineOptions({
    inheritAttrs: false,
});

const emits = defineEmits(["searched"]);

const props = defineProps({
    title: {
        type: String,
        default: "Liste",
    },
    paginated: {
        type: Object as () => LaravelPagination<TableItem>,
        required: false,
        default: null,
    },
    total: {
        type: Number,
        required: false,
        default: 0,
    },
    dataFilters: Object,
    filters: {
        type: Object,
        required: false,
        default: () => ({}),
    },
    filterKey: String,
    showSearch: {
        type: Boolean,
        default: true,
    },
    showPagination: {
        type: Boolean,
        default: true,
    },
    showId: {
        type: Boolean,
        default: true,
    },
    showCreationDate: {
        type: Boolean,
        default: false,
    },
    showUpdateDate: {
        type: Boolean,
        default: false,
    },
});

const data = ref<TableItem[]>([]);
const loading = ref(false);
const form = reactive<Record<string, any>>({
    // Added type annotation for form
    search: props.dataFilters?.search,
    per_page: props.paginated?.per_page,
    sort: props.dataFilters?.sort,
});

if (props.filterKey) {
    form[props.filterKey] = props.dataFilters?.[props.filterKey]; // Added optional chaining
}

const filters = ref<Record<string, any>>(props.filters ?? {});

const initFilters = () => {
    filters.value = {
        ...props.filters,
        created_at: { value: null, matchMode: FilterMatchMode.CONTAINS },
        updated_at: { value: null, matchMode: FilterMatchMode.CONTAINS },
    };
};
initFilters();

const onSort = (event: any) => {
    form.sort = {
        field: event.sortField,
        order: event.sortOrder === 1 ? "asc" : "desc",
    };
};

const onFilterUpdate = (filters: Record<string, any>): void => {
    // Transform the event.filters structure to { key: value } for useFilter
    const newFilters = Object.keys(filters).reduce(
        (acc: Record<string, any>, key) => {
            const filter = filters[key];
            if (filter) {
                acc[key] = filter.value;
            }
            return acc;
        },
        {}
    );
    if (Object.keys(newFilters).length > 0) {
        Object.assign(form, newFilters);
    }
};

const reset = () => {
    // Create a temporary object with null values for all keys in the initial form structure
    const initialFormKeys = ["search", "per_page", "sort"];
    if (props.filterKey) {
        initialFormKeys.push(props.filterKey);
    }
    const resetValues = initialFormKeys.reduce((acc, key) => {
        acc[key] = null;
        return acc;
    }, {} as Record<string, any>);

    // Reset PrimeVue filters if needed
    initFilters();

    // Assign the reset values back to the form
    Object.assign(form, resetValues);
};

watch(
    () => props.paginated?.data,
    (newData) => {
        const tab: TableItem[] = Array.isArray(newData) ? newData : [];
        data.value = tab.map((item: TableItem) => ({
            ...item,
            created_at: parseDate(item.created_at),
            updated_at: parseDate(item.updated_at, "humanReadable"),
        }));
    },
    { immediate: true }
);

watch(
    form,
    throttle(() => {
        const currentRoute = route().current();
        if (!currentRoute) {
            console.error("Could not determine the current route.");
            return; // Exit if route name is undefined
        }
        const url = route(currentRoute);
        const activeFilters = pickBy(
            form,
            (value: any) => value !== null && value !== ""
        ); // Added type annotation
        router.get(url, activeFilters, {
            preserveState: true,
            onStart: () => (loading.value = true),
            onFinish: () => (loading.value = false),
        });
        emits("searched", activeFilters);
    }, 150),
    { deep: true }
);

// Simplified date parsing
function parseDate(
    date: string | null | undefined,
    format = "default"
): string | null {
    if (!date) return null;
    if (format === "humanReadable") {
        return dayjs(date).fromNow();
    }
    return dayjs(date).format("YYYY-MM-DD HH:mm:ss");
}
</script>
