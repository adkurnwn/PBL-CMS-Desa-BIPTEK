<script>
import { ref, watch, computed, onMounted, onBeforeUnmount } from 'vue';

export default {
    props: {
        placeholder: {
            type: String,
            default: 'Filter'
        },
        filterFields: {
            type: Array,
            default: () => []
            // Expected format: [
            //   {
            //     id: 'position_id',        // Field identifier used for filtering
            //     name: 'Posisi',           // Display name for the filter field
            //     options: [                // Options for this filter field
            //       { id: 1, name: 'Admin' },
            //       { id: 2, name: 'Kepala Desa' }
            //     ]
            //   },
            //   ... more fields
            // ]
        },
        filters: {
            type: Object,
            default: () => ({})
        },
        width: {
            type: String,
            default: '200px'
        },
        height: {
            type: String,
            default: '44px'
        },
        // Additional configuration props for more flexibility
        closeOnSelect: {
            type: Boolean,
            default: false
        },
        multiSelect: {
            type: Boolean,
            default: false
        },
        labelKey: {
            type: String,
            default: 'name'
        },
        valueKey: {
            type: String,
            default: 'id'
        },
        dropdownTitle: {
            type: String,
            default: 'Filter berdasarkan'
        },
        activeFiltersTitle: {
            type: String,
            default: 'Filters aktif'
        },
        clearAllLabel: {
            type: String,
            default: 'Bersihkan'
        },
        // Configuration for header text in sub filter menu
        filterPrefix: {
            type: String,
            default: 'Filter : '
        }
    },

    emits: ['update:filters', 'filter-changed'],

    setup(props, { emit }) {
        const isOpen = ref(false);
        const currentStep = ref('fields'); // 'fields' or 'values'
        const selectedField = ref(null);
        const displayValue = ref('');

        // Determine if any filter is active
        const isActive = computed(() => {
            return Object.keys(props.filters).length > 0;
        });

        // Get display text for the input field
        const getDisplayText = computed(() => {
            if (Object.keys(props.filters).length === 0) {
                return '';
            }

            const entries = Object.entries(props.filters);
            if (entries.length === 1) {
                const [fieldId, valueId] = entries[0];
                // Find the field name
                const field = props.filterFields.find(f => f.id === fieldId);
                if (!field) return `${fieldId}: ${valueId}`;

                // Find the option name
                const option = field.options.find(o => o[props.valueKey] === valueId);
                return `${field.name}: ${option ? option[props.labelKey] : valueId}`;
            }

            return `${entries.length} filters active`;
        });

        // Toggle dropdown open/closed
        const toggleDropdown = () => {
            if (isOpen.value) {
                closeDropdown();
            } else {
                isOpen.value = true;
                currentStep.value = 'fields';
                selectedField.value = null;
            }
        };

        // Close dropdown
        const closeDropdown = () => {
            isOpen.value = false;
            currentStep.value = 'fields';
            selectedField.value = null;
        };

        // Select a filter field
        const selectField = (field) => {
            selectedField.value = field;
            currentStep.value = 'values';
        };

        // Go back to field selection
        const backToFields = () => {
            currentStep.value = 'fields';
            selectedField.value = null;
        };

        // Select a value for the current field
        const selectValue = (option) => {
            const newFilters = { ...props.filters };
            newFilters[selectedField.value.id] = option[props.valueKey];

            emit('update:filters', newFilters);
            emit('filter-changed', newFilters);

            if (props.closeOnSelect) {
                closeDropdown();
            } else {
                // Keep the dropdown open to allow selecting multiple filters
                currentStep.value = 'fields';
                selectedField.value = null;
            }
        };

        // Remove a specific filter
        const removeFilter = (fieldId) => {
            const newFilters = { ...props.filters };
            delete newFilters[fieldId];

            emit('update:filters', newFilters);
            emit('filter-changed', newFilters);
        };

        // Clear all filters
        const clearAllFilters = () => {
            emit('update:filters', {});
            emit('filter-changed', {});
        };

        // Get display name for a filter
        const getFilterDisplayName = (fieldId, valueId) => {
            const field = props.filterFields.find(f => f.id === fieldId);
            if (!field) return `${fieldId}: ${valueId}`;

            const option = field.options.find(o => o[props.valueKey] === valueId);
            return `${field.name}: ${option ? option[props.labelKey] : valueId}`;
        };

        // Check if a field is already filtered
        const isFieldFiltered = (fieldId) => {
            return props.filters[fieldId] !== undefined;
        };

        // Update display text when filters change
        watch(() => props.filters, () => {
            displayValue.value = getDisplayText.value;
        }, { immediate: true, deep: true });

        // Function to handle outside clicks to close dropdown
        const handleOutsideClick = (event) => {
            // Get the dropdown container
            const dropdownContainers = document.querySelectorAll('.dropdown-container');
            
            // Check if the click was outside all dropdown containers
            let clickedOutside = true;
            dropdownContainers.forEach(container => {
                if (container.contains(event.target)) {
                    clickedOutside = false;
                }
            });
            
            // Close dropdown if clicked outside
            if (isOpen.value && clickedOutside) {
                closeDropdown();
            }
        };

        // Setup outside click listener when component is mounted
        onMounted(() => {
            document.addEventListener('click', handleOutsideClick);
        });

        // Remove event listener when component is unmounted
        onBeforeUnmount(() => {
            document.removeEventListener('click', handleOutsideClick);
        });

        return {
            isOpen,
            currentStep,
            selectedField,
            displayValue,
            isActive,
            getDisplayText,
            toggleDropdown,
            closeDropdown,
            selectField,
            backToFields,
            selectValue,
            removeFilter,
            clearAllFilters,
            getFilterDisplayName,
            isFieldFiltered
        };
    }
}
</script>

