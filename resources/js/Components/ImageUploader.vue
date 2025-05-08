<script setup>
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import { ref, computed } from "vue";

const props = defineProps({
    modelValue: {
        type: Array,
        default: () => [],
    },
    alts: {
        type: Array,
        default: () => [],
    },
    label: {
        type: String,
        default: "Images",
    },
    error: {
        type: [String, Object],
        default: "",
    },
    altError: {
        type: String,
        default: "",
    },
    required: {
        type: Boolean,
        default: false,
    },
    altText: {
        type: Boolean,
        default: true,
    },
    defaultAltText: {
        type: String,
        default: "",
    },
});

const emit = defineEmits(["update:modelValue", "update:alts"]);

const URL = window.URL;
const localImages = ref([]);
const localAlts = ref([...props.alts]);

// New computed property to handle image-specific errors
const imageErrors = computed(() => {
    if (!props.error) return {};

    const errors = {};
    // Check for image-specific errors like images.0, images.1, etc.
    if (typeof props.error === "object") {
        Object.keys(props.error).forEach((key) => {
            if (key.startsWith("images.")) {
                const index = parseInt(key.split(".")[1]);
                if (!isNaN(index)) {
                    errors[index] = props.error[key];
                }
            }
        });
    }
    return errors;
});

const generalImageError = computed(() => {
    return typeof props.error === "string"
        ? props.error
        : props.error?.images || "";
});

const handleImagesChange = (e) => {
    const files = Array.from(e.target.files);

    // Update images and alts
    localImages.value = files;

    // Initialize alt texts if needed
    if (localAlts.value.length < files.length) {
        localAlts.value = files.map((_, index) => localAlts.value[index] || "");
    }

    emit("update:modelValue", files);
    emit("update:alts", localAlts.value);
};

const updateAltText = (index, value) => {
    localAlts.value[index] = value;
    emit("update:alts", localAlts.value);
};

const removeImage = (index) => {
    const newImages = [...localImages.value];
    newImages.splice(index, 1);
    localImages.value = newImages;

    const newAlts = [...localAlts.value];
    newAlts.splice(index, 1);
    localAlts.value = newAlts;

    emit("update:modelValue", newImages);
    emit("update:alts", newAlts);
};
</script>

<template>
    <div>
        <InputLabel :for="label.toLowerCase()" :value="label" />
        <input
            :id="label.toLowerCase()"
            type="file"
            @input="handleImagesChange"
            multiple
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            accept="image/png, image/jpeg, image/jpg, image/webp"
            :required="required"
        />

        <!-- General error for images field -->
        <InputError :message="generalImageError" class="mt-2" />

        <!-- Image previews with alt inputs -->
        <div v-if="modelValue.length > 0" class="mt-4 space-y-4">
            <div
                v-for="(image, index) in modelValue"
                :key="index"
                class="flex gap-4 items-start border p-3 rounded-md"
                :class="{ 'border-red-500': imageErrors[index] }"
            >
                <div class="w-32 relative">
                    <img
                        :src="URL.createObjectURL(image)"
                        class="w-full h-32 object-cover rounded"
                        @load="URL.revokeObjectURL(image)"
                    />
                    <button
                        type="button"
                        @click="removeImage(index)"
                        class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center"
                    >
                        ×
                    </button>
                </div>
                <div class="flex-1">
                    <template v-if="altText">
                        <InputLabel
                            :for="'alt-' + index"
                            :value="`Alt Text for Image ${index + 1}`"
                        />
                        <TextInput
                            :id="'alt-' + index"
                            v-model="localAlts[index]"
                            @input="(e) => updateAltText(index, e.target.value)"
                            type="text"
                            class="mt-1 block w-full"
                            :placeholder="
                                defaultAltText || 'Describe this image'
                            "
                        />
                    </template>
                    <InputError :message="imageErrors[index]" class="mt-2" />
                </div>
            </div>
        </div>

        <InputError v-if="altText" :message="altError" class="mt-2" />
    </div>
</template>
