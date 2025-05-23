<script setup>
import { ref, computed } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import { getSeverity, getFilters } from "@/utils/dataTable";
import CustomDataTable from "@/Components/Admin/Common/CustomDataTable.vue";

const props = defineProps({
    datas: Object,
    totalCount: Number,
    unverifiedCount: Number,
});

const page = usePage();

const filters = ref(getFilters());
const globalFilterFields = ref(
    page.props.siteConfig.globalFilterFields.verification.email
);
const statuses = ref(page.props.siteConfig.statuses.verification.email);
</script>
<template>
    <Toolbar class="mb-2">
        <template #end>
            <OverlayBadge
                :value="unverifiedCount.toString()"
                :severity="unverifiedCount > 0 ? 'danger' : 'secondary'"
            >
                <Tag :severity="getSeverity('pending')">Unverified</Tag>
            </OverlayBadge>
        </template>
    </Toolbar>

    <CustomDataTable
        title="Email verification list"
        :paginated="datas"
        :total="totalCount"
        :data-filters="$page.props.filters"
        filter-key="type"
        :filters="filters"
        filterDisplay="row"
        :globalFilterFields="globalFilterFields"
    >
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
    </CustomDataTable>

    <!-- <DataTable
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
    </DataTable> -->
</template>
