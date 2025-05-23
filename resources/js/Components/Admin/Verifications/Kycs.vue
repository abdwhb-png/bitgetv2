<script setup>
import { ref, computed } from "vue";
import { useForm, usePage, Link } from "@inertiajs/vue3";
import { useToast } from "primevue/usetoast";
import {
    getSeverity,
    rowsPerPage,
    getFilters,
    pt,
    scrollHeight,
} from "@/utils/dataTable";

const props = defineProps({
    datas: Object,
    loading: Boolean,
});

const page = usePage();

const toast = useToast();

const dataLength = ref(props.datas?.length);
const editingRows = ref([]);

const filters = ref(getFilters());
const globalFilterFields =
    page.props.siteConfig.globalFilterFields.verification.kyc;
const statuses = page.props.siteConfig.statuses.verification.kyc;

const pendingCount = computed(() => {
    return props.datas?.filter((item) => item.status == "pending").length || 0;
});

const form = useForm({
    status: null,
});

const onRowEditSave = (event) => {
    let { data, index, newData } = event;

    if (data.status == newData.status) return;

    form.transform((data) => ({
        status: newData.status,
        type: "kycs",
    })).put(route("admin.verifications.update", newData.id), {
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
        :loading="loading || form.processing"
        :value="props.datas"
        dataKey="id"
        v-model:editingRows="editingRows"
        editMode="row"
        @row-edit-save="onRowEditSave"
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
        <template #empty> No kyc verifications found. </template>

        <template #loading>
            <span v-if="form.processing">Updating kyc...</span>
            <span v-else>Fetching kyc verifications data. Please wait.</span>
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
                        placeholder="Search by user, ID type, ..."
                    />
                </IconField>
            </div>
        </template>

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
    </DataTable>
</template>
