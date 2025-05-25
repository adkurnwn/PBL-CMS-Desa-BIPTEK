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

        // Make component responsive to match search component
        const dropdownStyles = computed(() => {
            // Return width and height as object instead of inline styles
            return {
                width: props.width, 
                height: props.height
            };
        });

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
    <div class="dropdown-container w-[120px] h-[27px] sm:w-[125px] sm:h-[30px] md:w-[130px] md:h-[35px] xl:w-[133px] xl:h-[29px] 2xl:w-[150px] 2xl:h-[35px] 3xl:w-[175px] 3xl:h-[40px] 4xl:w-[200px] 4xl:h-[44px]">
        <div class="relative flex items-center w-full h-full gap-[6px] sm:gap-[7px] md:gap-[8px] xl:gap-[7px] 2xl:gap-[8px] 3xl:gap-[9px] 4xl:gap-[10px]">
            <!-- Modified to not use @click.stop which prevents event bubbling needed for outside click detection -->
            <input @click="toggleDropdown" type="text" v-model="displayValue" :placeholder="placeholder" readonly
                class="w-full h-full pl-[25px] sm:pl-[27px] md:pl-[30px] xl:pl-[28px] 2xl:pl-[32px] 3xl:pl-[35px] 4xl:pl-12 
                       pr-[8px] sm:pr-[9px] md:pr-[9px] xl:pr-[8px] 2xl:pr-[9px] 3xl:pr-[9px] 4xl:pr-10 
                       py-[1.5px] sm:py-[1.7px] md:py-[1.8px] xl:py-[1.5px] 2xl:py-[1.7px] 3xl:py-[1.8px] 4xl:py-2 
                       rounded-[10px] sm:rounded-[11px] md:rounded-[12px] xl:rounded-[10px] 2xl:rounded-[12px] 3xl:rounded-[13px] 4xl:rounded-[15px]
                       bg-transparent text-TableText border border-filterBorder focus:outline-none focus:ring-1 focus:ring-selectedText focus:border-selectedText 
                       transition-all duration-200 placeholder:text-searchIcon text-ellipsis cursor-pointer
                       text-[10px] sm:text-[11px] md:text-[11.5px] xl:text-[12px] 2xl:text-[14px] 3xl:text-[16px] 4xl:text-[18px]" />

            <div @click="toggleDropdown" class="absolute left-[2.8px] sm:left-[3px] md:left-[3.5px] xl:left-[6.67px] 2xl:left-[3.5px] 3xl:left-[3.8px] 4xl:left-4 text-searchIcon">
                <svg xmlns="http://www.w3.org/2000/svg" 
                    class="w-[14px] h-[14px] sm:w-[15px] sm:h-[15px] md:w-[16px] md:h-[16px] xl:w-[16px] xl:h-[16px] 2xl:w-[20px] 2xl:h-[20px] 3xl:w-[22px] 3xl:h-[22px] 4xl:w-[24px] 4xl:h-[24px]" 
                    viewBox="0 0 24 24" fill="none">
                    <path d="M10 18H14V16H10V18ZM3 6V8H21V6H3ZM6 13H18V11H6V13Z"
                        :fill="isActive ? '#0D6EFD' : '#4C7660'" />
                </svg>
            </div>

            <div @click="toggleDropdown" class="absolute right-[3px] sm:right-[3.5px] md:right-[3.7px] xl:right-[3px] 2xl:right-[3.5px] 3xl:right-[3.8px] 4xl:right-4 text-searchIcon">
                <svg xmlns="http://www.w3.org/2000/svg" 
                    class="w-[12px] h-[12px] sm:w-[13px] sm:h-[13px] md:w-[14px] md:h-[14px] xl:w-[12px] xl:h-[12px] 2xl:w-[15px] 2xl:h-[15px] 3xl:w-[16px] 3xl:h-[16px] 4xl:w-[20px] 4xl:h-[20px]"
                    viewBox="0 0 20 20" fill="none"
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
                class="absolute left-0 right-0 top-full mt-1 bg-menuBG rounded-md shadow-lg z-50 
                       min-w-[180px] sm:min-w-[200px] md:min-w-[220px] xl:min-w-[220px] 2xl:min-w-[230px] 3xl:min-w-[240px] 4xl:min-w-[250px]" 
                @click.stop>
                <div class="p-[8px] sm:p-[9px] md:p-[10px] xl:p-[8px] 2xl:p-[9px] 3xl:p-[10px] 4xl:p-3">
                    <!-- Active filters summary -->
                    <div v-if="Object.keys(filters).length > 0" class="mb-[6px] sm:mb-[7px] md:mb-[8px] xl:mb-[6px] 2xl:mb-[7px] 3xl:mb-[8px] 4xl:mb-3">
                        <div class="font-semibold text-selectedText mb-[5px] sm:mb-[6px] md:mb-[7px] xl:mb-[5px] 2xl:mb-[6px] 3xl:mb-[7px] 4xl:mb-2 
                                  text-[12px] sm:text-[13px] md:text-[14px] xl:text-[12px] 2xl:text-[14px] 3xl:text-[15px] 4xl:text-base
                                  flex justify-between items-center">
                            <span>{{ activeFiltersTitle }}</span>
                            <button @click.stop="clearAllFilters" 
                                   class="text-[8px] sm:text-[9px] md:text-[10px] xl:text-[8px] 2xl:text-[10px] 3xl:text-[11px] 4xl:text-xs text-red-500 hover:text-red-700">
                                {{ clearAllLabel }}
                            </button>
                        </div>
                        <div class="flex flex-wrap gap-[5px] sm:gap-[6px] md:gap-[7px] xl:gap-[5px] 2xl:gap-[6px] 3xl:gap-[7px] 4xl:gap-2">
                            <div v-for="(valueId, fieldId) in filters" :key="fieldId"
                                class="inline-flex items-center rounded-full bg-selectedBG 
                                       px-[6px] sm:px-[7px] md:px-[8px] xl:px-[6px] 2xl:px-[7px] 3xl:px-[8px] 4xl:px-2.5 
                                       py-[2px] sm:py-[2.5px] md:py-[3px] xl:py-[2px] 2xl:py-[2.5px] 3xl:py-[3px] 4xl:py-1 
                                       text-[8px] sm:text-[9px] md:text-[10px] xl:text-[8px] 2xl:text-[10px] 3xl:text-[11px] 4xl:text-xs 
                                       font-medium text-selectedText border border-selectedText">
                                <span>{{ getFilterDisplayName(fieldId, valueId) }}</span>
                                <button @click.stop="removeFilter(fieldId)"
                                    class="ml-1 text-selectedText hover:text-red-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" 
                                         class="w-[10px] h-[10px] sm:w-[11px] sm:h-[11px] md:w-[12px] md:h-[12px] xl:w-[10px] xl:h-[10px] 2xl:w-[12px] 2xl:h-[12px] 3xl:w-[13px] 3xl:h-[13px] 4xl:w-[14px] 4xl:h-[14px]" 
                                         viewBox="0 0 24 24"
                                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <line x1="18" y1="6" x2="6" y2="18"></line>
                                        <line x1="6" y1="6" x2="18" y2="18"></line>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="pt-[6px] sm:pt-[7px] md:pt-[8px] xl:pt-[6px] 2xl:pt-[7px] 3xl:pt-[8px] 4xl:pt-3">
                        <!-- Choose field step -->
                        <div v-if="currentStep === 'fields'">
                            <div class="font-semibold text-selectedText mb-[5px] sm:mb-[6px] md:mb-[7px] xl:mb-[5px] 2xl:mb-[6px] 3xl:mb-[7px] 4xl:mb-2
                                       text-[12px] sm:text-[13px] md:text-[14px] xl:text-[12px] 2xl:text-[14px] 3xl:text-[15px] 4xl:text-base">
                                <span>{{ dropdownTitle }}</span>
                            </div>
                            <div class="max-h-[150px] sm:max-h-[170px] md:max-h-[190px] xl:max-h-[170px] 2xl:max-h-[200px] 3xl:max-h-[220px] 4xl:max-h-[250px] overflow-y-auto">
                                <div v-for="field in filterFields" :key="field.id" @click.stop="selectField(field)"
                                    class="p-[5px] sm:p-[6px] md:p-[7px] xl:p-[5px] 2xl:p-[6px] 3xl:p-[7px] 4xl:p-2 
                                           hover:bg-selectedBG cursor-pointer 
                                           text-[10px] sm:text-[11px] md:text-[12px] xl:text-[10px] 2xl:text-[12px] 3xl:text-[13px] 4xl:text-sm 
                                           rounded flex justify-between items-center"
                                    :class="{ 'bg-blue-50': isFieldFiltered(field.id) }">
                                    <span>{{ field.name }}</span>
                                    <div class="flex items-center">
                                        <span v-if="isFieldFiltered(field.id)" class="text-selectedText mr-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" 
                                                 class="w-[12px] h-[12px] sm:w-[13px] sm:h-[13px] md:w-[14px] md:h-[14px] xl:w-[12px] xl:h-[12px] 2xl:w-[14px] 2xl:h-[14px] 3xl:w-[15px] 3xl:h-[15px] 4xl:w-[16px] 4xl:h-[16px]"
                                                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"
                                                 stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="20 6 9 17 4 12"></polyline>
                                            </svg>
                                        </span>
                                        <span class="text-gray-500">
                                            <svg xmlns="http://www.w3.org/2000/svg" 
                                                 class="w-[10px] h-[10px] sm:w-[11px] sm:h-[11px] md:w-[12px] md:h-[12px] xl:w-[10px] xl:h-[10px] 2xl:w-[12px] 2xl:h-[12px] 3xl:w-[13px] 3xl:h-[13px] 4xl:w-[14px] 4xl:h-[14px]"
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
                            <div class="font-semibold mb-[5px] sm:mb-[6px] md:mb-[7px] xl:mb-[5px] 2xl:mb-[6px] 3xl:mb-[7px] 4xl:mb-2
                                       text-[12px] sm:text-[13px] md:text-[14px] xl:text-[12px] 2xl:text-[14px] 3xl:text-[15px] 4xl:text-base 
                                       flex items-center">
                                <button @click.stop="backToFields"
                                    class="mr-2 text-selectedText hover:text-menuText flex items-center gap-[6px] sm:gap-[7px] md:gap-[8px] xl:gap-[6px] 2xl:gap-[7px] 3xl:gap-[8px] 4xl:gap-[10px]">
                                    <svg xmlns="http://www.w3.org/2000/svg" 
                                         class="w-[12px] h-[12px] sm:w-[13px] sm:h-[13px] md:w-[14px] md:h-[14px] xl:w-[12px] xl:h-[12px] 2xl:w-[14px] 2xl:h-[14px] 3xl:w-[15px] 3xl:h-[15px] 4xl:w-[16px] 4xl:h-[16px]"
                                         viewBox="0 0 24 24"
                                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="15 18 9 12 15 6"></polyline>
                                    </svg>
                                    <span>{{ selectedField.name }}</span>
                                </button>
                            </div>

                            <!-- Selected filter indicator for the current field -->
                            <div v-if="isFieldFiltered(selectedField.id)"
                                class="mb-[5px] sm:mb-[6px] md:mb-[7px] xl:mb-[5px] 2xl:mb-[6px] 3xl:mb-[7px] 4xl:mb-2 
                                       bg-selectedBG border border-selectedText 
                                       p-[5px] sm:p-[6px] md:p-[7px] xl:p-[5px] 2xl:p-[6px] 3xl:p-[7px] 4xl:p-2 
                                       rounded text-[10px] sm:text-[11px] md:text-[12px] xl:text-[10px] 2xl:text-[12px] 3xl:text-[13px] 4xl:text-sm">
                                {{ filterPrefix }} {{ getFilterDisplayName(selectedField.id,
                                    filters[selectedField.id]).split(': ')[1] }}
                            </div>

                            <div class="max-h-[150px] sm:max-h-[170px] md:max-h-[190px] xl:max-h-[170px] 2xl:max-h-[200px] 3xl:max-h-[220px] 4xl:max-h-[250px] overflow-y-auto">
                                <div v-for="option in selectedField.options" :key="option[valueKey]"
                                    @click.stop="selectValue(option)"
                                    class="p-[5px] sm:p-[6px] md:p-[7px] xl:p-[5px] 2xl:p-[6px] 3xl:p-[7px] 4xl:p-2 
                                           hover:bg-selectedBG cursor-pointer 
                                           text-[10px] sm:text-[11px] md:text-[12px] xl:text-[10px] 2xl:text-[12px] 3xl:text-[13px] 4xl:text-sm 
                                           text-TableText rounded flex justify-between items-center"
                                    :class="{ 'bg-blue-50 font-medium text-selectedText': filters[selectedField.id] === option[valueKey] }">
                                    {{ option[labelKey] }}
                                    <span v-if="filters[selectedField.id] === option[valueKey]" class="text-selectedText">
                                        <svg xmlns="http://www.w3.org/2000/svg" 
                                             class="w-[12px] h-[12px] sm:w-[13px] sm:h-[13px] md:w-[14px] md:h-[14px] xl:w-[12px] xl:h-[12px] 2xl:w-[14px] 2xl:h-[14px] 3xl:w-[15px] 3xl:h-[15px] 4xl:w-[16px] 4xl:h-[16px]"
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