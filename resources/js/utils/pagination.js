import { ref, computed } from "vue";

export function usePagination(items, pageSize = 10) {
    if (!items || !items.length) {
        return {
            currentPage: ref(1),
            paginatedItems: ref([]),
            hasMoreItems: ref(false),
            loadMore: () => {},
            resetPagination: () => {},
        };
    }
    const currentPage = ref(1);

    const paginatedItems = computed(() => {
        const startIndex = (currentPage.value - 1) * pageSize;
        const endIndex = currentPage.value * pageSize;
        return items.value.slice(startIndex, endIndex);
    });

    const hasMoreItems = computed(() => {
        return items.value.length > currentPage.value * pageSize;
    });

    function loadMore() {
        if (hasMoreItems.value) {
            currentPage.value++;
        }
    }

    function resetPagination() {
        currentPage.value = 1;
    }

    return {
        currentPage,
        paginatedItems,
        hasMoreItems,
        loadMore,
        resetPagination,
    };
}
