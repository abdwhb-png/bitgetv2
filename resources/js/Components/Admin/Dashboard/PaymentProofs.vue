<script setup>
import { ref } from "vue";
import { Link } from "@inertiajs/vue3";
import { rowsPerPage, getFilters, pt, scrollHeight } from "@/utils/dataTable";

const props = defineProps({
    datas: Object,
});

const filters = ref(getFilters());
</script>

<template>
    <DataTable
        :value="datas"
        dataKey="id"
        v-model:filters="filters"
        :globalFilterFields="[
            'detail.user',
            'detail.method',
            'detail.amount',
            'detail.payment_ref_id',
        ]"
        filterDisplay="row"
        paginator
        :rows="rowsPerPage[0]"
        :rowsPerPageOptions="rowsPerPage"
        showGridlines
        scrollable
        :scrollHeight
        :pt
    >
        <template #empty> No payment proofs found. </template>

        <template #header>
            <div class="flex flex-wrap gap-2 items-center justify-between">
                <h4 class="m-0 font-bold text-sky-500">
                    {{ datas.length }} results
                </h4>

                <IconField class="w-2/3">
                    <InputIcon>
                        <i class="pi pi-search" />
                    </InputIcon>
                    <InputText
                        fluid
                        v-model="filters['global'].value"
                        placeholder="Search by user, method, amount, ref_id, ..."
                    />
                </IconField>
            </div>
        </template>

        <Column field="file_url" header="File" style="width: 10%">
            <template #body="{ data, field }">
                <a
                    :href="data[field]"
                    target="_blank"
                    class="block text-blue-500 underline hover:underline-0 mb-2"
                >
                    Show file
                </a>
                <p class="font-bold">{{ data.detail.user }}</p>
            </template></Column
        >

        <Column field="detail" header="Details" style="width: 20%">
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
