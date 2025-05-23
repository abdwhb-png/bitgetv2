<script setup>
import { ref, onMounted } from "vue";
import { usePage, router } from "@inertiajs/vue3";
import { useToast } from "primevue/usetoast";
import { useConfirm } from "primevue/useconfirm";

import Show from "./Show.vue";
import Edit from "./Edit.vue";
import ConfirmsPassword from "@/Components/Admin/ConfirmsPassword.vue";

const props = defineProps({
    data: Object,
    field: String,
});

const toast = useToast();
const confirm = useConfirm();
const user = ref(null);
const [showUser, editUser] = [ref(false), ref(false)];

const selectUser = (type, record) => {
    user.value = record;

    if (type == "show") {
        showUser.value = true;
    }

    if (type == "edit") {
        editUser.value = true;
    }
};

const confirmDelete = (record) => {
    confirm.require({
        message: "Do you want to delete " + record.full_name + " account?",
        header: "Danger Zone",
        icon: "pi pi-info-circle",
        rejectLabel: "Cancel",
        rejectProps: {
            label: "Cancel",
            severity: "secondary",
            outlined: true,
        },
        acceptProps: {
            label: "Delete",
            severity: "danger",
        },
        accept: () => {
            router.visit(route("user.destroy", record.detail.email), {
                method: "DELETE",
                preserveScroll: true,
                onSuccess: (page) => {
                    toast.add({
                        severity: "success",
                        summary:
                            "Deletion performed successfully for " +
                            record.full_name,
                        detail: page.props.flash.status,
                        life: 5000,
                    });
                },
            });
        },
        reject: () => {},
    });
};
</script>

<template>
    <div>
        <div class="flex gap-2">
            <Button
                label="Show More"
                size="small"
                outlined
                icon="pi pi-eye"
                @click="selectUser('show', data.resource)"
            />
            <Button
                label="Edit User"
                size="small"
                severity="contrast"
                outlined
                icon="pi pi-pencil"
                :disabled="!$page.props.canEditUser"
                @click="selectUser('edit', data.resource)"
            />

            <Button
                as="a"
                label="Transactions"
                :href="
                    route($routePrefix + 'transactions.index', {
                        search: data.full_name,
                    })
                "
                severity="help"
                outlined
                icon="pi pi-arrow-right-arrow-left"
                size="small"
            />

            <Button
                as="a"
                label="Orders"
                raised
                :href="
                    route($routePrefix + 'orders.index', {
                        search: data.full_name,
                    })
                "
                icon="pi pi-chart-bar"
                severity="info"
                size="small"
            />

            <ConfirmsPassword @confirmed="confirmDelete(data)">
                <Button
                    label="Delete"
                    icon="pi pi-trash"
                    :disabled="!$page.props.canDeleteUser"
                    severity="danger"
                    size="small"
                />
            </ConfirmsPassword>
        </div>

        <!-- show user modal-->
        <Show
            :user
            :show="showUser"
            @hide="
                user = null;
                showUser = false;
            "
        />

        <!-- edit user modal-->
        <Edit
            :user
            :show="editUser"
            @hide="
                user = null;
                editUser = false;
            "
        />
    </div>
</template>
