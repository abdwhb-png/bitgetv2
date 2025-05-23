<script setup>
import { ref, computed, onMounted } from "vue";
import { Link, usePage, useForm } from "@inertiajs/vue3";
import { useToast } from "primevue/usetoast";
import {
    getSeverity,
    getFilters,
    parseStatuses,
    rowsPerPage,
    pt,
    scrollHeight,
} from "@/utils/dataTable";
import CustomDataTable from "@/Components/Admin/Common/CustomDataTable.vue";

const page = usePage();
const search = route().params.search || null;

const toast = useToast();
const editingRows = ref([]);
const statuses = parseStatuses(page.props.siteConfig.statuses.transaction);
const filters = ref(getFilters("transactions", search));
const globalFilterFields = page.props.siteConfig.globalFilterFields.transaction;
const types = page.props.siteConfig.dataTypes.transaction;

const form = useForm({
    status: null,
});

const onRowEditSave = (event) => {
    let { data, newData } = event;

    if (newData.status == data.status || data.type == "swap") {
        return;
    }

    form.status = newData.status;

    form.put(
        route(page.props.routePrefix + "transactions.update", newData.id),
        {
            onSuccess: () => {
                toast.add({
                    severity: "success",
                    summary: "Transaction Updated",
                    detail: newData,
                    life: 3000,
                });
                editingRows.value = [];
            },
            onFinish: () => {
                form.reset();
            },
        }
    );
};
</script>

<template>
    <AdminLayout title="Transactions">
        <ToastError :errors="page.props.errors" />

        <div class="flex flex-col space-y-5">
            <Toolbar>
                <template #end>
                    <OverlayBadge
                        :value="page.props.pendingCount.toString()"
                        :severity="
                            page.props.pendingCount > 0 ? 'danger' : 'secondary'
                        "
                    >
                        <Tag :severity="getSeverity('pending')">Pending</Tag>
                    </OverlayBadge>
                </template>
            </Toolbar>

            <CustomDataTable
                title="Transactions list"
                :paginated="$page.props.transactions"
                :total="$page.props.totalCount"
                :data-filters="$page.props.filters"
                filter-key="type"
                :filters="filters"
                filterDisplay="row"
                :globalFilterFields="globalFilterFields"
                v-model:editingRows="editingRows"
                editMode="row"
                @row-edit-save="onRowEditSave"
            >
                <!-- Type -->
                <Column
                    field="type"
                    header="Type"
                    :showFilterMenu="false"
                    style="width: 5%"
                >
                    <template #filter="{ filterModel, filterCallback }">
                        <Select
                            v-model="filterModel.value"
                            @change="filterCallback()"
                            :options="types"
                            placeholder="Filter Type"
                            :showClear="true"
                        >
                            <template #option="slotProps">
                                <Tag
                                    :value="slotProps.option"
                                    :severity="getSeverity(slotProps.option)"
                                />
                            </template>
                        </Select>
                    </template>
                    <template #body="{ data, field }">
                        <Tag
                            :value="data[field]"
                            :severity="getSeverity(data[field])"
                        />
                        <p>{{ data.ref_id }}</p>
                    </template>
                </Column>

                <!-- Edit -->
                <Column
                    :rowEditor="page.props.canEditTransaction"
                    style="width: 5%"
                    header="Edit"
                    bodyStyle="text-align:center"
                ></Column>

                <!-- Status -->
                <Column
                    field="status"
                    header="Status"
                    :showFilterMenu="false"
                    style="width: 5%"
                >
                    <template #filter="{ filterModel, filterCallback }">
                        <Select
                            v-model="filterModel.value"
                            @change="filterCallback()"
                            :options="statuses"
                            optionLabel="label"
                            optionValue="value"
                            placeholder="Filter Status"
                            :showClear="true"
                        >
                            <template #option="slotProps">
                                <Tag
                                    :value="slotProps.option.value"
                                    :severity="
                                        getSeverity(slotProps.option.value)
                                    "
                                />
                            </template>
                        </Select>
                    </template>

                    <template #editor="{ data, field }">
                        <Select
                            v-show="data.type != 'swap'"
                            v-model="data[field]"
                            :options="statuses"
                            optionLabel="label"
                            optionValue="value"
                            placeholder="Select a Status"
                            fluid
                        >
                            <template #option="slotProps">
                                <Tag
                                    :value="slotProps.option.value"
                                    :severity="
                                        getSeverity(slotProps.option.value)
                                    "
                                />
                            </template>
                        </Select>
                    </template>
                    <template #body="{ data }">
                        <Tag
                            :value="data.status"
                            :severity="getSeverity(data.status)"
                        />
                    </template>
                </Column>

                <Column
                    field="detail"
                    header="Transaction Details"
                    style="width: 85%"
                >
                    <template #body="{ data, field }">
                        <div class="flex justify-around gap-2">
                            <!-- details -->
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
                                                route(
                                                    $routePrefix +
                                                        'users.index',
                                                    { search: item }
                                                )
                                            "
                                            class="link ml-2"
                                            >{{ item }}</Link
                                        >
                                        <span
                                            v-else
                                            class="ml-2 max-w-xs truncate"
                                        >
                                            {{ item }}
                                        </span>
                                    </div>
                                </li>
                            </ul>

                            <!-- dates -->
                            <ul class="list-group">
                                <li
                                    v-for="(item, index) in data.dates"
                                    :key="index"
                                    class="list-group-item"
                                >
                                    <div class="flex justify-between">
                                        <span class="text-gray-400">
                                            {{ index + ":" }}
                                        </span>
                                        <span class="ml-2">
                                            {{ item }}
                                        </span>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </template>
                </Column>
            </CustomDataTable>
        </div>
    </AdminLayout>
</template>
