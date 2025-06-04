<script setup>
import { ref, onMounted, computed } from "vue";
import UserLayout from "@/Layouts/UserLayout.vue";
import Map from "@/Components/FrontendComponent/Map.vue";
import { useApiCache } from "@/Utility/useApiCache";
import { parseFasilitas, parseMisi } from "@/Utility/useParser";

const profile = ref({});
const demografi = ref({});

const fetchData = async () => {
    const profileApi = useApiCache("profileData", "/api/profile/profile");
    const demografiApi = useApiCache("demografiData", "/api/demografi/preview");

    await Promise.all([
        profileApi.fetchWithCache(),
        demografiApi.fetchWithCache(),
    ]);

    profile.value = profileApi.data.value || {};
    demografi.value = demografiApi.data.value || {};
};

onMounted(fetchData);
const missionList = computed(() => parseMisi(profile.value.mission || ""));
const fasilitasList = computed(() =>
    parseFasilitas(profile.value.fasilitas || "")
);
</script>
<!-- Profileview.vue -->
<template>
    <UserLayout v-slot="{ layout }">
        <header class="flex items-center justify-center font-merriweather">
            <!--Sejarah Desa-->
            <article class="h-screen font-merriweather">
                <img
                    :src="`/storage/${profile.hero_image}`"
                    alt="Logo"
                    class="absolute z-0 left-0 w-full h-screen object-cover"
                />
                <div
                    class="absolute bg-black z-0 left-0 w-full h-screen opacity-60"
                ></div>
                <h1
                    class="text-type1 xl:text-3xl md:text-4xl text-lg xl:text-left text-center font-bold relative z-0 xl:right-36 sm:right-56 right-0 xl:mt-72 md:mt-96 sm:mt-60 mt-64"
                >
                    Sejarah Desa
                </h1>
                <p
                    class="text-type1 xl:text-xl md:text-2xl sm:text-base text-xs text-justify font-thin relative z-0 xl:right-36 sm:right-0 right-0 mt-2 xl:max-w-4xl md:max-w-2xl sm:max-w-xl max-w-xs"
                >
                    {{ profile.history }}
                </p>
            </article>
        </header>
        <main
            class="flex flex-col items-center justify-center max-w-7xl mx-auto w-full font-merriweather"
        >
            <!-- Visi & Misi -->
            <article
                class="flex xl:flex-row flex-col relative items-center justify-center w-full font-merriweather bg-type1"
            >
                <div class="xl:mt-24 md:mt-24 sm:mt-20 mt-14 xl:mr-12 sm:mr-0">
                    <!-- Lingkaran Putih -->
                    <div class="relative oval-container xl:ml-4 sm:ml-4 ml-1">
                        <img
                            :src="`/storage/${profile.kepala_desa_image}`"
                            :alt="`${profile.kepala_desa_name} Profile`"
                            class="xl:py-5 md:py-7 sm:py-4 py-4 xl:ml-1 md:ml-2 sm:ml-0 xl:w-80 md:w-80 sm:w-72"
                        />
                    </div>
                    <!-- Kotak Hijau -->
                    <div
                        class="bg-accent2 text-white text-center xl:py-4 md:py-4 sm:py-3 py-3 xl:px-12 md:px-12 sm:px-10 px-2 rounded-lg relative z-0 xl:w-fit md:w-96 sm:w-72 w-64 xl:h-20 md:h-20 sm:h-16 h-16 xl:-mt-20 md:-mt-20 sm:-mt-16 -mt-16 xl:ml-0 md:-ml-1 sm:ml-0 ml-0"
                    >
                        <p
                            class="font-bold xl:text-xl md:text-xl sm:text-base text-sm"
                        >
                            {{ profile.kepala_desa_name }}
                        </p>
                        <p
                            class="italic xl:text-base md:text-base sm:text-xs text-xs"
                        >
                            Kepala {{ profile.name }}
                        </p>
                    </div>
                </div>
                <div class="text-semiBlack xl:mt-24 md:mt-6 mt-5">
                    <h1
                        class="font-bold text-accent1 xl:text-3xl md:text-2xl sm:text-xl text-lg text-center"
                    >
                        Visi
                    </h1>
                    <p
                        class="text-xs sm:text-base md:text-lg xl:text-xl text-semiBlack text-center italic xl:max-w-3xl sm:max-w-xl max-w-xs xl:mt-1"
                    >
                        “{{ profile.vision }}”
                    </p>
                    <h1
                        class="font-bold text-accent1 xl:text-3xl md:text-2xl sm:text-xl text-lg xl:mt-5 md:mt-5 mt-3 text-center"
                    >
                        Misi
                    </h1>
                    <div class="text-justify xl:mt-1">
                        <ol
                            class="list-decimal xl:pl-10 sm:pl-16 pl-4 xl:max-w-3xl md:max-w-xl sm:max-w-lg max-w-xs space-y-3 text-semiBlack text-xs sm:text-base md:text-lg xl:text-xl italic"
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
            </article>

            <!-- Struktur Pemerintahan -->
            <div
                class="flex flex-col items-center justify-center font-merriweather bg-type1"
            >
                <h1
                    id="struktur-pemerintahan"
                    class="font-bold text-accent2 xl:text-3xl md:text-2xl text-xl xl:-ml-96 xl:mr-96 sm:mr-80 mr-24 xl:pr-16 xl:mt-20 sm:mt-16 mt-14"
                >
                    Struktur Pemerintahan
                </h1>
                <p
                    class="xl:text-xl md:text-lg sm:text-base text-xs text-semiBlack xl:max-w-7xl sm:max-w-xl max-w-xs text-left xl:mr-56 md:mr-11 sm:mr-0 xl:ml-2 sm:ml-7 -ml-1 italic"
                >
                    Susunan organisasi yang bertanggung jawab dalam mengelola
                    administrasi dan pelayanan publik di desa.
                </p>
                <div
                    class="relative flex xl:flex-row flex-col xl:gap-7 sm:gap-5 gap-3 p-7 bg-transparant -mt-3 items-center xl:mr-52"
                >
                    <!-- Card 1 -->
                    <div
                        class="container bg-white rounded-xl shadow-lg text-center"
                    >
                        <img
                            :src="`/storage/${profile.organization_image}`"
                            alt="Struktur Organisasi"
                            class="w-full xl:h-72 xl:-mt-2 object-contain"
                        />
                        <p
                            class="text-accent1 text-sm xl:text-lg sm:text-lg font-semibold py-2 xl:-mt-1"
                        >
                            Struktur Organisasi Pemerintahan Desa
                        </p>
                    </div>
                    <!-- Card 2 -->
                    <div
                        class="container bg-white rounded-xl shadow-lg text-center"
                    >
                        <img
                            src="../../../images/SO_BPD.png"
                            alt="Struktur Organisasi"
                            class="w-full xl:h-72 xl:-mt-1 object-contain"
                        />
                        <p
                            class="text-accent1 text-sm xl:text-lg sm:text-lg font-semibold py-2 xl:-mt-2"
                        >
                            Struktur Organisasi Badan Permusyawaratan Desa
                        </p>
                    </div>
                </div>
            </div>
            <!-- Potensi & Keunggulan -->
            <div
                class="flex flex-col items-center w-full font-merriweather bg-type1 relative"
            >
                <h1
                    class="font-bold text-accent1 xl:text-3xl sm:text-xl max-w-30 xl:-ml-96 xl:mr-96 sm:mr-64 xl:pr-4 sm:pr-6 xl:mt-16 sm:mt-11"
                >
                    Potensi & Keunggulan Desa
                </h1>
                <p
                    class="xl:text-xl sm:text-base text-semiBlack xl:max-w-7xl sm:max-w-xl xl:ml-0 sm:ml-6 xl:mr-80 sm:mr-0 pr-3 italic"
                >
                    Potensi dan aset penting dalam mendorong pertumbuhan desa
                    dan kesejahteraan masyarakat.
                </p>
                <div
                    class="flex justify-center xl:gap-5 sm:gap-2 p-6 bg-transparant -mt-3 xl:mr-14 sm:mr-2"
                >
                    <!-- Card 1 -->
                    <div class="rounded-xl">
                        <img
                            src="../../../images/POTENSI_1.png"
                            alt="Gambar 1"
                            class="xl:w-[368px] sm:w-44"
                        />
                    </div>
                    <!-- Card 2 -->
                    <div class="rounded-xl">
                        <img
                            src="../../../images/POTENSI_2.png"
                            alt="Gambar 2"
                            class="xl:w-[368px] sm:w-44"
                        />
                    </div>
                    <!-- Card 3 -->
                    <div class="rounded-xl">
                        <img
                            src="../../../images/POTENSI_3.png"
                            alt="Gambar 2"
                            class="xl:w-[368px] sm:w-44"
                        />
                    </div>
                </div>
                <p
                    class="flex items-center xl:ml-96 ml-60 xl:-mr-96 pl-44 -mt-5 text-secondary text-lg"
                >
                    Selengkapnya
                    <svg
                        class="mt-0 w-6 h-6"
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
            <!-- Infrastruktur Desa -->
            <div
                class="flex flex-col items-center w-full font-merriweather bg-type1"
            >
                <h1
                    class="text-accent1 xl:text-3xl sm:text-xl font-bold max-w-30 xl:pr-96 xl:-ml-96 sm:-ml-56 mr-36 mt-14"
                >
                    Infrastruktur Desa
                </h1>
                <p
                    class="xl:text-xl sm:text-base text-semiBlack max-w-7xl xl:-ml-60 sm:ml-14 xl:mr-2 sm:mr-9 -mb-96 italic"
                >
                    Informasi tentang sarana dan prasarana yang tersedia di desa
                    yang mendukung kehidupan masyarakat.
                </p>
                <div
                    class="grid grid-cols-2 xl:gap-2 gap-1 p-80 bg-transparant xl:-ml-96 sm:-ml-64 xl:mr-56 sm:-mr-12 mt-20"
                >
                    <!-- Card 1 -->
                    <div>
                        <img
                            src="../../../images/JEMBATAN.png"
                            alt="Gambar 1"
                            class="w-72 rounded-lg"
                        />
                    </div>
                    <!-- Card 2 -->
                    <div>
                        <img
                            src="../../../images/GEDUNG_SERBA_GUNA.png"
                            alt="Gambar 2"
                            class="w-72 rounded-lg infrastruktur-img"
                        />
                    </div>
                    <!-- Card 3 -->
                    <div>
                        <img
                            src="../../../images/PASAR.png"
                            alt="Gambar 2"
                            class="w-72 rounded-lg"
                        />
                    </div>
                    <!-- Card 4 -->
                    <div>
                        <img
                            src="../../../images/BALAI_DESA.png"
                            alt="Gambar 2"
                            class="w-72 rounded-lg"
                        />
                    </div>
                </div>
                <div
                    class="flex flex-wrap ml-96 sm:pl-5 xl:-mt-96 -mt-48 xl:pt-12 sm:pt-7 text-accent2"
                >
                    <ol
                        class="list-disc list-inside font-semibold xl:text-xl sm:text-sm max-w-3xl xl:space-y-2 sm:space-y-1 xl:mr-0 sm:mr-5 -mt-96"
                    >
                        <li
                            v-for="(fasilitas, index) in fasilitasList"
                            :key="index"
                        >
                            {{ fasilitas }}
                        </li>
                    </ol>
                </div>
            </div>
            <!-- Data Geografi -->
            <div
                class="flex flex-col items-center w-full font-merriweather bg-type1 xl:mt-3 -mt-32"
            >
                <h1
                    class="font-bold text-accent1 xl:text-3xl text-xl max-w-30 xl:pr-96 pr-2 xl:-ml-96 -ml-52 mr-52 xl:mt-24 mt-16"
                >
                    Data Geografi
                </h1>
                <div
                    class="flex flex-col w-96 xl:-ml-96 -ml-36 xl:mr-96 mr-2 xl:text-xl text-base"
                >
                    <div class="flex w-full">
                        <p class="w-1/3 font-bold text-accent1 -ml-3">
                            Koordinat
                        </p>
                        <p class="w-1/2 text-accent2 -ml-4">
                            : -4.498, 119.600
                        </p>
                    </div>
                    <div class="flex w-full">
                        <p class="w-1/3 font-bold text-accent1 -ml-3">
                            Luas Desa
                        </p>
                        <p class="w-1/2 text-accent2 -ml-4">: 9.300.000 m²</p>
                    </div>
                </div>
                <!-- Pass location data to Map component if available -->
                <div
                    class="xl:w-[700px] w-[400px] xl:-ml-24 xl:mr-96 mr-40 mt-2 z-0"
                >
                    <Map
                        :location-name="
                            [
                                layout?.provinsi,
                                layout?.kabupaten,
                                layout?.kecamatan,
                                layout?.name,
                            ]
                                .filter(Boolean)
                                .join(' ')
                        "
                        :provinsi="profile?.name"
                        :kode-pos="profile.kode_pos"
                    />
                </div>
                <div
                    class="xl:ml-80 sm:ml-96 xl:-mr-80 -mr-10 xl:-mt-[455px] -mt-[305px] text-accent1 xl:text-xl text-sm"
                >
                    <h1 class="font-bold">Batas Desa</h1>
                    <div class="xl:max-w-96 max-w-40">
                        <p class="font-bold xl:mt-3 mt-2">Utara</p>
                        <span>Kelurahan Lalolang & Desa Tellumpanua</span>
                        <p class="font-bold xl:mt-3 mt-2">Barat</p>
                        <span>Selat Makassar</span>
                        <p class="font-bold xl:mt-3 mt-2">Timur</p>
                        <span>Kecamatan Ternate Riaja</span>
                        <p class="font-bold xl:mt-3 mt-2">Selatan</p>
                        <span>Desa Corawali</span>
                    </div>
                </div>
                <div
                    class="xl:mt-32 mt-16 xl:-ml-80 ml-12 xl:pr-4 text-accent1 xl:text-xl text-sm"
                >
                    <p class="font-bold -ml-16">
                        GJ22+QCF, Pao-Pao, Kec. Tanete Rilau, Kabupaten Barru,
                        Sulawesi Selatan 90761
                    </p>
                </div>
            </div>
            <!-- Data Demografi -->
            <div
                class="flex flex-col items-center w-full font-merriweather bg-type1 mb-12"
            >
                <h1
                    class="font-bold text-accent1 xl:text-3xl text-xl max-w-30 xl:-ml-96 -ml-96 xl:mr-96 mr-6 xl:pr-44 pr-0 m-20 mt-20"
                >
                    Data Demografi
                </h1>
                <p
                    class="xl:text-xl text-base text-accent1 xl:max-w-5xl max-w-xl xl:-ml-36 ml-3 -mt-20 italic"
                >
                    Menyajikan informasi mengenai aspek kependudukan yang
                    memberikan gambaran mengenai kondisi sosial ekonomi
                    masyarakat desa.
                </p>
                <div
                    class="flex flex-wrap gap-5 xl:mr-20 mr-9 mt-3 justify-center"
                >
                    <!-- Grid 1 -->
                    <div
                        class="bg-accent2 text-type1 p-4 rounded-lg shadow-lg w-64 h-40 text-center"
                    >
                        <svg
                            class="ml-44 h-16 -mt-3"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="http://www.w3.org/2000/svg"
                            width="48px"
                            fill="#DEA521"
                        >
                            <path
                                d="M40-160v-160q0-29 20.5-49.5T110-390h141q17 0 32.5 8.5T310-358q29 42 74 65t96 23q51 0 96-23t75-65q11-15 26-23.5t32-8.5h141q29 0 49.5 20.5T920-320v160H660v-119q-36 33-82.5 51T480-210q-51 0-97-18t-83-51v119H40Zm440-170q-35 0-67.5-16.5T360-392q-16-23-38.5-37T273-448q29-30 91-46t116-16q54 0 116.5 16t91.5 46q-26 5-48.5 19T601-392q-20 29-52.5 45.5T480-330ZM160-460q-45 0-77.5-32.5T50-570q0-46 32.5-78t77.5-32q46 0 78 32t32 78q0 45-32 77.5T160-460Zm640 0q-45 0-77.5-32.5T690-570q0-46 32.5-78t77.5-32q46 0 78 32t32 78q0 45-32 77.5T800-460ZM480-580q-45 0-77.5-32.5T370-690q0-46 32.5-78t77.5-32q46 0 78 32t32 78q0 45-32 77.5T480-580Z"
                            />
                        </svg>
                        <p class="text-3xl font-thin text-left ml-3 mt-3">
                            {{ demografi.penduduk }}
                        </p>
                        <p class="text-lg text-left ml-3">Penduduk</p>
                    </div>
                    <!-- Grid 2 -->
                    <div
                        class="bg-accent2 text-type1 p-4 rounded-lg shadow-lg w-64 h-40 text-center"
                    >
                        <svg
                            class="ml-44 h-16 -mt-3"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="http://www.w3.org/2000/svg"
                            width="48px"
                            fill="#DEA521"
                        >
                            <path
                                d="M720.08-727q-30.08 0-51.58-21.42t-21.5-51.5q0-30.08 21.42-51.58t51.5-21.5q30.08 0 51.58 21.42t21.5 51.5q0 30.08-21.42 51.58t-51.5 21.5ZM660-80v-336q0-30-15-54t-44-35l41-120q8-25 29.5-40t48.5-15q27 0 48.5 15t29.5 40l102 295H790v250H660ZM495-500q-25 0-42.5-17.5T435-560q0-25 17.5-42.5T495-620q25 0 42.5 17.5T555-560q0 25-17.5 42.5T495-500ZM220.08-727q-30.08 0-51.58-21.42t-21.5-51.5q0-30.08 21.42-51.58t51.5-21.5q30.08 0 51.58 21.42t21.5 51.5q0 30.08-21.42 51.58t-51.5 21.5ZM150-80v-290H80v-250q0-24.75 17.63-42.38Q115.25-680 140-680h160q24.75 0 42.38 17.62Q360-644.75 360-620v250h-70v290H150Zm290 0v-170h-50v-166q0-18.33 12.83-31.17Q415.67-460 434-460h122q18.33 0 31.17 12.83Q600-434.33 600-416v166h-50v170H440Z"
                            />
                        </svg>
                        <p class="text-3xl font-thin text-left ml-3 mt-3">
                            {{ demografi.kepala_keluarga }}
                        </p>
                        <p class="text-lg text-left ml-3">Kepala Keluarga</p>
                    </div>
                    <!-- Grid 3 -->
                    <div
                        class="bg-accent2 text-type1 p-4 rounded-lg shadow-lg w-64 h-40 text-center"
                    >
                        <svg
                            class="ml-44 h-16 -mt-3"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="http://www.w3.org/2000/svg"
                            width="48px"
                            fill="#DEA521"
                        >
                            <path
                                d="M800-800v220h-60v-117L556-514q21 29 32.5 63.5T600-380q0 92-64 156t-156 64q-92 0-156-64t-64-156q0-92 64-156t156-64q36 0 70 11.5t63 32.5l184-184H580v-60h220ZM380.2-540Q314-540 267-493.2t-47 113q0 66.2 46.8 113.2t113 47q66.2 0 113.2-46.8t47-113q0-66.2-46.8-113.2t-113-47Z"
                            />
                        </svg>
                        <p class="text-3xl font-thin text-left ml-3 mt-3">
                            {{ demografi.penduduk_laki_laki }}
                        </p>
                        <p class="text-lg text-left ml-3">Penduduk Laki-Laki</p>
                    </div>
                    <!-- Grid 4 -->
                    <div
                        class="bg-accent2 text-type1 p-4 rounded-lg shadow-lg w-64 h-40 text-center"
                    >
                        <svg
                            class="ml-44 h-16 -mt-3"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="http://www.w3.org/2000/svg"
                            width="48px"
                            fill="#DEA521"
                        >
                            <path
                                d="M450-120v-80h-80v-60h80v-102q-82-11-136-73.71-54-62.7-54-145.29 0-91.42 64.5-155.21Q389-800 480-800t155.5 63.79Q700-672.42 700-581q0 82.59-54 145.29Q592-373 510-362v102h80v60h-80v80h-60Zm29.8-300q66.2 0 113.2-46.8t47-113q0-66.2-46.8-113.2t-113-47Q414-740 367-693.2t-47 113q0 66.2 46.8 113.2t113 47Z"
                            />
                        </svg>
                        <p class="text-3xl font-thin text-left ml-3 mt-3">
                            {{ demografi.penduduk_perempuan }}
                        </p>
                        <p class="text-lg text-left ml-3">Penduduk Perempuan</p>
                    </div>
                </div>
                <p
                    class="flex items-center xl:ml-96 ml-60 xl:-mr-96 pl-28 mt-2 text-secondary text-xl"
                >
                    Selengkapnya
                    <svg
                        class="mt-0 w-6 h-6"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="M411-481 213-679l42-42 240 240-240 240-42-42 198-198Zm253 0L466-679l42-42 240 240-240 240-42-42 198-198Z"
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
    width: 320px;
    height: 420px;
    background: white;
    border-radius: 50% / 50%;
}
.container {
    max-width: 100%;
    overflow: hidden;
}
@media (max-width: 820px) {
    /* Ipad Air */
    .oval-container {
        width: 330px;
        height: 430px;
        background: white;
        border-radius: 50% / 50%;
    }
    .container {
        max-width: 80%;
        overflow: hidden;
    }
}
@media (max-width: 768px) {
    /* Ipad */
    .oval-container {
        width: 246px;
        height: 325px;
        background: white;
        border-radius: 50% / 50%;
    }
    .container {
        max-width: 90%;
        overflow: hidden;
    }
}
@media (max-width: 375px) {
    /* Mobile */
    .oval-container {
        width: 240px;
        height: 320px;
    }
    .container {
        max-width: 100%;
        overflow: hidden;
    }
}
</style>
