<template>
    <div class="dropdown-field-container" :style="{ width: customWidth }">
        <div class="field-name text-selectedText">{{ fieldName }}</div>
        <div class="relative">
            <div class="relative flex items-center w-full">
                <input 
                    @click="toggleDropdown" 
                    type="text" 
                    :value="displayText" 
                    :placeholder="placeholder" 
                    readonly
                    :id="id"
                    :required="required"
                    class="field-box text-TableText bg-menuBG border border-selectedText border-solid focus:outline-none focus:ring-1 focus:ring-selectedText focus:border-selectedText transition-all duration-200 cursor-pointer"
                />
                
                <div class="absolute right-3 top-1/2 transform -translate-y-1/2 pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none"
                        :class="{ 'rotate-180': isOpen, 'transition-transform': true }"
                        class="w-[8.9px] h-[8.9px] sm:w-[10.2px] sm:h-[10.2px] md:w-[11.6px] md:h-[11.6px] lg:w-[12.5px] lg:h-[12.5px] xl:w-[13.3px] xl:h-[13.3px] 2xl:w-[15.4px] 2xl:h-[15.4px] 3xl:w-[17.5px] 3xl:h-[17.5px] 4xl:w-[20px] 4xl:h-[20px]">
                        <mask id="mask0_1692_7218" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="20"
                            height="20">
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
            <div v-if="isOpen"
                :class=" [
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
                        <div 
                            v-for="option in filteredOptions" 
                            :key="option.value" 
                            @click="selectOption(option.value)"
                            class="p-[1.8px] sm:p-[2px] md:p-[2.2px] lg:p-[2.4px] xl:p-[2.67px] 2xl:p-[3px] 3xl:p-[3.6px] 4xl:p-[4px] hover:bg-selectedBG cursor-pointer rounded-[2.2px] sm:rounded-[2.5px] md:rounded-[2.9px] lg:rounded-[3.1px] xl:rounded-[3.33px] 2xl:rounded-[3.8px] 3xl:rounded-[4.4px] 4xl:rounded-[5px] flex justify-between items-center text-TableText text-[8px] sm:text-[9px] md:text-[10px] lg:text-[11px] xl:text-[12px] 2xl:text-[14px] 3xl:text-[16px] 4xl:text-[18px]"
                            :style="{ 'height': `${itemHeight}px` }"
                            :class="{ 'bg-blue-50 font-medium text-selectedText': isOptionSelected(option.value) }"
                        >
                            {{ option.label }}
                            <span v-if="isOptionSelected(option.value)" class="text-selectedText">
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
                        <div v-if="filteredOptions.length === 0" class="p-[1.8px] sm:p-[2px] md:p-[2.2px] lg:p-[2.4px] xl:p-[2.67px] 2xl:p-[3px] 3xl:p-[3.6px] 4xl:p-[4px] text-gray-500 text-center text-[8px] sm:text-[9px] md:text-[10px] lg:text-[11px] xl:text-[12px] 2xl:text-[14px] 3xl:text-[16px] 4xl:text-[18px]">
                            Tidak ada opsi yang sesuai
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'DropdownFieldInput',
    props: {
        fieldName: {
            type: String,
            required: true
        },
        modelValue: {
            type: [String, Number],
            default: ''
        },
        placeholder: {
            type: String,
            default: 'Select an option'
        },
        options: {
            type: Array,
            required: true
        },
        customWidth: {
            type: String,
            default: '100%'
        },
        required: {
            type: Boolean,
            default: false
        },
        id: {
            type: String,
            default: ''
        },
        // Add new prop for manually setting display text when options are loading
        initialLabel: {
            type: String,
            default: ''
        },
        // Add prop to indicate if we're loading data
        loading: {
            type: Boolean,
            default: false
        }
    },
    emits: ['update:modelValue'],
    data() {
        return {
            isOpen: false,
            searchQuery: '',
            dropUpwards: false,
            itemHeight: 15.1, // Default size before we calculate breakpoint adjustments
            // Add a local cached label to persist display value during async loading
            cachedLabel: '',
        }
    },
    computed: {
        displayText() {
            // If we have a cached label from a previous render, use it
            if (this.cachedLabel && !this.options.length) {
                return this.cachedLabel;
            }
            
            // If initialLabel is provided and we don't have options yet, use it
            if (this.initialLabel && (!this.options.length || this.loading)) {
                return this.initialLabel;
            }
            
            // Standard behavior - find in options
            if (!this.modelValue) return '';
            const selectedOption = this.options.find(option => option.value === this.modelValue || 
                                                                option.value.toString() === this.modelValue.toString());
            
            // If we found a match, cache the label and return it
            if (selectedOption) {
                this.cachedLabel = selectedOption.label;
                return selectedOption.label;
            }
            
            // If we have a value but no matching option (might be loading), show the value itself
            return this.modelValue ? this.modelValue.toString() : '';
        },
        filteredOptions() {
            if (!this.searchQuery.trim()) return this.options;
            
            const query = this.searchQuery.toLowerCase();
            return this.options.filter(option => 
                option.label.toLowerCase().includes(query)
            );
        },
        
        // Reactive item height based on current breakpoint
        itemHeight() {
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
        }
    },
    methods: {
        toggleDropdown() {
            this.isOpen = !this.isOpen;
            if (this.isOpen) {
                this.searchQuery = '';
                this.checkDropdownDirection();
                // Add event listener for clicks outside
                document.addEventListener('click', this.handleOutsideClick);
                window.addEventListener('scroll', this.checkDropdownDirection);
                window.addEventListener('resize', this.checkDropdownDirection);
            } else {
                document.removeEventListener('click', this.handleOutsideClick);
                window.removeEventListener('scroll', this.checkDropdownDirection);
                window.removeEventListener('resize', this.checkDropdownDirection);
            }
        },
        checkDropdownDirection() {
            this.$nextTick(() => {
                const element = this.$el;
                const rect = element.getBoundingClientRect();
                // Use a dynamic height calculation based on itemHeight
                const dropdownHeight = (this.itemHeight * 4) + 45; // 4 items plus search box and padding
                const spaceBelow = window.innerHeight - rect.bottom;
                this.dropUpwards = spaceBelow < dropdownHeight && rect.top > dropdownHeight;
            });
        },
        handleOutsideClick(event) {
            // Check if click was outside the component
            if (!this.$el.contains(event.target)) {
                this.isOpen = false;
                document.removeEventListener('click', this.handleOutsideClick);
                window.removeEventListener('scroll', this.checkDropdownDirection);
                window.removeEventListener('resize', this.checkDropdownDirection);
            }
        },
        selectOption(value) {
            this.$emit('update:modelValue', value);
            this.isOpen = false;
            document.removeEventListener('click', this.handleOutsideClick);
            window.removeEventListener('scroll', this.checkDropdownDirection);
            window.removeEventListener('resize', this.checkDropdownDirection);
        },
        updateItemHeight() {
            // This will trigger a reactivity update in the itemHeight computed property
            this.$forceUpdate();
        },
        // Add a new method to safely compare option values regardless of type
        isOptionSelected(optionValue) {
            if (!this.modelValue && !optionValue) return true;
            if (!this.modelValue || !optionValue) return false;
            
            // Convert both values to strings for comparison to handle type differences
            return String(this.modelValue) === String(optionValue);
        },
    },
    watch: {
        // Add watcher to update cached label when options change
        options: {
            handler(newOptions) {
                if (this.modelValue && newOptions.length) {
                    const selectedOption = newOptions.find(option => 
                        option.value === this.modelValue || 
                        option.value.toString() === this.modelValue.toString()
                    );
                    if (selectedOption) {
                        this.cachedLabel = selectedOption.label;
                    }
                }
            },
            deep: true,
            immediate: true
        },
        // Watch for model value changes to update cached label
        modelValue: {
            handler(newValue) {
                if (newValue && this.options.length) {
                    const selectedOption = this.options.find(option => 
                        option.value === newValue || 
                        option.value.toString() === newValue.toString()
                    );
                    if (selectedOption) {
                        this.cachedLabel = selectedOption.label;
                    }
                }
            },
            immediate: true
        }
    },
    mounted() {
        // Add window resize listener to update itemHeight
        window.addEventListener('resize', this.updateItemHeight);
        this.updateItemHeight();
    },
    beforeUnmount() {
        // Clean up event listener
        document.removeEventListener('click', this.handleOutsideClick);
        window.removeEventListener('scroll', this.checkDropdownDirection);
        window.removeEventListener('resize', this.checkDropdownDirection);
    }
}
</script>

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

.rotate-180 {
    transform: rotate(180deg);
    transition: transform 0.2s ease-in-out;
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
</style>
