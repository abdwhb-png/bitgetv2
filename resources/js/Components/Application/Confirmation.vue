<script>
import { Link } from "@inertiajs/vue3";

export default {
    props: {
        status: String,
        content: {
            type: String,
            default: "Everything went well",
        },
        button: {
            type: String,
            default: "Continue",
        },
        url: {
            type: String,
            default: "/",
        },
    },
    components: { Link },
};
</script>

<template>
    <div class="bg-camera" v-if="status == 'success'">
        <div class="tf-container">
            <div class="pt-30 pb-30">
                <div class="success_box">
                    <div class="icon-1 ani3">
                        <span
                            class="circle-box lg bg-circle check-icon bg-primary"
                        ></span>
                    </div>
                    <div class="icon-2 ani5">
                        <span class="circle-box md bg-primary"></span>
                    </div>
                    <div class="icon-3 ani8">
                        <span class="circle-box md bg-primary"></span>
                    </div>
                    <div class="icon-4 ani2">
                        <span class="circle-box sm bg-primary"></span>
                    </div>
                    <h2 class="text-surface text-center">Successful!</h2>
                    <div
                        class="text-large text-center mt-8"
                        v-html="content"
                    ></div>
                    <Link :href="url" class="tf-btn lg primary mt-40">{{
                        button
                    }}</Link>
                </div>
            </div>
        </div>
    </div>
</template>
