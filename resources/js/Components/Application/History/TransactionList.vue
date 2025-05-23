<script setup>
import { computed, ref } from "vue";
import { formatDateTime } from "@/utils/helpers";

const props = defineProps({
    data: Object | Array,
});

const [pageSize, step] = [ref(10), 10];

const list = computed(() => {
    return props.data.slice(0, pageSize.value);
});

const getStatus = (status) => {
    switch (status) {
        case "pending":
            return "secondary";

        case "approved":
            return "success";

        case "rejected":
            return "danger";

        case "deposit":
            return "primary";

        case "withdrawal":
            return "danger";

        default:
            return "info";
    }
};

const getIcons = (type) => {
    switch (type) {
        case "deposit":
            return "icon-way2";

        case "withdrawal":
            return "icon-way";

        case "swap":
            return "icon-swap";

        default:
            return "icon-wallet";
    }
};
</script>

<template>
    <ul>
        <li v-for="(item, index) in list" :key="index" class="mb-8">
            <a href="#" class="coin-item style-1 gap-12 bg-surface">
                <span class="rounded-circle p-2 fs-4 bg-dark">
                    <i
                        class="icon"
                        :class="
                            getIcons(item.type) +
                            ' text-' +
                            getStatus(item.type)
                        "
                    ></i>
                </span>
                <div class="content">
                    <div class="title">
                        <p class="mb-4 text-large text-capitalize">
                            {{ item.type }}
                        </p>
                        <span class="text-secondary">{{
                            formatDateTime(item.created_at)
                        }}</span>
                    </div>
                    <div class="box-price">
                        <p class="text-small mb-4">
                            <span
                                v-show="item.type == 'deposit'"
                                class="text-primary"
                                >+</span
                            >
                            <span
                                v-show="item.type == 'withdrawal'"
                                class="text-danger"
                                >-</span
                            >
                            <span v-if="item.type == 'swap'">
                                {{ item.amount }}
                                {{ item.method }}
                                <!-- {{ item.converted_amount }} -->
                            </span>
                            <span v-else
                                >{{ item.converted_amount || item.amount }}
                                {{ item.method }}</span
                            >
                        </p>
                        <p class="text-end">
                            <Tag
                                class="text-extra-small text-uppercase"
                                :value="item.status"
                                :severity="getStatus(item.status)"
                            ></Tag>
                        </p>
                    </div>
                </div>
            </a>
            <div class="text-center">
                <span class="text-small" style="color: gray"
                    >REF: {{ item.ref_id }}</span
                >
            </div>
        </li>
        <li class="">
            <!-- Bouton Load More -->
            <Button
                @click="pageSize = pageSize + step"
                v-show="props.data.length > pageSize"
                label="Show More"
                unstyled
                class="btn btn-secondary"
            />
        </li>
    </ul>
</template>
