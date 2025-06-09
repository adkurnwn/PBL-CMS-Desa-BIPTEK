<script setup>
import { ref, watch, computed, nextTick } from "vue";
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
    fieldName: {
        type: String,
        default: "Category",
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
const isDropdownOpen = ref(false);
const searchQuery = ref('');
const dropUpwards = ref(false);
const showNewCategoryInput = ref(false);

// Add reactive item height based on current breakpoint
const itemHeight = computed(() => {
    const breakpoints = {
        'xs': 15.1, // Base default
        'sm': 17.2,
        'md': 19.6,
        'lg': 21.2,
        'xl': 22.8,
        '2xl': 26.3,
        '3xl': 30.5,
        '4xl': 34
    }
    
    // Get width and determine breakpoint
    if (typeof window !== 'undefined') {
        const width = window.innerWidth;
        if (width >= 2560) return breakpoints['4xl'];
        if (width >= 1920) return breakpoints['3xl'];
        if (width >= 1536) return breakpoints['2xl'];
        if (width >= 1280) return breakpoints['xl'];
        if (width >= 1024) return breakpoints['lg'];
        if (width >= 768) return breakpoints['md'];
        if (width >= 640) return breakpoints['sm'];
    }
    
    return breakpoints['xs']; // Base default
});

// Computed for display text
const displayText = computed(() => {
    if (showNewCategoryInput.value) return 'Adding New Category...';
    if (!props.modelValue.category_id) return '';
    const selectedCategory = props.categories.find(cat => cat.id == props.modelValue.category_id);
    return selectedCategory ? selectedCategory.name : '';
});

// Computed for filtered categories
const filteredCategories = computed(() => {
    if (!searchQuery.value.trim()) return props.categories;
    
    const query = searchQuery.value.toLowerCase();
    return props.categories.filter(category => 
        category.name.toLowerCase().includes(query)
    );
});

// Make sure deleting_category_id is initialized as an array
if (!props.modelValue.deleting_category_id) {
    updateValue("deleting_category_id", []);
}

// Dropdown methods
const toggleDropdown = () => {
    if (isNewCategory.value) return;
    
    isDropdownOpen.value = !isDropdownOpen.value;
    if (isDropdownOpen.value) {
        searchQuery.value = '';
        checkDropdownDirection();
        document.addEventListener('click', handleOutsideClick);
        window.addEventListener('scroll', checkDropdownDirection);
        window.addEventListener('resize', checkDropdownDirection);
    } else {
        document.removeEventListener('click', handleOutsideClick);
        window.removeEventListener('scroll', checkDropdownDirection);
        window.removeEventListener('resize', checkDropdownDirection);
    }
};

const checkDropdownDirection = () => {
    nextTick(() => {
        const element = document.getElementById('category-dropdown-container');
        if (!element) return;
        
        const rect = element.getBoundingClientRect();
        const dropdownHeight = (itemHeight.value * 4) + 45;
        const spaceBelow = window.innerHeight - rect.bottom;
        dropUpwards.value = spaceBelow < dropdownHeight && rect.top > dropdownHeight;
    });
};

const handleOutsideClick = (event) => {
    const element = document.getElementById('category-dropdown-container');
    if (element && !element.contains(event.target)) {
        isDropdownOpen.value = false;
        document.removeEventListener('click', handleOutsideClick);
        window.removeEventListener('scroll', checkDropdownDirection);
        window.removeEventListener('resize', checkDropdownDirection);
    }
};

const selectCategory = (categoryId) => {
    updateValue('category_id', categoryId);
    showNewCategoryInput.value = false;
    updateValue('new_category', '');
    isDropdownOpen.value = false;
    document.removeEventListener('click', handleOutsideClick);
    window.removeEventListener('scroll', checkDropdownDirection);
    window.removeEventListener('resize', checkDropdownDirection);
};

const selectAddNew = () => {
    showNewCategoryInput.value = true;
    updateValue('category_id', '');
    updateValue('new_category', '');
    isDropdownOpen.value = false;
    document.removeEventListener('click', handleOutsideClick);
    window.removeEventListener('scroll', checkDropdownDirection);
    window.removeEventListener('resize', checkDropdownDirection);
};

const cancelNewCategory = () => {
    showNewCategoryInput.value = false;
    updateValue('new_category', '');
};

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
            showNewCategoryInput.value = false;
        }
    }
);
</script>

