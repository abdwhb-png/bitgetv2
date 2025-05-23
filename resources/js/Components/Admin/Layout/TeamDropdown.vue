<template>
    <Dropdown v-if="hasTeamFeatures" align="right" width="60">
        <template #trigger>
            <span class="inline-flex rounded-md">
                <button
                    type="button"
                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white dark:bg-neutral-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150"
                >
                    {{ currentTeam.name }}

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
            <div class="w-60">
                <!-- Team Management -->
                <div class="block px-4 py-2 text-xs text-gray-400">
                    Manage Team
                </div>

                <!-- Team Settings -->
                <DropdownLink :href="route('teams.show', currentTeam)">
                    Team Settings
                </DropdownLink>

                <DropdownLink
                    v-if="canCreateTeams"
                    :href="route('teams.create')"
                >
                    Create New Team
                </DropdownLink>

                <!-- Team Switcher -->
                <template v-if="allTeams.length > 1">
                    <div
                        class="border-t border-gray-200 dark:border-gray-600"
                    />

                    <div class="block px-4 py-2 text-xs text-gray-400">
                        Switch Teams
                    </div>

                    <template v-for="team in allTeams" :key="team.id">
                        <form @submit.prevent="switchToTeam(team)">
                            <DropdownLink as="button">
                                <div class="flex items-center">
                                    <svg
                                        v-if="team.id == currentTeamId"
                                        class="me-2 h-5 w-5 text-green-400"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.5"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                        />
                                    </svg>

                                    <div>{{ team.name }}</div>
                                </div>
                            </DropdownLink>
                        </form>
                    </template>
                </template>
            </div>
        </template>
    </Dropdown>
</template>

<script setup>
import { router } from "@inertiajs/vue3";
import Dropdown from "@/Components/Admin/Dropdown.vue";
import DropdownLink from "@/Components/Admin/DropdownLink.vue";

defineProps({
    hasTeamFeatures: {
        type: Boolean,
        required: true,
    },
    canCreateTeams: {
        type: Boolean,
        required: true,
    },
    currentTeam: {
        type: Object,
        required: true,
    },
    currentTeamId: {
        type: Number,
        required: true,
    },
    allTeams: {
        type: Array,
        required: true,
    },
});

const switchToTeam = (team) => {
    router.put(
        route("current-team.update"),
        {
            team_id: team.id,
        },
        {
            preserveState: false,
        }
    );
};
</script>
