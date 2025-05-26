<script setup>
import InputError from '@/Components/DashboardComponent/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/DashboardComponent/PrimaryButton.vue';
import SingleLineField from '@/Components/DashboardComponent/SingleLineField.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
});
</script>

<template>
    <section>

        <form
            @submit.prevent="form.patch(route('profile.update'))"
            class="mt-6 space-y-6"
        >
            <div>
                <SingleLineField
                    field-name="Nama"
                    id="name"
                    type="text"
                    v-model="form.name"
                    placeholder="Masukkan nama Anda"
                    :required="true"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <SingleLineField
                    field-name="Email"
                    id="email"
                    type="email"
                    v-model="form.email"
                    placeholder="Masukkan email Anda"
                    :required="true"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div v-if="mustVerifyEmail && user.email_verified_at === null">
                <p class="mt-2 text-sm text-gray-800">
                    Your email address is unverified.
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        Click here to re-send the verification email.
                    </Link>
                </p>

                <div
                    v-show="status === 'verification-link-sent'"
                    class="mt-2 text-sm font-medium text-green-600"
                >
                    A new verification link has been sent to your email address.
                </div>
            </div>

            <div class="flex items-center justify-end gap-4">
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
