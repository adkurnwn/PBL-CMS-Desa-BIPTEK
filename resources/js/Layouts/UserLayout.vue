<script setup>
import { ref, onMounted, onBeforeUnmount, provide } from "vue";
import Navbar from "../Components/Navbar.vue";
import axios from "axios";

const showPopup = ref(false);
const profile = ref({});
const loading = ref(true);

// Fetch profile data from API
const fetchProfile = async () => {
    try {
        loading.value = true;
        const response = await axios.get("/api/navbar");
        profile.value = response.data;
        loading.value = false;
    } catch (error) {
        console.error("Error fetching navbar data:", error);
        loading.value = false;
    }
};

onMounted(() => {
    fetchProfile();
    window.addEventListener("keydown", handleKeyDown);
});

onBeforeUnmount(() => {
    window.removeEventListener("keydown", handleKeyDown);
});

// Provide the profile data to child components
provide("profile", profile);

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
    isi: "",
});

// Submit logic
const submitForm = () => {
    console.log("Form submitted:", form.value);
    showPopup.value = false;
};

// ESC key to close popup
const handleKeyDown = (e) => {
    if (e.key === "Escape") {
        closePopup();
    }
};
</script>
<template>
    <div>
        <main class="bg-type1 min-h-screen">
            <!-- Navbar with profile data -->
            <div class="fixed top-0 left-0 w-full z-50">
                <Navbar :profile="profile" />
            </div>

            <!-- Page Content -->
            <slot />

            <!-- Overlay + Popup -->
            <div
                v-if="showPopup"
                class="fixed inset-0 z-40"
                @click.self="closePopup"
            >
                <!-- Transitioned Form -->
                <Transition name="fade-slide">
                    <div
                        v-show="showPopup"
                        class="absolute bottom-20 right-4 bg-white shadow-xl p-4 rounded-lg w-[90%] max-w-sm space-y-3 z-50"
                    >
                        <h2 class="text-lg font-semibold text-type2">
                            Form Pengaduan
                        </h2>
                        <form
                            @submit.prevent="submitForm"
                            class="flex flex-col gap-2"
                        >
                            <input
                                v-model="form.nama"
                                type="text"
                                placeholder="Nama"
                                class="border p-2 rounded text-sm"
                            />
                            <input
                                v-model="form.email"
                                type="email"
                                placeholder="Email"
                                class="border p-2 rounded text-sm"
                            />
                            <select
                                v-model="form.kategori"
                                class="border p-2 rounded text-sm"
                            >
                                <option value="" disabled selected>
                                    Pilih Kategori
                                </option>
                                <option value="sosial">Sosial</option>
                                <option value="ekonomi">Ekonomi</option>
                                <option value="kemasyarakatan">
                                    Kemasyarakatan
                                </option>
                            </select>
                            <textarea
                                v-model="form.isi"
                                placeholder="Isi Pengaduan"
                                rows="3"
                                class="border p-2 rounded text-sm resize-none"
                            ></textarea>
                            <button
                                type="submit"
                                class="bg-secondary text-white py-2 px-4 rounded hover:bg-opacity-80"
                            >
                                Kirim
                            </button>
                        </form>
                    </div>
                </Transition>
            </div>

            <!-- Floating Button -->
            <button
                @click="togglePopup"
                class="fixed bottom-4 right-4 z-50 bg-secondary opacity-90 rounded-full shadow-lg flex justify-center items-center w-14 h-14"
            >
                <button class="text-sm text-black opacity-100">
                    <img src="../../images/3p.png" alt="Buka Form" />
                </button>
            </button>
        </main>
    </div>
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
</style>
