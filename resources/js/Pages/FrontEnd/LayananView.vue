<script setup>
import UserLayout from "@/Layouts/UserLayout.vue";
import ProductCard from "@/Components/ProductCard.vue";
import useApiFetch from "@/Utility/useApiFetch";
import { ref, onMounted } from "vue";

const produkDesa = ref({});

const fetchData = async () => {
    const produkDesaLoading = ref(true);
    const produkDesaApi = useApiFetch();
    await produkDesaApi.fetchData("/api/produk-desa");
    produkDesa.value = produkDesaApi.data.value || [];
    produkDesaLoading.value = produkDesaApi.loading.value;
};

onMounted(fetchData());
</script>
<!-- Homeview.vue -->
<template>
    <UserLayout v-slot="{ layout }">
        <main
            class="flex flex-col items-center justify-center max-w-7xl mx-auto w-full font-merriweather"
        >
            <!--Layanan Publik-->
            <article
                class="flex xl:flex-row flex-col w-full xl:px-20 md:px-16 sm:px-24 px-10 xl:space-x-6 xl:mt-52 sm:mt-40 mt-32 xl:ml-2 justify-center"
            >
                <img
                    src="../../../images/LAYANAN_1.png"
                    alt="Logo"
                    class="h-auto w-full xl:max-w-xl md:max-w-4xl sm:max-w-xl"
                />
                <div
                    class="xl:max-w-xl md:max-w-4xl sm:max-w-xl text-justify xl:space-y-2 md:space-y-3 sm:space-y-2 space-y-1 xl:mt-12 sm:mt-3 mt-2"
                >
                    <h1
                        class="text-accent2 font-bold xl:text-3xl sm:text-xl text-md"
                    >
                        Layanan Publik {{ layout.name }}
                    </h1>
                    <p class="text-accent1 xl:text-xl sm:text-lg text-xs">
                        Kami berkomitmen untuk memberikan layanan yang
                        transparan, informatif, dan bermanfaat bagi seluruh
                        masyarakat {{ layout.name }}.
                    </p>
                    <p class="text-accent1 xl:text-xl sm:text-lg text-xs">
                        Masyarakat dapat menemukan informasi mengenai Laporan
                        Dana Desa, Produk Hukum dan Produk Unggulan Desa.
                    </p>
                </div>
            </article>
            <!--Produk Unggulan-->
            <article
                class="w-full xl:px-20 md:px-16 sm:px-16 px-10 flex flex-col bg-type1 font-merriweather xl:mt-20 md:mt-16 sm:mt-16 mt-11"
            >
                <h1
                    class="xl:text-3xl sm:text-xl text-md font-semibold text-accent2"
                >
                    Produk Unggulan
                </h1>
                <h2
                    class="xl:text-xl sm:text-lg text-xs text-semiBlack xl:my-2 sm:my-2 my-2"
                >
                    <p class="italic">
                        Produk unggulan desa yang berasal dari UMKM dan sumber
                        daya alam desa
                    </p>
                </h2>
                <div
                    class="grid grid-cols-2 gap-1 xl:flex xl:flex-row justify-center -m-1 mt-1"
                >
                    <div
                        v-if="produkDesaLoading"
                        class="text-center w-full col-span-2"
                    >
                        <p>Memuat produk desa...</p>
                    </div>
                    <div
                        v-else-if="produkDesa.length === 0"
                        class="text-center w-full col-span-2"
                    >
                        <p>Produk desa tidak tersedia.</p>
                    </div>
                    <ProductCard
                        v-else
                        v-for="produkDesa in produkDesa"
                        :key="produkDesa.id"
                        :produkDesa="produkDesa"
                    />
                </div>
                <h1
                    class="flex text-secondary xl:text-xl sm:text-lg text-xs justify-end mt-3"
                >
                    Selengkapnya
                    <svg
                        class="xl:mt-1 sm:mt-1 xl:w-6 xl:h-6 sm:w-5 sm:h-5 w-4 h-4"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 -960 960 960"
                        width="24px"
                        fill="#DEA521"
                    >
                        <path
                            d="M411-481 213-679l42-42 240 240-240 240-42-42 198-198Zm253 0L466-679l42-42 240 240-240 240-42-42 198-198Z"
                        />
                    </svg>
                </h1>
            </article>
            <!--Layanan Publik-->
            <article
                class="w-full xl:px-20 md:px-16 sm:px-16 px-10 flex flex-col bg-type1 font-merriweather xl:mt-10 md:mt-8 sm:mt-8 mt-5 mb-14"
            >
                <h1
                    class="xl:text-3xl sm:text-xl text-md font-semibold text-accent2"
                >
                    Layanan Publik
                </h1>
                <h2
                    class="xl:text-xl sm:text-lg text-xs text-semiBlack xl:my-2 sm:my-2 my-1"
                >
                    <p class="italic">
                        Produk unggulan desa yang berasal dari UMKM dan sumber
                        daya alam desa
                    </p>
                </h2>
                <div
                    class="flex flex-wrap max-w-3xl xl:gap-4 sm:gap-3 gap-2 mt-2"
                >
                    <!-- Grid 1 -->
                    <div
                        v-if="layout.produk_hukum"
                        class="bg-secondary text-accent1 text-center p-4 rounded-lg shadow-lg xl:w-8/12 sm:w-64 w-72 xl:h-40 sm:h-40 h-32"
                    >
                        <svg
                            class="xl:mt-1 sm:mt-1 -mt-1"
                            xmlns="http://www.w3.org/2000/svg"
                            height="40px"
                            viewBox="0 -960 960 960"
                            width="40px"
                            fill="#244031"
                        >
                            <path
                                d="M160-120v-66.67h480V-120H160Zm223.33-206L160-549.33 234.67-626 460-402.67 383.33-326Zm254-254L414-805.33 490.67-880 714-656.67 637.33-580Zm196 420L302-691.33 348.67-738 880-206.67 833.33-160Z"
                            />
                        </svg>
                        <p
                            class="xl:text-lg sm:text-lg text-md text-left ml-1 mt-1"
                        >
                            Produk Hukum
                        </p>
                        <p class="text-xs text-left ml-1 max-w-xs">
                            Kumpulan peraturan, keputusan, dan kebijakan resmi
                            yang berlaku di desa.
                        </p>
                    </div>
                    <!-- Grid 2 -->
                    <div
                        v-if="layout.dana_desa"
                        class="bg-secondary text-accent1 p-4 rounded-lg shadow-lg xl:w-60 sm:w-64 w-72 xl:h-40 sm:h-40 h-32 text-center"
                    >
                        <svg
                            class="-ml-1 xl:mt-1 sm:mt-1 -mt-1"
                            xmlns="http://www.w3.org/2000/svg"
                            height="40px"
                            viewBox="0 -960 960 960"
                            width="48px"
                            fill="#244031"
                        >
                            <path
                                d="M450-240h60v-40h60q12.75 0 21.38-8.63Q600-297.25 600-310v-130q0-12.75-8.62-21.38Q582.75-470 570-470H420v-70h180v-60h-90v-40h-60v40h-60q-12.75 0-21.37 8.62Q360-582.75 360-570v130q0 12.75 8.63 21.37Q377.25-410 390-410h150v70H360v60h90v40ZM220-80q-24 0-42-18t-18-42v-680q0-24 18-42t42-18h341l239 239v501q0 24-18 42t-42 18H220Zm0-60h520v-474L534-820H220v680Zm0 0v-680 680Z"
                            />
                        </svg>
                        <p
                            class="xl:text-lg sm:text-lg text-md text-left ml-1 mt-1"
                        >
                            Laporan Dana Desa
                        </p>
                        <p class="text-xs text-left ml-1">
                            Kumpulan laporan dana desa sebagai bentuk
                            transparansi kepada masyarakat.
                        </p>
                    </div>
                    <!-- Grid 3 -->
                    <div
                        v-if="layout.demografi"
                        class="bg-secondary text-accent1 p-4 rounded-lg shadow-lg xl:w-60 sm:w-64 w-72 xl:h-40 sm:h-40 h-32 text-center"
                    >
                        <svg
                            class="xl:mt-1 sm:mt-1 -mt-1"
                            xmlns="http://www.w3.org/2000/svg"
                            height="40px"
                            viewBox="0 -960 960 960"
                            width="48px"
                            fill="#244031"
                        >
                            <path
                                d="M40-160v-160q0-29 20.5-49.5T110-390h141q17 0 32.5 8.5T310-358q29 42 74 65t96 23q51 0 96-23t75-65q11-15 26-23.5t32-8.5h141q29 0 49.5 20.5T920-320v160H660v-119q-36 33-82.5 51T480-210q-51 0-97-18t-83-51v119H40Zm440-170q-35 0-67.5-16.5T360-392q-16-23-38.5-37T273-448q29-30 91-46t116-16q54 0 116.5 16t91.5 46q-26 5-48.5 19T601-392q-20 29-52.5 45.5T480-330ZM160-460q-45 0-77.5-32.5T50-570q0-46 32.5-78t77.5-32q46 0 78 32t32 78q0 45-32 77.5T160-460Zm640 0q-45 0-77.5-32.5T690-570q0-46 32.5-78t77.5-32q46 0 78 32t32 78q0 45-32 77.5T800-460ZM480-580q-45 0-77.5-32.5T370-690q0-46 32.5-78t77.5-32q46 0 78 32t32 78q0 45-32 77.5T480-580Z"
                            />
                        </svg>
                        <p class="xl:text-lg sm:text-lg text-md text-left ml-1">
                            Data Penduduk
                        </p>
                        <p class="text-xs text-left ml-1">
                            Statistik jumlah penduduk berdasarkan kategori
                            tertentu.
                        </p>
                    </div>
                    <!-- Grid 4 -->
                    <div
                        class="bg-secondary text-accent1 text-center rounded-lg shadow-lg p-4 xl:w-8/12 sm:w-64 w-72 xl:h-40 sm:h-40 h-32 col-span-1"
                    >
                        <svg
                            class="-ml-1 xl:mt-1 sm:mt-1 -mt-1"
                            xmlns="http://www.w3.org/2000/svg"
                            height="40px"
                            viewBox="0 -960 960 960"
                            width="48px"
                            fill="#244031"
                        >
                            <path
                                d="M480-242q-67 0-129 23.5T235-149v9h490v-9q-54-46-116-69.5T480-242Zm0-60q74 0 139.5 24.5T740-211v-609H220v609q55-42 120.5-66.5T480-302Zm2-139q-32.5 0-55.25-22.75T404-519q0-32.5 22.75-55.25T482-597q32.5 0 55.25 22.75T560-519q0 32.5-22.75 55.25T482-441ZM220-80q-24 0-42-18t-18-42v-680q0-24 18-42t42-18h520q24 0 42 18t18 42v680q0 24-18 42t-42 18H220Zm262-301q58 0 98-40t40-98q0-58-40-98t-98-40q-58 0-98 40t-40 98q0 58 40 98t98 40Zm-2-138Z"
                            />
                        </svg>
                        <p
                            class="xl:text-lg sm:text-lg text-md text-left ml-1 mt-1"
                        >
                            Struktur Organisasi
                        </p>
                        <p class="text-xs text-left ml-1 max-w-xs">
                            Susunan kepengurusan dan perangkat desa yang
                            bertugas melayani masyarakat.
                        </p>
                    </div>
                </div>
            </article>
        </main>
    </UserLayout>
</template>
