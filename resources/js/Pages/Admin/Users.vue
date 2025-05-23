<script setup>
import { ref } from "vue";
import { usePage } from "@inertiajs/vue3";
import { getSeverity, getFilters } from "@/utils/dataTable";
import { copyToClipboard } from "@/utils/helpers";

import ManageAdmins from "@/Components/Admin/Users/Admins.vue";
import ActionButtons from "@/Components/Admin/Users/ActionButtons.vue";
import CustomDataTable from "@/Components/Admin/Common/CustomDataTable.vue";

const page = usePage();
const pageName = "Users";
const search = route().params.search || null;
const filters = ref(getFilters("users", search));
const globalFilterFields = page.props.siteConfig.globalFilterFields.user;
</script>

<template>
    <AdminLayout :title="pageName">
        <ToastError :errors="$page.props.errors" />

        <div class="flex flex-col space-y-5">
            <!-- toolabr -->
            <Toolbar>
                <template #end>
                    <ManageAdmins v-if="page.props.auth.user.isSuperAdmin" />
                </template>
            </Toolbar>
            <!-- end toolbar -->

            <!-- data table -->
            <CustomDataTable
                title="Users list"
                :paginated="page.props.users"
                :total="page.props.totalCount"
                :data-filters="page.props.filters"
            >
                <Column
                    field="full_name"
                    header="Users Details"
                    sortable
                    :showFilterMenu="false"
                    style="width: 100%"
                >
                    <template #body="{ data, field }">
                        <!-- action toolbar -->
                        <Toolbar class="mb-2 bg-gray-100">
                            <template #start>
                                <div class="flex items-center gap-2">
                                    <Avatar
                                        :image="data.profile_photo_url"
                                        style="width: 32px; height: 32px"
                                    />
                                    <CopyBtn
                                        :text="data.full_name"
                                        class="font-bold uppercase text-blue-500"
                                    />
                                </div>
                            </template>

                            <template #end>
                                <ActionButtons :data="data" />
                            </template>
                        </Toolbar>

                        <!-- user overview -->
                        <div class="flex justify-around gap-2">
                            <!-- user details -->
                            <ul class="list-group">
                                <li class="list-group-item text-center">
                                    <span class="font-bold">User Details</span>
                                </li>
                                <li
                                    v-for="(item, index) in data.detail"
                                    :key="index"
                                    class="list-group-item"
                                >
                                    <div class="flex justify-between">
                                        <ShowKeyValue
                                            :index="index"
                                            :value="item"
                                        >
                                            <CopyBtn
                                                v-if="
                                                    index == 'phone' ||
                                                    index == 'email'
                                                "
                                                :text="item"
                                                class="text-blue-500"
                                            />
                                            <span v-else>{{
                                                item || "--"
                                            }}</span>
                                        </ShowKeyValue>
                                    </div>
                                </li>
                            </ul>

                            <!-- balances -->
                            <ul class="list-group">
                                <li class="list-group-item text-center">
                                    <span class="font-bold">Balances</span>
                                </li>
                                <li
                                    v-for="(item, index) in data.balances"
                                    :key="index"
                                    class="list-group-item"
                                >
                                    <div class="flex justify-between">
                                        <div>
                                            <ShowKeyValue
                                                :index="item.name"
                                                :value="item.amount"
                                            ></ShowKeyValue>
                                        </div>
                                        <div>
                                            <ShowKeyValue
                                                :index="'In Review'"
                                                :value="item.in_review || 0"
                                            ></ShowKeyValue>
                                        </div>
                                    </div>
                                </li>
                            </ul>

                            <!-- statuses -->
                            <ul class="list-group">
                                <li class="list-group-item text-center">
                                    <span class="font-bold">Statuses</span>
                                </li>
                                <li
                                    v-for="(item, index) in data.statuses"
                                    :key="index"
                                    class="list-group-item"
                                >
                                    <div class="flex justify-between">
                                        <ShowKeyValue
                                            :index="index"
                                            :value="item"
                                        >
                                            <Tag
                                                class="ml-2"
                                                :value="item"
                                                :severity="getSeverity(item)"
                                            />
                                        </ShowKeyValue>
                                    </div>
                                </li>
                            </ul>

                            <!-- dates -->
                            <ul class="list-group">
                                <li class="list-group-item text-center">
                                    <span class="font-bold">Dates</span>
                                </li>
                                <li
                                    v-for="(item, index) in data.dates"
                                    :key="index"
                                    class="list-group-item"
                                >
                                    <div class="flex justify-between">
                                        <ShowKeyValue
                                            :index="index"
                                            :value="item"
                                        />
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
