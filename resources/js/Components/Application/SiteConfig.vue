<template>
    <a
        href="#about"
        data-bs-toggle="modal"
        data-bs-target="#aboutModal"
        class="pt-12 pb-12 mt-4 d-flex justify-content-between align-items-center"
    >
        <h5>About {{ $page.props.siteConfig.appName }}</h5>
        <span class="arr-right"><i class="icon-arr-right"></i></span>
    </a>

    <a
        href="#tcs"
        data-bs-toggle="modal"
        data-bs-target="#tcsModal"
        class="pt-12 pb-12 mt-4 d-flex justify-content-between align-items-center"
    >
        <h5>Terms & Conditions</h5>
        <span class="arr-right"><i class="icon-arr-right"></i></span>
    </a>
</template>
