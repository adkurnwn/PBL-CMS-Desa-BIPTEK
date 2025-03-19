<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { ref, computed, onUnmounted } from "vue";

const props = defineProps({
    news: {
        type: Object,
        required: true,
    },
});

// Add a computed property for current image
const currentImage = computed(() => {
    return props.news.images?.length > 0 ? props.news.images[0] : null;
});

const form = useForm({
    title: props.news.title,
    category: props.news.category,
    content: props.news.content,
    image: null,
    video: null,
    is_moderate: props.news.is_moderate, // Change from is_moderated
    _method: "PUT",
});

const imagePreview = ref(null);

const handleImageChange = (e) => {
    const file = e.target.files[0];
    form.image = file;
    imagePreview.value = URL.createObjectURL(file);
};

const handleVideoChange = (e) => {
    form.video = e.target.files[0];
};

const submit = () => {
    form.post(route("dashboard.news.update", props.news.id), {
        preserveScroll: true,
    });
};

// Cleanup preview URL when component unmounts
onUnmounted(() => {
    if (imagePreview.value) {
        URL.revokeObjectURL(imagePreview.value);
    }
});
</script>

<template>
    <Head title="Edit News" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Edit News
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form
                            @submit.prevent="submit"
                            enctype="multipart/form-data"
                        >
                            <div class="grid grid-cols-2 gap-4">
                                <!-- Name Field -->
                                <div>
                                    <InputLabel for="title" value="Title" />
                                    <TextInput
                                        id="title"
                                        v-model="form.title"
                                        type="text"
                                        class="mt-1 block w-full"
                                        required
                                    />
                                    <InputError
                                        :message="form.errors.title"
                                        class="mt-2"
                                    />
                                </div>

                                <div>
                                    <InputLabel
                                        for="category"
                                        value="Category"
                                    />
                                    <select
                                        id="category"
                                        v-model="form.category"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    >
                                        <option value="news">
                                            Berita Umum
                                        </option>
                                        <option value="announcement">
                                            Pemberitahuan
                                        </option>
                                        <option value="event">Kegiatan</option>
                                        <option value="education">
                                            Pendidikan
                                        </option>
                                        <option value="health">
                                            Kesehatan
                                        </option>
                                        <option value="Tourist">
                                            Pariwisata
                                        </option>
                                    </select>
                                    <InputError
                                        :message="form.errors.category"
                                        class="mt-2"
                                    />
                                </div>

                                <div>
                                    <InputLabel for="content" value="Content" />
                                    <textarea
                                        id="content"
                                        v-model="form.content"
                                        type="text"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        required
                                    />
                                    <InputError
                                        :message="form.errors.content"
                                        class="mt-2"
                                    />
                                </div>

                                <div>
                                    <InputLabel for="image" value="Image" />
                                    <!-- Current Image Preview -->
                                    <div v-if="currentImage" class="mb-2">
                                        <p class="text-sm text-gray-600 mb-1">
                                            Current Image:
                                        </p>
                                        <img
                                            :src="`/storage/${currentImage.path}`"
                                            :alt="currentImage.alt"
                                            class="h-32 w-auto object-cover rounded-lg"
                                        />
                                    </div>
                                    <!-- New Image Input -->
                                    <input
                                        id="image"
                                        type="file"
                                        @input="handleImageChange"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        accept="image/png, image/jpeg, image/jpg"
                                    />
                                    <!-- New Image Preview -->
                                    <div v-if="imagePreview" class="mt-2">
                                        <p class="text-sm text-gray-600 mb-1">
                                            New Image Preview:
                                        </p>
                                        <img
                                            :src="imagePreview"
                                            class="h-32 w-auto object-cover rounded-lg"
                                        />
                                    </div>
                                    <InputError
                                        :message="form.errors.image"
                                        class="mt-2"
                                    />
                                </div>

                                <div>
                                    <InputLabel for="video" value="Video" />
                                    <input
                                        id="video"
                                        type="file"
                                        @input="handleVideoChange"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        accept="video/mp4, video/ogg, video/webm"
                                    />
                                    <InputError
                                        :message="form.errors.video"
                                        class="mt-2"
                                    />
                                </div>

                                <div>
                                    <div class="flex-row items-center">
                                        <InputLabel
                                            for="is_moderated"
                                            value="Is Moderated"
                                        />
                                        <input
                                            id="is_moderated"
                                            v-model="form.is_moderated"
                                            type="checkbox"
                                            class="rounded border-gray-300 mt-2"
                                        />
                                    </div>
                                    <InputError
                                        :message="form.errors.is_moderated"
                                        class="mt-2"
                                    />
                                </div>
                            </div>

                            <div class="mt-6">
                                <PrimaryButton :disabled="form.processing">
                                    Edit News
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
