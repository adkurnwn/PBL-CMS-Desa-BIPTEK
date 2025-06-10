<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, Link, usePage } from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/DashboardComponent/InputError.vue";
import PrimaryButton from "@/Components/DashboardComponent/PrimaryButton.vue";
import JsonArrayInput from "@/Components/JsonArrayInput.vue";
import ImageInput from "@/Components/DashboardComponent/ImageInput.vue";
import HeadingMenu from "@/Components/DashboardComponent/HeadingMenu.vue";
import SingleLineField from "@/Components/DashboardComponent/SingleLineField.vue";
import DropdownFieldInput from "@/Components/DashboardComponent/DropdownFieldInput.vue";
import { ref, computed, watch } from "vue";
import SecondaryButton from "@/Components/DashboardComponent/SecondaryButton.vue";
import axios from "axios";

const props = defineProps({
    settings: Object,
    timezones: Array,
    dbConnections: Array,
    mailDrivers: Array,
    colorOptions: Object,
    secondaryColorOptions: Object,
});

const page = usePage();

const activeTab = ref("theme");
const faviconPreview = ref(null);

// Add reactive states for section locks
const isDatabaseLocked = ref(true);
const isEmailLocked = ref(true);

// Add reactive state for confirmation popup
const showConfirmation = ref(false);

// Add reactive state for testing database connection
const testingConnection = ref(false);
const connectionTestResult = ref(null);

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
    backup_enabled: props.settings.backup_enabled || false,
    backup_frequency: props.settings.backup_frequency || "daily",
});

// Add separate form for theme settings
const themeForm = useForm({
    primary_color: props.settings.primary_color || "#244031",
    secondary_color: props.settings.secondary_color || "#3B614A",
    favicon: null,
});

