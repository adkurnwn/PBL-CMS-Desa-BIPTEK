<script setup>
import { Head, useForm } from "@inertiajs/vue3";
import { ref, computed } from "vue";

const props = defineProps({
    requirements: Object,
    permissions: Object,
});

const step = ref(1);
const totalSteps = 4;

const form = useForm({
    app_name: "PBL-CMS-Desa-BIPTEK",
    app_url: "http://localhost",
    db_connection: "mysql",
    db_host: "localhost",
    db_port: "3306",
    db_database: "",
    db_username: "root",
    db_password: "",
    admin_name: "Admin",
    admin_email: "",
    admin_password: "",
    setup_nginx: false,
    nginx_server_name: "",
});

const isRequirementsMet = computed(() => {
    return Object.values(props.requirements).every(Boolean);
});

const isPermissionsMet = computed(() => {
    return Object.values(props.permissions).every(Boolean);
});

const canProceed = computed(() => {
    if (step.value === 1) return isRequirementsMet.value;
    if (step.value === 2) return isPermissionsMet.value;
    return true;
});

const nextStep = () => {
    if (step.value < totalSteps) {
        step.value++;
    }
};

const prevStep = () => {
    if (step.value > 1) {
        step.value--;
    }
};

const submit = () => {
    form.post(route("install.store"));
};
</script>

