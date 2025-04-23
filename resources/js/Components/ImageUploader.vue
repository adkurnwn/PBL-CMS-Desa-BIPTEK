<script setup>
import { ref, onMounted } from "vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import DangerButton from "@/Components/DangerButton.vue";

// Props
const props = defineProps({
    existing_images: {
        type: Array,
        default: () => [],
    },
    modelValue: {
        type: Object,
        default: () => ({
            images: [],
            alts: [],
            removed_images: [],
        }),
    },
    errors: {
        type: Object,
        default: () => ({}),
    },
    label: {
        type: String,
        default: "Gallery Images",
    },
});

const emit = defineEmits(["update:modelValue"]);

// Model updating function
const updateModelValue = (key, value) => {
    const updated = {
        ...props.modelValue,
        [key]: value,
    };
    emit("update:modelValue", updated);
};

// Handle image file input
const handleImagesChange = (e) => {
    const files = Array.from(e.target.files);
    if (files.length > 0) {
        // Initialize alts array if it doesn't exist or create new empty alt entries
        const newAlts = files.map(() => "");

        updateModelValue("images", files);
        updateModelValue("alts", newAlts);
    }
};

// Remove existing image
const removeImage = (imageId) => {
    if (confirm("Are you sure you want to remove this image?")) {
        const updatedRemoved = [...props.modelValue.removed_images, imageId];
        updateModelValue("removed_images", updatedRemoved);
    }
};
</script>

<template>
    <div>
        <!-- Label -->
        <InputLabel :for="label.toLowerCase()" :value="label" />

        <!-- Existing Images -->
        <div v-if="existing_images?.length" class="mb-4">
            <p class="text-sm text-gray-600 mb-2">Current Images:</p>
            <div class="grid grid-cols-2 gap-4">
                <div
                    v-for="image in existing_images"
                    :key="image.id"
                    v-show="!modelValue.removed_images.includes(image.id)"
                    class="relative"
                >
                    <img
                        :src="`/storage/${image.path}`"
                        :alt="image.alt"
                        class="h-32 w-full object-cover rounded-lg"
                    />
                    <p class="text-sm text-gray-600 mt-1">
                        Alt: {{ image.alt }}
                    </p>
                    <DangerButton
                        type="button"
                        @click="removeImage(image.id)"
                        class="absolute top-2 right-2"
                    >
                        ×
                    </DangerButton>
                </div>
            </div>
        </div>

        <!-- File Input -->
        <input
            :id="label.toLowerCase()"
            type="file"
            @input="handleImagesChange"
            multiple
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            accept="image/png, image/jpeg, image/jpg"
        />

        <!-- New Images Preview + Alt Inputs -->
        <div
            v-if="modelValue.images && modelValue.images.length > 0"
            class="mt-4 space-y-4"
        >
            <div
                v-for="(image, index) in modelValue.images"
                :key="index"
                class="flex gap-4 items-start"
            >
                <div class="w-32">
                    <img
                        :src="URL.createObjectURL(image)"
                        class="w-full h-32 object-cover rounded"
                        @load="() => URL.revokeObjectURL(image)"
                    />
                </div>
                <div class="flex-1">
                    <InputLabel
                        :for="'alt-' + index"
                        :value="`Alt Text for Image ${index + 1}`"
                    />
                    <TextInput
                        :id="'alt-' + index"
                        :modelValue="modelValue.alts[index]"
                        @update:modelValue="
                            (value) => {
                                const updatedAlts = [...modelValue.alts];
                                updatedAlts[index] = value;
                                updateModelValue('alts', updatedAlts);
                            }
                        "
                        type="text"
                        class="mt-1 block w-full"
                        :placeholder="`Describe image ${index + 1}`"
                        required
                    />
                </div>
            </div>
        </div>

        <!-- Errors -->
        <InputError :message="errors.images" class="mt-2" />
        <InputError :message="errors.alts" class="mt-2" />
    </div>
</template>
