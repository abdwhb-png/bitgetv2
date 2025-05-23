<script setup>
import { Button } from "@/Components/ui/button";
import {
    Pagination,
    PaginationEllipsis,
    PaginationFirst,
    PaginationLast,
    PaginationList,
    PaginationListItem,
    PaginationNext,
    PaginationPrevious as PaginationPrev,
} from "@/Components/ui/pagination";
import { Link } from "@inertiajs/vue3";
import { ref, watch } from "vue";

const props = defineProps({
    meta: {
        type: Object,
        required: false,
        default: null,
    },
});

const currentPage = ref(1);

watch(
    () => props.meta,
    () => {
        currentPage.value = props.meta?.current_page || 1;
    },
    { immediate: true }
);
</script>

<template>
    <Pagination
        v-if="meta"
        v-slot="{ page }"
        :items-per-page="meta.per_page"
        :total="meta.total"
        :sibling-count="1"
        show-edges
        :default-page="1"
    >
        <PaginationList
            v-slot="{ items }"
            class="flex items-center justify-center gap-1"
        >
            <PaginationFirst
                @click="$inertia.visit(meta?.first_page_url || '')"
            />
            <PaginationPrev
                @click="
                    meta?.prev_page_url
                        ? $inertia.visit(meta.prev_page_url)
                        : ''
                "
            />

            <template v-for="(item, index) in items">
                <PaginationListItem
                    v-if="item.type === 'page'"
                    :key="index"
                    :value="item.value"
                    as-child
                >
                    <span class="hidden">{{ item }} {{ page }}</span>
                    <Button
                        class="h-9 w-9 p-0"
                        :variant="
                            item.value === currentPage ? 'secondary' : 'outline'
                        "
                        as-child
                    >
                        <Link
                            preserve-scroll
                            preserve-state
                            prefetch
                            cache-for="1m"
                            :href="
                                route(route().current(), {
                                    ...route().params,
                                    page: item.value,
                                })
                            "
                        >
                            {{ item.value }}
                        </Link>
                    </Button>
                </PaginationListItem>
                <PaginationEllipsis v-else :key="item.type" :index="index" />
            </template>

            <PaginationNext
                @click="
                    meta?.next_page_url
                        ? $inertia.visit(meta.next_page_url)
                        : ''
                "
            />
            <PaginationLast
                @click="$inertia.visit(meta?.last_page_url || '')"
            />
        </PaginationList>
    </Pagination>
</template>
