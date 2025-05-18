<template>
    <Head title="Application Settings" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Application Settings
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="mb-4 border-b border-gray-200">
                    <ul
                        class="flex flex-wrap -mb-px text-sm font-medium text-center"
                    >
                        <li class="mr-2">
                            <a
                                href="#"
                                :class="{
                                    'text-blue-600 border-b-2 border-blue-600':
                                        activeTab === 'general',
                                    'text-gray-500 hover:text-gray-600':
                                        activeTab !== 'general',
                                }"
                                class="inline-block p-4 rounded-t-lg"
                                @click.prevent="setActiveTab('general')"
                            >
                                General
                            </a>
                        </li>
                        <li class="mr-2">
                            <a
                                href="#"
                                :class="{
                                    'text-blue-600 border-b-2 border-blue-600':
                                        activeTab === 'database',
                                    'text-gray-500 hover:text-gray-600':
                                        activeTab !== 'database',
                                }"
                                class="inline-block p-4 rounded-t-lg"
                                @click.prevent="setActiveTab('database')"
                            >
                                Database
                            </a>
                        </li>
                        <li class="mr-2">
                            <a
                                href="#"
                                :class="{
                                    'text-blue-600 border-b-2 border-blue-600':
                                        activeTab === 'mail',
                                    'text-gray-500 hover:text-gray-600':
                                        activeTab !== 'mail',
                                }"
                                class="inline-block p-4 rounded-t-lg"
                                @click.prevent="setActiveTab('mail')"
                            >
                                Mail
                            </a>
                        </li>
                        <li class="mr-2">
                            <a
                                href="#"
                                :class="{
                                    'text-blue-600 border-b-2 border-blue-600':
                                        activeTab === 'theme',
                                    'text-gray-500 hover:text-gray-600':
                                        activeTab !== 'theme',
                                }"
                                class="inline-block p-4 rounded-t-lg"
                                @click.prevent="setActiveTab('theme')"
                            >
                                Theme
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form
                            @submit.prevent="submit"
                            enctype="multipart/form-data"
                        >
                            <!-- General Settings -->
                            <div v-show="activeTab === 'general'">
                                <h3 class="mb-4 text-lg font-semibold">
                                    General Settings
                                </h3>
                                <div
                                    class="grid grid-cols-1 gap-4 md:grid-cols-2"
                                >
                                    <!-- App Name -->
                                    <div>
                                        <InputLabel
                                            for="app_name"
                                            value="Application Name"
                                        />
                                        <TextInput
                                            id="app_name"
                                            v-model="form.app_name"
                                            type="text"
                                            class="mt-1 block w-full"
                                            required
                                        />
                                        <InputError
                                            :message="form.errors.app_name"
                                            class="mt-2"
                                        />
                                    </div>

                                    <!-- App URL -->
                                    <div>
                                        <InputLabel
                                            for="app_url"
                                            value="Application URL"
                                        />
                                        <TextInput
                                            id="app_url"
                                            v-model="form.app_url"
                                            type="text"
                                            class="mt-1 block w-full"
                                            required
                                        />
                                        <InputError
                                            :message="form.errors.app_url"
                                            class="mt-2"
                                        />
                                    </div>

                                    <!-- Timezone -->
                                    <div>
                                        <InputLabel
                                            for="app_timezone"
                                            value="Timezone"
                                        />
                                        <select
                                            id="app_timezone"
                                            v-model="form.app_timezone"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            required
                                        >
                                            <option
                                                v-for="timezone in timezones"
                                                :key="timezone"
                                                :value="timezone"
                                            >
                                                {{ timezone }}
                                            </option>
                                        </select>
                                        <InputError
                                            :message="form.errors.app_timezone"
                                            class="mt-2"
                                        />
                                    </div>

                                    <!-- Favicon -->
                                    <div>
                                        <InputLabel
                                            for="favicon"
                                            value="Favicon"
                                        />
                                        <div class="flex items-center mt-2">
                                            <img
                                                v-if="faviconPreview"
                                                :src="faviconPreview"
                                                class="h-16 w-16 object-cover rounded mr-4"
                                            />
                                            <img
                                                v-else-if="settings.favicon"
                                                :src="`/storage/${settings.favicon}`"
                                                class="h-16 w-16 object-cover rounded mr-4"
                                            />
                                        </div>
                                        <input
                                            id="favicon"
                                            type="file"
                                            @input="handleFaviconChange"
                                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                            accept="image/png, image/jpeg, image/gif, image/ico"
                                        />
                                        <p class="mt-1 text-sm text-gray-500">
                                            Recommended size: 32x32px or 64x64px
                                            (PNG or ICO format)
                                        </p>
                                        <InputError
                                            :message="form.errors.favicon"
                                            class="mt-2"
                                        />
                                    </div>
                                </div>
                            </div>

                            <!-- Database Settings -->
                            <div v-show="activeTab === 'database'">
                                <h3 class="mb-4 text-lg font-semibold">
                                    Database Settings
                                </h3>
                                <div
                                    class="grid grid-cols-1 gap-4 md:grid-cols-2"
                                >
                                    <!-- Database Connection -->
                                    <div>
                                        <InputLabel
                                            for="db_connection"
                                            value="Database Connection"
                                        />
                                        <select
                                            id="db_connection"
                                            v-model="form.db_connection"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        >
                                            <option
                                                v-for="connection in dbConnections"
                                                :key="connection"
                                                :value="connection"
                                            >
                                                {{ connection }}
                                            </option>
                                        </select>
                                        <InputError
                                            :message="form.errors.db_connection"
                                            class="mt-2"
                                        />
                                    </div>

                                    <!-- Database Host -->
                                    <div>
                                        <InputLabel
                                            for="db_host"
                                            value="Database Host"
                                        />
                                        <TextInput
                                            id="db_host"
                                            v-model="form.db_host"
                                            type="text"
                                            class="mt-1 block w-full"
                                        />
                                        <InputError
                                            :message="form.errors.db_host"
                                            class="mt-2"
                                        />
                                    </div>

                                    <!-- Database Port -->
                                    <div>
                                        <InputLabel
                                            for="db_port"
                                            value="Database Port"
                                        />
                                        <TextInput
                                            id="db_port"
                                            v-model="form.db_port"
                                            type="text"
                                            class="mt-1 block w-full"
                                        />
                                        <InputError
                                            :message="form.errors.db_port"
                                            class="mt-2"
                                        />
                                    </div>

                                    <!-- Database Name -->
                                    <div>
                                        <InputLabel
                                            for="db_database"
                                            value="Database Name"
                                        />
                                        <TextInput
                                            id="db_database"
                                            v-model="form.db_database"
                                            type="text"
                                            class="mt-1 block w-full"
                                        />
                                        <InputError
                                            :message="form.errors.db_database"
                                            class="mt-2"
                                        />
                                    </div>

                                    <!-- Database Username -->
                                    <div>
                                        <InputLabel
                                            for="db_username"
                                            value="Database Username"
                                        />
                                        <TextInput
                                            id="db_username"
                                            v-model="form.db_username"
                                            type="text"
                                            class="mt-1 block w-full"
                                        />
                                        <InputError
                                            :message="form.errors.db_username"
                                            class="mt-2"
                                        />
                                    </div>

                                    <!-- Database Password -->
                                    <div>
                                        <InputLabel
                                            for="db_password"
                                            value="Database Password"
                                        />
                                        <TextInput
                                            id="db_password"
                                            v-model="form.db_password"
                                            type="password"
                                            class="mt-1 block w-full"
                                            placeholder="Leave empty to keep current password"
                                        />
                                        <InputError
                                            :message="form.errors.db_password"
                                            class="mt-2"
                                        />
                                    </div>
                                </div>
                            </div>

                            <!-- Mail Settings -->
                            <div v-show="activeTab === 'mail'">
                                <h3 class="mb-4 text-lg font-semibold">
                                    Mail Settings
                                </h3>
                                <div
                                    class="grid grid-cols-1 gap-4 md:grid-cols-2"
                                >
                                    <!-- Mail Driver -->
                                    <div>
                                        <InputLabel
                                            for="mail_mailer"
                                            value="Mail Driver"
                                        />
                                        <select
                                            id="mail_mailer"
                                            v-model="form.mail_mailer"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        >
                                            <option
                                                v-for="driver in mailDrivers"
                                                :key="driver"
                                                :value="driver"
                                            >
                                                {{ driver }}
                                            </option>
                                        </select>
                                        <InputError
                                            :message="form.errors.mail_mailer"
                                            class="mt-2"
                                        />
                                    </div>

                                    <!-- Mail Host -->
                                    <div>
                                        <InputLabel
                                            for="mail_host"
                                            value="Mail Host"
                                        />
                                        <TextInput
                                            id="mail_host"
                                            v-model="form.mail_host"
                                            type="text"
                                            class="mt-1 block w-full"
                                        />
                                        <InputError
                                            :message="form.errors.mail_host"
                                            class="mt-2"
                                        />
                                    </div>

                                    <!-- Mail Port -->
                                    <div>
                                        <InputLabel
                                            for="mail_port"
                                            value="Mail Port"
                                        />
                                        <TextInput
                                            id="mail_port"
                                            v-model="form.mail_port"
                                            type="text"
                                            class="mt-1 block w-full"
                                        />
                                        <InputError
                                            :message="form.errors.mail_port"
                                            class="mt-2"
                                        />
                                    </div>

                                    <!-- Mail Username -->
                                    <div>
                                        <InputLabel
                                            for="mail_username"
                                            value="Mail Username"
                                        />
                                        <TextInput
                                            id="mail_username"
                                            v-model="form.mail_username"
                                            type="text"
                                            class="mt-1 block w-full"
                                        />
                                        <InputError
                                            :message="form.errors.mail_username"
                                            class="mt-2"
                                        />
                                    </div>

                                    <!-- Mail Password -->
                                    <div>
                                        <InputLabel
                                            for="mail_password"
                                            value="Mail Password"
                                        />
                                        <TextInput
                                            id="mail_password"
                                            v-model="form.mail_password"
                                            type="password"
                                            class="mt-1 block w-full"
                                            placeholder="Leave empty to keep current password"
                                        />
                                        <InputError
                                            :message="form.errors.mail_password"
                                            class="mt-2"
                                        />
                                    </div>

                                    <!-- Mail Encryption -->
                                    <div>
                                        <InputLabel
                                            for="mail_encryption"
                                            value="Mail Encryption"
                                        />
                                        <select
                                            id="mail_encryption"
                                            v-model="form.mail_encryption"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        >
                                            <option value="tls">TLS</option>
                                            <option value="ssl">SSL</option>
                                            <option value="null">None</option>
                                        </select>
                                        <InputError
                                            :message="
                                                form.errors.mail_encryption
                                            "
                                            class="mt-2"
                                        />
                                    </div>

                                    <!-- Mail From Address -->
                                    <div>
                                        <InputLabel
                                            for="mail_from_address"
                                            value="From Email Address"
                                        />
                                        <TextInput
                                            id="mail_from_address"
                                            v-model="form.mail_from_address"
                                            type="email"
                                            class="mt-1 block w-full"
                                        />
                                        <InputError
                                            :message="
                                                form.errors.mail_from_address
                                            "
                                            class="mt-2"
                                        />
                                    </div>

                                    <!-- Mail From Name -->
                                    <div>
                                        <InputLabel
                                            for="mail_from_name"
                                            value="From Name"
                                        />
                                        <TextInput
                                            id="mail_from_name"
                                            v-model="form.mail_from_name"
                                            type="text"
                                            class="mt-1 block w-full"
                                        />
                                        <InputError
                                            :message="
                                                form.errors.mail_from_name
                                            "
                                            class="mt-2"
                                        />
                                    </div>
                                </div>
                            </div>

                            <!-- Theme Settings -->
                            <div v-show="activeTab === 'theme'">
                                <h3 class="mb-4 text-lg font-semibold">
                                    Theme Settings
                                </h3>
                                <div
                                    class="grid grid-cols-1 gap-4 md:grid-cols-2"
                                >
                                    <!-- Primary Color -->
                                    <div>
                                        <InputLabel
                                            for="primary_color"
                                            value="Primary Color"
                                        />
                                        <div
                                            class="mt-2 flex items-center space-x-3"
                                        >
                                            <div
                                                v-for="(
                                                    name, color
                                                ) in colorOptions"
                                                :key="color"
                                                @click="
                                                    form.primary_color = color
                                                "
                                                :class="{
                                                    'ring-2 ring-offset-2 ring-blue-500':
                                                        form.primary_color ===
                                                        color,
                                                }"
                                                class="w-8 h-8 rounded-full cursor-pointer transition-all"
                                                :style="{
                                                    backgroundColor: color,
                                                }"
                                                :title="name"
                                            ></div>
                                        </div>
                                        <p class="mt-1 text-sm text-gray-500">
                                            Primary theme color for headers and
                                            main elements
                                        </p>
                                        <InputError
                                            :message="form.errors.primary_color"
                                            class="mt-2"
                                        />
                                    </div>

                                    <!-- Secondary Color -->
                                    <div>
                                        <InputLabel
                                            for="secondary_color"
                                            value="Secondary Color"
                                        />
                                        <div
                                            class="mt-2 flex items-center space-x-3"
                                        >
                                            <div
                                                v-for="(
                                                    name, color
                                                ) in colorOptions"
                                                :key="color"
                                                @click="
                                                    form.secondary_color = color
                                                "
                                                :class="{
                                                    'ring-2 ring-offset-2 ring-blue-500':
                                                        form.secondary_color ===
                                                        color,
                                                }"
                                                class="w-8 h-8 rounded-full cursor-pointer transition-all"
                                                :style="{
                                                    backgroundColor: color,
                                                }"
                                                :title="name"
                                            ></div>
                                        </div>
                                        <p class="mt-1 text-sm text-gray-500">
                                            Secondary theme color for buttons
                                            and accents
                                        </p>
                                        <InputError
                                            :message="
                                                form.errors.secondary_color
                                            "
                                            class="mt-2"
                                        />
                                    </div>

                                    <!-- Preview Section -->
                                    <div class="col-span-2">
                                        <h4
                                            class="font-medium text-gray-700 mb-2"
                                        >
                                            Theme Preview
                                        </h4>
                                        <div
                                            class="border rounded-lg p-4 overflow-hidden"
                                        >
                                            <div
                                                class="py-2 px-4 mb-4"
                                                :style="{
                                                    backgroundColor:
                                                        form.primary_color,
                                                    color: getContrastColor(
                                                        form.primary_color
                                                    ),
                                                }"
                                            >
                                                <h5 class="font-bold">
                                                    Header with Primary Color
                                                </h5>
                                            </div>
                                            <div class="flex space-x-2 mb-4">
                                                <button
                                                    class="px-4 py-2 rounded"
                                                    :style="{
                                                        backgroundColor:
                                                            form.secondary_color,
                                                        color: getContrastColor(
                                                            form.secondary_color
                                                        ),
                                                    }"
                                                >
                                                    Button with Secondary Color
                                                </button>
                                                <button
                                                    class="px-4 py-2 rounded border"
                                                    :style="{
                                                        borderColor:
                                                            form.primary_color,
                                                        color: form.primary_color,
                                                    }"
                                                >
                                                    Outline Button
                                                </button>
                                            </div>
                                            <div class="flex items-center mb-2">
                                                <div
                                                    class="w-4 h-4 rounded-full mr-2"
                                                    :style="{
                                                        backgroundColor:
                                                            form.primary_color,
                                                    }"
                                                ></div>
                                                <span
                                                    :style="{
                                                        color: form.primary_color,
                                                    }"
                                                    >Text in Primary Color</span
                                                >
                                            </div>
                                            <div class="flex items-center">
                                                <div
                                                    class="w-4 h-4 rounded-full mr-2"
                                                    :style="{
                                                        backgroundColor:
                                                            form.secondary_color,
                                                    }"
                                                ></div>
                                                <span
                                                    :style="{
                                                        color: form.secondary_color,
                                                    }"
                                                    >Text in Secondary
                                                    Color</span
                                                >
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6">
                                <PrimaryButton :disabled="form.processing">
                                    Save Settings
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { ref, computed } from "vue";

