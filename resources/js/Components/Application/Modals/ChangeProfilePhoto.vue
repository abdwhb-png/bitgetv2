<style scoped>
.p-avatar {
    max-width: 100px;
}
</style>
<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import Avatar from "primevue/avatar";
import { showSuccessToast, showFailToast, showLoadingToast } from "vant";

defineProps({
    user: Object,
});

const photoPreview = ref(null);
const photoInput = ref(null);

const form = useForm({
    _method: "PUT",
    photo: null,
});

const selectNewPhoto = () => {
    photoInput.value.click();
};

const updatePhotoPreview = () => {
    const photo = photoInput.value.files[0];

    if (!photo) return;

    const reader = new FileReader();

    reader.onload = (e) => {
        photoPreview.value = e.target.result;
    };

    reader.readAsDataURL(photo);
};

const updatePhoto = () => {
    if (photoInput.value) {
        form.photo = photoInput.value.files[0];
    }

    form.post(route("user.profile-photo.update"), {
        preserveScroll: true,
        onProgress: () =>
            showLoadingToast({
                message: "Loading...",
                forbidClick: true,
            }),
        onSuccess: (page) => {
            clearPhotoFileInput();
            showSuccessToast("Profile photo updated");
        },
        onError: (errors) => {
            showFailToast({
                message: errors.photo,
                wordBreak: "break-word",
            });
        },
    });
};

const clearPhotoFileInput = () => {
    if (photoInput.value?.value) {
        photoInput.value.value = null;
    }
};
</script>

<template>
    <div class="modals">
        <!-- change profile picture -->
        <div
            class="modal fade action-sheet"
            id="filterPicture"
            style="display: none"
            aria-hidden="true"
        >
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <span>Click on the picture to select one</span>
                        <span
                            class="icon-cancel"
                            data-bs-dismiss="modal"
                        ></span>
                    </div>
                    <ul class="mt-20 pb-16">
                        <li @click="selectNewPhoto">
                            <input
                                id="photo"
                                ref="photoInput"
                                type="file"
                                class="d-none"
                                accept="image/jpg, image/jpeg, image/png, image/heic, image/heif"
                                @change="updatePhotoPreview"
                            />

                            <!-- Current Profile Photo -->
                            <div
                                v-show="!photoPreview"
                                class="mt-2 text-center"
                            >
                                <Avatar
                                    :image="user.profile_photo_url"
                                    :alt="user.name"
                                    class="mr-2"
                                    size="xlarge"
                                    shape="circle"
                                />
                            </div>

                            <!-- New Profile Photo Preview -->
                            <div v-show="photoPreview" class="mt-2 text-center">
                                <Avatar
                                    :image="photoPreview"
                                    :alt="user.name"
                                    class="mr-2"
                                    size="xlarge"
                                    shape="circle"
                                />
                            </div>
                        </li>
                        <li
                            v-if="photoPreview"
                            @click="updatePhoto"
                            class="mb-2"
                            data-bs-dismiss="modal"
                        >
                            <div
                                class="d-flex justify-content-between align-items-center gap-8 text-large item-check active"
                            >
                                Save
                                <i class="icon icon-check-circle"></i>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- change profile -->
        <div
            class="modal fade modalCenter"
            id="changeProfile"
            style="display: none"
            aria-hidden="true"
        >
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content modal-sm">
                    <div class="p-16 line-bt">
                        <h4 class="text-center">Change profile picture</h4>
                        <p class="mt-2 text-center text-large">
                            Do you want to change your profile picture?
                        </p>
                    </div>
                    <div class="grid-2">
                        <a
                            href="#"
                            class="line-r text-center text-button fw-6 p-12"
                            data-bs-dismiss="modal"
                            >Cancel</a
                        >
                        <a
                            href="#"
                            class="text-center text-button fw-6 p-12 text-primary"
                            data-bs-toggle="modal"
                            data-bs-target="#filterPicture"
                            >Yes</a
                        >
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
