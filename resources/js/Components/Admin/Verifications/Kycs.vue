<script setup>
import { ref, computed } from "vue";
import { useForm, usePage, Link } from "@inertiajs/vue3";
import { useToast } from "primevue/usetoast";
import { getSeverity, getFilters } from "@/utils/dataTable";
import CustomDataTable from "@/Components/Admin/Common/CustomDataTable.vue";

const props = defineProps({
    datas: Object,
    totalCount: Number,
    unverifiedCount: Number,
});

const page = usePage();
const toast = useToast();

const editingRows = ref([]);
const filters = ref(getFilters());
const globalFilterFields =
    page.props.siteConfig.globalFilterFields.verification.kyc;
const statuses = page.props.siteConfig.statuses.verification.kyc;

const form = useForm({
    status: null,
});

const onRowEditSave = (event) => {
    let { data, index, newData } = event;

    if (data.status == newData.status) return;

    form.transform((data) => ({
        status: newData.status,
        type: "kycs",
    })).put(route(page.routePrefix + "verifications.update", newData.id), {
        onSuccess: () => {
            toast.add({
                severity: "success",
                summary: "Verification Updated",
                life: 5000,
            });
            editingRows.value = [];
        },
        onFinish: () => {
            form.reset();
        },
    });
};
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
        title="KYC verification list"
        :paginated="datas"
        :total="totalCount"
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
            :rowEditor="page.props.canEditVerification"
            header="Edit"
            style="width: 5%"
            bodyStyle="text-align:center"
        ></Column>

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
                    style="min-width: 12rem"
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

            <template #editor="{ data, field }">
                <FloatLabel variant="in">
                    <Select
                        :id="data.id + 'kyc_status'"
                        v-model="data[field]"
                        :options="statuses"
                        optionLabel="label"
                        optionValue="label"
                        placeholder="Select a Status"
                        fluid
                    >
                        <template #option="slotProps">
                            <Tag
                                :value="slotProps.option.label"
                                :severity="getSeverity(slotProps.option.label)"
                            />
                        </template>
                    </Select>
                    <label :for="data.id + 'kyc_status'">Status</label>
                </FloatLabel>
            </template>

            <template #body="{ data, field }">
                <a
                    :href="data.detail.file_url"
                    target="_blank"
                    class="block text-blue-500 underline hover:underline-0 mb-2"
                >
                    {{ data.detail.id_type }}
                </a>
                <Tag
                    :value="data[field]"
                    :severity="getSeverity(data[field])"
                />
            </template>
        </Column>

        <Column field="detail" header="Details" style="width: 85%">
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
</template>