<template>
    <div class="dropdown-field-container" :style="{ width: '100%' }">
        <div class="field-name text-selectedText">{{ fieldName }}</div>

        <!-- Editing a category -->
        <div v-if="isEditingCategory" class="relative">
            <div class="flex gap-[1.8px] sm:gap-[2px] md:gap-[2.2px] lg:gap-[2.4px] xl:gap-[2.67px] 2xl:gap-[3px] 3xl:gap-[3.6px] 4xl:gap-[4px]">
                <input
                    id="edit-category-name"
                    v-model="modelValue.new_category_name"
                    type="text"
                    class="field-box text-TableText bg-menuBG border border-selectedText border-solid focus:outline-none focus:ring-1 focus:ring-selectedText focus:border-selectedText transition-all duration-200 flex-1"
                    placeholder="Edit category name"
                />
                <button
                    type="button"
                    class="border-selectedText border-[1px] bg-white px-[3.5px] sm:px-[4px] md:px-[4.5px] lg:px-[4.8px] xl:px-[5.3px] 2xl:px-[6px] 3xl:px-[7px] 4xl:px-[8px] py-[1.8px] sm:py-[2px] md:py-[2.2px] lg:py-[2.4px] xl:py-[2.67px] 2xl:py-[3px] 3xl:py-[3.6px] 4xl:py-[4px] text-selectedText rounded-[2.2px] sm:rounded-[2.5px] md:rounded-[2.9px] lg:rounded-[3.1px] xl:rounded-[3.33px] 2xl:rounded-[3.8px] 3xl:rounded-[4.4px] 4xl:rounded-[5px] hover:bg-selectedBG hover:border-transparent text-[8px] sm:text-[9px] md:text-[10px] lg:text-[11px] xl:text-[12px] 2xl:text-[14px] 3xl:text-[16px] 4xl:text-[18px]"
                    @click="cancelEditCategory"
                >
                    BATAL
                </button>
            </div>
            <p class="text-[6px] sm:text-[7px] md:text-[8px] lg:text-[9px] xl:text-[10px] 2xl:text-[11px] 3xl:text-[13px] 4xl:text-[15px] text-gray-500 mt-[1.8px] sm:mt-[2px] md:mt-[2.2px] lg:mt-[2.4px] xl:mt-[2.67px] 2xl:mt-[3px] 3xl:mt-[3.6px] 4xl:mt-[4px]">
                Tekan tombol Update pada form untuk menghapus kategori.
            </p>
        </div>

        <!-- Deleting categories -->
        <div v-else-if="isDeletingCategories" class="mt-[1.8px] sm:mt-[2px] md:mt-[2.2px] lg:mt-[2.4px] xl:mt-[2.67px] 2xl:mt-[3px] 3xl:mt-[3.6px] 4xl:mt-[4px]">
            <h3 class="font-medium text-gray-700 mb-[1.8px] sm:mb-[2px] md:mb-[2.2px] lg:mb-[2.4px] xl:mb-[2.67px] 2xl:mb-[3px] 3xl:mb-[3.6px] 4xl:mb-[4px] text-[8px] sm:text-[9px] md:text-[10px] lg:text-[11px] xl:text-[12px] 2xl:text-[14px] 3xl:text-[16px] 4xl:text-[18px]">
                Pilih kategori yang ingin dihapus
            </h3>

            <div
                class="space-y-[1.8px] sm:space-y-[2px] md:space-y-[2.2px] lg:space-y-[2.4px] xl:space-y-[2.67px] 2xl:space-y-[3px] 3xl:space-y-[3.6px] 4xl:space-y-[4px] max-h-[60px] sm:max-h-[68px] md:max-h-[78px] lg:max-h-[85px] xl:max-h-[92px] 2xl:max-h-[105px] 3xl:max-h-[122px] 4xl:max-h-[140px] overflow-y-auto border border-selectedText p-[1.8px] sm:p-[2px] md:p-[2.2px] lg:p-[2.4px] xl:p-[2.67px] 2xl:p-[3px] 3xl:p-[3.6px] 4xl:p-[4px] rounded-[2.2px] sm:rounded-[2.5px] md:rounded-[2.9px] lg:rounded-[3.1px] xl:rounded-[3.33px] 2xl:rounded-[3.8px] 3xl:rounded-[4.4px] 4xl:rounded-[5px] bg-menuBG"
                style="scrollbar-width: thin; scrollbar-color: #4C7660 transparent;"
            >
                <div
                    v-for="category in categories"
                    :key="category.id"
                    class="flex items-center space-x-[1.8px] sm:space-x-[2px] md:space-x-[2.2px] lg:space-x-[2.4px] xl:space-x-[2.67px] 2xl:space-x-[3px] 3xl:space-x-[3.6px] 4xl:space-x-[4px]"
                >
                    <input
                        type="checkbox"
                        :id="`delete-category-${category.id}`"
                        :value="category.id"
                        :checked="isCategorySelected(category.id)"
                        @change="toggleCategorySelection(category.id)"
                        class="rounded border-menuBG text-selectedText shadow-sm focus:border-selectedBG focus:ring focus:ring-selectedBG focus:ring-opacity-50 w-[8px] sm:w-[9px] md:w-[10px] lg:w-[11px] xl:w-[12px] 2xl:w-[14px] 3xl:w-[16px] 4xl:w-[18px] h-[8px] sm:h-[9px] md:h-[10px] lg:h-[11px] xl:h-[12px] 2xl:h-[14px] 3xl:h-[16px] 4xl:h-[18px]"
                    />
                    <label
                        :for="`delete-category-${category.id}`"
                        class="text-[8px] sm:text-[9px] md:text-[10px] lg:text-[11px] xl:text-[12px] 2xl:text-[14px] 3xl:text-[16px] 4xl:text-[18px] text-gray-700"
                    >
                        {{ category.name }}
                    </label>
                </div>
            </div>

            <div v-if="categories.length > 0" class="mt-[3.5px] sm:mt-[4px] md:mt-[4.5px] lg:mt-[4.8px] xl:mt-[5.3px] 2xl:mt-[6px] 3xl:mt-[7px] 4xl:mt-[8px] flex gap-[1.8px] sm:gap-[2px] md:gap-[2.2px] lg:gap-[2.4px] xl:gap-[2.67px] 2xl:gap-[3px] 3xl:gap-[3.6px] 4xl:gap-[4px]">
                <button
                    type="button"
                    class="border-selectedText border-[1px] bg-white px-[3.5px] sm:px-[4px] md:px-[4.5px] lg:px-[4.8px] xl:px-[5.3px] 2xl:px-[6px] 3xl:px-[7px] 4xl:px-[8px] py-[1.8px] sm:py-[2px] md:py-[2.2px] lg:py-[2.4px] xl:py-[2.67px] 2xl:py-[3px] 3xl:py-[3.6px] 4xl:py-[4px] text-selectedText rounded-[2.2px] sm:rounded-[2.5px] md:rounded-[2.9px] lg:rounded-[3.1px] xl:rounded-[3.33px] 2xl:rounded-[3.8px] 3xl:rounded-[4.4px] 4xl:rounded-[5px] hover:bg-selectedBG hover:border-transparent text-[8px] sm:text-[9px] md:text-[10px] lg:text-[11px] xl:text-[12px] 2xl:text-[14px] 3xl:text-[16px] 4xl:text-[18px]"
                    @click="toggleDeletionMode"
                >
                    BATAL
                </button>
                <p class="text-[6px] sm:text-[7px] md:text-[8px] lg:text-[9px] xl:text-[10px] 2xl:text-[11px] 3xl:text-[13px] 4xl:text-[15px] text-gray-500 flex items-center">
                    Selected categories will be deleted when you save the form.
                </p>
            </div>
        </div>

        <!-- Creating or selecting a category -->
        <div v-else class="flex flex-col gap-[1.8px] sm:gap-[2px] md:gap-[2.2px] lg:gap-[2.4px] xl:gap-[2.67px] 2xl:gap-[3px] 3xl:gap-[3.6px] 4xl:gap-[4px]">
            <!-- New category input -->
            <div v-if="showNewCategoryInput" class="flex gap-[1.8px] sm:gap-[2px] md:gap-[2.2px] lg:gap-[2.4px] xl:gap-[2.67px] 2xl:gap-[3px] 3xl:gap-[3.6px] 4xl:gap-[4px]">
                <input
                    id="new-category"
                    v-model="modelValue.new_category"
                    type="text"
                    class="field-box text-TableText bg-menuBG border border-selectedText border-solid focus:outline-none focus:ring-1 focus:ring-selectedText focus:border-selectedText transition-all duration-200 flex-1"
                    placeholder="Enter new category name"
                    autofocus
                />
                <button
                    type="button"
                    class="border-selectedText border-[1px] bg-white px-[3.5px] sm:px-[4px] md:px-[4.5px] lg:px-[4.8px] xl:px-[5.3px] 2xl:px-[6px] 3xl:px-[7px] 4xl:px-[8px] py-[1.8px] sm:py-[2px] md:py-[2.2px] lg:py-[2.4px] xl:py-[2.67px] 2xl:py-[3px] 3xl:py-[3.6px] 4xl:py-[4px] text-selectedText rounded-[2.2px] sm:rounded-[2.5px] md:rounded-[2.9px] lg:rounded-[3.1px] xl:rounded-[3.33px] 2xl:rounded-[3.8px] 3xl:rounded-[4.4px] 4xl:rounded-[5px] hover:bg-selectedBG hover:border-transparent text-[8px] sm:text-[9px] md:text-[10px] lg:text-[11px] xl:text-[12px] 2xl:text-[14px] 3xl:text-[16px] 4xl:text-[18px]"
                    @click="cancelNewCategory"
                >
                    BATAL
                </button>
            </div>
            
            <!-- Category dropdown -->
            <div v-else class="relative" id="category-dropdown-container">
                <div class="relative flex items-center w-full">
                    <input 
                        @click="toggleDropdown" 
                        type="text" 
                        :value="displayText" 
                        placeholder="Select Category" 
                        readonly
                        id="category"
                        required
                        class="field-box text-TableText bg-menuBG border border-selectedText border-solid focus:outline-none focus:ring-1 focus:ring-selectedText focus:border-selectedText transition-all duration-200 cursor-pointer"
                    />
                    
                    <div class="absolute right-3 top-1/2 transform -translate-y-1/2 pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none"
                            :class="{ 'rotate-180': isDropdownOpen, 'transition-transform': true }"
                            class="w-[8.9px] h-[8.9px] sm:w-[10.2px] sm:h-[10.2px] md:w-[11.6px] md:h-[11.6px] lg:w-[12.5px] lg:h-[12.5px] xl:w-[13.3px] xl:h-[13.3px] 2xl:w-[15.4px] 2xl:h-[15.4px] 3xl:w-[17.5px] 3xl:h-[17.5px] 4xl:w-[20px] 4xl:h-[20px]">
                            <mask id="mask0_1692_7218" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="20" height="20">
                                <rect width="20" height="20" fill="#D9D9D9" />
                            </mask>
                            <g mask="url(#mask0_1692_7218)">
                                <path
                                    d="M10 12.8327L5 7.83268L6.16667 6.66602L10 10.4993L13.8333 6.66602L15 7.83268L10 12.8327Z"
                                    class="fill-selectedText" />
                            </g>
                        </svg>
                    </div>
                </div>

                <!-- Dropdown menu -->
                <div v-if="isDropdownOpen"
                    :class="[
                        'absolute left-0 right-0 mt-1 bg-menuBG rounded-md shadow-lg z-50 min-w-[112px] sm:min-w-[127px] md:min-w-[145px] lg:min-w-[156px] xl:min-w-[167px] 2xl:min-w-[193px] 3xl:min-w-[220px] 4xl:min-w-[250px]',
                        { 'top-full': !dropUpwards, 'bottom-full mb-1': dropUpwards }
                    ]" 
                    @click.stop>
                    <div class="p-[2.6px] sm:p-[3px] md:p-[3.4px] lg:p-[3.6px] xl:p-[4px] 2xl:p-[4.6px] 3xl:p-[5.4px] 4xl:p-[6px]">
                        <!-- Search box -->
                        <div class="mb-[2.6px] sm:mb-[3px] md:mb-[3.4px] lg:mb-[3.6px] xl:mb-[4px] 2xl:mb-[4.6px] 3xl:mb-[5.4px] 4xl:mb-[6px]">
                            <input 
                                type="text" 
                                v-model="searchQuery" 
                                placeholder="Cari..." 
                                class="w-full p-[1.8px] sm:p-[2px] md:p-[2.2px] lg:p-[2.4px] xl:p-[2.67px] 2xl:p-[3px] 3xl:p-[3.6px] 4xl:p-[4px] border border-selectedText rounded-[2.2px] sm:rounded-[2.5px] md:rounded-[2.9px] lg:rounded-[3.1px] xl:rounded-[3.33px] 2xl:rounded-[3.8px] 3xl:rounded-[4.4px] 4xl:rounded-[5px] focus:outline-none focus:ring-1 focus:ring-selectedText text-[8px] sm:text-[9px] md:text-[10px] lg:text-[11px] xl:text-[12px] 2xl:text-[14px] 3xl:text-[16px] 4xl:text-[18px]"
                                @click.stop
                            />
                        </div>

                        <!-- Options list with custom scrollbar -->
                        <div class="options-container" 
                             :style="{
                                'max-height': `calc(4 * ${itemHeight}px)`, 
                                'overflow-y': 'auto', 
                                'scrollbar-width': 'thin', 
                                'scrollbar-color': '#4C7660 transparent'
                             }">
                            <!-- Add New Category Option -->
                            <div 
                                @click="selectAddNew"
                                class="border-[1px] border-selectedText border-dashed p-[1.8px] sm:p-[2px] md:p-[2.2px] lg:p-[2.4px] xl:p-[2.67px] 2xl:p-[3px] 3xl:p-[3.6px] 4xl:p-[4px] hover:bg-selectedBG cursor-pointer rounded-[2.2px] sm:rounded-[2.5px] md:rounded-[2.9px] lg:rounded-[3.1px] xl:rounded-[3.33px] 2xl:rounded-[3.8px] 3xl:rounded-[4.4px] 4xl:rounded-[5px] flex justify-between items-center text-selectedText font-medium text-[8px] sm:text-[9px] md:text-[10px] lg:text-[11px] xl:text-[12px] 2xl:text-[14px] 3xl:text-[16px] 4xl:text-[18px] mb-[1.8px] sm:mb-[2px] md:mb-[2.2px] lg:mb-[2.4px] xl:mb-[2.67px] 2xl:mb-[3px] 3xl:mb-[3.6px] 4xl:mb-[4px]"
                                :style="{ 'height': `${itemHeight}px` }"
                            >
                                + Buat Kategori Baru
                                <svg xmlns="http://www.w3.org/2000/svg" 
                                    width="16" height="16"
                                    viewBox="0 0 24 24" 
                                    fill="none" 
                                    stroke="currentColor" 
                                    stroke-width="2"
                                    stroke-linecap="round" 
                                    stroke-linejoin="round"
                                    class="w-[7.1px] h-[7.1px] sm:w-[8.1px] sm:h-[8.1px] md:w-[9.3px] md:h-[9.3px] lg:w-[10px] lg:h-[10px] xl:w-[10.7px] xl:h-[10.7px] 2xl:w-[12.3px] 2xl:h-[12.3px] 3xl:w-[14px] 3xl:h-[14px] 4xl:w-[16px] 4xl:h-[16px]">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="8" x2="12" y2="16"></line>
                                    <line x1="8" y1="12" x2="16" y2="12"></line>
                                </svg>
                            </div>
                            
                            <!-- Existing Categories -->
                            <div 
                                v-for="category in filteredCategories" 
                                :key="category.id" 
                                @click="selectCategory(category.id)"
                                class="p-[1.8px] sm:p-[2px] md:p-[2.2px] lg:p-[2.4px] xl:p-[2.67px] 2xl:p-[3px] 3xl:p-[3.6px] 4xl:p-[4px] hover:bg-selectedBG cursor-pointer rounded-[2.2px] sm:rounded-[2.5px] md:rounded-[2.9px] lg:rounded-[3.1px] xl:rounded-[3.33px] 2xl:rounded-[3.8px] 3xl:rounded-[4.4px] 4xl:rounded-[5px] flex justify-between items-center text-TableText text-[8px] sm:text-[9px] md:text-[10px] lg:text-[11px] xl:text-[12px] 2xl:text-[14px] 3xl:text-[16px] 4xl:text-[18px]"
                                :style="{ 'height': `${itemHeight}px` }"
                                :class="{ 'bg-blue-50 font-medium text-selectedText': modelValue.category_id == category.id }"
                            >
                                {{ category.name }}
                                <span v-if="modelValue.category_id == category.id" class="text-selectedText">
                                    <svg xmlns="http://www.w3.org/2000/svg" 
                                        width="16" height="16"
                                        viewBox="0 0 24 24" 
                                        fill="none" 
                                        stroke="currentColor" 
                                        stroke-width="3"
                                        stroke-linecap="round" 
                                        stroke-linejoin="round"
                                        class="w-[7.1px] h-[7.1px] sm:w-[8.1px] sm:h-[8.1px] md:w-[9.3px] md:h-[9.3px] lg:w-[10px] lg:h-[10px] xl:w-[10.7px] xl:h-[10.7px] 2xl:w-[12.3px] 2xl:h-[12.3px] 3xl:w-[14px] 3xl:h-[14px] 4xl:w-[16px] 4xl:h-[16px]">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                </span>
                            </div>
                            
                            <!-- No results message -->
                            <div v-if="filteredCategories.length === 0" class="p-[1.8px] sm:p-[2px] md:p-[2.2px] lg:p-[2.4px] xl:p-[2.67px] 2xl:p-[3px] 3xl:p-[3.6px] 4xl:p-[4px] text-gray-500 text-center text-[8px] sm:text-[9px] md:text-[10px] lg:text-[11px] xl:text-[12px] 2xl:text-[14px] 3xl:text-[16px] 4xl:text-[18px]">
                                Tidak ada kategori yang sesuai
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Category management buttons -->
            <div v-if="modelValue.category_id" class="flex gap-[3.5px] sm:gap-[4px] md:gap-[4.5px] lg:gap-[4.8px] xl:gap-[5.3px] 2xl:gap-[6px] 3xl:gap-[7px] 4xl:gap-[8px] mt-[0.9px] sm:mt-[1px] md:mt-[1.1px] lg:mt-[1.2px] xl:mt-[1.33px] 2xl:mt-[1.5px] 3xl:mt-[1.8px] 4xl:mt-[2px]">
                <button
                    v-if="!showNewCategoryInput"
                    type="button"
                    class="rounded-[5px] text-[8px] sm:text-[9px] md:text-[10px] lg:text-[11px] xl:text-[12px] 2xl:text-[14px] 3xl:text-[16px] 4xl:text-[18px] text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 px-[3.5px] sm:px-[4px] md:px-[4.5px] lg:px-[4.8px] xl:px-[5.3px] 2xl:px-[6px] 3xl:px-[7px] 4xl:px-[8px] py-[1.8px] sm:py-[2px] md:py-[2.2px] lg:py-[2.4px] xl:py-[2.67px] 2xl:py-[3px] 3xl:py-[3.6px] 4xl:py-[4px]"
                    @click="startEditCategory(modelValue.category_id)"
                >
                    Edit Kategori
                </button>
                <button
                    type="button"
                    class="rounded-[5px] text-[8px] sm:text-[9px] md:text-[10px] lg:text-[11px] xl:text-[12px] 2xl:text-[14px] 3xl:text-[16px] 4xl:text-[18px] text-red-600 hover:text-red-800 bg-red-50 hover:bg-red-100 px-[3.5px] sm:px-[4px] md:px-[4.5px] lg:px-[4.8px] xl:px-[5.3px] 2xl:px-[6px] 3xl:px-[7px] 4xl:px-[8px] py-[1.8px] sm:py-[2px] md:py-[2.2px] lg:py-[2.4px] xl:py-[2.67px] 2xl:py-[3px] 3xl:py-[3.6px] 4xl:py-[4px]"
                    @click="toggleDeletionMode"
                >
                    Hapus Kategori
                </button>
            </div>
        </div>

        <!-- Flash message for category deletion results -->
        <div
            v-if="$page.props.flash.categoryDeletionResults"
            class="mt-[3.5px] sm:mt-[4px] md:mt-[4.5px] lg:mt-[4.8px] xl:mt-[5.3px] 2xl:mt-[6px] 3xl:mt-[7px] 4xl:mt-[8px] p-[3.5px] sm:p-[4px] md:p-[4.5px] lg:p-[4.8px] xl:p-[5.3px] 2xl:p-[6px] 3xl:p-[7px] 4xl:p-[8px] bg-gray-100 rounded-[2.2px] sm:rounded-[2.5px] md:rounded-[2.9px] lg:rounded-[3.1px] xl:rounded-[3.33px] 2xl:rounded-[3.8px] 3xl:rounded-[4.4px] 4xl:rounded-[5px]"
        >
            <h4 class="font-medium text-[8px] sm:text-[9px] md:text-[10px] lg:text-[11px] xl:text-[12px] 2xl:text-[14px] 3xl:text-[16px] 4xl:text-[18px]">Category Deletion Results:</h4>
            <ul class="list-disc ml-[4.4px] sm:ml-[5px] md:ml-[5.6px] lg:ml-[6.1px] xl:ml-[6.67px] 2xl:ml-[7.7px] 3xl:ml-[8.9px] 4xl:ml-[10px] mt-[1.8px] sm:mt-[2px] md:mt-[2.2px] lg:mt-[2.4px] xl:mt-[2.67px] 2xl:mt-[3px] 3xl:mt-[3.6px] 4xl:mt-[4px]">
                <li
                    v-for="(result, category) in $page.props.flash
                        .categoryDeletionResults"
                    :key="category"
                    class="text-[8px] sm:text-[9px] md:text-[10px] lg:text-[11px] xl:text-[12px] 2xl:text-[14px] 3xl:text-[16px] 4xl:text-[18px]"
                >
                    <span class="font-medium">{{ category }}:</span>
                    {{ result }}
                </li>
            </ul>
        </div>

        <!-- Replace the existing error section with this -->
        <div class="mt-[1.8px] sm:mt-[2px] md:mt-[2.2px] lg:mt-[2.4px] xl:mt-[2.67px] 2xl:mt-[3px] 3xl:mt-[3.6px] 4xl:mt-[4px]">
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

