<script setup>
import { ref, watch } from "vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/DashboardComponent/InputError.vue";

const props = defineProps({
    categories: {
        type: Array,
        required: true,
    },
    unused_categories: {
        type: Array,
        default: () => [],
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

// Add the updateValue function here
const updateValue = (key, value) => {
    if (props.modelValue) {
        props.modelValue[key] = value;
    }
};

const isNewCategory = ref(false);
const isEditingCategory = ref(false);
const selectedCategoryForEdit = ref(null);
const isDeletingCategories = ref(false);

// Make sure deleting_category_id is initialized as an array
if (!props.modelValue.deleting_category_id) {
    updateValue("deleting_category_id", []);
}

// Function to start editing a category
const startEditCategory = (categoryId) => {
    const category = props.categories.find((c) => c.id == categoryId);
    if (category) {
        isEditingCategory.value = true;
        selectedCategoryForEdit.value = category.name;
        updateValue("edit_category_id", categoryId);
        updateValue("new_category_name", category.name);
    }
};

// Function to cancel editing
const cancelEditCategory = () => {
    isEditingCategory.value = false;
    selectedCategoryForEdit.value = null;
    updateValue("edit_category_id", null);
    updateValue("new_category_name", "");
};

// Function to handle changes to the edited category name
const handleCategoryNameChange = (event) => {
    selectedCategoryForEdit.value = event.target.value;
    updateValue("new_category_name", event.target.value);
};

// Toggle deletion mode
const toggleDeletionMode = () => {
    isDeletingCategories.value = !isDeletingCategories.value;
    if (!isDeletingCategories.value) {
        updateValue("deleting_category_id", []);
    }
};

// Toggle category selection for deletion
const toggleCategorySelection = (categoryId) => {
    // Make sure deleting_category_id is an array
    if (!Array.isArray(props.modelValue.deleting_category_id)) {
        updateValue("deleting_category_id", []);
    }

    // Check if already selected
    const index = props.modelValue.deleting_category_id.indexOf(categoryId);

    if (index === -1) {
        // Add to selection
        updateValue("deleting_category_id", [
            ...props.modelValue.deleting_category_id,
            categoryId,
        ]);
    } else {
        // Remove from selection
        const newSelection = [...props.modelValue.deleting_category_id];
        newSelection.splice(index, 1);
        updateValue("deleting_category_id", newSelection);
    }
};

// Check if a category is selected for deletion
const isCategorySelected = (categoryId) => {
    return (
        Array.isArray(props.modelValue.deleting_category_id) &&
        props.modelValue.deleting_category_id.includes(categoryId)
    );
};

watch(
    () => props.modelValue.category_id,
    (newValue) => {
        if (newValue) {
            isNewCategory.value = false;
        }
    }
);
</script>

<template>
    <div>
        <InputLabel for="category" value="Category" />

        <!-- Editing a category -->
        <div v-if="isEditingCategory" class="mt-1">
            <div class="flex gap-2">
                <TextInput
                    id="edit-category-name"
                    v-model="modelValue.new_category_name"
                    type="text"
                    class="block w-full"
                    placeholder="Edit category name"
                />
                <button
                    type="button"
                    class="px-3 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400"
                    @click="cancelEditCategory"
                >
                    Cancel
                </button>
            </div>
            <p class="text-sm text-gray-500 mt-1">
                Press Save on the form to update the category.
            </p>
        </div>

        <!-- Deleting categories -->
        <div v-else-if="isDeletingCategories" class="mt-2">
            <h3 class="font-medium text-gray-700 mb-2">
                Select unused categories to delete
            </h3>

            <div
                v-if="unused_categories.length === 0"
                class="text-gray-500 italic"
            >
                No unused categories available for deletion
            </div>

            <div
                v-else
                class="space-y-2 max-h-60 overflow-y-auto border p-2 rounded"
            >
                <div
                    v-for="category in unused_categories"
                    :key="category.id"
                    class="flex items-center space-x-2"
                >
                    <input
                        type="checkbox"
                        :id="`delete-category-${category.id}`"
                        :value="category.id"
                        :checked="isCategorySelected(category.id)"
                        @change="toggleCategorySelection(category.id)"
                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                    />
                    <label
                        :for="`delete-category-${category.id}`"
                        class="text-sm text-gray-700"
                    >
                        {{ category.name }}
                    </label>
                </div>
            </div>

            <div class="mt-3 flex gap-2">
                <button
                    type="button"
                    class="px-3 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400"
                    @click="toggleDeletionMode"
                >
                    Cancel
                </button>
                <p class="text-sm text-gray-500 flex items-center">
                    Selected categories will be deleted when you save the form.
                </p>
            </div>
        </div>

        <!-- Creating or selecting a category -->
        <div v-else class="flex flex-col gap-2">
            <div class="flex gap-2">
                <!-- Select existing category -->
                <select
                    v-if="!isNewCategory"
                    id="category"
                    v-model="modelValue.category_id"
                    required
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

                <!-- Enter new category -->
                <TextInput
                    v-else
                    id="new-category"
                    v-model="modelValue.new_category"
                    type="text"
                    class="mt-1 block w-full"
                    placeholder="Enter new category name"
                />

                <!-- Toggle button -->
                <button
                    type="button"
                    class="mt-1 px-4 py-2 text-sm text-blue-600 hover:text-blue-800"
                    @click="isNewCategory = !isNewCategory"
                >
                    {{ isNewCategory ? "Select Existing" : "Add New" }}
                </button>
            </div>

            <!-- Category management buttons -->
            <div class="flex gap-3 mt-1">
                <button
                    v-if="!isNewCategory && modelValue.category_id"
                    type="button"
                    class="text-sm text-blue-600 hover:text-blue-800"
                    @click="startEditCategory(modelValue.category_id)"
                >
                    Edit Category
                </button>

                <button
                    v-if="unused_categories.length > 0"
                    type="button"
                    class="text-sm text-red-600 hover:text-red-800"
                    @click="toggleDeletionMode"
                >
                    Delete Unused Categories
                </button>
            </div>
        </div>

        <!-- Flash message for category deletion results -->
        <div
            v-if="$page.props.flash.categoryDeletionResults"
            class="mt-3 p-3 bg-gray-100 rounded"
        >
            <h4 class="font-medium">Category Deletion Results:</h4>
            <ul class="list-disc ml-5 mt-2">
                <li
                    v-for="(result, category) in $page.props.flash
                        .categoryDeletionResults"
                    :key="category"
                >
                    <span class="font-medium">{{ category }}:</span>
                    {{ result }}
                </li>
            </ul>
        </div>

        <!-- Replace the existing error section with this -->
        <div class="mt-2">
            <!-- Category ID error -->
            <InputError :message="errors['category.category_id']" />

            <!-- New category error -->
            <InputError :message="errors['category.new_category']" />

            <!-- Edit category errors -->
            <InputError :message="errors['category.edit_category_id']" />
            <InputError :message="errors['category.new_category_name']" />

            <!-- Delete category errors -->
            <InputError
                v-if="errors['category.deleting_category_id']"
                :message="errors['category.deleting_category_id']"
            />
        </div>
    </div>
</template>