const props = defineProps({
    settings: Object,
    timezones: Array,
    dbConnections: Array,
    mailDrivers: Array,
    colorOptions: Object,
});

const activeTab = ref("general");
const faviconPreview = ref(null);

const form = useForm({
    app_name: props.settings.app_name || "",
    app_timezone: props.settings.app_timezone || "UTC",
    app_url: props.settings.app_url || "",
    db_connection: props.settings.db_connection || "",
    db_host: props.settings.db_host || "",
    db_port: props.settings.db_port || "",
    db_database: props.settings.db_database || "",
    db_username: props.settings.db_username || "",
    db_password: "",
    mail_mailer: props.settings.mail_mailer || "",
    mail_host: props.settings.mail_host || "",
    mail_port: props.settings.mail_port || "",
    mail_username: props.settings.mail_username || "",
    mail_password: "",
    mail_encryption: props.settings.mail_encryption || "null",
    mail_from_address: props.settings.mail_from_address || "",
    mail_from_name: props.settings.mail_from_name || "",
    favicon: null,
    primary_color: props.settings.primary_color || "#244031",
    secondary_color: props.settings.secondary_color || "#3B614A",
});

const handleFaviconChange = (e) => {
    if (e.target.files.length > 0) {
        const file = e.target.files[0];
        form.favicon = file;

        // Preview the selected image
        const reader = new FileReader();
        reader.onload = (e) => {
            faviconPreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const setActiveTab = (tab) => {
    activeTab.value = tab;
};

const submit = () => {
    form.post(route("dashboard.settings.update"), {
        preserveScroll: true,
        forceFormData: true,
    });
};

// Helper function to determine if text should be white or black based on background color
const getContrastColor = (hexColor) => {
    // Remove # if present
    if (hexColor.startsWith("#")) {
        hexColor = hexColor.slice(1);
    }

    // Convert hex to RGB
    let r = parseInt(hexColor.substring(0, 2), 16);
    let g = parseInt(hexColor.substring(2, 4), 16);
    let b = parseInt(hexColor.substring(4, 6), 16);

    // Calculate luminance
    const luminance = (0.299 * r + 0.587 * g + 0.114 * b) / 255;

    // Return white for dark backgrounds, black for light backgrounds
    return luminance > 0.5 ? "#000000" : "#ffffff";
};
</script>

<style scoped>
/* You can add custom CSS for the settings page here */
</style>
