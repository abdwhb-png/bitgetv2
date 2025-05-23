<script setup>
import { ref } from "vue";

const props = defineProps({
    label: {
        type: String,
        default: "",
    },
    class: String,
    modelValue: String,
    placeholder: String,
    type: {
        type: String,
        default: "text",
    },
    required: {
        type: Boolean,
        default: true,
    },
    showRequired: {
        type: Boolean,
        default: true,
    },
    autofocus: {
        type: Boolean,
        default: false,
    },
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
        <input
            ref="input"
            :value="modelValue"
            :type="type"
            :class="class"
            :required="required"
            :autofocus="autofocus"
            :autocomplete="label ? label.toLowerCase() : 'off'"
            :placeholder="placeholder || `Enter ${label}`"
            @input="$emit('update:modelValue', $event.target.value)"
        />
    </label>
    <input
        v-else
        ref="input"
        :value="modelValue"
        :type="type"
        :class="class"
        :required="required"
        :autofocus="autofocus"
        :autocomplete="label ? label.toLowerCase() : 'off'"
        :placeholder="placeholder || `Enter ${label}`"
        @input="$emit('update:modelValue', $event.target.value)"
    />
</template>
