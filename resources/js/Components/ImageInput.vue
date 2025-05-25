<script setup>
import { ref, onUnmounted } from "vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/DashboardComponent/InputError.vue";

const props = defineProps({
    modelValue: {
        type: [File, null],
        default: null,
    },
    label: {
        type: String,
        required: true,
    },
    error: {
        type: String,
        default: "",
    },
    currentImage: {
        type: String,
        default: null,
    },
    imageClass: {
        type: String,
        default: "h-32 w-32 object-cover rounded-lg",
    },
    accept: {
        type: String,
        default: "image/png, image/jpeg, image/jpg",
    },
});

const emit = defineEmits(["update:modelValue"]);
const imagePreview = ref(null);

const handleImageChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        emit("update:modelValue", file);
        imagePreview.value = URL.createObjectURL(file);
    }
};

// Cleanup preview URL when component unmounts
onUnmounted(() => {
    if (imagePreview.value) {
        URL.revokeObjectURL(imagePreview.value);
    }
});
</script>

<template>
    <div>
        <InputLabel :for="label" :value="label" />
        <input
            :id="label"
            type="file"
            @input="handleImageChange"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            :accept="accept"
        />
        <div v-if="imagePreview || currentImage" class="mt-2">
            <img
                :src="imagePreview || `/storage/${currentImage}`"
                :class="imageClass"
            />
        </div>
        <InputError :message="error" class="mt-2" />
    </div>
</template>
