<template>
    <DataTable
        v-model:editingRows="editingRows"
        :value="list"
        editMode="row"
        dataKey="id"
        @row-edit-save="onRowEditSave"
        :pt
    >
        <Column
            :rowEditor="true"
            header="Edit"
            style="width: 5%"
            bodyStyle="text-align:center"
        ></Column>

        <Column field="title" header="Title" style="width: 30%">
            <template #editor="{ data, field }">
                <InputText v-model="data[field]" />
            </template>
        </Column>
        <Column field="url" header="URL" style="width: 65%">
            <template #editor="{ data, field }">
                <InputText v-model="data[field]" />
            </template>
        </Column>
    </DataTable>
</template>

<script>
import { useToast } from "primevue/usetoast";
import { router } from "@inertiajs/vue3";
import { pt } from "@/utils/dataTable";

export default {
    props: {
        datas: { type: Object, required: true },
        canEdit: { type: Number, default: 1 },
    },
    setup() {
        const toast = useToast();
        return { toast };
    },
    data(props) {
        return {
            list: props.datas,
            editingRows: [],
            pt: pt,
        };
    },
    methods: {
        async onRowEditSave(event) {
            const { data, index, newData } = event;

            if (data.title == newData.title && data.url == newData.url) return;

            router.put(
                route(this.$routePrefix + "cservice.update", data.id),
                newData,
                {
                    onSuccess: () => {
                        this.list[index] = newData;
                        this.toast.add({
                            severity: "success",
                            summary: "Customer Service Updated",
                            life: 5000,
                        });
                    },
                }
            );
        },
    },
};
</script>
