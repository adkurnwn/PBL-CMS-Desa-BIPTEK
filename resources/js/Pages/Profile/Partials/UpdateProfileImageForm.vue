<script setup>
import PrimaryButton from '@/Components/DashboardComponent/PrimaryButton.vue';
import ImageInput from '@/Components/DashboardComponent/ImageInput.vue';
import InputError from '@/Components/DashboardComponent/InputError.vue';
import { useForm, usePage } from '@inertiajs/vue3';

const user = usePage().props.auth.user;

const form = useForm({
    photo_profile: null
});

const updateProfileImage = () => {
    form.post(route('profile.update-image'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <section>

        <form @submit.prevent="updateProfileImage" class="mt-6 space-y-6">
            <div>
                <ImageInput 
                    label="Foto Profil" 
                    v-model="form.photo_profile"
                    imageHeight="200px" 
                    customWidth="100%"
                    :required="false"
                    :currentImage="user.photo_profile"
                />

                <InputError class="mt-2" :message="form.errors.photo_profile" />
            </div>

            <div class="flex items-center gap-4 justify-end">
                <PrimaryButton 
                    :disabled="form.processing || !form.photo_profile"
                >
                    Simpan
                </PrimaryButton>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p
                        v-if="form.recentlySuccessful"
                        class="text-sm text-gray-600"
                    >
                        Foto Profil diperbarui.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>
