<style>
.p-dialog-content {
    padding-top: 5px;
}
</style>
<template>
    <div class="">
        <ConfirmsPassword @confirmed="passwordConfirmed = true">
            <Button
                type="button"
                icon="pi pi-lock"
                label="Manage Admins"
                size="small"
                :disabled="!Object.keys(admins).length"
                @click="toggle"
            />
        </ConfirmsPassword>

        <Popover ref="op">
            <div v-if="passwordConfirmed" class="max-w-2xl">
                <div>
                    <span class="font-medium block mb-2"
                        >Here is the admins list</span
                    >
                    <Select
                        v-model="selectedAdmin"
                        :options="admins"
                        optionLabel="full_name"
                        placeholder="Select an admin"
                        fluid
                        checkmark
                        :highlightOnSelect="true"
                        filter
                        :filter-fields="['full_name', 'email']"
                    >
                        <template #option="slotProps">
                            <div class="flex justify-between gap-2">
                                <span>{{
                                    slotProps.option.full_name + " :"
                                }}</span>
                                <span class="text-sky-500">{{
                                    slotProps.option.email
                                }}</span>
                            </div>
                        </template>
                        <template #dropdownicon>
                            <i class="pi pi-users" />
                        </template>
                    </Select>
                </div>

                <div class="my-2" v-if="selectedAdmin">
                    <div class="flex flex-wrap items-center gap-2 mb-2">
                        Role:
                        <Tag
                            severity="secondary"
                            v-for="(item, index) in selectedAdmin.roles"
                            :key="item.name"
                            >{{ item.name }}</Tag
                        >
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        Permissions:
                        <Tag
                            severity="secondary"
                            v-for="(item, index) in selectedAdmin.permissions"
                            :key="item.name"
                            >{{ item.name }}
                        </Tag>
                    </div>
                </div>

                <div class="flex flex-wrap justify-end gap-2 border-t pt-2">
                    <Button
                        v-for="(item, index) in actionButtons"
                        :key="index"
                        :severity="item.severity || 'secondary'"
                        :label="item.label"
                        :icon="item.icon"
                        size="small"
                        :disabled="!selectedAdmin"
                        @click="next(item.action)"
                    />
                </div>
            </div>
        </Popover>

        <Dialog
            v-model:visible="showDialog"
            maximizable
            modal
            :style="{ width: '50rem' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
        >
            <template #header>
                <h3 class="text-lg font-medium text-sky-900 dark:text-sky-700">
                    Edit {{ form.section }}:
                    <span class="underline text-sky-600 dark:text-sky-500">{{
                        selectedAdmin.full_name
                    }}</span>
                    ({{ selectedAdmin.email }})
                </h3>
            </template>

            <!-- Roles -->
            <PickList
                v-if="form.section == 'roles'"
                v-model="rolesList"
                dataKey="id"
                breakpoint="1400px"
                :show-source-controls="false"
                :show-target-controls="false"
            >
                <template #sourceheader>
                    <div class="bg-gray-100 p-2 font-bold">
                        Non Attributed Roles
                    </div>
                </template>
                <template #targetheader>
                    <div class="bg-gray-100 p-2 font-bold">
                        Attributed Roles
                    </div>
                </template>

                <template #option="{ option }">
                    <Tag :value="option.name" severity="secondary" />
                </template>
            </PickList>

            <!-- Permissions -->
            <PickList
                v-if="form.section == 'permissions'"
                v-model="permsList"
                dataKey="id"
                breakpoint="1400px"
                :show-source-controls="false"
                :show-target-controls="false"
            >
                <template #sourceheader>
                    <div class="bg-gray-100 p-2 font-bold">
                        Non Attributed Permissions
                    </div>
                </template>
                <template #targetheader>
                    <div class="bg-gray-100 p-2 font-bold">
                        Attributed Permissions
                    </div>
                </template>

                <template #option="{ option }">
                    <Tag :value="option.name" severity="secondary" />
                </template>
            </PickList>

            <!-- Password -->
            <div
                v-if="form.section == 'password'"
                class="md:flex justify-around gap-4"
            >
                <FloatLabel variant="on" class="mb-4 md:w-1/2">
                    <Password
                        id="password"
                        v-model="form.password"
                        toggleMask
                        fluid
                    />
                    <label for="password">New Password</label>
                </FloatLabel>

                <FloatLabel variant="on" class="mb-4 md:w-1/2">
                    <Password
                        fluid
                        id="password_confirmation"
                        v-model="form.password_confirmation"
                        toggleMask
                    />
                    <label for="password_confirmation"
                        >Confirm New Password</label
                    >
                </FloatLabel>
            </div>

            <div v-if="form.section == 'delete'">
                <Message severity="error" icon="pi pi-exclamation-triangle">
                    Are you sure you want to delete this admin?</Message
                >
            </div>

            <div class="flex justify-center mt-5">
                <Button
                    label="Confirm"
                    class="w-2/3"
                    :loading="form.processing"
                    @click="submit"
                />
            </div>
        </Dialog>
    </div>