<style scoped>
.dropdown-field-container {
    padding: 5.3px;
}

.field-name {
    font-size: 8px;
    margin-bottom: 4.4px;
}

.field-box {
    height: 20.7px;
    font-size: 8px;
    padding: 5.3px;
    width: 100%;
    box-sizing: border-box;
    border-radius: 2.2px;
    appearance: none !important;
    -webkit-appearance: none !important;
    -moz-appearance: none !important;
    background-image: none !important;
}

/* Responsive styles using media queries */
@media (min-width: 640px) { /* sm */
    .dropdown-field-container {
        padding: 6px;
    }
    
    .field-name {
        font-size: 9px;
        margin-bottom: 5px;
    }
    
    .field-box {
        height: 23.5px;
        font-size: 9px;
        padding: 6px;
        border-radius: 2.5px;
    }
}

@media (min-width: 768px) { /* md */
    .dropdown-field-container {
        padding: 6.7px;
    }
    
    .field-name {
        font-size: 10px;
        margin-bottom: 5.6px;
    }
    
    .field-box {
        height: 26.3px;
        font-size: 10px;
        padding: 6.7px;
        border-radius: 2.9px;
    }
}

@media (min-width: 1024px) { /* lg */
    .dropdown-field-container {
        padding: 7.3px;
    }
    
    .field-name {
        font-size: 11px;
        margin-bottom: 6.1px;
    }
    
    .field-box {
        height: 28.7px;
        font-size: 11px;
        padding: 7.3px;
        border-radius: 3.1px;
    }
}

