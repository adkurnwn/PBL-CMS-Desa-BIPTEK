<template>
  <div class="textareainput-container border-none" :style="{ width: customWidth }">
    <div class="field-name text-selectedText">{{ fieldName }}</div>
    <div class="relative">
      <textarea 
        class="field-box text-TableText bg-menuBG border border-selectedText border-solid focus:outline-none focus:border-2 focus:border-selectedText" 
        :value="modelValue" 
        @input="$emit('update:modelValue', $event.target.value)"
        @keydown="handleKeyDown" 
        :placeholder="placeholder"
        :required="required"
        :disabled="disabled"
        :id="id"
        :style="{ resize: resizable ? 'vertical' : 'none' }"
        :class="{ 'opacity-75 cursor-not-allowed': disabled }"
        :rows="rows"
      ></textarea>
    </div>
  </div>
</template>

<script>
export default {
  name: 'TextAreaInput',
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
    },
    resizable: {
      type: Boolean,
      default: true
    },
    rows: {
      type: Number,
      default: 3
    },
    type: {
      type: String,
      default: 'text'
    }
  },
  methods: {
    handleKeyDown(event) {
      // For type 'numbers', add numbered items when Enter is pressed
      if (this.type === 'numbers' && event.key === 'Enter') {
        event.preventDefault(); // Prevent default line break
        
        const currentText = this.modelValue || '';
        const lines = currentText.split('\n').filter(line => line.trim());
        
        // Calculate the next number
        const nextNumber = lines.length + 1;
        
        // Add the new numbered line
        const newText = currentText.trim() 
          ? `${currentText}${currentText.endsWith('\n') ? '' : '\n'}${nextNumber}. `
          : `${nextNumber}. `;
        
        this.$emit('update:modelValue', newText);
      }
    }
  },
  emits: ['update:modelValue']
}
</script>

<style scoped>
.textareainput-container {
  padding: 5.3px;
  padding-top: 5.3px;
  padding-bottom: 5.3px;
  padding-left: 5.3px;
  padding-right: 5.3px;
}

.field-name {
  font-size: 8px;
  margin-bottom: 4.4px;
}

.field-box {
  min-height: 60px;
  font-size: 8px;
  padding: 5.3px;
  width: 100%;
  box-sizing: border-box;
  border-radius: 2.2px;
  transition: border 0.2s ease;
}

/* Responsive styles using media queries */
@media (min-width: 640px) { /* sm */
  .textareainput-container {
    padding: 6px;
  }
  
  .field-name {
    font-size: 9px;
    margin-bottom: 5px;
  }
  
  .field-box {
    min-height: 70px;
    font-size: 9px;
    padding: 6px;
    border-radius: 2.5px;
  }
}

@media (min-width: 768px) { /* md */
  .textareainput-container {
    padding: 6.7px;
  }
  
  .field-name {
    font-size: 10px;
    margin-bottom: 5.6px;
  }
  
  .field-box {
    min-height: 80px;
    font-size: 10px;
    padding: 6.7px;
    border-radius: 2.9px;
  }
}

@media (min-width: 1024px) { /* lg */
  .textareainput-container {
    padding: 7.3px;
  }
  
  .field-name {
    font-size: 11px;
    margin-bottom: 6.1px;
  }
  
  .field-box {
    min-height: 90px;
    font-size: 11px;
    padding: 7.3px;
    border-radius: 3.1px;
  }
}

@media (min-width: 1280px) { /* xl */
  .textareainput-container {
    padding: 8px;
  }
  
  .field-name {
    font-size: 12px;
    margin-bottom: 6.67px;
  }
  
  .field-box {
    min-height: 100px;
    font-size: 12px;
    padding: 8px;
    border-radius: 3.33px;
  }
}

@media (min-width: 1536px) { /* 2xl */
  .textareainput-container {
    padding: 9.3px;
  }
  
  .field-name {
    font-size: 14px;
    margin-bottom: 7.7px;
  }
  
  .field-box {
    min-height: 110px;
    font-size: 14px;
    padding: 9.3px;
    border-radius: 3.8px;
  }
}

@media (min-width: 1920px) { /* 3xl */
  .textareainput-container {
    padding: 10.7px;
  }
  
  .field-name {
    font-size: 16px;
    margin-bottom: 8.9px;
  }
  
  .field-box {
    min-height: 120px;
    font-size: 16px;
    padding: 10.7px;
    border-radius: 4.4px;
  }
}

@media (min-width: 2560px) { /* 4xl */
  .textareainput-container {
    padding: 12px;
  }
  
  .field-name {
    font-size: 18px;
    margin-bottom: 10px;
  }
  
  .field-box {
    min-height: 140px;
    font-size: 18px;
    padding: 12px;
    border-radius: 5px;
  }
}

/* Keep existing rules for focus and disabled states */
.field-box:focus {
  outline: none !important;
  box-shadow: none !important;
}

.field-box:disabled {
  background-color: #C2C2C2;
  cursor: not-allowed;
}
</style>
