<script>
export default {
    props: {
        status: Number | String,
        label: String,
    },
};
</script>
<template>
    <span v-if="status == 1" class="border text-success border-success">
        {{ label || "Verified" }}
        <i class="icon icon-check"></i>
    </span>
    <span v-else-if="status == 0" class="border text-warning border-warning">
        {{ label || "Pending" }}
        <i class="icon icon-clockwise"></i>
    </span>
    <span v-else-if="status == -1" class="border text-danger border-danger">
        {{ label || "Rejected" }}
        <i class="icon icon-close"></i>
    </span>
    <span v-else class="border text-secondary border-secondary">
        {{ label || "Not Provided" }}
        <i class="icon icon-close"></i>
    </span>
</template>