@media (min-width: 1280px) { /* xl */
    .dropdown-field-container {
        padding: 8px;
    }
    
    .field-name {
        font-size: 12px;
        margin-bottom: 6.67px;
    }
    
    .field-box {
        height: 31px;
        font-size: 12px;
        padding: 8px;
        border-radius: 3.33px;
    }
}

@media (min-width: 1536px) { /* 2xl */
    .dropdown-field-container {
        padding: 9.3px;
    }
    
    .field-name {
        font-size: 14px;
        margin-bottom: 7.7px;
    }
    
    .field-box {
        height: 35.3px;
        font-size: 14px;
        padding: 9.3px;
        border-radius: 3.8px;
    }
}

@media (min-width: 1920px) { /* 3xl */
    .dropdown-field-container {
        padding: 10.7px;
    }
    
    .field-name {
        font-size: 16px;
        margin-bottom: 8.9px;
    }
    
    .field-box {
        height: 40px;
        font-size: 16px;
        padding: 10.7px;
        border-radius: 4.4px;
    }
}

@media (min-width: 2560px) { /* 4xl */
    .dropdown-field-container {
        padding: 12px;
    }
    
    .field-name {
        font-size: 18px;
        margin-bottom: 10px;
    }
    
    .field-box {
        height: 46px;
        font-size: 18px;
        padding: 12px;
        border-radius: 5px;
    }
}

.rotate-180 {
    transform: rotate(180deg);
    transition: transform 0.2s ease-in-out;
}
</style>
