<script setup>
import { ref, onMounted, onBeforeUnmount } from "vue";
import axios from "axios";

const uploadStatus = ref("");
const uploadMessage = ref("");
const showPopup = ref(false);
const isSubmitting = ref(false); // Add loading state
const categories = ref([]); // Add new ref for categories
const mainCaptchaData = ref(null);
const mainCaptchaCode = ref(""); // Add missing variable

const refreshCaptcha = async (type = "main") => {
    try {
        const response = await axios.get("/captcha/api/default");
        if (type === "main") {
            mainCaptchaData.value = response.data;
            mainCaptchaCode.value = response.data.code;
        } else {
            replyCaptchaData.value = response.data;
            replyCaptchaCode.value = response.data.code;
        }
    } catch (err) {
        console.error("Error loading captcha:", err);
    }
};

const togglePopup = () => {
    showPopup.value = !showPopup.value;
};

const closePopup = () => {
    showPopup.value = false;
};

// Form Data
const form = ref({
    nama: "",
    email: "",
    kategori: "",
    judul: "",
    isi: "",
    files: [],
    captcha: "", // Add captcha to form
});

function handleFileUpload(event) {
    const files = Array.from(event.target.files);

    if (files.length > 3) {
        uploadStatus.value = "error";
        uploadMessage.value = "Maksimal 3 file yang diperbolehkan.";
        form.value.files = [];
        return;
    }

    // Validate each file
    for (const file of files) {
        const validExtensions = ["jpg", "jpeg", "png", "pdf"];
        const fileExtension = file.name.split(".").pop().toLowerCase();

        if (!validExtensions.includes(fileExtension)) {
            uploadStatus.value = "error";
            uploadMessage.value =
                "Format file tidak valid. Hanya JPG, JPEG, PNG, dan PDF yang diperbolehkan.";
            form.value.files = [];
            return;
        }

        if (file.size > 1 * 1024 * 1024) {
            // 1MB limit
            uploadStatus.value = "error";
            uploadMessage.value =
                "Ukuran file terlalu besar (maks 1MB per file).";
            form.value.files = [];
            return;
        }
    }

    uploadStatus.value = "success";
    uploadMessage.value = `${files.length} file berhasil diunggah!`;
    form.value.files = files;
}

const fetchCategories = async () => {
    try {
        const response = await axios.get("/api/feedback/kategori");
        categories.value = response.data;
    } catch (error) {
        console.error("Failed to fetch categories:", error);
    }
};

const submitForm = async () => {
    try {
        isSubmitting.value = true;
        const formData = new FormData();
        formData.append("judul", form.value.judul);
        formData.append("isi", form.value.isi);
        formData.append("kategori", form.value.kategori);
        formData.append("nama_user", form.value.nama);
        formData.append("email", form.value.email);
        formData.append("captcha", form.value.captcha); // Add captcha
        formData.append("code", mainCaptchaCode.value); // Add captcha code

        // Append each file
        form.value.files.forEach((file, index) => {
            formData.append(`bukti[${index}]`, file);
        });

        const response = await axios.post("/api/feedback", formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });

        // Show success message
        uploadStatus.value = "success";
        uploadMessage.value =
            response.data.message || "Feedback berhasil dikirim";

        // Reset form
        form.value = {
            nama: "",
            email: "",
            kategori: "",
            judul: "",
            isi: "",
            files: [],
            captcha: "",
        };

        // Refresh captcha after successful submission
        await refreshCaptcha('main');

        // Close popup after short delay
        setTimeout(() => {
            showPopup.value = false;
            uploadStatus.value = "";
            uploadMessage.value = "";
        }, 2000);
    } catch (error) {
        // Handle errors
        uploadStatus.value = "error";
        uploadMessage.value =
            error.response?.data?.message || "Gagal mengirim feedback";
        
        // Refresh captcha on error
        await refreshCaptcha('main');
        form.value.captcha = ""; // Clear captcha input
    } finally {
        isSubmitting.value = false;
    }
};

// ESC key to close popup
const handleKeyDown = (e) => {
    if (e.key === "Escape") {
        closePopup();
    }
};

onMounted(() => {
    window.addEventListener("keydown", handleKeyDown);
    fetchCategories();
    refreshCaptcha('main'); // Add captcha initialization
});

onBeforeUnmount(() => {
    window.removeEventListener("keydown", handleKeyDown);
});
</script>

