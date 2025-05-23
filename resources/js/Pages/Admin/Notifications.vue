<script setup>
import { ref, onBeforeMount } from "vue";
import { useUserStore } from "@/stores/user";
import { FilterMatchMode } from "@primevue/core/api";
import { rowsPerPage } from "@/utils/dataTable";
import { useToast } from "primevue";

const userStore = useUserStore();

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
    title: { value: null, matchMode: FilterMatchMode.STARTS_WITH },
    body: { value: null, matchMode: FilterMatchMode.STARTS_WITH },
    created_at: { value: null, matchMode: FilterMatchMode.STARTS_WITH },
    status: { value: null, matchMode: FilterMatchMode.EQUALS },
});

const statuses = ref(["readed", "unread"]);

const loading = ref(false);

const getSeverity = (status) => {
    switch (status) {
        case "unread":
            return "danger";

        case "readed":
            return "success";
    }
};

onBeforeMount(() => {
    userStore.fetchNotifications().then(() => {
        loading.value = false;
    });
});
</script>

<template>
    <AdminLayout title="Notifications">
        <div class="flex flex-col space-y-5">
            <Toolbar class="m-2">
                <template #start>
                    <h4 class="m-0 font-bold text-sky-500">
                        {{ userStore.notifications.length || 0 }}
                        results
                    </h4>
                </template>
                <template #end>
                    <div class="flex gap-3 items-center">
                        <OverlayBadge
                            :value="userStore.unreadCount.toString()"
                            :severity="
                                userStore.unreadCount > 0
                                    ? 'danger'
                                    : 'secondary'
                            "
                        >
                            <Tag severity="danger">Unreaded</Tag>
                        </OverlayBadge>
                        <Button
                            size="small"
                            severity="contrast"
                            label="Mark All As Readed"
                            :disabled="!userStore.unreadCount"
                            :loading="userStore.loading"
                            @click="userStore.readNotifications()"
                        />
                        <Button
                            size="small"
                            severity="danger"
                            label="Delete All"
                            :disabled="!userStore.hasNotifications"
                            :loading="userStore.loading"
                            @click="userStore.deleteNotifications()"
                        />
                    </div>
                </template>
            </Toolbar>

            <DataTable
                :value="userStore.notifications"
                paginator
                :rows="rowsPerPage[0] || 10"
                :rowsPerPageOptions="rowsPerPage"
                v-model:filters="filters"
                :globalFilterFields="['title', 'body', 'created_at']"
                filterDisplay="row"
                showGridlines
                scrollable
                scrollHeight="700px"
                tableStyle="min-width: 50rem"
            >
                <template #header>
                    <div class="flex flex-wrap gap-2 items-center justify-end">
                        <IconField class="w-full md:w-2/3">
                            <InputIcon>
                                <i class="pi pi-search" />
                            </InputIcon>
                            <InputText
                                fluid
                                v-model="filters['global'].value"
                                placeholder="Search notification"
                            />
                        </IconField>
                    </div>
                </template>

                <template #empty> No notifications found. </template>

                <Column field="status" header="Status" :showFilterMenu="false">
                    <template #filter="{ filterModel, filterCallback }">
                        <Select
                            v-model="filterModel.value"
                            @change="filterCallback()"
                            :options="statuses"
                            placeholder="Filter Status"
                            style="min-width: 12rem"
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
                    <template #body="{ data }">
                        <div class="">
                            <Tag
                                :value="data.status"
                                :severity="getSeverity(data.status)"
                            />
                            <br />
                            <button
                                v-if="data.status == 'unread'"
                                @click="userStore.readNotification(data.id)"
                                class="text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-2 py-1 me-2 mt-2 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700"
                            >
                                read now
                            </button>
                        </div>
                        <p>
                            received :
                            {{ data.created ?? data.created_at }}
                        </p>
                    </template>
                </Column>

                <Column field="title" header="Title">
                    <template #body="{ data }">
                        <p v-html="data.title"></p>
                    </template>
                </Column>

                <Column field="body" header="Body">
                    <template #body="{ data }">
                        <div
                            style="
                                max-width: 500px;
                                white-space: normal;
                                word-break: break-all;
                            "
                            v-html="data.body"
                        ></div>
                    </template>
                </Column>
            </DataTable>
        </div>
    </AdminLayout>
</template>
