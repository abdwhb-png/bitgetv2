<template>
    <div
        :class="{
            block: showingNavigationDropdown,
            hidden: !showingNavigationDropdown,
        }"
        class="sm:hidden"
    >
        <div class="pt-2 pb-3 space-y-1">
            <ResponsiveNavLink
                v-for="(item, index) in navLinks"
                :key="index"
                :href="item.url || route(item.route)"
                :active="route().current(item.route)"
            >
                <div class="flex items-center">
                    <i v-if="item.icon" class="mr-2" :class="item.icon"></i>
                    <span class="text-capitalize">
                        {{ item.title ?? item.name }}
                        <Badge
                            v-if="
                                item.name === 'notifications' && unreadCount > 0
                            "
                            :value="unreadCount"
                            severity="danger"
                            size="small"
                        />
                    </span>
                </div>
            </ResponsiveNavLink>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="flex items-center px-4">
                <div v-if="managesProfilePhotos" class="shrink-0 me-3">
                    <img
                        class="h-10 w-10 rounded-full object-cover"
                        :src="profilePhotoUrl"
                        :alt="userName"
                    />
                </div>

                <div>
                    <div
                        class="font-medium text-base text-gray-800 dark:text-gray-200"
                    >
                        {{ userName }}
                    </div>
                    <div class="font-medium text-sm text-gray-500">
                        {{ userEmail }}
                    </div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <slot name="responsive-nav-links"></slot>
            </div>
        </div>
    </div>
</template>

<script setup>
import { inject } from "vue";
import ResponsiveNavLink from "@/Components/Admin/ResponsiveNavLink.vue";

defineProps({
    navLinks: {
        type: Array,
        required: true,
    },
    showingNavigationDropdown: {
        type: Boolean,
        required: true,
    },
    unreadCount: {
        type: Number,
        default: 0,
    },
    managesProfilePhotos: {
        type: Boolean,
        default: false,
    },
    profilePhotoUrl: {
        type: String,
        default: "",
    },
    userName: {
        type: String,
        default: "",
    },
    userEmail: {
        type: String,
        default: "",
    },
});
</script>

<style scoped>
.flex {
    display: flex;
    align-items: center;
}

.mr-2 {
    margin-right: 0.5rem;
}
</style>
