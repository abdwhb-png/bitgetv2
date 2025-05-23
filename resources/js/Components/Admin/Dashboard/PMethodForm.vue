<template>
    <div class="md:flex gap-4">
        <FloatLabel variant="in" class="mb-4">
            <InputText :id="'name'" v-model="form.name" fluid />
            <label :for="'name'">Name</label>
        </FloatLabel>

        <FloatLabel variant="in" class="mb-4">
            <InputText :id="'symbol'" v-model="form.symbol" fluid />
            <label :for="'symbol'">Symbol</label>
        </FloatLabel>
    </div>

    <FloatLabel variant="in" class="mb-4">
        <InputText :id="'address'" v-model="form.address" fluid />
        <label :for="'address'">Address or Value</label>
    </FloatLabel>

    <div class="flex justify-end gap-4">
        <Button
            v-if="item"
            label="Delete"
            icon="pi pi-trash"
            outlined
            size="small"
            severity="danger"
            @click="deleteItem(item.id)"
        />
        <Button
            label="Save"
            icon="pi pi-save"
            :loading="form.processing"
            @click="submit(item)"
        />
    </div>
</template>

<script setup>
import { useForm, usePage } from "@inertiajs/vue3";
import { useToast } from "primevue/usetoast";

const emits = defineEmits(["updated", "created"]);

const props = defineProps({
    item: Object,
});

const page = usePage();
const toast = useToast();

const form = useForm({
    name: props.item?.name || null,
    symbol: props.item?.symbol || null,
    address: props.item?.address || null,
});

const submit = (item) => {
    const url = props.item
        ? route(page.props.routePrefix + "pmethod.update", item.id)
        : route(page.props.routePrefix + "pmethod.store");

    form.transform((data) => ({
        _method: props.item ? "PUT" : "POST",
        ...data,
    })).post(url, {
        onSuccess: (page) => {
            toast.add({
                severity: "success",
                summary: page.props.flash.status,
                life: 5000,
            });

            form.reset();
            emits(props.item ? "updated" : "created");
        },
    });
};

const deleteItem = (id) => {
    if (
        confirm(
            "Are you sure you want to delete this payment method? " +
                "All transactions and their payments will be deleted as well."
        )
    ) {
        form.delete(route(page.props.routePrefix + "pmethod.destroy", id), {
            onSuccess: (page) => {
                toast.add({
                    severity: "success",
                    summary: page.props.flash.status,
                    life: 5000,
                });
            },
        });
    }
};
</script>
