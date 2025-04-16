<script setup>
import { ref, watch } from "vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";

const props = defineProps({
    categories: {
        type: Array,
        required: true,
    },
    modelValue: {
        type: Object,
        required: true,
    },
    errors: {
        type: Object,
        default: () => ({}),
    },
});

const emit = defineEmits(["update:modelValue"]);

const isNewCategory = ref(false);

watch(isNewCategory, (newValue) => {
    const updatedValue = {
        category_id: newValue ? "" : props.modelValue.category_id,
        new_category: newValue ? props.modelValue.new_category : "",
    };
    emit("update:modelValue", updatedValue);
});

const updateValue = (key, value) => {
    emit("update:modelValue", {
        ...props.modelValue,
        [key]: value,
    });
};
</script>

<template>
    <div>
        <InputLabel for="category" value="Category" />
        <div class="flex gap-4">
            <select
                v-if="!isNewCategory"
                id="category"
                :value="modelValue.category_id"
                @input="updateValue('category_id', $event.target.value)"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            >
                <option value="">Select Category</option>
                <option
                    v-for="category in categories"
                    :key="category.id"
                    :value="category.id"
                >
                    {{ category.name }}
                </option>
            </select>

            <TextInput
                v-else
                id="new-category"
                :value="modelValue.new_category"
                @input="updateValue('new_category', $event.target.value)"
                type="text"
                class="mt-1 block w-full"
                placeholder="Enter new category name"
            />

            <button
                type="button"
                class="mt-1 px-4 py-2 text-sm text-blue-600 hover:text-blue-800"
                @click="isNewCategory = !isNewCategory"
            >
                {{ isNewCategory ? "Select Existing" : "Add New" }}
            </button>
        </div>
        <InputError :message="errors.category_id" class="mt-2" />
        <InputError :message="errors.new_category" class="mt-2" />
    </div>
</template>
