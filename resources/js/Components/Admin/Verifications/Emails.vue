<script setup>
import { ref, computed } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import {
    getSeverity,
    getFilters,
    rowsPerPage,
    pt,
    scrollHeight,
} from "@/utils/dataTable";

const props = defineProps({
    datas: Object,
    loading: Boolean,
});

const page = usePage();

const dataLength = ref(props.datas?.length);

const filters = ref(getFilters());
const globalFilterFields = ref(
    page.props.siteConfig.globalFilterFields.verification.email
);
const statuses = ref(page.props.siteConfig.statuses.verification.email);

const pendingCount = computed(() => {
    return (
        props.datas?.filter((item) => item.status == "unverified").length || 0
    );
});
</script>
<template>
    <Toolbar class="m-2">
        <template #start>
            <h4 class="m-0 font-bold text-sky-500">{{ dataLength }} results</h4>
        </template>

        <template #end>
            <OverlayBadge
                :value="pendingCount.toString()"
                :severity="pendingCount > 0 ? 'danger' : 'secondary'"
            >
                <Tag :severity="getSeverity('pending')">Pending</Tag>
            </OverlayBadge>
        </template>
    </Toolbar>

    <DataTable
        :loading="loading"
        :value="props.datas"
        dataKey="id"
        v-model:filters="filters"
        :globalFilterFields
        @filter="dataLength = $event.filteredValue.length"
        filterDisplay="row"
        paginator
        :rows="rowsPerPage[0] || 10"
        :rowsPerPageOptions="rowsPerPage"
        showGridlines
        scrollable
        :scrollHeight
        :pt
    >
        <template #empty> No email verifications found. </template>

        <template #loading>
            <span>Fetching email verifications data. Please wait.</span>
        </template>

        <template #header>
            <div class="flex flex-wrap gap-2 items-center justify-end">
                <IconField class="w-full md:w-2/3">
                    <InputIcon>
                        <i class="pi pi-search" />
                    </InputIcon>
                    <InputText
                        fluid
                        v-model="filters['global'].value"
                        placeholder="Search by user, email, ..."
                    />
                </IconField>
            </div>
        </template>

        <Column
            field="status"
            header="Status"
            :showFilterMenu="false"
            style="width: 10%"
        >
            <template #filter="{ filterModel, filterCallback }">
                <Select
                    v-model="filterModel.value"
                    @change="filterCallback()"
                    :options="statuses"
                    optionLabel="label"
                    optionValue="label"
                    placeholder="Filter Status"
                    :showClear="true"
                >
                    <template #option="slotProps">
                        <Tag
                            :value="slotProps.option.label"
                            :severity="getSeverity(slotProps.option.label)"
                        />
                    </template>
                </Select>
            </template>

            <template #body="{ data, field }">
                <p class="block text-blue-500 mb-2">
                    {{ data.detail.email }}
                </p>
                <Tag
                    :value="data[field]"
                    :severity="getSeverity(data[field])"
                />
            </template>
        </Column>

        <Column field="detail" header="Details" style="width: 90%">
            <template #body="{ data, field }">
                <ul class="list-group">
                    <li
                        v-for="(item, index) in data[field]"
                        :key="index"
                        class="list-group-item"
                    >
                        <div class="flex justify-between">
                            <span class="text-gray-400">
                                {{ index + ":" }}
                            </span>
                            <Link
                                v-if="index == 'user'"
                                :href="
                                    route($routePrefix + 'users.index', {
                                        search: item,
                                    })
                                "
                                class="link ml-2"
                                >{{ item }}</Link
                            >
                            <span v-else class="ml-2">
                                {{ item }}
                            </span>
                        </div>
                    </li>
                </ul>
            </template>
        </Column>
    </DataTable>
</template>
