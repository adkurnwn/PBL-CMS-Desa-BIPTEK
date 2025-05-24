<template>
  <div class="checkbox-field-container" :style="{ width: customWidth }">
    <div class="field-name text-selectedText">{{ fieldName }}</div>
    <div class="checkbox-container">
      <input
        :id="id"
        v-model="localValue"
        type="checkbox"
        class="checkbox-input rounded border-menuBG text-selectedText shadow-sm focus:border-selectedBG focus:ring focus:ring-selectedBG focus:ring-opacity-50"
        :disabled="disabled"
        :class="{ 'opacity-75 cursor-not-allowed': disabled }"
      />
      <label :for="id" class="checkbox-label text-TableText">
        {{ labelText }}
      </label>
    </div>
  </div>
</template>

<script>
export default {
  name: 'CheckboxField',
  props: {
    fieldName: {
      type: String,
      required: true
    },
    modelValue: {
      type: Boolean,
      default: false
    },
    labelText: {
      type: String,
      default: ''
    },
    customWidth: {
      type: String,
      default: '100%'
    },
    disabled: {
      type: Boolean,
      default: false
    },
    id: {
      type: String,
      required: true
    }
  },
  computed: {
    localValue: {
      get() {
        return this.modelValue;
      },
      set(value) {
        this.$emit('update:modelValue', value);
      }
    }
  },
  emits: ['update:modelValue']
}
</script>

<style scoped>
.checkbox-field-container {
  padding: 5.3px;
}

.field-name {
  font-size: 8px;
  margin-bottom: 4.4px;
}

.checkbox-container {
  display: flex;
  align-items: center;
  gap: 8px;
  min-height: 20.7px;
}

.checkbox-input {
  width: 16px;
  height: 16px;
  cursor: pointer;
}

.checkbox-label {
  font-size: 8px;
  cursor: pointer;
}

/* Responsive styles */
@media (min-width: 640px) {
  .checkbox-field-container {
    padding: 6px;
  }

  .field-name {
    font-size: 9px;
    margin-bottom: 5px;
  }

  .checkbox-container {
    min-height: 23.5px;
  }

  .checkbox-input {
    width: 18px;
    height: 18px;
  }

  .checkbox-label {
    font-size: 9px;
  }
}

@media (min-width: 768px) {
  .checkbox-field-container {
    padding: 6.7px;
  }

  .field-name {
    font-size: 10px;
    margin-bottom: 5.6px;
  }

  .checkbox-container {
    min-height: 26.3px;
  }

  .checkbox-input {
    width: 20px;
    height: 20px;
  }

  .checkbox-label {
    font-size: 10px;
  }
}

@media (min-width: 1024px) {
  .checkbox-field-container {
    padding: 7.3px;
  }

  .field-name {
    font-size: 11px;
    margin-bottom: 6.1px;
  }

  .checkbox-container {
    min-height: 28.7px;
  }

  .checkbox-input {
    width: 22px;
    height: 22px;
  }

  .checkbox-label {
    font-size: 11px;
  }
}

@media (min-width: 1280px) {
  .checkbox-field-container {
    padding: 8px;
  }

  .field-name {
    font-size: 12px;
    margin-bottom: 6.67px;
  }

  .checkbox-container {
    min-height: 31px;
  }

  .checkbox-input {
    width: 24px;
    height: 24px;
  }

  .checkbox-label {
    font-size: 12px;
  }
}

@media (min-width: 1536px) {
  .checkbox-field-container {
    padding: 9.3px;
  }

  .field-name {
    font-size: 14px;
    margin-bottom: 7.7px;
  }

  .checkbox-container {
    min-height: 35.3px;
  }

  .checkbox-input {
    width: 28px;
    height: 28px;
  }

  .checkbox-label {
    font-size: 14px;
  }
}

@media (min-width: 1920px) {
  .checkbox-field-container {
    padding: 10.7px;
  }

  .field-name {
    font-size: 16px;
    margin-bottom: 8.9px;
  }

  .checkbox-container {
    min-height: 40px;
  }

  .checkbox-input {
    width: 32px;
    height: 32px;
  }

  .checkbox-label {
    font-size: 16px;
  }
}

@media (min-width: 2560px) {
  .checkbox-field-container {
    padding: 12px;
  }

  .field-name {
    font-size: 18px;
    margin-bottom: 10px;
  }

  .checkbox-container {
    min-height: 46px;
  }

  .checkbox-input {
    width: 36px;
    height: 36px;
  }

  .checkbox-label {
    font-size: 18px;
  }
}

.checkbox-input:disabled {
  cursor: not-allowed;
}

.checkbox-label:has(+ .checkbox-input:disabled) {
  cursor: not-allowed;
}
</style>
