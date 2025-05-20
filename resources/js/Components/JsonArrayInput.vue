<script setup>
import { ref } from "vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/DashboardComponent/PrimaryButton.vue";

const props = defineProps({
    modelValue: {
        type: Array,
        required: true,
    },
    label: {
        type: String,
        required: true,
    },
    placeholder: {
        type: String,
        default: "Add new item",
    },
    error: {
        type: String,
        default: "",
    },
});

const emit = defineEmits(["update:modelValue"]);

const newItem = ref("");

const addItem = (e) => {
    e.preventDefault();
    if (newItem.value.trim()) {
        const updatedArray = [...props.modelValue, newItem.value.trim()];
        emit("update:modelValue", updatedArray);
        newItem.value = "";
    }
};

const removeItem = (index) => {
    const updatedArray = props.modelValue.filter((_, i) => i !== index);
    emit("update:modelValue", updatedArray);
};
</script>

<template>
    <div class="col-span-2 mb-4">
        <InputLabel :for="label" :value="label" />
        <div class="flex mt-1">
            <TextInput
                :id="label"
                v-model="newItem"
                type="text"
                class="block w-full"
                :placeholder="placeholder"
                @keydown.enter.prevent="addItem"
            />
            <PrimaryButton type="button" class="ml-2" @click="addItem">
                Tambah
            </PrimaryButton>
        </div>

        <!-- Display items as tags -->
        <div class="mt-2 flex flex-wrap gap-2">
            <div
                v-for="(item, index) in modelValue"
                :key="index"
                class="bg-gray-100 px-2 py-1 rounded-md flex items-center gap-1"
            >
                <span>{{ item }}</span>
                <button
                    type="button"
                    class="text-red-500 hover:text-red-700 focus:outline-none"
                    @click="removeItem(index)"
                >
                    &times;
                </button>
            </div>
        </div>
        <InputError :message="error" class="mt-2" />
    </div>
</template>