<template>
    <!-- Overlay + Popup -->
    <div v-if="showPopup" class="fixed inset-0 z-40" @click.self="closePopup">
        <!-- Transitioned Form -->
        <Transition name="fade-slide">
            <div v-show="showPopup"
                class="absolute bottom-20 right-4 bg-white shadow-xl p-4 rounded-lg w-[90%] max-w-sm space-y-3 z-50">
                <h2 class="text-lg font-semibold text-type2">
                    Keluhan dan Saran
                </h2>
                <form @submit.prevent="submitForm" class="flex flex-col gap-2">
                    <input v-model="form.nama" type="text" placeholder="*Nama Pengirim"
                        class="border p-2 rounded text-sm" required="true" />
                    <input v-model="form.email" type="email" placeholder="*Email"
                        class="flex border p-2 rounded text-sm" required="true" />
                    <div class="flex flex-col md:flex-row gap-2">
                        <input v-model="form.judul" type="text" placeholder="*Judul" class="border p-2 rounded text-sm"
                            required="true" />
                        <select v-model="form.kategori" class="flex w-full border p-2 rounded text-sm">
                            <option value="" disabled selected>
                                Pilih Kategori
                            </option>
                            <option v-for="category in categories" :key="category" :value="category">
                                {{ category }}
                            </option>
                        </select>
                    </div>

                    <textarea v-model="form.isi" placeholder="*Isi Pengaduan" rows="3"
                        class="border p-2 rounded text-sm resize-none" required="true"></textarea>
                    <div class="relative w-full">
                        <div class="flex items-center gap-3 flex-wrap">
                            <!-- Tombol Upload -->
                            <label for="fileUpload"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 border-2 text-accent1 rounded cursor-pointer hover:bg-opacity-90 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960"
                                    width="24px" fill="currentColor">
                                    <path
                                        d="M440-200h80v-167l64 64 56-57-160-160-160 160 57 56 63-63v167ZM240-80q-33 0-56.5-23.5T160-160v-640q0-33 23.5-56.5T240-880h320l240 240v480q0-33-23.5-56.5T720-80H240Zm280-520v-200H240v640h480v-440H520ZM240-800v200-200 640-640Z" />
                                </svg>
                                <span>Pilih File (Opsional)</span>
                            </label>

                            <!-- Input File -->
                            <input type="file" id="fileUpload" @change="handleFileUpload" accept=".jpg,.jpeg,.png,.pdf"
                                class="hidden" multiple />

                            <!-- File List -->
                            <div v-if="form.files.length > 0" class="space-y-2">
                                <p v-for="(file, index) in form.files" :key="index"
                                    class="text-sm text-gray-700 flex items-center gap-2">
                                    📎 <strong>{{ file.name }}</strong>
                                </p>
                            </div>
                            <div class="flex items-center w-full gap-2">
                                <img :src="mainCaptchaData?.image" alt="CAPTCHA" class="" />
                                <input v-model="form.captcha" type="text" placeholder="Masukkan kode captcha..."
                                    class="w-full text-xs lg:text-base border-1 border-zinc-300 rounded shadow-inner bg-type1"
                                    required />
                            </div>
                            <button type="button" @click="refreshCaptcha('main')"
                                class="text-xs text-blue-600 hover:text-blue-800">
                                Refresh Captcha
                            </button>

                        </div>
                    </div>

                    <!-- Status Upload -->
                    <Transition name="fade">
                        <div v-if="uploadStatus" :class="{
                            'text-green-600 bg-green-100 border border-green-300':
                                uploadStatus === 'success',
                            'text-red-600 bg-red-100 border border-red-300':
                                uploadStatus === 'error',
                        }" class="mt-2 p-2 rounded text-sm">
                            {{ uploadMessage }}
                        </div>
                    </Transition>
                    <button type="submit"
                        class="bg-secondary text-white py-2 px-4 rounded hover:bg-opacity-80 disabled:opacity-50"
                        :disabled="isSubmitting">
                        {{ isSubmitting ? "Mengirim..." : "Kirim" }}
                    </button>
                </form>
            </div>
        </Transition>
    </div>

    <!-- Floating Button -->
    <button @click="togglePopup"
        class="fixed bottom-4 right-4 z-50 bg-secondary opacity-90 rounded-full shadow-lg flex justify-center items-center w-14 h-14">
        <span class="text-sm opacity-100">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"
                fill="currentColor" class="text-accent1">
                <path
                    d="M240-400h122l200-200q9-9 13.5-20.5T580-643q0-11-5-21.5T562-684l-36-38q-9-9-20-13.5t-23-4.5q-11 0-22.5 4.5T440-722L240-522v122Zm280-243-37-37 37 37ZM300-460v-38l101-101 20 18 18 20-101 101h-38Zm121-121 18 20-38-38 20 18Zm26 181h273v-80H527l-80 80ZM80-80v-720q0-33 23.5-56.5T160-880h640q33 0 56.5 23.5T880-800v480q0 33-23.5 56.5T800-240H240L80-80Zm126-240h594v-480H160v525l46-45Zm-46 0v-480 480Z" />
            </svg>
        </span>
    </button>
</template>

<style scoped>
/* Animasi fade-in dan fade-out yang elegan */
.fade-slide-enter-active,
.fade-slide-leave-active {
    transition: opacity 0.5s ease;
}

.fade-slide-enter-from,
.fade-slide-leave-to {
    opacity: 0;
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
