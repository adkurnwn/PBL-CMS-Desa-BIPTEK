<script setup>
import { ref, onMounted, computed } from "vue";
import UserLayout from "@/Layouts/UserLayout.vue";
import Map from "@/Components/FrontendComponent/Map.vue";
import { useApiCache } from "@/Utility/useApiCache";
import { useMeta } from "@/Utility/useMeta";
import { parseFasilitas, parseMisi, parseBatas } from "@/Utility/useParser";
import { useImageErrorHandler } from "@/Utility/useImageErrorHandler";
import { Head, Link } from "@inertiajs/vue3";

const profile = ref({});
const demografi = ref({});
const coordinates = ref({ lat: null, lon: null, display_name: null });
const { handleImageError, getImageSrc } = useImageErrorHandler();

// Meta tags setup
const {
  metaTitle,
  metaDescription,
  metaImage,
  canonicalUrl,
  metaType,
  metaSiteName
} = useMeta({
  title: 'Profil Desa',
  description: computed(() => {
    if (profile.value.history) {
      // Strip HTML tags and limit description
      const textContent = profile.value.history.replace(/<[^>]*>/g, '');
      return textContent.length > 160 
        ? textContent.substring(0, 157) + '...' 
        : textContent;
    }
    return 'Profil lengkap desa meliputi sejarah, visi misi, struktur pemerintahan, infrastruktur, data geografi dan demografi.';
  }),
  type: 'website',
  url: `${window.location.origin}/profile`,
  image: computed(() => profile.value.hero_image || 'default-profile-image.jpg')
});

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
const batasList = computed(() =>
    parseBatas(profile.value.batas_desa || "")
);