<template>
    <Head title="Installation Setup" />

    <div
        class="min-h-screen bg-gray-100 flex flex-col items-center py-12 px-4 sm:px-6 lg:px-8"
    >
        <div class="w-full max-w-3xl bg-white p-8 rounded-lg shadow-md">
            <div class="mb-8 text-center">
                <h1 class="text-3xl font-semibold text-gray-800">
                    PBL-CMS-Desa-BIPTEK Installation
                </h1>
                <p class="text-gray-600 mt-2">
                    Complete the setup process to get started with your CMS
                </p>
            </div>

            <!-- Progress Bar -->
            <div class="w-full mb-8">
                <div class="flex justify-between mb-2">
                    <span class="text-xs font-medium"
                        >Step {{ step }} of {{ totalSteps }}</span
                    >
                    <span class="text-xs font-medium"
                        >{{ Math.round((step / totalSteps) * 100) }}%</span
                    >
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div
                        class="bg-blue-600 h-2 rounded-full"
                        :style="{ width: (step / totalSteps) * 100 + '%' }"
                    ></div>
                </div>
            </div>

            <!-- Step 1: Requirements -->
            <div v-if="step === 1">
                <h2 class="text-xl font-medium text-gray-800 mb-4">
                    System Requirements
                </h2>
                <div class="space-y-2">
                    <div
                        v-for="(value, key) in requirements"
                        :key="key"
                        class="flex justify-between p-3 rounded"
                        :class="value ? 'bg-green-50' : 'bg-red-50'"
                    >
                        <span class="font-medium">
                            {{ key.toUpperCase() }}
                        </span>
                        <span
                            :class="value ? 'text-green-600' : 'text-red-600'"
                            class="font-medium"
                        >
                            {{ value ? "✓" : "✗" }}
                        </span>
                    </div>
                </div>

                <div
                    v-if="!isRequirementsMet"
                    class="mt-4 text-red-600 text-sm"
                >
                    Please ensure all system requirements are met before
                    continuing.
                </div>
            </div>

            <!-- Step 2: Directory Permissions -->
            <div v-if="step === 2">
                <h2 class="text-xl font-medium text-gray-800 mb-4">
                    Directory Permissions
                </h2>
                <div class="space-y-2">
                    <div
                        v-for="(value, key) in permissions"
                        :key="key"
                        class="flex justify-between p-3 rounded"
                        :class="value ? 'bg-green-50' : 'bg-red-50'"
                    >
                        <span class="font-medium">
                            {{ key }}
                        </span>
                        <span
                            :class="value ? 'text-green-600' : 'text-red-600'"
                            class="font-medium"
                        >
                            {{ value ? "Writable" : "Not Writable" }}
                        </span>
                    </div>
                </div>

                <div v-if="!isPermissionsMet" class="mt-4 text-red-600 text-sm">
                    Please ensure all directories are writable before
                    continuing.
                </div>
            </div>

            <!-- Step 3: App & Database Config -->
            <div v-if="step === 3">
                <h2 class="text-xl font-medium text-gray-800 mb-4">
                    Application Configuration
                </h2>

                <div class="space-y-4">
                    <div>
                        <label
                            for="app_name"
                            class="block text-sm font-medium text-gray-700"
                        >
                            Application Name
                        </label>
                        <input
                            id="app_name"
                            v-model="form.app_name"
                            type="text"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        />
                        <div
                            v-if="form.errors.app_name"
                            class="text-red-500 text-sm mt-1"
                        >
                            {{ form.errors.app_name }}
                        </div>
                    </div>

                    <div>
                        <label
                            for="app_url"
                            class="block text-sm font-medium text-gray-700"
                        >
                            Application URL
                        </label>
                        <input
                            id="app_url"
                            v-model="form.app_url"
                            type="text"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        />
                        <div
                            v-if="form.errors.app_url"
                            class="text-red-500 text-sm mt-1"
                        >
                            {{ form.errors.app_url }}
                        </div>
                    </div>

                    <div>
                        <label
                            for="db_connection"
                            class="block text-sm font-medium text-gray-700"
                        >
                            Database Connection
                        </label>
                        <select
                            id="db_connection"
                            v-model="form.db_connection"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="mysql">MySQL</option>
                            <option value="pgsql">PostgreSQL</option>
                        </select>
                        <div
                            v-if="form.errors.db_connection"
                            class="text-red-500 text-sm mt-1"
                        >
                            {{ form.errors.db_connection }}
                        </div>
                    </div>

                    <div>
                        <label
                            for="db_host"
                            class="block text-sm font-medium text-gray-700"
                        >
                            Database Host
                        </label>
                        <input
                            id="db_host"
                            v-model="form.db_host"
                            type="text"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        />
                        <div
                            v-if="form.errors.db_host"
                            class="text-red-500 text-sm mt-1"
                        >
                            {{ form.errors.db_host }}
                        </div>
                    </div>

                    <div>
                        <label
                            for="db_port"
                            class="block text-sm font-medium text-gray-700"
                        >
                            Database Port
                        </label>
                        <input
                            id="db_port"
                            v-model="form.db_port"
                            type="text"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        />
                        <div
                            v-if="form.errors.db_port"
                            class="text-red-500 text-sm mt-1"
                        >
                            {{ form.errors.db_port }}
                        </div>
                    </div>

                    <div>
                        <label
                            for="db_database"
                            class="block text-sm font-medium text-gray-700"
                        >
                            Database Name
                        </label>
                        <input
                            id="db_database"
                            v-model="form.db_database"
                            type="text"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        />
                        <div
                            v-if="form.errors.db_database"
                            class="text-red-500 text-sm mt-1"
                        >
                            {{ form.errors.db_database }}
                        </div>
                    </div>

                    <div>
                        <label
                            for="db_username"
                            class="block text-sm font-medium text-gray-700"
                        >
                            Database Username
                        </label>
                        <input
                            id="db_username"
                            v-model="form.db_username"
                            type="text"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        />
                        <div
                            v-if="form.errors.db_username"
                            class="text-red-500 text-sm mt-1"
                        >
                            {{ form.errors.db_username }}
                        </div>
                    </div>

                    <div>
                        <label
                            for="db_password"
                            class="block text-sm font-medium text-gray-700"
                        >
                            Database Password
                        </label>
                        <input
                            id="db_password"
                            v-model="form.db_password"
                            type="password"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        />
                        <div
                            v-if="form.errors.db_password"
                            class="text-red-500 text-sm mt-1"
                        >
                            {{ form.errors.db_password }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 4: Admin & Server Config -->
            <div v-if="step === 4">
                <h2 class="text-xl font-medium text-gray-800 mb-4">
                    Admin Account & Server Setup
                </h2>

                <div class="space-y-4">
                    <div>
                        <label
                            for="admin_name"
                            class="block text-sm font-medium text-gray-700"
                        >
                            Admin Name
                        </label>
                        <input
                            id="admin_name"
                            v-model="form.admin_name"
                            type="text"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        />
                        <div
                            v-if="form.errors.admin_name"
                            class="text-red-500 text-sm mt-1"
                        >
                            {{ form.errors.admin_name }}
                        </div>
                    </div>

                    <div>
                        <label
                            for="admin_email"
                            class="block text-sm font-medium text-gray-700"
                        >
                            Admin Email
                        </label>
                        <input
                            id="admin_email"
                            v-model="form.admin_email"
                            type="email"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        />
                        <div
                            v-if="form.errors.admin_email"
                            class="text-red-500 text-sm mt-1"
                        >
                            {{ form.errors.admin_email }}
                        </div>
                    </div>

                    <div>
                        <label
                            for="admin_password"
                            class="block text-sm font-medium text-gray-700"
                        >
                            Admin Password
                        </label>
                        <input
                            id="admin_password"
                            v-model="form.admin_password"
                            type="password"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        />
                        <div
                            v-if="form.errors.admin_password"
                            class="text-red-500 text-sm mt-1"
                        >
                            {{ form.errors.admin_password }}
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input
                                    id="setup_nginx"
                                    v-model="form.setup_nginx"
                                    type="checkbox"
                                    class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded"
                                />
                            </div>
                            <div class="ml-3">
                                <label
                                    for="setup_nginx"
                                    class="text-sm font-medium text-gray-700"
                                >
                                    Setup Nginx Configuration
                                </label>
                                <p class="text-sm text-gray-500">
                                    This requires proper permissions to write to
                                    /etc/nginx/sites-enabled/
                                </p>
                            </div>
                        </div>
                    </div>

                    <div v-if="form.setup_nginx">
                        <label
                            for="nginx_server_name"
                            class="block text-sm font-medium text-gray-700"
                        >
                            Server Name
                        </label>
                        <input
                            id="nginx_server_name"
                            v-model="form.nginx_server_name"
                            type="text"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="example.com www.example.com"
                        />
                        <div
                            v-if="form.errors.nginx_server_name"
                            class="text-red-500 text-sm mt-1"
                        >
                            {{ form.errors.nginx_server_name }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex justify-between">
                <button
                    v-if="step > 1"
                    type="button"
                    @click="prevStep"
                    class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                >
                    Previous
                </button>
                <div v-else></div>

                <button
                    v-if="step < totalSteps"
                    type="button"
                    @click="nextStep"
                    :disabled="!canProceed"
                    class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    :class="{ 'opacity-50 cursor-not-allowed': !canProceed }"
                >
                    Next
                </button>
                <button
                    v-else
                    type="button"
                    @click="submit"
                    :disabled="form.processing"
                    class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                    :class="{
                        'opacity-50 cursor-not-allowed': form.processing,
                    }"
                >
                    Install
                </button>
            </div>
        </div>
    </div>
</template>
