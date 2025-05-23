<script setup>
import { ref, onMounted } from "vue";
import { usePage } from "@inertiajs/vue3";
import { useToast } from "primevue/usetoast";
import { useAdminStore } from "@/stores/admin";
import Kycs from "@/Components/Admin/Verifications/Kycs.vue";
import Emails from "@/Components/Admin/Verifications/Emails.vue";

const page = usePage();
const pageName = "Verifications";

const toast = useToast();
const adminStore = useAdminStore();
const loading = ref(true);

const verifications = ref([]);

const tabValue = ref(
    localStorage.getItem("verificationsActiveTab") ?? "emails"
);

const changeTab = (value) => {
    localStorage.setItem("verificationsActiveTab", value);
    tabValue.value = value;
};

onMounted(async () => {
    await adminStore.fetchVerifications(toast);
    verifications.value = adminStore.getData("verifications");
    loading.value = adminStore.loading("verifications");
});
</script>

<template>
    <AdminLayout :title="pageName">
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-400 leading-tight"
            >
                {{ pageName }}
            </h2>
        </template>

        <ToastError :errors="page.props.errors" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <Tabs
                        :value="tabValue"
                        scrollable
                        @update:value="changeTab"
                    >
                        <TabList>
                            <Tab
                                v-for="(item, index) in Object.keys(
                                    verifications
                                )"
                                :key="index"
                                :value="item"
                            >
                                <span class="uppercase">{{ item }}</span>
                            </Tab>
                        </TabList>
                        <TabPanels>
                            <!-- emails verifications -->
                            <TabPanel value="emails">
                                <Emails
                                    :datas="
                                        adminStore.data.verifications.emails
                                    "
                                    :loading
                                />
                            </TabPanel>

                            <!-- kycs verifications -->
                            <TabPanel value="kycs">
                                <Kycs
                                    :datas="adminStore.data.verifications.kycs"
                                    :loading
                                />
                            </TabPanel>
                        </TabPanels>
                    </Tabs>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