<template>
    <div class="dropdown-container" :style="{ width, height }">
        <div class="relative flex items-center w-full h-full gap-[10px]">
            <!-- Modified to not use @click.stop which prevents event bubbling needed for outside click detection -->
            <input @click="toggleDropdown" type="text" v-model="displayValue" :placeholder="placeholder" readonly
                class="w-full h-full pl-12 pr-10 py-2 rounded-[15px] bg-transparent text-TableText border border-filterBorder focus:outline-none focus:ring-1 focus:ring-selectedText focus:border-selectedText transition-all duration-200 placeholder:text-searchIcon text-ellipsis cursor-pointer" />

            <div @click="toggleDropdown" class="absolute left-4 text-searchIcon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M10 18H14V16H10V18ZM3 6V8H21V6H3ZM6 13H18V11H6V13Z"
                        :fill="isActive ? '#0D6EFD' : '#4C7660'" />
                </svg>
            </div>

            <div @click="toggleDropdown" class="absolute right-4 text-searchIcon">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none"
                    :class="{ 'rotate-180': isOpen }">
                    <mask id="mask0_1692_3439" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="20"
                        height="20">
                        <rect width="20" height="20" fill="#D9D9D9" />
                    </mask>
                    <g mask="url(#mask0_1692_3439)">
                        <path
                            d="M10 12.8337L5 7.83366L6.16667 6.66699L10 10.5003L13.8333 6.66699L15 7.83366L10 12.8337Z"
                            fill="#111827" />
                    </g>
                </svg>
            </div>

            <!-- Dropdown menu -->
            <div v-if="isOpen"
                class="absolute left-0 right-0 top-full mt-1 bg-menuBG rounded-md shadow-lg z-50 min-w-[250px]" 
                @click.stop>
                <div class="p-3">
                    <!-- Active filters summary -->
                    <div v-if="Object.keys(filters).length > 0" class="mb-3">
                        <div class="font-semibold text-selectedText mb-2 text-base flex justify-between items-center">
                            <span>{{ activeFiltersTitle }}</span>
                            <button @click.stop="clearAllFilters" class="text-xs text-red-500 hover:text-red-700">
                                {{ clearAllLabel }}
                            </button>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <div v-for="(valueId, fieldId) in filters" :key="fieldId"
                                class="inline-flex items-center rounded-full bg-selectedBG px-2.5 py-1 text-xs font-medium text-selectedText border border-selectedText">
                                <span>{{ getFilterDisplayName(fieldId, valueId) }}</span>
                                <button @click.stop="removeFilter(fieldId)"
                                    class="ml-1 text-selectedText hover:text-red-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <line x1="18" y1="6" x2="6" y2="18"></line>
                                        <line x1="6" y1="6" x2="18" y2="18"></line>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class=" pt-3">
                        <!-- Choose field step -->
                        <div v-if="currentStep === 'fields'">
                            <div class="font-semibold text-selectedText mb-2 text-base">
                                <span>{{ dropdownTitle }}</span>
                            </div>
                            <div class="max-h-[250px] overflow-y-auto">
                                <div v-for="field in filterFields" :key="field.id" @click.stop="selectField(field)"
                                    class="p-2 hover:bg-selectedBG cursor-pointer text-sm rounded flex justify-between items-center"
                                    :class="{ 'bg-blue-50': isFieldFiltered(field.id) }">
                                    <span>{{ field.name }}</span>
                                    <div class="flex items-center">
                                        <span v-if="isFieldFiltered(field.id)" class="text-xs text-selectedText mr-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"
                                                stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="20 6 9 17 4 12"></polyline>
                                            </svg>
                                        </span>
                                        <span class="text-gray-500">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" 
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" 
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="9 18 15 12 9 6"></polyline>
                                            </svg>
                                        </span>
                                    </div>
                                </div>
                                
                            </div>
                        </div>

                        <!-- Choose value step -->
                        <div v-else-if="currentStep === 'values' && selectedField">
                            <div class="font-semibold mb-2 text-base flex items-center">
                                <button @click.stop="backToFields"
                                    class="mr-2 text-selectedText hover:text-menuText flex items-center gap-[10px]">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <polyline points="15 18 9 12 15 6"></polyline>
                                    </svg>
                                    <span>{{ selectedField.name }}</span>
                                </button>

                            </div>

                            <!-- Selected filter indicator for the current field -->
                            <div v-if="isFieldFiltered(selectedField.id)"
                                class="mb-2 bg-selectedBG border border-selectedText p-2 rounded text-sm">
                                {{ filterPrefix }} {{ getFilterDisplayName(selectedField.id,
                                    filters[selectedField.id]).split(': ')[1] }}
                            </div>

                            <div class="max-h-[250px] overflow-y-auto">
                                <div v-for="option in selectedField.options" :key="option[valueKey]"
                                    @click.stop="selectValue(option)"
                                    class="p-2 hover:bg-selectedBG cursor-pointer text-sm text-TableText rounded flex justify-between items-center"
                                    :class="{ 'bg-blue-50 font-medium text-selectedText': filters[selectedField.id] === option[valueKey] }">
                                    {{ option[labelKey] }}
                                    <span v-if="filters[selectedField.id] === option[valueKey]" class="text-selectedText">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="20 6 9 17 4 12"></polyline>
                                        </svg>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.rotate-180 {
    transform: rotate(180deg);
    transition: transform 0.2s ease-in-out;
}
</style>