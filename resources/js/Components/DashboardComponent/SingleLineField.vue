<template>
  <div class="p-[5.3px] sm:p-[6px] md:p-[6.7px] lg:p-[7.3px] xl:p-[8px] 2xl:p-[9.3px] 3xl:p-[10.7px] 4xl:p-[12px]" :style="{ width: customWidth }">
    <div class="text-selectedText text-[8px] mb-[4.4px] sm:text-[9px] sm:mb-[5px] md:text-[10px] md:mb-[5.6px] lg:text-[11px] lg:mb-[6.1px] xl:text-[12px] xl:mb-[6.67px] 2xl:text-[14px] 2xl:mb-[7.7px] 3xl:text-[16px] 3xl:mb-[8.9px] 4xl:text-[18px] 4xl:mb-[10px]">{{ fieldName }}</div>
    <div class="flex items-start gap-[5px]">
      <div class="relative flex-1">
        <input
          class="h-[20.7px] text-[8px] p-[5.3px] w-full box-border rounded-[2.2px] transition-[border] duration-200 ease-in-out text-TableText bg-menuBG border border-selectedText border-solid focus:outline-none focus:border-2 focus:border-selectedText focus:shadow-none disabled:bg-[#C2C2C2] disabled:cursor-not-allowed sm:h-[23.5px] sm:text-[9px] sm:p-[6px] sm:rounded-[2.5px] md:h-[26.3px] md:text-[10px] md:p-[6.7px] md:rounded-[2.9px] lg:h-[28.7px] lg:text-[11px] lg:p-[7.3px] lg:rounded-[3.1px] xl:h-[31px] xl:text-[12px] xl:p-[8px] xl:rounded-[3.33px] 2xl:h-[35.3px] 2xl:text-[14px] 2xl:p-[9.3px] 2xl:rounded-[3.8px] 3xl:h-[40px] 3xl:text-[16px] 3xl:p-[10.7px] 3xl:rounded-[4.4px] 4xl:h-[46px] 4xl:text-[18px] 4xl:p-[12px] 4xl:rounded-[5px]"
          :type="showPassword ? 'text' : (type === 'numbers' ? 'text' : type)" 
          :value="modelValue"
          @input="handleInput"
          @keydown="handleKeyDown"
          :placeholder="placeholder" 
          :required="required"
          :disabled="disabled" 
          :id="id" 
          :class="{ 'opacity-75 cursor-not-allowed': disabled }" />
        <div v-if="type === 'password'" @click="togglePasswordVisibility"
          class="absolute right-3 top-1/2 transform -translate-y-1/2 cursor-pointer w-[14.7px] h-[14.7px] sm:w-[16.7px] sm:h-[16.7px] md:w-[18.7px] md:h-[18.7px] lg:w-[20.3px] lg:h-[20.3px] xl:w-[14.7px] xl:h-[14.7px] 2xl:w-[16.7px] 2xl:h-[16.7px] 3xl:w-[19.3px] 3xl:h-[19.3px] 4xl:w-[22px] 4xl:h-[22px] flex items-center justify-center">
          <!-- Show password icon (eye) - Updated -->
          <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"
            class="text-selectedText">
            <path
              d="M480-320q75 0 127.5-52.5T660-500q0-75-52.5-127.5T480-680q-75 0-127.5 52.5T300-500q0 75 52.5 127.5T480-320Zm0-72q-45 0-76.5-31.5T372-500q0-45 31.5-76.5T480-608q45 0 76.5 31.5T588-500q0 45-31.5 76.5T480-392Zm0 192q-146 0-266-81.5T40-500q54-137 174-218.5T480-800q146 0 266 81.5T920-500q-54 137-174 218.5T480-200Zm0-300Zm0 220q113 0 207.5-59.5T832-500q-50-101-144.5-160.5T480-720q-113 0-207.5 59.5T128-500q50 101 144.5 160.5T480-280Z"
              fill="currentColor" />
          </svg>

          <!-- Hide password icon (crossed eye) -->
          <svg v-else xmlns="http://www.w3.org/2000/svg" height="16" viewBox="http://www.w3.org/2000/svg" width="16"
            class="text-selectedText">
            <path
              d="m644-428-58-58q9-47-27-88t-93-32l-58-58q17-8 34.5-12t37.5-4q75 0 127.5 52.5T660-500q0 20-4 37.5T644-428Zm128 126-58-56q38-29 67.5-63.5T832-500q-50-101-143.5-160.5T480-720q-29 0-57 4t-55 12l-62-62q41-17 84-25.5t90-8.5q151 0 269 83.5T920-500q-23 59-60.5 109.5T772-302Zm20 246L624-222q-35 11-70.5 16.5T480-200q-151 0-269-83.5T40-500q21-53 53-98.5t73-81.5L56-792l56-56 736 736-56 56ZM222-624q-29 26-53 57t-41 67q50 101 143.5 160.5T480-280q20 0 39-2.5t39-5.5l-36-38q-11 3-21 4.5t-21 1.5q-75 0-127.5-52.5T300-500q0-11 1.5-21t4.5-21l-84-82Zm319 93Zm-151 75Z"
              fill="currentColor" />
          </svg>
        </div>
      </div>
      <div v-if="type === 'add'" class="mt-0 flex items-center h-[20.7px] sm:h-[23.5px] md:h-[26.3px] lg:h-[28.7px] xl:h-[31px] 2xl:h-[35.3px] 3xl:h-[40px] 4xl:h-[46px]">
        <PrimaryButton type="button" @click="$emit('click')">
          Tambah
        </PrimaryButton>
      </div>
    </div>
  </div>
</template>

<script setup>
import PrimaryButton from '@/Components/DashboardComponent/PrimaryButton.vue';
</script>

<script>
export default {
  name: 'SingleLineField',
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
      default: ''
    },
    type: {
      type: String,
      default: 'text'
    },
    customWidth: {
      type: String,
      default: '100%'
    },
    required: {
      type: Boolean,
      default: false
    },
    disabled: {
      type: Boolean,
      default: false
    },
    id: {
      type: String,
      default: ''
    }
  },
  data() {
    return {
      showPassword: false
    }
  },
  methods: {
    togglePasswordVisibility() {
      this.showPassword = !this.showPassword;
    },
    handleKeyDown(event) {
      // For type 'numbers', capture Enter key press to add numbered items
      if (this.type === 'numbers' && event.key === 'Enter') {
        event.preventDefault(); // Prevent form submission
        
        if (this.modelValue.trim()) {
          // Emit a custom event with the current value
          this.$emit('add-numbered-item', this.modelValue);
          // Clear the input
          this.$emit('update:modelValue', '');
        }
      }
    },
    handleInput(event) {
      // First emit the custom input event if provided
      this.$emit('input', event);
      
      // Then emit the standard update event
      this.$emit('update:modelValue', event.target.value);
    }
  },
  emits: ['update:modelValue', 'click', 'add-numbered-item', 'input']
}
</script>
