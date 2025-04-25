<script setup>
import { ref, onMounted, computed } from "vue";
import UserLayout from "@/Layouts/UserLayout.vue";

// Define reactive variables
const profile = ref({});
const loading = ref(false);

const fetchProfile = async () => {
    try {
        loading.value = true;
        const response = await axios.get("/api/profile");
        profile.value = response.data;
        loading.value = false;
    } catch (error) {
        console.error("Error fetching profile data:", error);
        loading.value = false;
    }
};

onMounted(() => {
    fetchProfile();
});

// Compute missionList after profile is loaded
const missionList = computed(() => {
    if (!profile.value || !profile.value.mission) return [];

    if (typeof profile.value.mission === "string") {
        return profile.value.mission
            .split(/\d+\.\s/)
            .filter((item) => item.trim() !== "");
    }

    return [];
});

const parseFasilitas = computed(() => {
    if (!profile.value || !profile.value.fasilitas) return [];

    if (typeof profile.value.fasilitas === "string") {
        try {
            return JSON.parse(profile.value.fasilitas);
        } catch (e) {
            console.error("Error parsing fasilitas:", e);
            return [];
        }
    }

    return Array.isArray(profile.value.fasilitas)
        ? profile.value.fasilitas
        : [];
});

const parseJson = (value) => {
    if (typeof value === "string") {
        try {
            return JSON.parse(value);
        } catch (e) {
            return [];
        }
    }
    return Array.isArray(value) ? value : [];
};
</script>
<!-- Profileview.vue -->
<template>
    <UserLayout>
        <!-- Sejarah Desa -->
        <div
            class="flex flex-col justify-center h-screen font-merriweather space-y-1 bg-green-100"
        >
            <img
                :src="`/storage/${profile.hero_image}`"
                alt="Logo"
                class="absolute z-0 h-screen w-full object-cover"
            />
            <div class="absolute bg-black z-0 w-full h-screen opacity-60"></div>
            <h1
                class="text-type1 text-3xl font-bold text-center md:text-left md:ml-20 pt-40 z-10"
            >
                Sejarah Desa
            </h1>
            <p
                class="text-type1 font-thin text-md md:text-xl text-justify max-w-2xl md:max-w-4xl ml-11 md:ml-20 mr-11 md:mr-0 pt-2 z-10"
            >
                {{ profile.history }}
            </p>
        </div>
        <main class="mx-auto w-full">
            <!-- Visi & Misi -->
            <div
                class="flex flex-col relative items-center w-full font-merriweather bg-type1"
            >
                <!-- Lingkaran Putih -->
                <div
                    class="relative oval-container mb-40 mt-10 md:mt-36 mr-96 ml-96 md:-ml-80"
                >
                    <img
                        v-if="profile.kepala_desa"
                        :src="`/storage/${profile.kepala_desa.photo_profile}`"
                        alt="Kepala Desa"
                        class="mt-4 md:mt-5 ml-0 md:ml-3 w-62 md:w-80"
                    />
                </div>
                <!-- Kotak Hijau -->
                <div
                    class="bg-accent2 text-white text-center py-2 md:py-4 px-0 md:px-12 rounded-lg z-10 w-72 md:w-fit h-16 md:h-20 -mt-56 md:-mt-60 -mr-80 md:mr-96 -ml-80"
                >
                    <p
                        v-if="profile.kepala_desa"
                        class="font-bold text-md md:text-xl"
                    >
                        {{ profile.kepala_desa.name }}
                    </p>
                    <p class="italic text-sm md:text-md">
                        Kepala {{ profile.name }}
                    </p>
                </div>
                <div
                    class="flex flex-col relative md:-top-20 text-semiBlack ml-0 md:ml-72 mr-0 md:-mr-40 mt-4 md:-mt-80"
                >
                    <h1
                        class="font-bold text-accent1 text-xl md:text-sm lg:text-3xl max-w-30 text-center"
                    >
                        Visi
                    </h1>
                    <p
                        class="text-sm md:text-xl text-semiBlack max-w-md md:max-w-3xl text-center mt-0 md:mt-1 italic"
                    >
                        “{{ profile.vision }}”
                    </p>
                    <h1
                        class="font-bold text-accent1 text-xl md:text-3xl max-w-30 md:max-w-30 mt-2 md:mt-5 text-center"
                    >
                        Misi
                    </h1>
                    <div class="text-justify mt-0 md:mt-1">
                        <ol
                            class="list-decimal text-semiBlack pl-5 text-sm md:text-xl max-w-md md:max-w-2xl space-y-3 italic"
                        >
                            <li
                                v-for="(misi, index) in missionList"
                                :key="index"
                            >
                                {{ misi }}
                            </li>
                        </ol>
                    </div>
                </div>
            </div>

            <!-- Struktur Pemerintahan -->
            <div
                class="flex flex-col items-center font-merriweather bg-type1 mt-10 md:mt-0 px-4 md:px-0"
            >
                <h1
                    class="font-bold text-accent2 text-xl md:text-4xl max-w-30 -ml-96 -mr-0 md:mr-96 md:mt-15"
                >
                    Struktur Pemerintahan
                </h1>
                <p
                    class="text-sm md:text-xl text-semiBlack max-w-xl md:max-w-7xl text-left -ml-10 md:-ml-56 italic"
                >
                    Susunan organisasi yang bertanggung jawab dalam mengelola
                    administrasi dan pelayanan publik di desa.
                </p>
                <div
                    class="flex flex-wrap gap-6 p-60 md:p-7 -ml-32 md:-ml-4 bg-transparant mb-28 md:mb-0 -mt-60 md:-mt-3"
                >
                    <!-- Card 1 -->
                    <div
                        class="container bg-white rounded-lg md:rounded-xl shadow-lg text-center mt-2 md:mt-0 ml-2 md:ml-0"
                    >
                        <img
                            :src="`/storage/${profile.organization_image}`"
                            alt="Gambar 1"
                            class="w-full h-72 -mt-5 md:-mt-3 object-contain"
                        />
                        <p
                            class="text-accent1 text-lg font-semibold py-1 -mt-5 md:-mt-3"
                        >
                            Struktur Organisasi Pemerintahan Desa
                        </p>
                    </div>
                    <!-- Card 2 -->
                    <div
                        class="container bg-white rounded-lg md:rounded-xl shadow-lg text-center ml-2 md:ml-0"
                    >
                        <img
                            src="../../../images/SO_BPD.png"
                            alt="Gambar 2"
                            class="w-full h-72 -mt-5 md:-mt-3 object-contain"
                        />
                        <p
                            class="text-accent1 text-lg font-semibold py-1 -mt-5 md:-mt-3"
                        >
                            Struktur Organisasi Badan Permusyawaratan Desa
                        </p>
                    </div>
                </div>
            </div>
            <!-- Potensi & Keunggulan -->
            <div
                class="flex flex-col items-center w-full font-merriweather bg-type1 -mt-72 md:-mt-2 relative"
            >
                <h1
                    class="font-bold text-accent1 text-xl md:text-4xl max-w-30 -ml-6 md:-ml-96 mr-80 m-20 mt-0 md:mt-12"
                >
                    Potensi & Keunggulan Desa
                </h1>
                <p
                    class="text-sm md:text-xl text-semiBlack max-w-2xl md:max-w-7xl -ml-1 md:-ml-80 mr-3 -mt-20 md:-mt-20 italic"
                >
                    Potensi dan aset penting dalam mendorong pertumbuhan desa
                    dan kesejahteraan masyarakat.
                </p>
                <div
                    class="flex justify-center gap-2 md:gap-5 p-6 bg-transparant -ml-6 md:-ml-1 -mt-4 md:-mt-3"
                >
                    <!-- Card 1 -->
                    <div class="rounded-xl">
                        <img
                            src="../../../images/POTENSI_1.png"
                            alt="Gambar 1"
                            class="w-48 md:w-96"
                        />
                    </div>
                    <!-- Card 2 -->
                    <div class="rounded-xl">
                        <img
                            src="../../../images/POTENSI_2.png"
                            alt="Gambar 2"
                            class="w-48 md:w-96"
                        />
                    </div>
                    <!-- Card 3 -->
                    <div class="rounded-xl">
                        <img
                            src="../../../images/POTENSI_3.png"
                            alt="Gambar 2"
                            class="w-48 md:w-96"
                        />
                    </div>
                </div>
                <div class="-ml-60 md:ml-32">
                    <p
                        class="flex ml-40 md:ml-96 -mr-96 pl-40 text-secondary text-md md:text-xl -mt-5"
                    >
                        Selengkapnya
                        <svg
                            class="mt-1 w-5 h-5 md:w-6 md:h-6"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 -960 960 960"
                            width="24px"
                            fill="#DEA521"
                        >
                            <path
                                d="M411-481 213-679l42-42 240 240-240 240-42-42 198-198Zm253 0L466-679l42-42 240 240-240 240-42-42 198-198Z"
                            />
                        </svg>
                    </p>
                </div>
            </div>
            <!-- Infrastruktur Desa -->
            <div
                class="flex flex-col items-center w-full font-merriweather bg-type1 -mt-14 md:-mt-10 -ml-11"
            >
                <h1
                    class="font-bold text-accent1 text-xl md:text-4xl max-w-30 md:max-w-30 -ml-64 md:-ml-96 md:mr-96 m-20 md:m-20"
                >
                    Infrastruktur Desa
                </h1>
                <p
                    class="text-sm md:text-xl text-semiBlack max-w-2xl md:max-w-7xl ml-16 md:-ml-40 pl-2 md:pl-0 -mt-20 md:-mt-20 -mb-2 italic"
                >
                    Informasi tentang sarana dan prasarana yang tersedia di desa
                    yang mendukung kehidupan masyarakat.
                </p>
                <div
                    class="grid grid-cols-2 gap-1 p-[340px] bg-transparant -ml-96 -mr-64 md:mr-52 -mt-80"
                >
                    <!-- Card 1 -->
                    <div class="rounded-xl">
                        <img
                            src="../../../images/JEMBATAN.png"
                            alt="Gambar 1"
                            class="w-48 md:w-64 rounded-lg"
                        />
                    </div>
                    <!-- Card 2 -->
                    <div class="rounded-xl">
                        <img
                            src="../../../images/GEDUNG_SERBA_GUNA.png"
                            alt="Gambar 2"
                            class="w-48 md:w-64 rounded-lg"
                        />
                    </div>
                    <!-- Card 3 -->
                    <div class="rounded-xl">
                        <img
                            src="../../../images/PASAR.png"
                            alt="Gambar 2"
                            class="w-48 md:w-64 rounded-lg"
                        />
                    </div>
                    <!-- Card 4 -->
                    <div class="rounded-xl">
                        <img
                            src="../../../images/BALAI_DESA.png"
                            alt="Gambar 2"
                            class="w-48 md:w-64 rounded-lg"
                        />
                    </div>
                </div>
                <div
                    class="flex flex-wrap ml-96 md:ml-80 pr-12 -mr-40 md:-mr-0 -mt-96 pt-40 md:pt-20 text-accent2"
                >
                    <ol
                        class="list-disc list-inside font-semibold text-sm md:text-xl max-w-lg md:max-w-3xl space-y-1 -mt-96 ml-20"
                    >
                        <li
                            v-for="(fasilitas, index) in parseFasilitas"
                            :key="index"
                        >
                            {{ fasilitas }}
                        </li>
                    </ol>
                </div>
            </div>
            <!-- Data Geografi -->
            <div
                class="flex flex-col items-center w-full font-merriweather bg-type1 -mt-28 md:-mt-9"
            >
                <h1
                    class="font-bold text-accent1 text-xl md:text-4xl max-w-30 -ml-96 md:-ml-44 pr-20 md:pr-96 -mr-0 md:mr-96 m-56 md:m-16 mt-12 md:mt-16"
                >
                    Data Geografi
                </h1>
                <div
                    class="flex flex-col w-96 -ml-96 -mr-48 md:mr-96 -mt-56 md:-mt-16 pt-1 text-sm md:text-lg"
                >
                    <div class="flex w-full">
                        <p class="w-1/3 font-bold text-accent1 -ml-3 md:-ml-4">
                            Koordinat
                        </p>
                        <p class="w-1/2 text-accent2 -ml-10 md:-ml-4">
                            : -4.498, 119.600
                        </p>
                    </div>
                    <div class="flex w-full">
                        <p class="w-1/3 font-bold text-accent1 -ml-3 md:-ml-4">
                            Luas Desa
                        </p>
                        <p class="w-1/2 text-accent2 -ml-10 md:-ml-4">
                            : 9.300.000 m²
                        </p>
                    </div>
                </div>
                <div class="h-96">
                    <iframe
                        class="w-[450px] md:w-[600px] h-[290px] md:h-full -ml-48 md:-ml-52 -mr-11 md:mr-96 rounded-lg mt-2"
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15910.138432608619!2d119.61024555!3d-4.4971499999999995!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dbe258c1d785e89%3A0xcb84698ceea645b7!2sPao-Pao%2C%20Kec.%20Tanete%20Rilau%2C%20Kabupaten%20Barru%2C%20Sulawesi%20Selatan!5e0!3m2!1sid!2sid!4v1741847539077!5m2!1sid!2sid"
                        style="border: 0"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                    ></iframe>
                </div>
                <div
                    class="-ml-64 -mr-64 md:-mr-40 pl-96 text-accent1 text-sm md:text-lg -mt-96 pt-2"
                >
                    <h1 class="font-bold ml-24">Batas Desa</h1>
                    <div class="ml-24 max-w-40 md:max-w-96">
                        <p class="font-bold mt-2 md:mt-3">Utara</p>
                        <span>Kelurahan Lalolang & Desa Tellumpanua</span>
                        <p class="font-bold mt-2 md:mt-3">Barat</p>
                        <span>Selat Makassar</span>
                        <p class="font-bold mt-2 md:mt-3">Timur</p>
                        <span>Kecamatan Ternate Riaja</span>
                        <p class="font-bold mt-2 md:mt-3">Selatan</p>
                        <span>Desa Corawali</span>
                    </div>
                </div>
                <div
                    class="mt-11 md:mt-24 ml-14 md:-ml-96 text-accent1 text-sm md:text-lg"
                >
                    <p class="font-bold -ml-28">
                        GJ22+QCF, Pao-Pao, Kec. Tanete Rilau, Kabupaten Barru,
                        Sulawesi Selatan 90761
                    </p>
                </div>
            </div>
            <!-- Data Demografi -->
            <div
                class="flex flex-col items-center w-full font-merriweather bg-type1"
            >
                <h1
                    class="font-bold text-accent1 text-xl md:text-4xl max-w-30 md:max-w-30 -ml-16 md:-ml-96 mr-0 md:mr-96 pr-96 md:pr-40 m-12 md:m-14"
                >
                    Data Demografi
                </h1>
                <p
                    class="text-sm md:text-xl text-accent1 max-w-5xl ml-8 md:-ml-44 mr-2 -mt-12 md:-mt-14 italic"
                >
                    Menyajikan informasi mengenai aspek kependudukan yang
                    memberikan gambaran mengenai kondisi sosial ekonomi
                    masyarakat desa.
                </p>
                <div
                    class="flex flex-wrap gap-2 md:gap-5 mr-36 md:mr-32 mt-3 justify-center"
                >
                    <!-- Grid 1 -->
                    <div
                        class="bg-accent2 text-white p-4 rounded-lg w-56 md:w-64 h-32 md:h-36 text-center"
                    >
                        <svg
                            class="ml-36 md:ml-44 h-16 -mt-3"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 -960 960 960"
                            width="48px"
                            fill="#DEA521"
                        >
                            <path
                                d="M40-160v-160q0-29 20.5-49.5T110-390h141q17 0 32.5 8.5T310-358q29 42 74 65t96 23q51 0 96-23t75-65q11-15 26-23.5t32-8.5h141q29 0 49.5 20.5T920-320v160H660v-119q-36 33-82.5 51T480-210q-51 0-97-18t-83-51v119H40Zm440-170q-35 0-67.5-16.5T360-392q-16-23-38.5-37T273-448q29-30 91-46t116-16q54 0 116.5 16t91.5 46q-26 5-48.5 19T601-392q-20 29-52.5 45.5T480-330ZM160-460q-45 0-77.5-32.5T50-570q0-46 32.5-78t77.5-32q46 0 78 32t32 78q0 45-32 77.5T160-460Zm640 0q-45 0-77.5-32.5T690-570q0-46 32.5-78t77.5-32q46 0 78 32t32 78q0 45-32 77.5T800-460ZM480-580q-45 0-77.5-32.5T370-690q0-46 32.5-78t77.5-32q46 0 78 32t32 78q0 45-32 77.5T480-580Z"
                            />
                        </svg>
                        <p
                            v-if="profile.data_demografi"
                            class="text-2xl md:text-3xl font-thin text-left ml-2 md:ml-3 -mt-1 md:-mt-0"
                        >
                            {{ profile.data_demografi.penduduk }}
                        </p>
                        <p class="text-sm md:text-lg text-left ml-2 md:ml-3">
                            Penduduk
                        </p>
                    </div>
                    <!-- Grid 2 -->
                    <div
                        class="bg-accent2 text-white p-4 rounded-lg w-56 md:w-64 h-32 md:h-36 text-center"
                    >
                        <svg
                            class="ml-36 md:ml-44 h-16 -mt-3"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 -960 960 960"
                            width="48px"
                            fill="#DEA521"
                        >
                            <path
                                d="M720.08-727q-30.08 0-51.58-21.42t-21.5-51.5q0-30.08 21.42-51.58t51.5-21.5q30.08 0 51.58 21.42t21.5 51.5q0 30.08-21.42 51.58t-51.5 21.5ZM660-80v-336q0-30-15-54t-44-35l41-120q8-25 29.5-40t48.5-15q27 0 48.5 15t29.5 40l102 295H790v250H660ZM495-500q-25 0-42.5-17.5T435-560q0-25 17.5-42.5T495-620q25 0 42.5 17.5T555-560q0 25-17.5 42.5T495-500ZM220.08-727q-30.08 0-51.58-21.42t-21.5-51.5q0-30.08 21.42-51.58t51.5-21.5q30.08 0 51.58 21.42t21.5 51.5q0 30.08-21.42 51.58t-51.5 21.5ZM150-80v-290H80v-250q0-24.75 17.63-42.38Q115.25-680 140-680h160q24.75 0 42.38 17.62Q360-644.75 360-620v250h-70v290H150Zm290 0v-170h-50v-166q0-18.33 12.83-31.17Q415.67-460 434-460h122q18.33 0 31.17 12.83Q600-434.33 600-416v166h-50v170H440Z"
                            />
                        </svg>
                        <p
                            v-if="profile.data_demografi"
                            class="text-2xl md:text-3xl font-thin text-left ml-2 md:ml-3 -mt-1 md:-mt-0"
                        >
                            {{ profile.data_demografi.kepala_keluarga }}
                        </p>
                        <p class="text-sm md:text-lg text-left ml-2 md:ml-3">
                            Kepala Keluarga
                        </p>
                    </div>
                    <!-- Grid 3 -->
                    <div
                        class="bg-accent2 text-white p-4 rounded-lg w-56 md:w-64 h-32 md:h-36 text-center"
                    >
                        <svg
                            class="ml-36 md:ml-44 h-16 -mt-3"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 -960 960 960"
                            width="48px"
                            fill="#DEA521"
                        >
                            <path
                                d="M800-800v220h-60v-117L556-514q21 29 32.5 63.5T600-380q0 92-64 156t-156 64q-92 0-156-64t-64-156q0-92 64-156t156-64q36 0 70 11.5t63 32.5l184-184H580v-60h220ZM380.2-540Q314-540 267-493.2t-47 113q0 66.2 46.8 113.2t113 47q66.2 0 113.2-46.8t47-113q0-66.2-46.8-113.2t-113-47Z"
                            />
                        </svg>
                        <p
                            v-if="profile.data_demografi"
                            class="text-2xl md:text-3xl font-thin text-left ml-2 md:ml-3 -mt-1 md:-mt-0"
                        >
                            {{ profile.data_demografi.penduduk_laki_laki }}
                        </p>
                        <p class="text-sm md:text-lg text-left ml-2 md:ml-3">
                            Penduduk Laki-Laki
                        </p>
                    </div>
                    <!-- Grid 4 -->
                    <div
                        class="bg-accent2 text-white p-4 rounded-lg w-56 md:w-64 h-32 md:h-36 text-center"
                    >
                        <svg
                            class="ml-36 md:ml-44 h-16 -mt-3"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 -960 960 960"
                            width="48px"
                            fill="#DEA521"
                        >
                            <path
                                d="M450-120v-80h-80v-60h80v-102q-82-11-136-73.71-54-62.7-54-145.29 0-91.42 64.5-155.21Q389-800 480-800t155.5 63.79Q700-672.42 700-581q0 82.59-54 145.29Q592-373 510-362v102h80v60h-80v80h-60Zm29.8-300q66.2 0 113.2-46.8t47-113q0-66.2-46.8-113.2t-113-47Q414-740 367-693.2t-47 113q0 66.2 46.8 113.2t113 47Z"
                            />
                        </svg>
                        <p
                            v-if="profile.data_demografi"
                            class="text-2xl md:text-3xl font-thin text-left ml-2 md:ml-3 -mt-1 md:-mt-0"
                        >
                            {{ profile.data_demografi.penduduk_perempuan }}
                        </p>
                        <p class="text-sm md:text-lg text-left ml-2 md:ml-3">
                            Penduduk Perempuan
                        </p>
                    </div>
                </div>

                <p
                    class="flex items-center ml-96 mr-40 md:-mr-96 md:pl-20 mt-1 md:mt-2 text-secondary text-md md:text-xl"
                >
                    Selengkapnya
                    <svg
                        class="mt-1 md:mt-0 w-5 h-5 md:w-6 md:h-6"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 -960 960 960"
                        width="24px"
                        fill="#DEA521"
                    >
                        <path
                            d="M411-481 213-679l42-42 240 240-240 240-42-42 198-198Zm253 0L466-679l42-42 240 240-240 240-42-42 198-198Z"
                        />
                    </svg>
                </p>
            </div>
        </main>
    </UserLayout>
</template>

<style scoped>
.oval-container {
    width: 240px;
    height: 320px;
    background: white;
    border-radius: 50% / 50%;
}
.container {
    width: 500px;
    height: 285px;
    overflow: hidden;
}

@media (min-width: 768px) {
    .oval-container {
        width: 340px;
        height: 420px;
        background: white;
        border-radius: 50% / 50%;
    }
    .container {
        max-width: 45%;
        overflow: hidden;
    }
}
</style>
