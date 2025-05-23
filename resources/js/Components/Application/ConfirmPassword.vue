<script setup>
import { ref, reactive, nextTick } from "vue";
import PasswordInputGroup from "@/Components/Application/Form/PasswordInputGroup.vue";
import InputError from "@/Components/Application/Form/InputError.vue";
import { showFailToast } from "vant";

const emit = defineEmits(["confirmed"]);

const props = defineProps({
    title: {
        type: String,
        default: "Confirm Password",
    },
    content: {
        type: String,
        default: "For your security, please confirm your password to continue.",
    },
    button: {
        type: String,
        default: "Confirm",
    },
    alwaysCheck: {
        type: Boolean,
        default: false,
    },
});

const inputPassword = defineModel({
    type: String,
});

const confirmingPassword = ref(false);

const form = reactive({
    password: inputPassword,
    error: "",
    processing: false,
});

const startConfirmingPassword = () => {
    axios.get(route("password.confirmation")).then((response) => {
        if (!props.alwaysCheck && response.data.confirmed) {
            emit("confirmed");
        } else {
            confirmingPassword.value = true;
        }
    });
};

const confirmPassword = () => {
    form.processing = true;

    axios
        .post(route("password.confirm"), {
            password: form.password,
        })
        .then(() => {
            form.processing = false;

            closeModal();
            nextTick().then(() => emit("confirmed"));
        })
        .catch((error) => {
            form.processing = false;
            form.error = error.response.data.errors.password[0];
            showFailToast({
                message: form.error,
                wordBreak: "break-word",
            });
        });
};

const closeModal = () => {
    confirmingPassword.value = false;
    form.password = "";
    form.error = "";
};
</script>

<template>
    <div>
        <span @click="startConfirmingPassword">
            <slot />
        </span>

        <van-dialog
            v-model:show="confirmingPassword"
            @closed="closeModal"
            @confirm="confirmPassword"
            show-cancel-button
            cancel-button-text="Cancel"
            :confirm-button-text="props.button"
            :confirm-button-disabled="form.processing || !form.password"
        >
            <template #title>
                <h4 class="text-center">
                    <i class="pi pi-lock"></i>
                    {{ props.title }}
                </h4>
            </template>
            <div class="p-16 line-bt">
                <p class="mt-12 text-center text-large">
                    {{ props.content }}
                </p>
                <div class="form-group mt-16">
                    <PasswordInputGroup
                        placeholder="Password"
                        v-model="inputPassword"
                        type="password"
                    />
                    <InputError class="mt-2" :message="form.error" />
                </div>
            </div>
        </van-dialog>
    </div>
</template>
