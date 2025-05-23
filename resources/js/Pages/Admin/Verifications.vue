<script setup>
import { ref, computed } from "vue";
import { usePage } from "@inertiajs/vue3";
import Kycs from "@/Components/Admin/Verifications/Kycs.vue";
import Emails from "@/Components/Admin/Verifications/Emails.vue";

const page = usePage();
const verifications = computed(() => page.props.verifications || {});
const totalCount = computed(() => page.props.totalCount || {});
const pendingCount = computed(() => page.props.pendingCount || {});

const tabValue = ref(
    localStorage.getItem("verificationsActiveTab") ?? "emails"
);

// Ensure tab value is valid
const validTabs = computed(() => Object.keys(verifications.value));
const isValidTab = computed(() => validTabs.value.includes(tabValue.value));

// Set default tab if current is invalid
if (!isValidTab.value && validTabs.value.length > 0) {
    tabValue.value = validTabs.value[0];
}

const changeTab = (value) => {
    if (validTabs.value.includes(value)) {
        localStorage.setItem("verificationsActiveTab", value);
        tabValue.value = value;
    }
};
</script>

<template>
    <AdminLayout title="Verifications">
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
                                v-for="(item, index) in verifications"
                                :key="index"
                                :value="index"
                                class="capitalize"
                            >
                                {{ index }}
                            </Tab>
                        </TabList>
                        <TabPanels v-if="Object.keys(verifications).length > 0">
                            <!-- emails verifications -->
                            <TabPanel
                                value="emails"
                                v-if="verifications.emails"
                            >
                                <Emails
                                    :datas="verifications.emails"
                                    :totalCount="totalCount.emails || 0"
                                    :unverifiedCount="pendingCount.emails || 0"
                                />
                            </TabPanel>

                            <!-- kycs verifications -->
                            <TabPanel value="kycs" v-if="verifications.kycs">
                                <Kycs
                                    :datas="verifications.kycs"
                                    :totalCount="totalCount.kycs || 0"
                                    :unverifiedCount="pendingCount.kycs || 0"
                                />
                            </TabPanel>
                        </TabPanels>
                        <div v-else class="p-6 text-center text-gray-500">
                            <p>No verification data available.</p>
                        </div>
                    </Tabs>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
