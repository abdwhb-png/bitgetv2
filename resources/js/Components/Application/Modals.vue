<script setup>
import ChangeProfilePhoto from "@/Components/Application/Modals/ChangeProfilePhoto.vue";
import ChangePassword from "@/Components/Application/Modals/ChangePassword.vue";
import MyWallet from "@/Components/Application/Modals/MyWallet.vue";
import Notifications from "@/Components/Application/Modals/Notifications.vue";
import WalletAddreses from "@/Components/Application/Modals/WalletAddreses.vue";
import PaymentMethods from "@/Components/Application/Modals/PaymentMethods.vue";
import BrowserSessions from "@/Components/Application/Modals/BrowserSessions.vue";
import CoinsSearch from "@/Components/Application/Modals/CoinsSearch.vue";
import PaymentProof from "@/Components/Application/Modals/PaymentProof.vue";
import PersonalInformation from "@/Components/Application/Modals/PersonalInformation.vue";
import { logout } from "@/composables/useAuth";
</script>

<template>
    <section id="modals">
        <div v-if="$page.props.auth.user" class="isAuth">
            <!-- my wallet -->
            <MyWallet />

            <!-- coins search -->
            <CoinsSearch />

            <!-- notification -->
            <Notifications />

            <!-- update personal information -->
            <PersonalInformation :user="$page.props.auth.user" />

            <!-- wallet addresses -->
            <WalletAddreses />

            <!-- payment methods -->
            <PaymentMethods />

            <!-- payment proof -->
            <PaymentProof />

            <!-- change profile photo -->
            <ChangeProfilePhoto :user="$page.props.auth.user" />

            <!-- change password -->
            <ChangePassword />

            <!-- browser sessions -->
            <BrowserSessions />

            <!-- logout -->
            <div class="modal fade modalCenter" id="logout">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content modal-sm">
                        <div class="p-16 line-bt">
                            <h4 class="text-center">
                                <i class="pi pi-power-off"></i>
                                Log Out
                            </h4>
                            <p class="mt-12 text-center text-large">
                                Are you sure you want to sign out?
                            </p>
                        </div>
                        <div class="grid-2">
                            <a
                                href="#"
                                class="line-r text-center text-button fw-6 p-10"
                                data-bs-dismiss="modal"
                                >Cancel</a
                            >
                            <a
                                href="javascript:void(0)"
                                data-bs-dismiss="modal"
                                @click="logout(false)"
                                class="text-center text-button fw-6 p-10 text-red"
                                >Log Out</a
                            >
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- support -->
        <div class="modal fade action-sheet" id="supportModal">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <span>Customer Service</span>
                        <span
                            class="icon-cancel"
                            data-bs-dismiss="modal"
                        ></span>
                    </div>
                    <ul class="mt-20 pb-16">
                        <li
                            v-for="(item, index) in $page.props
                                .customerServices"
                            :key="index"
                        >
                            <a
                                :href="item.url"
                                target="_blank"
                                class="d-flex justify-content-between align-items-center gap-8 text-large mb-1 item-check active dom-value"
                            >
                                {{ item.title }}
                                <i
                                    class="icon icon-arr-right"
                                    style="font-size: small"
                                ></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- about -->
        <div class="modal fade modalRight" id="aboutModal">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div
                        class="header fixed-top bg-surface d-flex justify-content-center align-items-center"
                    >
                        <span
                            class="left"
                            data-bs-dismiss="modal"
                            aria-hidden="true"
                            ><i class="icon-left-btn"></i
                        ></span>
                        <h3>About Us</h3>
                    </div>
                    <div class="overflow-auto pt-45 pb-16">
                        <div class="tf-container">
                            <div
                                class="bg-menuDark p-10"
                                v-html="$page.props.siteConfig.about_us"
                            ></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- tcs -->
        <div class="modal fade modalRight" id="tcsModal">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div
                        class="header fixed-top bg-surface d-flex justify-content-center align-items-center"
                    >
                        <span
                            class="left"
                            data-bs-dismiss="modal"
                            aria-hidden="true"
                            ><i class="icon-left-btn"></i
                        ></span>
                        <h3>Terms & Conditions</h3>
                    </div>
                    <div class="overflow-auto pt-45 pb-16">
                        <div class="tf-container">
                            <div
                                class="bg-menuDark p-10"
                                v-html="$page.props.siteConfig.tcs"
                            ></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- noti popup -->
        <div class="modal fade modalCenter" id="modalNoti">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content modal-sm">
                    <div class="p-16 line-bt text-center">
                        <h4>
                            “{{ $page.props.siteConfig.appName }}” Would Like To
                            Send You Notifications
                        </h4>
                        <p class="mt-8 text-large">
                            Notifications may include alerts, sounds, and icon
                            badges. These can be configured in Settings.
                        </p>
                    </div>
                    <div class="grid-2">
                        <a
                            href="#"
                            class="line-r text-center text-button fw-6 p-10 text-secondary btn-hide-modal"
                            data-bs-dismiss="modal"
                            >Don’t Allow</a
                        >
                        <a
                            href="#"
                            class="text-center text-button fw-6 p-10 text-primary btn-hide-modal"
                            data-bs-toggle="modal"
                            data-bs-target="#notiPrivacy"
                        >
                            Allow</a
                        >
                    </div>
                </div>
            </div>
        </div>

        <!-- noti popup 2-->
        <div class="modal fade modalCenter" id="notiPrivacy">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content p-20">
                    <div class="heading">
                        <h3>Privacy</h3>
                        <div class="mt-4 text-small">
                            <p>
                                A mobile app privacy policy is a legal statement
                                that must be clear, conspicuous, and consented
                                to by all users. It must disclose how a mobile
                                app gathers, stores, and uses the personally
                                identifiable information it collects from its
                                users.
                            </p>
                            <p>
                                A mobile privacy app is developed and presented
                                to users so that mobile app developers stay
                                compliant with state, federal, and international
                                laws. As a result, they fulfill the legal
                                requirement to safeguard user privacy while
                                protecting the company itself from legal
                                challenges.
                            </p>
                        </div>
                        <h3 class="mt-12">Authorized Users</h3>
                        <p class="mt-4 text-small">
                            A mobile app privacy policy is a legal statement
                            that must be clear, conspicuous, and consented to by
                            all users. It must disclose how a mobile app
                            gathers, stores, and uses the personally
                            identifiable information it collects from its users.
                        </p>
                        <div class="cb-noti mt-12">
                            <input
                                type="checkbox"
                                class="tf-checkbox"
                                id="cb-ip"
                            />
                            <label for="cb-ip"
                                >I agree to the Term of service and Privacy
                                policy</label
                            >
                        </div>
                    </div>
                    <div class="mt-20">
                        <a
                            href="#"
                            class="tf-btn md primary"
                            data-bs-dismiss="modal"
                            >I Accept</a
                        >
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
