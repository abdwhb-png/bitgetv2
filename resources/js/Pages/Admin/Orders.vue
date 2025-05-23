<script setup>
import { ref, computed, onMounted } from "vue";
import { Link, usePage, useForm } from "@inertiajs/vue3";
import { useToast } from "primevue/usetoast";
import { getSeverity, getFilters, parseStatuses } from "@/utils/dataTable";
import CustomDataTable from "@/Components/Admin/Common/CustomDataTable.vue";

const page = usePage();
const search = route().params.search || null;

const toast = useToast();
const editingRows = ref([]);
const statuses = ref(parseStatuses(page.props.siteConfig.statuses.order));
const filters = ref(getFilters("orders", search));
const globalFilterFields = ref(page.props.siteConfig.globalFilterFields.order);
const types = ref(page.props.siteConfig.dataTypes.order);

const form = useForm({
    status: null,
});

const closeOrder = (order) => {
    toast.add({
        severity: "warn",
        summary: "Feature not available",
        detail: "The order will be closed automatically once the expiration settlement is completed",
        life: 3000,
    });
    editingRows.value = [];
};

const onRowEditSave = (event) => {
    let { data, newData } = event;

    if (newData.status == data.status) {
        return;
    }

    form.status = newData.status;

    form.put(route(page.props.routePrefix + "orders", newData.id), {
        onSuccess: () => {
            editingRows.value = [];
            toast.add({
                severity: "success",
                summary: "Order Updated",
                detail: newData,
                life: 3000,
            });
        },
        onFinish: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <AdminLayout title="Orders">
        <ToastError :errors="page.props.errors" />

        <div class="flex flex-col space-y-5">
            <Toolbar class="m-2">
                <template #end>
                    <OverlayBadge
                        :value="page.props.openedCount.toString()"
                        :severity="
                            page.props.openedCount > 0 ? 'danger' : 'secondary'
                        "
                    >
                        <Tag severity="info">Still Opened</Tag>
                    </OverlayBadge>
                </template>
            </Toolbar>

            <CustomDataTable
                title="Orders list"
                :paginated="$page.props.orders"
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

                <Column
                    :rowEditor="page.props.canEditOrder"
                    style="width: 5%"
                    header="Edit"
                    bodyStyle="text-align:center"
                ></Column>

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
                        <Button
                            v-show="data.status != 'closed'"
                            label="CLOSE NOW"
                            size="small"
                            severity="contrast"
                            @click="closeOrder(data)"
                        />
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
                    header="Order Details"
                    style="width: 85%"
                >
                    <template #body="{ data, field }">
                        <div class="flex justify-around gap-2">
                            <ul
                                v-for="(value, key) in data[field]"
                                :key="key"
                                class="list-group"
                            >
                                <li
                                    v-for="(item, index) in value"
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
                                        <span v-else class="ml-2">
                                            {{ item }}
                                        </span>
                                    </div>
                                </li>
                            </ul>
                            <ul class="list-group">
                                <li
                                    v-for="(item, index) in data['dates']"
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
