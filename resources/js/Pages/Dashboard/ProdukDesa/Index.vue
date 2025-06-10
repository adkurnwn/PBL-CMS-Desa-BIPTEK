<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import HeadingMenu from "@/Components/DashboardComponent/HeadingMenu.vue";
import { Head, Link, useForm, router } from "@inertiajs/vue3";
import PrimaryButton from "@/Components/DashboardComponent/PrimaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";
import Search from "@/Components/DashboardComponent/Search.vue";
import DropdownFilter from "@/Components/DashboardComponent/DropdownFilter.vue";
import TopButton from "@/Components/DashboardComponent/TopButton.vue";
import Tabel from "@/Components/DashboardComponent/Tabel.vue";
import IconAksi from "@/Components/DashboardComponent/IconAksi.vue";
import SwitchToggle from "@/Components/DashboardComponent/SwitchToggle.vue";
import { ref, computed, watch } from "vue";

const props = defineProps({
    produkDesa: Object,
    search: {
        type: String,
        default: ''
    },
    filters: {
        type: Object,
        default: () => ({})
    },
    sort: {
        type: String,
        default: 'product_name'
    },
    direction: {
        type: String,
        default: 'asc'
    },
});

const form = useForm({});

// State variables for search, filter, and sort
const searchQuery = ref(props.search || '');
const filterState = ref(props.filters || {});
const sortField = ref(props.sort || 'product_name');
const sortDirection = ref(props.direction || 'asc');

// Table headers configuration
const tableHeaders = [
    {
        label: 'Produk',
        field: 'image',
        sortable: false,
        width: '15%',
    },
    {
        label: 'Nama Produk',
        field: 'product_name',
        sortable: true,
        width: '20%',
        truncate: true,
    },
    {
        label: 'Nama Toko',
        field: 'nama_penjual',
        sortable: true,
        width: '20%',
        truncate: true,
    },
    {
        label: 'Harga',
        field: 'price',
        sortable: true,
        width: '15%'
    },
    {
        label: 'Status',
        field: 'status',
        sortable: false,
        width: '10%'
    },
    {
        label: 'Aksi',
        field: 'actions',
        sortable: false,
        width: '10%'
    }
];

// Filter fields configuration
const filterFields = computed(() => [
    {
        id: 'status',
        name: 'Status',
        options: [
            { id: 1, name: 'Aktif' }, // Changed from string '1' to number 1
            { id: 0, name: 'Tidak Aktif' } // Changed from string '0' to number 0
        ]
    },
]);

// Format price for display with dots as thousands separators
const formatPrice = (price) => {
    if (!price) return 'Rp 0';
    
    // Convert to number and format with dots for thousands separators
    const formattedPrice = Number(price).toLocaleString('id-ID', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    });
    
    return `Rp ${formattedPrice}`;
};

// For debugging - add to show console error messages
const handleError = (error) => {
  console.error('Error in component:', error);
};

// Simplify Status rendering to debug the issue
const produkItems = computed(() => {
    try {
        const items = props.produkDesa?.data || props.produkDesa || [];
        return items.map(item => {
            // Get up to 3 images from the produk desa for the stack effect
            let produkImages = [];
            if (item.images && item.images.length > 0) {
                // Get up to 3 images for the stack
                produkImages = item.images.slice(0, 3).map(img => ({
                    path: `/storage/${img.path}`,
                    alt: img.alt || item.product_name
                }));
            }

            return {
                ...item,
                formattedPrice: formatPrice(item.price),
                status: Boolean(item.status),
                thumbnailGambar: item.images && item.images.length > 0 ? `/storage/${item.images[0].path}` : null,
                produkImages: produkImages,
                imageCount: item.images ? item.images.length : 0
            };
        });
    } catch (error) {
        console.error('Error processing items:', error);
        return [];
    }
});

// Simplify the toggle function
const toggleProdukStatus = (slug) => {
    try {
        form.delete(route("dashboard.produk-desa.destroy", slug), {
            preserveScroll: true,
        });
    } catch (error) {
        console.error('Error toggling status:', error);
    }
};

// Handle search functionality
const handleSearch = (query) => {
    router.get(route('dashboard.produk-desa.index'), {
        search: query,
        ...filterState.value,
        sort: sortField.value,
        direction: sortDirection.value
    }, {
        preserveScroll: true,
        preserveState: true
    });
};

