<style scoped>
.tf-container {
    max-width: 500px;
}
</style>

<script setup>
import { Link } from "@inertiajs/vue3";

defineProps({
    title: String,
});
</script>

<template>
    <div>
        <Modals />
        <div class="header fixed-top bg-surface">
            <Link href="/" class="left back-btn"
                ><i class="icon-home2 fs-20"></i
            ></Link>
        </div>
        <div class="pt-45 pb-20">
            <div class="tf-container">
                <div class="mt-32">
                    <h2 class="text-center">{{ title }}</h2>
                </div>

                <main>
                    <slot />
                </main>

                <p class="mt-30 text-center text-small">
                    <slot name="bottom-text" />
                </p>
            </div>
        </div>
    </div>
</template>