</template>

<script setup>
import { usePage, useForm } from "@inertiajs/vue3";
import { ref, onBeforeMount } from "vue";
import { useToast } from "primevue/usetoast";
import ConfirmsPassword from "@/Components/Admin/ConfirmsPassword.vue";

const page = usePage();
const toast = useToast();

const passwordConfirmed = ref(false);
const op = ref();
const actionButtons = [
    {
        label: "Roles",
        severity: "help",
        icon: "pi pi-users",
        action: "roles",
    },
    {
        label: "Permissions",
        severity: "help",
        icon: "pi pi-users",
        action: "permissions",
    },
    {
        label: "Password",
        icon: "pi pi-key",
        severity: "contrast",
        action: "password",
    },
    {
        label: "Delete",
        icon: "pi pi-trash",
        severity: "danger",
        action: "delete",
    },
];

const admins = ref([]);
const selectedAdmin = ref(null);

const allRoles = ref([]);
const rolesList = ref(null);

const allPerms = ref([]);
const permsList = ref(null);

const showDialog = ref(false);

const form = useForm({
    section: null,
    password: null,
    password_confirmation: null,
});

const toggle = (event) => {
    op.value.toggle(event);
};

const submit = () => {
    var newFormData = {};
    var url = null;

    if (form.section == "roles") {
        newFormData = {
            roles: rolesList.value[1].map((role) => role.name),
        };
        url = route(
            page.props.routePrefix + "roles-perms.update",
            selectedAdmin.value.email
        );
    }

    if (form.section == "permissions") {
        newFormData = {
            permissions: permsList.value[1].map((perm) => perm.name),
        };
        url = route(
            page.props.routePrefix + "roles-perms.update",
            selectedAdmin.value.email
        );
    }

    if (form.section == "password") {
        url = route("user.password.update", selectedAdmin.value.email);
    }

    if (form.section == "delete") {
        url = route("user.destroy", selectedAdmin.value.email);
    }

    form.transform((data) => ({
        ...data,
        ...newFormData,
        _method: form.section == "delete" ? "DELETE" : "PUT",
    }));

    if (!url) {
        return;
    }

    form.post(url, {
        onSuccess: (page) => {
            toast.add({
                severity: "success",
                summary:
                    "Action performed successfully for " +
                    selectedAdmin.value.full_name,
                detail:
                    page.props.flash.status ||
                    "The " + form.section + " has/have been upated",
                life: 5000,
            });
        },
        onFinish: async () => {
            form.reset();
            showDialog.value = false;

            selectedAdmin.value = null;
            rolesList.value = null;
            permsList.value = null;

            await fetchAdmins();
            await fetchRolesPerms();
        },
    });
};

function next(paramAction) {
    if (paramAction === "roles") {
        const unselectedRoles = allRoles.value.filter(
            (role) => !selectedAdmin.value.roles.some((r) => r.id === role.id)
        );
        rolesList.value = [unselectedRoles, selectedAdmin.value.roles];
    }

    if (paramAction === "permissions") {
        const unselectedPerms = allPerms.value.filter(
            (perm) =>
                !selectedAdmin.value.permissions.some((p) => p.id === perm.id)
        );
        permsList.value = [unselectedPerms, selectedAdmin.value.permissions];
    }

    if (paramAction === "paswword") {
    }

    if (paramAction === "delete") {
    }

    form.section = paramAction;
    showDialog.value = true;
}

async function fetchAdmins() {
    admins.value = [];
    await axios
        .get(route(page.props.routePrefix + "admins.index"))
        .then((response) => {
            if (response.data) {
                Object.values(response.data).forEach((value) => {
                    admins.value.push(value);
                });
            }
        })
        .catch((error) => {
            toast.add({
                severity: "error",
                summary: "Error while fetching admins list",
                detail: error.response?.data.message || error.message,
            });
        });
}

async function fetchRolesPerms() {
    allRoles.value = [];
    allPerms.value = [];
    await axios
        .get(route(page.props.routePrefix + "roles-perms.index"))
        .then((response) => {
            allRoles.value = response.data.roles || [];
            allPerms.value = response.data.permissions || [];
        })
        .catch((error) => {
            toast.add({
                severity: "error",
                summary: "Error while fetching roles & permissions list",
                detail: error.response.data.message,
            });
        });
}

onBeforeMount(async () => {
    await fetchAdmins();
    await fetchRolesPerms();
});
</script>