// Handle filter changes - Fix to properly format and apply filters
const handleFilterChange = (newFilters) => {
    filterState.value = newFilters;
    
    // Format filters for the backend
    const formattedFilters = {};
    Object.entries(newFilters).forEach(([key, value]) => {
        // Ensure we're sending the actual filter value, not the label
        formattedFilters[key] = value;
    });

    router.get(route('dashboard.produk-desa.index'), {
        search: searchQuery.value,
        filters: formattedFilters, // Send the formatted filters
        sort: sortField.value,
        direction: sortDirection.value
    }, {
        preserveScroll: true,
        preserveState: true,
        only: ['produkDesa']
    });
};

// Handle sort functionality
const handleSort = (field) => {
    // If clicking on the same field, toggle direction
    if (field === sortField.value) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
        // New field, default to ascending
        sortField.value = field;
        sortDirection.value = 'asc';
    }

    // Navigate with updated sort params
    router.get(route('dashboard.produk-desa.index'), {
        search: searchQuery.value,
        ...filterState.value,
        sort: sortField.value,
        direction: sortDirection.value
    }, {
        preserveScroll: true,
        preserveState: true
    });
};

// Handle page change
const handlePageChange = (page) => {
    router.get(route('dashboard.produk-desa.index'), {
        search: searchQuery.value,
        ...filterState.value,
        sort: sortField.value,
        direction: sortDirection.value,
        page: page,
    }, {
        preserveScroll: true,
        preserveState: true
    });
};

// Watch for external search changes
watch(() => props.search, (newValue) => {
    if (newValue !== searchQuery.value) {
        searchQuery.value = newValue;
    }
}, { immediate: true });

// Watch for external sort changes
watch(() => [props.sort, props.direction], ([newSort, newDirection]) => {
    if (newSort !== sortField.value) {
        sortField.value = newSort;
    }
    if (newDirection !== sortDirection.value) {
        sortDirection.value = newDirection;
    }
}, { immediate: true });

// Watch for external filter changes
watch(() => props.filters, (newValue) => {
    if (JSON.stringify(newValue) !== JSON.stringify(filterState.value)) {
        filterState.value = { ...newValue };
    }
}, { deep: true, immediate: true });
</script>