const handleFaviconChange = (file) => {
    form.favicon = file;
    themeForm.favicon = file;
    // Clear any previous preview since ImageInput handles its own preview
    faviconPreview.value = null;
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

// Add new method for theme-only save
const submitTheme = () => {
    // Debug the form data being sent
    console.log("Theme form data:", {
        primary_color: themeForm.primary_color,
        secondary_color: themeForm.secondary_color,
        favicon: themeForm.favicon,
    });

    themeForm.post(route("dashboard.settings.update-theme"), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: (page) => {
            console.log("Theme updated successfully", page);
            // Update the main form's color values to sync them
            form.primary_color = themeForm.primary_color;
            form.secondary_color = themeForm.secondary_color;
        },
        onError: (errors) => {
            console.log("Theme update errors:", errors);
        },
        onFinish: () => {
            console.log("Theme update finished");
        },
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

// Add computed properties for dropdown options
const timezoneOptions = computed(() =>
    props.timezones.map((timezone) => ({ value: timezone, label: timezone }))
);

const dbConnectionOptions = computed(() =>
    props.dbConnections.map((connection) => ({
        value: connection,
        label: connection,
    }))
);

const mailDriverOptions = computed(() =>
    props.mailDrivers.map((driver) => ({ value: driver, label: driver }))
);

const mailEncryptionOptions = [
    { value: "tls", label: "TLS" },
    { value: "ssl", label: "SSL" },
    { value: "null", label: "None" },
];

const backupFrequencyOptions = [
    { value: "daily", label: "Daily" },
    { value: "weekly", label: "Weekly" },
    { value: "monthly", label: "Monthly" },
];

// Add methods to unlock sections
const unlockDatabase = () => {
    isDatabaseLocked.value = false;
};

const unlockEmail = () => {
    isEmailLocked.value = false;
};

// Update submit method
const showConfirmationDialog = () => {
    if (activeTab.value === "theme") {
        console.log("Submitting theme with color:", themeForm.primary_color);
        submitTheme();
    } else {
        showConfirmation.value = true;
    }
};

const confirmSave = () => {
    showConfirmation.value = false;
    submit();
};

const cancelSave = () => {
    showConfirmation.value = false;
};

// Add computed property to map primary colors to secondary colors
const getSecondaryColorForPrimary = (primaryColor) => {
    const colorMapping = {
        "#244031": "#3B614A", // Green (Default) -> Green (Default)
        "#242540": "#3B4659", // Denim -> Blue Gray
        "#882828": "#9F4941", // Maroon -> Red Brown
    };
    return colorMapping[primaryColor] || "#3B614A"; // Default to green secondary
};

// Watch for primary color changes and update secondary color automatically
watch(
    () => themeForm.primary_color,
    (newPrimaryColor) => {
        themeForm.secondary_color =
            getSecondaryColorForPrimary(newPrimaryColor);
    }
);

// Method to test database connection
const testDatabaseConnection = () => {
    testingConnection.value = true;
    connectionTestResult.value = null;

    // Make actual API call to test connection
    axios
        .post(route("dashboard.settings.test-connection"), {
            db_connection: form.db_connection,
            db_host: form.db_host,
            db_port: form.db_port,
            db_database: form.db_database,
            db_username: form.db_username,
            db_password: form.db_password,
        })
        .then((response) => {
            testingConnection.value = false;
            connectionTestResult.value = {
                success: response.data.success,
                message: response.data.message,
            };
        })
        .catch((error) => {
            testingConnection.value = false;
            connectionTestResult.value = {
                success: false,
                message:
                    error.response?.data?.message || "Connection test failed",
            };
        });
};
</script>

<template>
    <Head title="Pengaturan Aplikasi" />
    <AuthenticatedLayout>
        <template #header>
            <HeadingMenu title="Pengaturan Aplikasi">
                <template #icon>
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        height="24px"
                        viewBox="0 -960 960 960"
                        width="24px"
                        fill="#40483D"
                    >
                        <path
                            d="m370-80-16-128q-13-5-24.5-12T307-235l-119 50L78-375l103-78q-1-7-1-13.5v-27q0-6.5 1-13.5L78-585l110-190 119 50q11-8 23-15t24-12l16-128h220l16 128q13 5 24.5 12t22.5 15l119-50 110 190-103 78q1 7 1 13.5v27q0 6.5-2 13.5l103 78-110 190-118-50q-11 8-23 15t-24 12L590-80H370Zm70-80h79l14-106q31-8 57.5-23.5T639-327l99 41 39-68-86-65q5-14 7-29.5t2-31.5q0-16-2-31.5t-7-29.5l86-65-39-68-99 42q-22-23-48.5-38.5T533-694l-13-106h-79l-14 106q-31 8-57.5 23.5T321-633l-99-41-39 68 86 64q-5 15-7 30t-2 32q0 16 2 31t7 30l-86 65 39 68 99-42q22 23 48.5 38.5T427-266l13 106Zm42-180q58 0 99-41t41-99q0-58-41-99t-99-41q-59 0-99.5 41T342-480q0 58 40.5 99t99.5 41Zm-2-140Z"
                        />
                    </svg>
                </template>
            </HeadingMenu>
        </template>

        <template #tab-devider> </template>

        <div class="h-full">
            <div
                class="overflow-hidden pl-[6px] sm:pl-[7px] md:pl-[8px] xl:pl-[8px] 2xl:pl-[9px] 3xl:pl-[9px] 4xl:pl-[10px]"
            >
                <div class="mb-4">
                    <div class="tab-devider">
                        <div class="flex">
                            <div class="relative">
                                <a
                                    href="#"
                                    class="px-[8px] sm:px-[9px] md:px-[10px] xl:px-[8px] 2xl:px-[10px] 3xl:px-[11px] 4xl:px-[12px] py-[6px] sm:py-[7px] md:py-[8px] xl:py-[6.5px] 2xl:py-[8px] 3xl:py-[9px] 4xl:py-[10px] inline-block text-[14.5px] sm:text-[16px] md:text-[18px] xl:text-[14.5px] 2xl:text-[17px] 3xl:text-[20px] 4xl:text-[22px] font-medium transition-colors duration-200"
                                    :class="
                                        activeTab === 'theme'
                                            ? 'text-accent1'
                                            : 'text-menuText hover:text-accent1/80'
                                    "
                                    @click.prevent="setActiveTab('theme')"
                                >
                                    Tema
                                    <div
                                        v-if="activeTab === 'theme'"
                                        class="absolute bottom-0 left-0 w-full h-[1px] sm:h-[1px] md:h-[1.5px] xl:h-[1.5px] 2xl:h-[1.5px] 3xl:h-[2px] 4xl:h-[2px] bg-accent1"
                                    ></div>
                                </a>
                            </div>
                            <div class="relative">
                                <a
                                    href="#"
                                    class="px-[8px] sm:px-[9px] md:px-[10px] xl:px-[8px] 2xl:px-[10px] 3xl:px-[11px] 4xl:px-[12px] py-[6px] sm:py-[7px] md:py-[8px] xl:py-[6.5px] 2xl:py-[8px] 3xl:py-[9px] 4xl:py-[10px] inline-block text-[14.5px] sm:text-[16px] md:text-[18px] xl:text-[14.5px] 2xl:text-[17px] 3xl:text-[20px] 4xl:text-[22px] font-medium transition-colors duration-200"
                                    :class="
                                        activeTab === 'general'
                                            ? 'text-accent1'
                                            : 'text-menuText hover:text-accent1/80'
                                    "
                                    @click.prevent="setActiveTab('general')"
                                >
                                    Umum
                                    <div
                                        v-if="activeTab === 'general'"
                                        class="absolute bottom-0 left-0 w-full h-[1px] sm:h-[1px] md:h-[1.5px] xl:h-[1.5px] 2xl:h-[1.5px] 3xl:h-[2px] 4xl:h-[2px] bg-accent1"
                                    ></div>
                                </a>
                            </div>
                            <div class="relative">
                                <a
                                    href="#"
                                    class="px-[8px] sm:px-[9px] md:px-[10px] xl:px-[8px] 2xl:px-[10px] 3xl:px-[11px] 4xl:px-[12px] py-[6px] sm:py-[7px] md:py-[8px] xl:py-[6.5px] 2xl:py-[8px] 3xl:py-[9px] 4xl:py-[10px] inline-block text-[14.5px] sm:text-[16px] md:text-[18px] xl:text-[14.5px] 2xl:text-[17px] 3xl:text-[20px] 4xl:text-[22px] font-medium transition-colors duration-200"
                                    :class="
                                        activeTab === 'database'
                                            ? 'text-accent1'
                                            : 'text-menuText hover:text-accent1/80'
                                    "
                                    @click.prevent="setActiveTab('database')"
                                >
                                    Database
                                    <div
                                        v-if="activeTab === 'database'"
                                        class="absolute bottom-0 left-0 w-full h-[1px] sm:h-[1px] md:h-[1.5px] xl:h-[1.5px] 2xl:h-[1.5px] 3xl:h-[2px] 4xl:h-[2px] bg-accent1"
                                    ></div>
                                </a>
                            </div>
                            <div class="relative">
                                <a
                                    href="#"
                                    class="px-[8px] sm:px-[9px] md:px-[10px] xl:px-[8px] 2xl:px-[10px] 3xl:px-[11px] 4xl:px-[12px] py-[6px] sm:py-[7px] md:py-[8px] xl:py-[6.5px] 2xl:py-[8px] 3xl:py-[9px] 4xl:py-[10px] inline-block text-[14.5px] sm:text-[16px] md:text-[18px] xl:text-[14.5px] 2xl:text-[17px] 3xl:text-[20px] 4xl:text-[22px] font-medium transition-colors duration-200"
                                    :class="
                                        activeTab === 'mail'
                                            ? 'text-accent1'
                                            : 'text-menuText hover:text-accent1/80'
                                    "
                                    @click.prevent="setActiveTab('mail')"
                                >
                                    Email
                                    <div
                                        v-if="activeTab === 'mail'"
                                        class="absolute bottom-0 left-0 w-full h-[1px] sm:h-[1px] md:h-[1.5px] xl:h-[1.5px] 2xl:h-[1.5px] 3xl:h-[2px] 4xl:h-[2px] bg-accent1"
                                    ></div>
                                </a>
                            </div>
                            <div class="relative">
                                <a
                                    href="#"
                                    class="px-[8px] sm:px-[9px] md:px-[10px] xl:px-[8px] 2xl:px-[10px] 3xl:px-[11px] 4xl:px-[12px] py-[6px] sm:py-[7px] md:py-[8px] xl:py-[6.5px] 2xl:py-[8px] 3xl:py-[9px] 4xl:py-[10px] inline-block text-[14.5px] sm:text-[16px] md:text-[18px] xl:text-[14.5px] 2xl:text-[17px] 3xl:text-[20px] 4xl:text-[22px] font-medium transition-colors duration-200"
                                    :class="
                                        activeTab === 'backup'
                                            ? 'text-accent1'
                                            : 'text-menuText hover:text-accent1/80'
                                    "
                                    @click.prevent="setActiveTab('backup')"
                                >
                                    Cadangan
                                    <div
                                        v-if="activeTab === 'backup'"
                                        class="absolute bottom-0 left-0 w-full h-[1px] sm:h-[1px] md:h-[1.5px] xl:h-[1.5px] 2xl:h-[1.5px] 3xl:h-[2px] 4xl:h-[2px] bg-accent1"
                                    ></div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="overflow-hidden">
                    <div>
                        <form
                            @submit.prevent="submit"
                            enctype="multipart/form-data"
                        >
                            <!-- General Settings -->
                            <div v-show="activeTab === 'general'">
                                <div
                                    class="grid grid-cols-1 gap-4 md:grid-cols-2"
                                >
                                    <!-- App Name -->
                                    <div>
                                        <SingleLineField
                                            id="app_name"
                                            v-model="form.app_name"
                                            type="text"
                                            fieldName="Nama Website"
                                            required
                                            custom-width="100%"
                                        />
                                        <InputError
                                            :message="form.errors.app_name"
                                            class="mt-2"
                                        />
                                    </div>

                                    <!-- App URL -->
                                    <div>
                                        <SingleLineField
                                            id="app_url"
                                            v-model="form.app_url"
                                            type="text"
                                            fieldName="Application URL"
                                            required
                                            custom-width="100%"
                                        />
                                        <InputError
                                            :message="form.errors.app_url"
                                            class="mt-2"
                                        />
                                    </div>

                                    <!-- Timezone -->
                                    <div>
                                        <DropdownFieldInput
                                            id="app_timezone"
                                            v-model="form.app_timezone"
                                            fieldName="Timezone"
                                            :options="timezoneOptions"
                                            :required="true"
                                            custom-width="100%"
                                        />
                                        <InputError
                                            :message="form.errors.app_timezone"
                                            class="mt-2"
                                        />
                                    </div>

                                    <!-- Favicon -->
                                    <div>
                                        <ImageInput
                                            :model-value="form.favicon"
                                            @update:model-value="
                                                handleFaviconChange
                                            "
                                            label="Favicon"
                                            :current-image="settings.favicon"
                                            :error="form.errors.favicon"
                                            accept="image/png, image/jpeg, image/gif, image/ico, image/x-icon"
                                            custom-width="100%"
                                            image-height="64px"
                                            image-class="rounded-sm border"
                                        />
                                        <p
                                            class="mt-1 text-[6.2px] sm:text-[7px] md:text-[8px] lg:text-[8.7px] xl:text-[9.33px] 2xl:text-[10.8px] 3xl:text-[12.4px] 4xl:text-[14px] text-gray-500"
                                        >
                                            Recommended size: 32x32px or 64x64px
                                            (PNG or ICO format)
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Database Settings -->
                            <div
                                v-show="activeTab === 'database'"
                                class="relative"
                            >
                                <!-- Blur overlay when locked -->
                                <div
                                    v-if="isDatabaseLocked"
                                    class="absolute inset-0 bg-white/50 backdrop-blur-sm z-10 flex items-center justify-center"
                                >
                                    <div
                                        class="bg-menuBG p-6 rounded-[5px] shadow-2xl text-center"
                                    >
                                        <div class="mb-4">
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                width="48"
                                                height="48"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                class="mx-auto text-yellowLabelText"
                                            >
                                                <rect
                                                    width="18"
                                                    height="11"
                                                    x="3"
                                                    y="11"
                                                    rx="2"
                                                    ry="2"
                                                />
                                                <circle cx="12" cy="16" r="1" />
                                                <path
                                                    d="m7 11V7a5 5 0 0 1 10 0v4"
                                                />
                                            </svg>
                                        </div>
                                        <h3
                                            class="text-[14.5px] sm:text-[16px] md:text-[18px] xl:text-[14.5px] 2xl:text-[17px] 3xl:text-[20px] 4xl:text-[22px] font-semibold text-selectedText mb-2"
                                        >
                                            Pengaturan Database Terkunci
                                        </h3>
                                        <p
                                            class="text-menuText text-[12px] md:text-[13px] xl:text-[11px] 2xl:text-[13px] 3xl:text-[14px] 4xl:text-[15px] mb-4"
                                        >
                                            Klik unlock untuk membuka pengaturan
                                            database
                                        </p>
                                        <SecondaryButton
                                            @click="unlockDatabase"
                                            class="!border-red-600 hover:!border-red-700 active:!border-red-800 !text-red-600 hover:!text-red-700 active:!text-red-800 hover:!bg-red-50"
                                        >
                                            Unlock
                                        </SecondaryButton>
                                    </div>
                                </div>

                                <div
                                    :class="{
                                        'blur-sm pointer-events-none':
                                            isDatabaseLocked,
                                    }"
                                >
                                    <div
                                        class="grid grid-cols-1 gap-4 md:grid-cols-2"
                                    >
                                        <!-- Database Connection -->
                                        <div>
                                            <DropdownFieldInput
                                                id="db_connection"
                                                v-model="form.db_connection"
                                                fieldName="Database Connection"
                                                :options="dbConnectionOptions"
                                                custom-width="100%"
                                            />
                                            <InputError
                                                :message="
                                                    form.errors.db_connection
                                                "
                                                class="mt-2"
                                            />
                                        </div>

                                        <!-- Database Host -->
                                        <div>
                                            <SingleLineField
                                                id="db_host"
                                                v-model="form.db_host"
                                                type="text"
                                                fieldName="Database Host"
                                                custom-width="100%"
                                            />
                                            <InputError
                                                :message="form.errors.db_host"
                                                class="mt-2"
                                            />
                                        </div>

                                        <!-- Database Port -->
                                        <div>
                                            <SingleLineField
                                                id="db_port"
                                                v-model="form.db_port"
                                                type="text"
                                                fieldName="Database Port"
                                                custom-width="100%"
                                            />
                                            <InputError
                                                :message="form.errors.db_port"
                                                class="mt-2"
                                            />
                                        </div>

                                        <!-- Database Name -->
                                        <div>
                                            <SingleLineField
                                                id="db_database"
                                                v-model="form.db_database"
                                                type="text"
                                                fieldName="Database Name"
                                                custom-width="100%"
                                            />
                                            <InputError
                                                :message="
                                                    form.errors.db_database
                                                "
                                                class="mt-2"
                                            />
                                        </div>

                                        <!-- Database Username -->
                                        <div>
                                            <SingleLineField
                                                id="db_username"
                                                v-model="form.db_username"
                                                type="text"
                                                fieldName="Database Username"
                                                custom-width="100%"
                                            />
                                            <InputError
                                                :message="
                                                    form.errors.db_username
                                                "
                                                class="mt-2"
                                            />
                                        </div>

                                        <!-- Database Password -->
                                        <div>
                                            <SingleLineField
                                                id="db_password"
                                                v-model="form.db_password"
                                                type="password"
                                                fieldName="Database Password (Opsional)"
                                                custom-width="100%"
                                            />
                                            <InputError
                                                :message="
                                                    form.errors.db_password
                                                "
                                                class="mt-2"
                                            />
                                        </div>
                                    </div>

                                    <!-- Add this button in the database settings section -->
                                    <div class="col-span-2 mt-4">
                                        <SecondaryButton
                                            type="button"
                                            @click="testDatabaseConnection"
                                            :disabled="
                                                testingConnection ||
                                                isDatabaseLocked
                                            "
                                            class="mr-2"
                                        >
                                            <span v-if="testingConnection"
                                                >Testing...</span
                                            >
                                            <span v-else
                                                >Test Database Connection</span
                                            >
                                        </SecondaryButton>

                                        <!-- Connection status feedback -->
                                        <div
                                            v-if="connectionTestResult"
                                            class="mt-2"
                                        >
                                            <div
                                                v-if="
                                                    connectionTestResult.success
                                                "
                                                class="text-green-600 text-sm"
                                            >
                                                ✓
                                                {{
                                                    connectionTestResult.message
                                                }}
                                            </div>
                                            <div
                                                v-else
                                                class="text-red-600 text-sm"
                                            >
                                                ✗
                                                {{
                                                    connectionTestResult.message
                                                }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Mail Settings -->
                            <div v-show="activeTab === 'mail'" class="relative">
                                <!-- Blur overlay when locked -->
                                <div
                                    v-if="isEmailLocked"
                                    class="absolute inset-0 bg-white/50 backdrop-blur-sm z-10 flex items-center justify-center"
                                >
                                    <div
                                        class="bg-menuBG p-6 rounded-[5px] shadow-2xl text-center"
                                    >
                                        <div class="mb-4">
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                width="48"
                                                height="48"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                class="mx-auto text-yellowLabelText"
                                            >
                                                <rect
                                                    width="18"
                                                    height="11"
                                                    x="3"
                                                    y="11"
                                                    rx="2"
                                                    ry="2"
                                                />
                                                <circle cx="12" cy="16" r="1" />
                                                <path
                                                    d="m7 11V7a5 5 0 0 1 10 0v4"
                                                />
                                            </svg>
                                        </div>
                                        <h3
                                            class="text-[14.5px] sm:text-[16px] md:text-[18px] xl:text-[14.5px] 2xl:text-[17px] 3xl:text-[20px] 4xl:text-[22px] font-semibold text-selectedText mb-2"
                                        >
                                            Pengaturan Email Terkunci
                                        </h3>
                                        <p
                                            class="text-menuText text-[12px] md:text-[13px] xl:text-[11px] 2xl:text-[13px] 3xl:text-[14px] 4xl:text-[15px] mb-4"
                                        >
                                            Klik unlock untuk membuka pengaturan
                                            email
                                        </p>
                                        <SecondaryButton
                                            @click="unlockEmail"
                                            class="!border-red-600 hover:!border-red-700 active:!border-red-800 !text-red-600 hover:!text-red-700 active:!text-red-800 hover:!bg-red-50"
                                        >
                                            Unlock
                                        </SecondaryButton>
                                    </div>
                                </div>

                                <div
                                    :class="{
                                        'blur-sm pointer-events-none':
                                            isEmailLocked,
                                    }"
                                >
                                    <div
                                        class="grid grid-cols-1 gap-4 md:grid-cols-2"
                                    >
                                        <!-- Mail Driver -->
                                        <div>
                                            <DropdownFieldInput
                                                id="mail_mailer"
                                                v-model="form.mail_mailer"
                                                fieldName="Mail Driver"
                                                :options="mailDriverOptions"
                                                custom-width="100%"
                                            />
                                            <InputError
                                                :message="
                                                    form.errors.mail_mailer
                                                "
                                                class="mt-2"
                                            />
                                        </div>

                                        <!-- Mail Host -->
                                        <div>
                                            <SingleLineField
                                                id="mail_host"
                                                v-model="form.mail_host"
                                                type="text"
                                                fieldName="Mail Host"
                                                custom-width="100%"
                                            />
                                            <InputError
                                                :message="form.errors.mail_host"
                                                class="mt-2"
                                            />
                                        </div>

                                        <!-- Mail Port -->
                                        <div>
                                            <SingleLineField
                                                id="mail_port"
                                                v-model="form.mail_port"
                                                type="text"
                                                fieldName="Mail Port"
                                                custom-width="100%"
                                            />
                                            <InputError
                                                :message="form.errors.mail_port"
                                                class="mt-2"
                                            />
                                        </div>

                                        <!-- Mail Username -->
                                        <div>
                                            <SingleLineField
                                                id="mail_username"
                                                v-model="form.mail_username"
                                                type="text"
                                                fieldName="Mail Username"
                                                custom-width="100%"
                                            />
                                            <InputError
                                                :message="
                                                    form.errors.mail_username
                                                "
                                                class="mt-2"
                                            />
                                        </div>

                                        <!-- Mail Password -->
                                        <div>
                                            <SingleLineField
                                                id="mail_password"
                                                v-model="form.mail_password"
                                                type="password"
                                                fieldName="Mail Password (Opsional)"
                                                custom-width="100%"
                                            />
                                            <InputError
                                                :message="
                                                    form.errors.mail_password
                                                "
                                                class="mt-2"
                                            />
                                        </div>

                                        <!-- Mail Encryption -->
                                        <div>
                                            <DropdownFieldInput
                                                id="mail_encryption"
                                                v-model="form.mail_encryption"
                                                fieldName="Mail Encryption"
                                                :options="mailEncryptionOptions"
                                                custom-width="100%"
                                            />
                                            <InputError
                                                :message="
                                                    form.errors.mail_encryption
                                                "
                                                class="mt-2"
                                            />
                                        </div>

                                        <!-- Mail From Address -->
                                        <div>
                                            <SingleLineField
                                                id="mail_from_address"
                                                v-model="form.mail_from_address"
                                                type="email"
                                                fieldName="From Email Address"
                                                custom-width="100%"
                                            />
                                            <InputError
                                                :message="
                                                    form.errors
                                                        .mail_from_address
                                                "
                                                class="mt-2"
                                            />
                                        </div>

                                        <!-- Mail From Name -->
                                        <div>
                                            <SingleLineField
                                                id="mail_from_name"
                                                v-model="form.mail_from_name"
                                                type="text"
                                                fieldName="From Name"
                                                custom-width="100%"
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
                            </div>

                            <!-- Theme Settings -->
                            <div v-show="activeTab === 'theme'">
                                <div
                                    class="grid grid-cols-1 gap-4 md:grid-cols-2 p-[5.3px] sm:p-[6px] md:p-[6.7px] lg:p-[7.3px] xl:p-[8px] 2xl:p-[9.3px] 3xl:p-[10.7px] 4xl:p-[12px]"
                                >
                                    <!-- Primary Color -->
                                    <div>
                                        <InputLabel
                                            for="primary_color"
                                            value="Warna Utama"
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
                                                    themeForm.primary_color =
                                                        color
                                                "
                                                :class="{
                                                    'ring-2 ring-offset-2 ring-blue-500':
                                                        themeForm.primary_color ===
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
                                            Warna utama yang akan digunakan di
                                            seluruh aplikasi web.
                                        </p>
                                        <InputError
                                            :message="
                                                themeForm.errors.primary_color
                                            "
                                            class="mt-2"
                                        />
                                    </div>

                                    <!-- Secondary Color Display (Read-only) -->
                                    <div>
                                        <InputLabel
                                            for="secondary_color"
                                            value="Warna Aksen (Otomatis)"
                                        />
                                        <div
                                            class="mt-2 flex items-center space-x-3"
                                        >
                                            <div
                                                class="w-8 h-8 rounded-full border-2 border-gray-300"
                                                :style="{
                                                    backgroundColor:
                                                        themeForm.secondary_color,
                                                }"
                                                :title="
                                                    secondaryColorOptions[
                                                        themeForm
                                                            .secondary_color
                                                    ] || 'Auto-selected color'
                                                "
                                            ></div>
                                            <span class="text-sm text-gray-600">
                                                {{
                                                    secondaryColorOptions[
                                                        themeForm
                                                            .secondary_color
                                                    ] || "Auto-selected"
                                                }}
                                            </span>
                                        </div>
                                        <p class="mt-1 text-sm text-gray-500">
                                            Warna aksen terpilih secara otomatis
                                            berdasarkan warna utama.
                                        </p>
                                        <InputError
                                            :message="
                                                themeForm.errors.secondary_color
                                            "
                                            class="mt-2"
                                        />
                                    </div>

                                    <!-- Preview Section -->
                                    <div class="col-span-2">
                                        <h4
                                            class="font-medium text-selectedText mb-2"
                                        >
                                            Preview Tema
                                        </h4>
                                        <div
                                            class="border rounded-lg p-4 overflow-hidden"
                                        >
                                            <div
                                                class="py-2 px-4 mb-4"
                                                :style="{
                                                    backgroundColor:
                                                        themeForm.primary_color,
                                                    color: getContrastColor(
                                                        themeForm.primary_color
                                                    ),
                                                }"
                                            >
                                            <div class="flex gap-x-3 items-center">
                                                <div class="flex-shrink-0 flex items-center h-full">
                                                    <img
                                                        v-if="page.props.profile?.logo"
                                                        :src="`/storage/${page.props.profile.logo
                                                        }`"
                                                        alt="Logo"
                                                        class="h-full w-auto max-h-12 object-contain"
                                                    />
                                                    <span
                                                        v-else
                                                        class="text-lg font-bold"
                                                    >
                                                        Logo Desa
                                                    </span>
                                                </div>
                                                <div class="flex flex-col justify-center">
                                                    <h1>
                                                        Content Management System
                                                    </h1>
                                                    <h5 class="font-bold">{{ page.props.profile?.name }}</h5>
                                                </div>
                                            </div>
                                            </div>
                                            <div class="flex space-x-2 mb-4">
                                                <PrimaryButton
                                                    class="cursor-not-allowed"
                                                    :style="{
                                                        backgroundColor:
                                                            themeForm.secondary_color,
                                                        color: getContrastColor(
                                                            themeForm.secondary_color
                                                        ),
                                                    }"
                                                    disabled
                                                >
                                                    Tombol Utama
                                                </PrimaryButton>
                                                <SecondaryButton
                                                    class="cursor-not-allowed"
                                                    :style="{
                                                        borderColor:
                                                            themeForm.primary_color,
                                                        color: themeForm.primary_color,
                                                    }"
                                                    disabled
                                                >
                                                    Outline Button
                                                </SecondaryButton>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Backup Settings -->
                            <div v-show="activeTab === 'backup'">
                                <div
                                    class="grid grid-cols-1 gap-4 md:grid-cols-2"
                                >
                                    <!-- Enable Scheduled Backups -->
                                    <div class="col-span-2">
                                        <div class="flex items-center">
                                            <input
                                                id="backup_enabled"
                                                v-model="form.backup_enabled"
                                                type="checkbox"
                                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500"
                                            />
                                            <label
                                                for="backup_enabled"
                                                class="ml-2 text-sm font-medium text-gray-900"
                                            >
                                                Enable automatic database
                                                backups
                                            </label>
                                        </div>
                                        <p class="mt-1 text-sm text-gray-500">
                                            When enabled, the system will
                                            automatically create database
                                            backups according to the schedule.
                                        </p>
                                    </div>

                                    <!-- Backup Frequency -->
                                    <div>
                                        <DropdownFieldInput
                                            id="backup_frequency"
                                            v-model="form.backup_frequency"
                                            fieldName="Backup Frequency"
                                            :options="backupFrequencyOptions"
                                            custom-width="100%"
                                            :class="
                                                !form.backup_enabled
                                                    ? 'opacity-50 pointer-events-none'
                                                    : ''
                                            "
                                        />
                                        <InputError
                                            :message="
                                                form.errors.backup_frequency
                                            "
                                            class="mt-2"
                                        />
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6 flex justify-end">
                                <PrimaryButton
                                    type="button"
                                    @click="showConfirmationDialog"
                                    :disabled="
                                        activeTab === 'theme'
                                            ? themeForm.processing
                                            : form.processing
                                    "
                                >
                                    <span
                                        v-if="
                                            activeTab === 'theme' &&
                                            themeForm.processing
                                        "
                                        >Menyimpan Tema...</span
                                    >
                                    <span v-else-if="form.processing"
                                        >Menyimpan...</span
                                    >
                                    <span v-else>Simpan Pengaturan</span>
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Confirmation Popup -->
        <div
            v-if="showConfirmation"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
        >
            <div
                class="bg-menuBG p-6 rounded-[5px] shadow-2xl max-w-md w-full mx-4 text-center"
            >
                <div class="mb-4">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="48"
                        height="48"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        class="mx-auto text-yellowLabelText"
                    >
                        <circle cx="12" cy="12" r="10" />
                        <path d="M12 16v-4" />
                        <path d="M12 8h.01" />
                    </svg>
                </div>
                <h3
                    class="text-[14.5px] sm:text-[16px] md:text-[18px] xl:text-[14.5px] 2xl:text-[17px] 3xl:text-[20px] 4xl:text-[22px] font-semibold text-selectedText mb-2"
                >
                    Konfirmasi Simpan Pengaturan
                </h3>
                <p
                    class="text-menuText text-[12px] md:text-[13px] xl:text-[11px] 2xl:text-[13px] 3xl:text-[14px] 4xl:text-[15px] mb-6"
                >
                    Apakah Anda yakin ingin menyimpan perubahan pengaturan
                    aplikasi ini? Perubahan yang salah dapat menyebabkan
                    kesalahan kinerja website.
                </p>
                <div class="flex space-x-3 justify-center">
                    <SecondaryButton type="button" @click="cancelSave">
                        Batal
                    </SecondaryButton>
                    <PrimaryButton
                        type="button"
                        @click="confirmSave"
                        :disabled="form.processing"
                    >
                        <span v-if="form.processing">Menyimpan...</span>
                        <span v-else>Ya, Simpan</span>
                    </PrimaryButton>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