const handleCoordinatesUpdate = (coords) => {
    coordinates.value = coords;
};
</script>
<!-- Profileview.vue -->
<template>
    <Head>
        <title>{{ metaTitle }}</title>
        <meta name="description" :content="metaDescription" />
        
        <!-- Open Graph / Facebook -->
        <meta property="og:type" :content="metaType" />
        <meta property="og:title" :content="metaTitle" />
        <meta property="og:description" :content="metaDescription" />
        <meta property="og:url" :content="canonicalUrl" />
        <meta property="og:site_name" :content="metaSiteName" />
        <meta property="og:image" :content="metaImage" />
        
        <!-- Twitter -->
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="metaTitle" />
        <meta name="twitter:description" :content="metaDescription" />
        <meta name="twitter:image" :content="metaImage" />
        
        <!-- Canonical URL -->
        <link rel="canonical" :href="canonicalUrl" />
    </Head>
    <UserLayout v-slot="{ layout }">
        <header class="flex items-center justify-center font-merriweather">
            <!--Sejarah Desa-->
            <article class="h-screen font-merriweather">
                <img :src="getImageSrc(`/storage/${profile.hero_image}`)" alt="Logo"
                    class="absolute z-0 left-0 w-full h-screen object-cover" @error="handleImageError(`/storage/${profile.hero_image}`)" />
                <div class="absolute bg-black z-0 left-0 w-full h-screen opacity-60"></div>
                <div
                    class="flex flex-col justify-center items-center h-full px-[6.56%] pb-[6.56%] sm:justify-end sm:items-start sm:pl-[6.56%] sm:pr-[25%] sm:pb-[6.56%] sm:px-0">
                    <h1 class="text-type1 text-lg font-bold z-0 text-center sm:text-left md:text-4xl xl:text-3xl">
                        Sejarah Desa
                    </h1>
                    <p
                        class="text-type1 text-xs font-thin relative z-0 mt-2 sm:text-justify text-justify sm:text-base md:text-2xl xl:text-xl">
                        {{ profile.history }}
                    </p>
                </div>

            </article>
        </header>
        <main class="max-w-screen-4xl mx-auto w-full px-[6.56%] font-merriweather space-y-10 xl:mt-24 md:mt-6 mt-5 xl:mb-24 md:mb-6 mb-5">
            <!-- Visi & Misi -->
            <article
                class="flex xl:flex-row flex-col relative justify-center w-full font-merriweather bg-type1 lg:space-x-20">
                <div class="justify-start items-center flex flex-col">
                    <div class="xl:w-80 md:w-80 sm:w-72">
                        <!-- Lingkaran Putih -->
                        <div class="relative overflow-hidden rounded-full aspect-[514/620] w-full">
                            <img :src="getImageSrc(`/storage/${profile.kepala_desa_image}`)"
                                :alt="`${profile.kepala_desa_name} Profile`" class="w-full h-full object-cover"
                                @error="handleImageError(`/storage/${profile.kepala_desa_image}`)" />
                        </div>

                        <!-- Kotak Hijau -->
                        <div class="bg-accent2 text-white text-center p-2 rounded-lg relative z-0 -mt-16">
                            <p class="font-bold xl:text-xl md:text-xl sm:text-base text-sm">
                                {{ profile.kepala_desa_name }}
                            </p>
                            <p class="italic xl:text-base md:text-base sm:text-xs text-xs">
                                Kepala {{ profile.name }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="text-semiBlack">
                    <h1 class="font-bold text-accent1 xl:text-3xl md:text-2xl sm:text-xl text-lg text-center">
                        Visi
                    </h1>
                    <p class="text-xs sm:text-base md:text-lg xl:text-xl text-semiBlack text-center italic xl:mt-1">
                        “{{ profile.vision }}”
                    </p>
                    <h1
                        class="font-bold text-accent1 xl:text-3xl md:text-2xl sm:text-xl text-lg xl:mt-10 md:mt-5 mt-5 text-center">
                        Misi
                    </h1>
                    <div class="text-justify xl:mt-1">
                        <ol
                            class="list-decimal space-y-3 text-semiBlack text-xs sm:text-base md:text-lg xl:text-xl italic">
                            <li v-for="(misi, index) in missionList" :key="index">
                                {{ misi }}
                            </li>
                        </ol>
                    </div>
                </div>
            </article>

            <!-- Struktur Pemerintahan -->
            <div class="flex flex-col items-left justify-center w-full font-merriweather bg-type1">
                <h1 id="struktur-pemerintahan" class="font-bold text-accent2 xl:text-3xl md:text-2xl text-xl">
                    Struktur Pemerintahan
                </h1>
                <p class="xl:text-xl md:text-lg sm:text-base text-xs text-semiBlack text-left italic">
                    Susunan organisasi yang bertanggung jawab dalam mengelola
                    administrasi dan pelayanan publik di {{ layout.name }}.
                </p>
                <div
                    class="relative flex xl:flex-row flex-col xl:gap-7 sm:gap-5 gap-3 p-7 bg-transparant -mt-3 items-center justify-center">
                    <!-- Card 1 -->
                    <div class=" bg-white rounded-xl shadow-lg text-center">
                        <img :src="getImageSrc(`/storage/${profile.organization_image}`)" alt="Struktur Organisasi"
                            class="w-full xl:h-72 object-contain" @error="handleImageError(`/storage/${profile.organization_image}`)" />
                        <p class="text-accent1 text-sm xl:text-lg sm:text-lg font-semibold py-2 xl:-mt-1">
                            Struktur Organisasi Pemerintahan {{ layout.name }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center justify-end">
                    <p class="flex items-center xl:-mt-4 text-secondary text-lg">
                        Selengkapnya
                        <svg class="mt-0 w-6 h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"
                            width="24px" fill="#DEA521">
                            <path
                                d="M411-481 213-679l42-42 240 240-240 240-42-42 198-198Zm253 0L466-679l42-42 240 240-240 240-42-42 198-198Z" />
                        </svg>
                    </p>
                </div>
            </div>
            <!-- Infrastruktur Desa -->
            <div class="flex flex-col w-full font-merriweather bg-type1">
                <h1 class="text-accent2 xl:text-3xl sm:text-xl font-bold">
                    Infrastruktur Desa
                </h1>
                <p class="xl:text-xl sm:text-base text-semiBlack max-w-7xl italic">
                    Informasi tentang sarana dan prasarana yang tersedia di desa
                    yang mendukung kehidupan masyarakat.
                </p>
                <div class="text-accent2 mt-2">
                    <ol class="columns-2 list-disc list-inside font-semibold xl:text-xl sm:text-sm max-w-3xl space-y-1">
                        <li v-for="(fasilitas, index) in fasilitasList" :key="index">
                            {{ fasilitas }}
                        </li>
                    </ol>
                </div>

            </div>
            <!-- Data Geografi -->
            <div class="flex flex-col w-full font-merriweather bg-type1">
                <h1 class="font-bold text-accent2 xl:text-3xl text-xl">
                    Data Geografi
                </h1>
                <div class="flex flex-col w-96 xl:text-xl text-base">
                    <div class="flex w-full">
                        <p class="w-1/3 font-bold text-accent1">
                            Koordinat
                        </p>
                        <p class="w-2/3 text-accent2">
                            : {{ coordinates.lat && coordinates.lon ? `${coordinates.lat}, ${coordinates.lon}` : '-' }}
                        </p>
                    </div>
                    <div class="flex w-full">
                        <p class="w-1/3 font-bold text-accent1">
                            Luas Desa
                        </p>
                        <p class="w-2/3 text-accent2">: 9.300.000 m²</p>
                    </div>
                </div>
                <div class="flex space-x-[2%]">
                    <!-- Pass location data to Map component if available -->
                    <div class="w-[58.06%] mt-2 z-0">
                        <Map :location-name="[
                            layout?.provinsi,
                            layout?.kabupaten,
                            layout?.kecamatan,
                            layout?.name,
                        ]
                            .filter(Boolean)
                            .join(' ')
                            " :kelurahan="profile?.name" :kode-pos="profile.kode_pos"
                            @coordinates-found="handleCoordinatesUpdate" />
                    </div>
                    <div class="text-accent1 xl:text-xl text-sm">
                        <h1 class="font-bold">Batas Desa</h1>
                        <div class="xl:max-w-96 max-w-40">
                            <p class="font-bold xl:mt-3 mt-2">Utara</p>
                            <span>{{ batasList.utara }}</span>
                            <p class="font-bold xl:mt-3 mt-2">Barat</p>
                            <span>{{ batasList.barat }}</span>
                            <p class="font-bold xl:mt-3 mt-2">Timur</p>
                            <span>{{ batasList.timur }}</span>
                            <p class="font-bold xl:mt-3 mt-2">Selatan</p>
                            <span>{{ batasList.selatan }}</span>
                        </div>
                    </div>
                </div>
                <div class="text-accent1 xl:text-xl text-sm mt-1">
                    <p class="font-bold">
                        {{ coordinates.display_name || '-' }}
                    </p>
                </div>
            </div>
            <!-- Data Demografi -->
            <div class="flex flex-col w-full font-merriweather bg-type1">
                <h1 class="font-bold text-accent1 xl:text-3xl text-xl">
                    Data Demografi
                </h1>
                <p class="xl:text-xl text-base text-accent1 xl:max-w-5xl max-w-xl italic">
                    Menyajikan informasi mengenai aspek kependudukan yang
                    memberikan gambaran mengenai kondisi sosial ekonomi
                    masyarakat desa.
                </p>
                <div class="flex flex-wrap lg:grid lg:grid-cols-4 gap-5 mt-3 sm:mb-6">
                    <!-- Grid 1 -->
                    <div class="bg-accent2 text-type1 p-4 rounded-lg shadow-lg w-full h-40 text-center">
                        <svg class="flex ml-auto" xmlns="http://www.w3.org/2000/svg" height="48px"
                            viewBox="0 -960 960 960" width="48px" fill="#DEA521">
                            <path
                                d="M40-160v-160q0-34 23.5-57t56.5-23h131q20 0 38 10t29 27q29 39 71.5 61t90.5 22q49 0 91.5-22t70.5-61q13-17 30.5-27t36.5-10h131q34 0 57 23t23 57v160H640v-91q-35 25-75.5 38T480-200q-43 0-84-13.5T320-252v92H40Zm440-160q-38 0-72-17.5T351-386q-17-25-42.5-39.5T253-440q22-37 93-58.5T480-520q63 0 134 21.5t93 58.5q-29 0-55 14.5T609-386q-22 32-56 49t-73 17ZM160-440q-50 0-85-35t-35-85q0-51 35-85.5t85-34.5q51 0 85.5 34.5T280-560q0 50-34.5 85T160-440Zm640 0q-50 0-85-35t-35-85q0-51 35-85.5t85-34.5q51 0 85.5 34.5T920-560q0 50-34.5 85T800-440ZM480-560q-50 0-85-35t-35-85q0-51 35-85.5t85-34.5q51 0 85.5 34.5T600-680q0 50-34.5 85T480-560Z" />
                        </svg>
                        <p class="text-3xl font-thin text-left ml-3 mt-3">
                            {{ demografi.jumlah_penduduk }}
                        </p>
                        <p class="text-lg text-left ml-3">Penduduk</p>
                    </div>
                    <!-- Grid 2 -->
                    <div class="bg-accent2 text-type1 p-4 rounded-lg shadow-lg w-full h-40 text-center">
                        <svg class="flex ml-auto" xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"
                            width="48px" fill="#DEA521">
                            <path
                                d="M720.08-727q-30.08 0-51.58-21.42t-21.5-51.5q0-30.08 21.42-51.58t51.5-21.5q30.08 0 51.58 21.42t21.5 51.5q0 30.08-21.42 51.58t-51.5 21.5ZM660-80v-336q0-30-15-54t-44-35l41-120q8-25 29.5-40t48.5-15q27 0 48.5 15t29.5 40l102 295H790v250H660ZM495-500q-25 0-42.5-17.5T435-560q0-25 17.5-42.5T495-620q25 0 42.5 17.5T555-560q0 25-17.5 42.5T495-500ZM220.08-727q-30.08 0-51.58-21.42t-21.5-51.5q0-30.08 21.42-51.58t51.5-21.5q30.08 0 51.58 21.42t21.5 51.5q0 30.08-21.42 51.58t-51.5 21.5ZM150-80v-290H80v-250q0-24.75 17.63-42.38Q115.25-680 140-680h160q24.75 0 42.38 17.62Q360-644.75 360-620v250h-70v290H150Zm290 0v-170h-50v-166q0-18.33 12.83-31.17Q415.67-460 434-460h122q18.33 0 31.17 12.83Q600-434.33 600-416v166h-50v170H440Z" />
                        </svg>
                        <p class="text-3xl font-thin text-left ml-3 mt-3">
                            {{ demografi.jumlah_keluarga }}
                        </p>
                        <p class="text-lg text-left ml-3">Kepala Keluarga</p>
                    </div>
                    <!-- Grid 3 -->
                    <div class="bg-accent2 text-type1 p-4 rounded-lg shadow-lg w-full h-40 text-center">
                        <svg class="flex ml-auto" xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"
                            width="48px" fill="#DEA521">
                            <path
                                d="M800-800v220h-60v-117L556-514q21 29 32.5 63.5T600-380q0 92-64 156t-156 64q-92 0-156-64t-64-156q0-92 64-156t156-64q36 0 70 11.5t63 32.5l184-184H580v-60h220ZM380.2-540Q314-540 267-493.2t-47 113q0 66.2 46.8 113.2t113 47q66.2 0 113.2-46.8t47-113q0-66.2-46.8-113.2t-113-47Z" />
                        </svg>
                        <p class="text-3xl font-thin text-left ml-3 mt-3">
                            {{ demografi.penduduk_pria }}
                        </p>
                        <p class="text-lg text-left ml-3">Penduduk Laki-Laki</p>
                    </div>
                    <!-- Grid 4 -->
                    <div class="bg-accent2 text-type1 p-4 rounded-lg shadow-lg w-full h-40 text-center">
                        <svg class="flex ml-auto" xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"
                            width="48px" fill="#DEA521">
                            <path
                                d="M450-120v-80h-80v-60h80v-102q-82-11-136-73.71-54-62.7-54-145.29 0-91.42 64.5-155.21Q389-800 480-800t155.5 63.79Q700-672.42 700-581q0 82.59-54 145.29Q592-373 510-362v102h80v60h-80v80h-60Zm29.8-300q66.2 0 113.2-46.8t47-113q0-66.2-46.8-113.2t-113-47Q414-740 367-693.2t-47 113q0 66.2 46.8 113.2t113 47Z" />
                        </svg>
                        <p class="text-3xl font-thin text-left ml-3 mt-3">
                            {{ demografi.penduduk_wanita }}
                        </p>
                        <p class="text-lg text-left ml-3">Penduduk Perempuan</p>
                    </div>
                </div>
                <Link
                    href="/statistik-penduduk"
                    class="flex items-center justify-end mt-2 text-secondary text-xl"
                >
                    Selengkapnya
                    <svg class="mt-0 w-6 h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" width="24px" fill="#DEA521">
                    <path
                        d="M411-481 213-679l42-42 240 240-240 240-42-42 198-198Zm253 0L466-679l42-42 240 240-240 240-42-42 198-198Z"
                    />
                    </svg>
                </Link>
            </div>
        </main>
    </UserLayout>
</template>

<style scoped>
.oval-container {
    background: white;
    border-radius: 50% / 50%;
}

.container {
    max-width: 100%;
    overflow: hidden;
}
</style>
