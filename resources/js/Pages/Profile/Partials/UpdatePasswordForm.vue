<script setup>
import InputError from '@/Components/DashboardComponent/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/DashboardComponent/PrimaryButton.vue';
import SingleLineField from '@/Components/DashboardComponent/SingleLineField.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value.focus();
            }
            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value.focus();
            }
        },
    });
};
</script>

<template>
    <section>

        <form @submit.prevent="updatePassword" class="mt-6 space-y-6">
            <div>
                <SingleLineField
                    field-name="Password Saat Ini"
                    id="current_password"
                    ref="currentPasswordInput"
                    v-model="form.current_password"
                    type="password"
                    placeholder="Masukkan password saat ini"
                />

                <InputError
                    :message="form.errors.current_password"
                    class="mt-2"
                />
            </div>

            <div>
                <SingleLineField
                    field-name="Password Baru"
                    id="password"
                    ref="passwordInput"
                    v-model="form.password"
                    type="password"
                    placeholder="Masukkan password baru"
                />

                <InputError :message="form.errors.password" class="mt-2" />
            </div>

            <div>
                <SingleLineField
                    field-name="Konfirmasi Password Baru"
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    placeholder="Masukkan konfirmasi password baru"
                />

                <InputError
                    :message="form.errors.password_confirmation"
                    class="mt-2"
                />
            </div>

            <div class="flex items-center gap-4 justify-end">
                <PrimaryButton :disabled="form.processing">Simpan</PrimaryButton>

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
                        Disimpan.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>
