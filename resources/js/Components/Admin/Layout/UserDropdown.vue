<template>
    <Dropdown align="right" width="48">
        <template #trigger>
            <span class="inline-flex rounded-md">
                <button
                    type="button"
                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white dark:bg-neutral-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150"
                >
                    {{ userName }}

                    <svg
                        class="ms-2 -me-0.5 h-4 w-4"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            clip-rule="evenodd"
                        />
                    </svg>
                </button>
            </span>
        </template>

        <template #content>
            <DropdownLink
                :href="route('profile.show')"
                :active="route().current('profile.show')"
            >
                Profile
            </DropdownLink>

            <DropdownLink
                v-if="hasApiFeatures"
                :href="route('api-tokens.index')"
                :active="route().current('api-tokens.index')"
            >
                API Tokens
            </DropdownLink>

            <div class="border-t border-gray-100 dark:border-gray-600"></div>

            <!-- Authentication -->
            <form @submit.prevent="logout">
                <DropdownLink as="button"> Log Out </DropdownLink>
            </form>
        </template>
    </Dropdown>
</template>

<script setup>
import { router } from "@inertiajs/vue3";
import Dropdown from "@/Components/Admin/Dropdown.vue";
import DropdownLink from "@/Components/Admin/DropdownLink.vue";

defineProps({
    userName: {
        type: String,
        required: true,
    },
    hasApiFeatures: {
        type: Boolean,
        default: false,
    },
});

const logout = () => {
    router.post(route("logout"));
};
</script>
