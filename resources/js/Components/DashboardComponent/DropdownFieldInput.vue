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
                        :class="{ 'rotate-180': isOpen, 'transition-transform': true }">
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
                    'absolute left-0 right-0 mt-1 bg-menuBG rounded-md shadow-lg z-50 min-w-[250px]',
                    { 'top-full': !dropUpwards, 'bottom-full mb-1': dropUpwards }
                ]" 
                @click.stop>
                <div class="p-3">
                    <!-- Search box -->
                    <div class="mb-3">
                        <input 
                            type="text" 
                            v-model="searchQuery" 
                            placeholder="Cari..." 
                            class="w-full p-2 border border-selectedText rounded focus:outline-none focus:ring-1 focus:ring-selectedText"
                            @click.stop
                        />
                    </div>

                    <!-- Options list with custom scrollbar -->
                    <div class="options-container" 
                         style="max-height: calc(4 * 34px); overflow-y: auto; scrollbar-width: thin; scrollbar-color: #4C7660 transparent;">
                        <div 
                            v-for="option in filteredOptions" 
                            :key="option.value" 
                            @click="selectOption(option.value)"
                            class="p-2 hover:bg-selectedBG cursor-pointer text-sm text-TableText rounded flex justify-between items-center"
                            style="height: 34px;"
                            :class="{ 'bg-blue-50 font-medium text-selectedText': modelValue === option.value }"
                        >
                            {{ option.label }}
                            <span v-if="modelValue === option.value" class="text-selectedText">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                            </span>
                        </div>
                        
                        <!-- No results message -->
                        <div v-if="filteredOptions.length === 0" class="p-2 text-sm text-gray-500 text-center">
                            No matching options found
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
        }
    },
    emits: ['update:modelValue'],
    data() {
        return {
            isOpen: false,
            searchQuery: '',
            dropUpwards: false,
        }
    },
    computed: {
        displayText() {
            if (!this.modelValue) return '';
            const selectedOption = this.options.find(option => option.value === this.modelValue);
            return selectedOption ? selectedOption.label : '';
        },
        filteredOptions() {
            if (!this.searchQuery.trim()) return this.options;
            
            const query = this.searchQuery.toLowerCase();
            return this.options.filter(option => 
                option.label.toLowerCase().includes(query)
            );
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
                const dropdownHeight = 300; // Approximate height for dropdown (includes search box and max 4 items)
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
        }
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
    padding: 12px;
}

.field-name {
    font-size: 18px;
    margin-bottom: 10px;
}

.field-box {
    height: 46px;
    font-size: 18px;
    padding: 12px 12px 12px 12px;
    width: 100%;
    box-sizing: border-box;
    border-radius: 5px;
    appearance: none !important;
    -webkit-appearance: none !important;
    -moz-appearance: none !important;
    background-image: none !important;
}

.rotate-180 {
    transform: rotate(180deg);
    transition: transform 0.2s ease-in-out;
}
</style>
