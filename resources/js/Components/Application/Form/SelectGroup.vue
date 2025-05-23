<script setup>
import { ref } from "vue";

const props = defineProps({
    label: {
        type: String,
        default: "",
    },
    class: String,
    modelValue: String,
    required: {
        type: Boolean,
        default: true,
    },
    showRequired: {
        type: Boolean,
        default: true,
    },
    placeholder: String,
    data: Object,
});

defineEmits(["update:modelValue"]);

const input = ref(null);

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <label class="label-ip" v-if="label">
        <p class="mb-8 text-small text-capitalize">
            {{ label.replace("_", " ") }}
            <span v-show="showRequired" class="text-danger">{{
                required ? "*" : ""
            }}</span>
        </p>
        <div class="select-wrapper">
            <select
                ref="input"
                :value="modelValue"
                @change="$emit('update:modelValue', $event.target.value)"
                class="tf-select text-white"
                :class="class"
                :required="required"
            >
                <option
                    v-for="(item, index) in data"
                    :key="index"
                    :value="item.value ?? item"
                >
                    {{ item.label ?? item }}
                </option>
            </select>
        </div>
    </label>
    <select
        v-else
        ref="input"
        :value="modelValue"
        @change="$emit('update:modelValue', $event.target.value)"
        class="tf-select text-white"
        :class="class"
        :required="required"
    >
        <option v-for="(item, index) in data" :key="index">
            {{ item }}
        </option>
    </select>
</template>
