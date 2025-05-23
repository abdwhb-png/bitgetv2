<!-- account -->
<div class="modal fade action-sheet" id="accountWallet">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <span>Wallet</span>
                <span class="icon-cancel" data-bs-dismiss="modal"></span>
            </div>
            <ul class="mt-20 pb-16">
                <li data-bs-dismiss="modal">
                    <div
                        class="d-flex justify-content-between align-items-center gap-8 text-large item-check active dom-value">
                        Account 1 <i class="icon icon-check-circle"></i> </div>
                </li>
                <li class="mt-4" data-bs-dismiss="modal">
                    <div class="d-flex  justify-content-between gap-8 text-large item-check dom-value">Account 2<i
                            class="icon icon-check-circle"></i></div>
                </li>
            </ul>
        </div>

    </div>
</div>

<!-- notification -->
<div class="modal fade modalRight" id="notification">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="header fixed-top bg-surface d-flex justify-content-center align-items-center">
                <span class="left" data-bs-dismiss="modal" aria-hidden="true"><i class="icon-left-btn"></i></span>
                <h3>Notification</h3>
            </div>
            <div class="overflow-auto pt-45 pb-16">
                <div class="tf-container">
                    <ul class="mt-12">
                        <li>
                            <a href="#" class="noti-item bg-menuDark">
                                <div class="pb-8 line-bt d-flex">
                                    <p class="text-button fw-6">Cointex to just tick size and trading amount precision
                                        of spots/margins and perpetual swaps</p>
                                    <i class="dot-lg bg-primary"></i>
                                </div>
                                <span class="d-block mt-8">5 minutes ago</span>
                            </a>
                        </li>
                        <li class="mt-12">
                            <a href="#" class="noti-item bg-menuDark">
                                <div class="pb-8 line-bt d-flex">
                                    <p class="text-button fw-6">Cointex to adjust components of several indexes</p>
                                    <i class="dot-lg bg-primary"></i>
                                </div>
                                <span class="d-block mt-8">5 minutes ago</span>
                            </a>
                        </li>
                        <li class="mt-12">
                            <a href="#" class="noti-item bg-menuDark">
                                <div class="pb-8 line-bt d-flex">
                                    <p class="text-button fw-6">Cointex to just tick size and trading amount precision
                                        of spots/margins and perpetual swaps</p>
                                    <i class="dot-lg bg-primary"></i>
                                </div>
                                <span class="d-block mt-8">5 minutes ago</span>
                            </a>
                        </li>
                        <li class="mt-12">
                            <a href="#" class="noti-item bg-menuDark">
                                <div class="pb-8 line-bt">
                                    <p class="text-button fw-6 text-secondary">Cointex to adjust components of several
                                        indexes</p>
                                </div>
                                <span class="d-block mt-8 text-secondary">1 day ago</span>
                            </a>
                        </li>
                        <li class="mt-12">
                            <a href="#" class="noti-item bg-menuDark">
                                <div class="pb-8 line-bt">
                                    <p class="text-button fw-6 text-secondary">Cryptex wallet uses Achain network
                                        service</p>
                                </div>
                                <span class="d-block mt-8 text-secondary">1 day ago</span>
                            </a>
                        </li>
                        <li class="mt-12">
                            <a href="#" class="noti-item bg-menuDark">
                                <div class="pb-8 line-bt">
                                    <p class="text-button fw-6 text-secondary">Cointex to adjust components of several
                                        indexes</p>
                                </div>
                                <span class="d-block mt-8 text-secondary">1 day ago</span>
                            </a>
                        </li>
                    </ul>
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
                <h4>“Cointex” Would Like To Send You Notifications</h4>
                <p class="mt-8 text-large">Notifications may include alerts, sounds, and icon badges. These can be
                    configured in Settings.</p>
            </div>
            <div class="grid-2">
                <a href="#" class="line-r text-center text-button fw-6 p-10 text-secondary btn-hide-modal"
                    data-bs-dismiss="modal">Don’t Allow</a>
                <a href="#" class="text-center text-button fw-6 p-10 text-primary btn-hide-modal"
                    data-bs-toggle="modal" data-bs-target="#notiPrivacy"> Allow</a>
                </a>
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
                    <p>A mobile app privacy policy is a legal statement that must be clear, conspicuous, and consented
                        to by all users. It must disclose how a mobile app gathers, stores, and uses the personally
                        identifiable information it collects from its users.</p>
                    <p>A mobile privacy app is developed and presented to users so that mobile app developers stay
                        compliant with state, federal, and international laws. As a result, they fulfill the legal
                        requirement to safeguard user privacy while protecting the company itself from legal challenges.
                    </p>
                </div>
                <h3 class="mt-12">Authorized Users</h3>
                <p class="mt-4 text-small">
                    A mobile app privacy policy is a legal statement that must be clear, conspicuous, and consented to
                    by all users. It must disclose how a mobile app gathers, stores, and uses the personally
                    identifiable information it collects from its users.
                </p>
                <div class="cb-noti mt-12">
                    <input type="checkbox" class="tf-checkbox" id="cb-ip">
                    <label for="cb-ip">I agree to the Term of service and Privacy policy</label>
                </div>

            </div>
            <div class="mt-20">
                <a href="#" class="tf-btn md primary" data-bs-dismiss="modal">I Accept</a>
            </div>
        </div>
    </div>
</div>
