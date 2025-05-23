<script setup>
import { ref } from "vue";
import { usePage } from "@inertiajs/vue3";
import Settings from "@/Components/Admin/Dashboard/Settings.vue";
import CustomerServices from "@/Components/Admin/Dashboard/CustomerServices.vue";
import PaymentMethods from "@/Components/Admin/Dashboard/PaymentMethods.vue";
import PaymentProofs from "@/Components/Admin/Dashboard/PaymentProofs.vue";

const page = usePage();

const activeTab = ref(localStorage.getItem("dashboardActiveTab") ?? "pMethods");

const changeTab = (tab) => {
    localStorage.setItem("dashboardActiveTab", tab);
    activeTab.value = tab;
};
</script>

<template>
    <AdminLayout title="Dashboard">
        <ToastError :errors="page.props.errors" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <Tabs
                        :value="activeTab"
                        @update:value="changeTab"
                        scrollable
                    >
                        <TabList>
                            <Tab value="pMethods">Payment Methods</Tab>
                            <Tab value="pProofs">Payment Proofs</Tab>
                            <Tab value="settings">Settings</Tab>
                            <Tab value="cServices">Customer Services</Tab>
                        </TabList>
                        <TabPanels>
                            <!-- payment methods -->
                            <TabPanel value="pMethods">
                                <PaymentMethods :datas="page.props.pMethods" />
                            </TabPanel>

                            <!-- payment proofs -->
                            <TabPanel value="pProofs">
                                <PaymentProofs :datas="page.props.pProofs" />
                            </TabPanel>

                            <!-- settings -->
                            <TabPanel value="settings">
                                <Settings :datas="page.props.setting" />
                            </TabPanel>

                            <!-- customer services -->
                            <TabPanel value="cServices">
                                <CustomerServices
                                    :datas="page.props.cServices"
                                />
                            </TabPanel>
                        </TabPanels>
                    </Tabs>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
