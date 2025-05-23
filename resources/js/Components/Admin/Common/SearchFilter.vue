<template>
    <div class="flex flex-col space-y-0 5">
        <div class="flex items-center">
            <div class="flex gap-2 w-full bg-white rounded shadow pl-2">
                <DropdownMenu>
                    <DropdownMenuTrigger>
                        <Button
                            raised
                            outlined
                            size="small"
                            :severity="
                                dataFilters[filterKey] ? 'info' : 'secondary'
                            "
                        >
                            {{ dataFilters[filterKey] ? "Filtered" : "Filter" }}
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent>
                        <DropdownMenuLabel
                            >Filter by : {{ filterKey }}
                        </DropdownMenuLabel>
                        <DropdownMenuLabel v-if="slots.filterContent">
                            <ResetBtn @reset="emits('reset')" />
                        </DropdownMenuLabel>
                        <DropdownMenuSeparator />
                        <slot name="filterContent" />
                    </DropdownMenuContent>
                </DropdownMenu>
                <input
                    class="relative px-6 py-3 w-full rounded-r focus:shadow-outline border-0"
                    autocomplete="off"
                    type="text"
                    name="search"
                    placeholder="Search…"
                    :value="modelValue"
                    ref="searchInput"
                    @input="$emit('update:modelValue', $event.target.value)"
                />
            </div>
            <ResetBtn
                v-if="modelValue"
                :class="{ underline: modelValue }"
                @reset="reset"
            />
        </div>
        <ProgressBar v-if="loading" mode="indeterminate" style="height: 5px" />
    </div>
</template>

<script setup>
import { ref, useSlots } from "vue";
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuRadioItem,
    DropdownMenuTrigger,
} from "@/Components/ui/dropdown-menu";
import ResetBtn from "./ResetBtn.vue";

const emits = defineEmits(["update:modelValue", "reset", "reset:filterKey"]);
const slots = useSlots();

const props = defineProps({
    modelValue: String,
    maxWidth: {
        type: Number,
        default: 300,
    },
    loading: Boolean,
    filterKey: String,
    dataFilters: Object,
});

const searchInput = ref();

const reset = () => {
    searchInput.value.value = null;
    emits("update:modelValue", null);
};
</script>
