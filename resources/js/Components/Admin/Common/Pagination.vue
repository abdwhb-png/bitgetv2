<template>
    <div
        class="flex flex-col md:flex-row gap-5 text-gray-600 text-2sm font-medium"
        :class="[
            sizeClass,
            showItemsSelection ? 'justify-between' : 'justify-center',
        ]"
    >
        <!-- Items per page selection -->
        <div
            class="flex items-center gap-2 order-2 md:order-1"
            v-if="showItemsSelection"
        >
            Items per page
            <Select
                class="w-40"
                :default-value="modelValue"
                :options="itemsPerPage"
                editable
                @change="$emit('update:modelValue', $event.value)"
                :disabled="loading || !paginated.total"
            />
        </div>

        <!-- Boutons de pagination -->
        <div
            class="flex flex-wrap justify-center items-center gap-4 order-1 md:order-2"
        >
            <span data-datatable-info="true">{{ paginationInfo }}</span>
            <PaginationBtns v-if="true" :meta="paginatedMeta" />
            <div v-else class="pagination">
                <div class="pagination space-x-2">
                    <!-- Bouton Précédent -->
                    <Link
                        class="btn"
                        :class="{ disabled: !paginated.prev_page_url }"
                        :href="paginated.prev_page_url ?? ''"
                        prefetch
                        cache-for="1m"
                    >
                        <i
                            class="ki-outline ki-black-left rtl:transform rtl:rotate-180"
                        ></i>
                    </Link>

                    <!-- Affichage dynamique des pages -->
                    <UiButton
                        v-for="link in filteredLinks"
                        :key="link.label"
                        as-child
                        :class="{
                            'active disabled': link.active,
                        }"
                        :variant="link.active ? 'default' : 'outline'"
                    >
                        <Link
                            preserve-scroll
                            prefetch
                            cache-for="1m"
                            :href="link.url ?? ''"
                            class="rt"
                        >
                            {{ link.label }}
                        </Link>
                    </UiButton>

                    <!-- Bouton Suivant -->
                    <Link
                        class="btn"
                        :class="{ disabled: !paginated.next_page_url }"
                        prefetch
                        cache-for="1m"
                        :href="paginated.next_page_url ?? ''"
                    >
                        <i
                            class="ki-outline ki-black-right rtl:transform rtl:rotate-180"
                        ></i>
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import PaginationBtns from "./PaginationBtns.vue";
import { Link } from "@inertiajs/vue3";
import { ref, computed, watch } from "vue";
import { LaravelPagination } from "@/types";
import { itemsPerPage } from "@/utils/dataTable";

const emits = defineEmits(["update:modelValue", "reset"]);

const props = defineProps({
    modelValue: [String, Number],
    paginated: {
        type: Object as () => LaravelPagination<any>,
        required: false,
        default: {},
    },
    showItemsSelection: {
        type: Boolean,
        default: true,
    },
    maxVisiblePages: {
        type: Number,
        required: false,
        default: 5,
    },
    sizeClass: String,
});

const loading = ref(false);
const paginatedMeta = ref();

const currentPage = computed(() => paginatedMeta.value?.current_page);

watch(
    () => props.paginated,
    () => {
        const { data, ...meta } = props.paginated;
        paginatedMeta.value = meta;
    },
    { immediate: true }
);

// Calcul des indices de début et de fin des éléments affichés
const from = computed(
    () => (currentPage.value - 1) * paginatedMeta.value?.per_page + 1
);
const to = computed(() =>
    Math.min(
        currentPage.value * paginatedMeta.value?.per_page,
        paginatedMeta.value?.total
    )
);
const paginationInfo = computed(
    () => `${from.value} to ${to.value} of ${props.paginated.total}`
);

// Filtrer les liens de pagination pour exclure "Précédent" et "Suivant"
const filteredLinks = computed(() => paginatedMeta.value?.links?.slice(1, -1));

// Limiter à 5 pages visibles à la fois
const visiblePageNumbers = computed(() => {
    let start = Math.max(
        currentPage.value - Math.floor(props.maxVisiblePages / 2),
        0
    );
    let end = start + props.maxVisiblePages;

    return filteredLinks.value?.slice(start, end);
});
</script>