<template>
    <Head title="UMKM/Produk Unggulan" />

    <AuthenticatedLayout>
        <template #header>
            <HeadingMenu title="UMKM/Produk Unggulan">
                <template #icon>
                    <svg xmlns="http://www.w3.org/2000/svg" width="35" height="30" viewBox="0 0 35 30" fill="none">
                        <path
                            d="M26.7493 29.9587V25.3337H22.1243V22.2503H26.7493V17.6253H29.8327V22.2503H34.4577V25.3337H29.8327V29.9587H26.7493ZM2.08268 25.3337V16.0837H0.541016V13.0003L2.08268 5.29199H25.2077L26.7493 13.0003V16.0837H25.2077V20.7087H22.1243V16.0837H15.9577V25.3337H2.08268ZM5.16602 22.2503H12.8743V16.0837H5.16602V22.2503ZM2.08268 3.75033V0.666992H25.2077V3.75033H2.08268Z"
                            fill="#4C7660" />
                    </svg>
                </template>
            </HeadingMenu>
        </template>

        <template #search-area>
            <Search v-model="searchQuery" placeholder="Cari produk atau toko..." @search="handleSearch" />
            <DropdownFilter placeholder="Filter..." :filter-fields="filterFields" 
                v-model:filters="filterState"
                @filter-changed="handleFilterChange" 
                width="250px" 
                dropdownTitle="Filter berdasarkan"
                activeFiltersTitle="Filter yang aktif" 
                :close-on-select="false" />
            <!-- Debug button for troubleshooting -->
            <!-- <button @click="logFilters" class="px-3 py-1 bg-gray-200 rounded">Log Filters</button> -->
            <Link :href="route('dashboard.produk-desa.create')">
                <TopButton></TopButton>
            </Link>
        </template>

        <template #table-content>
            <div class="h-full">
                <div class="overflow-hidden bg-white shadow-sm h-full">
                    <!-- Add error handling to the table component -->
                    <Tabel :headers="tableHeaders" :items="produkItems" :pagination="true"
                        :items-per-page="pagination?.perPage || 10" :current-page="pagination?.currentPage || 1"
                        :total-pages="pagination?.lastPage || 1" :total-items="pagination?.total || produkItems.length"
                        :filters="filterState" :sort-field="sortField" :sort-direction="sortDirection"
                        empty-state-message="Belum ada produk yang ditambahkan"
                        @sort="handleSort" @filter-changed="handleFilterChange" @page-changed="handlePageChange"
                        @error="handleError">
                        
                        <!-- Add custom template for Produk images column with stacked effect -->
                        <template #['item.Produk']="{ item }">
                            <div class="flex justify-center items-center h-full">
                                <div
                                    class="relative w-[40px] h-[40px] sm:w-[50px] sm:h-[50px] md:w-[55px] md:h-[55px] lg:w-[60px] lg:h-[60px] xl:w-[60px] xl:h-[60px] 2xl:w-[70px] 2xl:h-[70px] 3xl:w-[80px] 3xl:h-[80px] 4xl:w-[90px] 4xl:h-[90px]">
                                    <!-- Show empty state if no images -->
                                    <div v-if="!item.imageCount || item.imageCount === 0"
                                        class="w-full h-full flex items-center justify-center bg-gray-200 rounded-md">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-1/2 w-1/2 text-gray-400"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>

                                    <!-- Single image display when only one image is available -->
                                    <div v-else-if="item.imageCount === 1"
                                        class="w-full h-full rounded-md overflow-hidden bg-gray-100">
                                        <img v-if="item.thumbnailGambar" :src="item.thumbnailGambar" :alt="item.product_name"
                                            class="w-full h-full object-cover" />
                                    </div>

                                    <!-- Stacked images when multiple images are available -->
                                    <template v-else>
                                        <!-- Display each image in the stack, in reverse order so first image is on top -->
                                        <div v-for="(image, index) in [...item.produkImages].reverse()" :key="index"
                                            :style="{
                                                zIndex: item.produkImages.length - index,
                                                transform: `translateX(${(item.produkImages.length - index - 1) * -8}px) translateY(${(item.produkImages.length - index - 1) * -4}px)`,
                                                opacity: index === 0 ? 1 : 0.85 - (index * 0.2)
                                            }"
                                            class="produk-stack-image absolute rounded-md overflow-hidden shadow-sm bg-white">
                                            <img :src="image.path" :alt="image.alt"
                                                class="w-full h-full object-cover" />
                                        </div>

                                        <!-- Image count badge when more than shown in stack -->
                                        <div v-if="item.imageCount > 3"
                                            class="absolute -bottom-2 -right-2 bg-selectedText text-white text-xs rounded-full w-6 h-6 flex items-center justify-center shadow-sm"
                                            :style="{ zIndex: item.produkImages.length + 1 }">
                                            +{{ item.imageCount - 3 }}
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>
                        
                        <!-- Use underscores instead of spaces in slot names for multi-word headers -->
                        <template #['item.Nama_Toko']="{ item }">
                            <div class="flex flex-col">
                                <span class="text-TableText text-[8px] sm:text-[9px] md:text-[10px] lg:text-[11px] xl:text-[12px] 2xl:text-[14px] 3xl:text-[16px] 4xl:text-[18px]">
                                    {{ item.nama_penjual || '-' }}
                            </span>
                            <span class="italic text-emailText text-[8px] sm:text-[9px] md:text-[10px] lg:text-[11px] xl:text-[12px] 2xl:text-[14px] 3xl:text-[16px] 4xl:text-[18px]">
                                {{ item.kontak_penjual || '-' }}
                            </span>
                        </div>
                        </template>

                        <!-- Customize the Harga column -->
                        <template #['item.Harga']="{ item }">
                            <span>{{ formatPrice(item.price) }}</span>
                        </template>


                        <!-- Customize the actions column -->
                        <template #['item.Aksi']="{ item }">
                            <div
                                class="flex justify-left gap-[7px] sm:gap-[7px] md:gap-[7px] xl:gap-[7px] 2xl:gap-[8px] 3xl:gap-[9px] 4xl:gap-[10px] items-center">
                                <Link :href="route('dashboard.produk-desa.edit', item.slug)">
                                    <button
                                        class="w-[21px] h-[21px] flex items-center justify-center hover:opacity-80 transition-opacity">
                                        <IconAksi type="edit" />
                                    </button>
                                </Link>
                            </div>
                        </template>

                        <template #['item.Status']="{ item }">
                            <SwitchToggle 
                                :type="item.status" 
                                :text="item.status ? 'Aktif' : 'Nonaktif'" 
                                @update:type="(newValue) => toggleProdukStatus(item.slug, newValue)"
                            />
                        </template>
                    </Tabel>
                </div>
            </div>
        </template>
    </AuthenticatedLayout>
</template>

<style scoped>
.produk-stack-image {
    width: 100%;
    height: 100%;
    transition: all 0.2s ease;
    border: 1px solid rgba(229, 231, 235, 0.5);
}

/* Hover effect to spread the stack slightly */
.produk-stack-image:hover {
    transform: translateX(0) translateY(0) !important;
    z-index: 10 !important;
    opacity: 1 !important;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}
</style>
