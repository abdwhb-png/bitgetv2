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
        default: "password",
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
const isPasswordVisible = ref(false);

defineExpose({ focus: () => input.value.focus() });

const showHidePassword = () => {
    isPasswordVisible.value = !isPasswordVisible.value;
    console.log("ok", isPasswordVisible.value);
};
</script>

<template>
    <label class="label-ip" v-if="label">
        <p class="mb-8 text-small">
            {{ label }}
            <span v-show="showRequired" class="text-danger">{{
                required ? "*" : ""
            }}</span>
        </p>
        <div class="box-auth-pass">
            <input
                ref="input"
                :value="modelValue"
                :type="isPasswordVisible ? 'text' : 'password'"
                :class="class"
                :required="required"
                :autofocus="autofocus"
                :autocomplete="label ? label.toLowerCase() : 'off'"
                :placeholder="placeholder || `Enter ${label}`"
                @input="$emit('update:modelValue', $event.target.value)"
            />
            <span class="show-pass" @click="showHidePassword()">
                <i class="pi pi-eye" v-show="isPasswordVisible"></i>
                <i class="pi pi-eye-slash" v-show="!isPasswordVisible"></i>
            </span>
        </div>
    </label>
    <div class="box-auth-pass" v-else>
        <input
            ref="input"
            :value="modelValue"
            :type="isPasswordVisible ? 'text' : 'password'"
            :class="class"
            :required="required"
            :autofocus="autofocus"
            :autocomplete="label ? label.toLowerCase() : 'off'"
            :placeholder="placeholder || `Enter ${label}`"
            @input="$emit('update:modelValue', $event.target.value)"
        />
        <span class="show-pass" @click="showHidePassword">
            <i class="pi pi-eye" v-show="isPasswordVisible"></i>
            <i class="pi pi-eye-slash" v-show="!isPasswordVisible"></i>
        </span>
    </div>
</template>
