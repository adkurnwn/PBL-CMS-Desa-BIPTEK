<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, router, Link } from "@inertiajs/vue3";
import { ref, computed, watch } from "vue";
import HeadingMenu from "@/Components/DashboardComponent/HeadingMenu.vue";
import Tabel from "@/Components/DashboardComponent/Tabel.vue";
import Labels from "@/Components/DashboardComponent/Labels.vue";
import Comments from "@/Components/DashboardComponent/Comments.vue";
import IconAksi from "@/Components/DashboardComponent/IconAksi.vue";
import PrimaryButton from "@/Components/DashboardComponent/PrimaryButton.vue";

const props = defineProps({
    produkHukum: Object,
    comments: Object,
    flash: Object,
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
        default: 'created_at'
    },
    direction: {
        type: String,
        default: 'desc'
    },
    pendingCommentsCount: {
        type: Number,
        default: 0
    }
});

// Extract pagination info from the comments object
const pagination = computed(() => {
    if (!props.comments) return null;

    return {
        currentPage: props.comments.current_page,
        lastPage: props.comments.last_page,
        perPage: props.comments.per_page,
        total: props.comments.total,
    };
});

// Extract the comment data from the paginated response
const commentItems = computed(() => {
    return props.comments?.data || [];
});

const form = useForm({});

// Update searchQuery to use the prop
const searchQuery = ref(props.search || '');

// Add filter state - exclude is_approved from filters since it's handled by tabs
const filterState = ref((() => {
    const filters = { ...props.filters };
    delete filters.is_approved;
    return filters;
})());

// Add activeTab state - fix initialization
const activeTab = ref((() => {
    if (props.filters?.is_approved === 'pending') return 'pending';
    if (props.filters?.is_approved === 'approved') return 'approved';
    return 'all';
})());

// Add sort state
const sortField = ref(props.sort || 'created_at');
const sortDirection = ref(props.direction || 'desc');

// Table headers for comment data
const tableHeaders = [
    {
        label: 'Komentar Pengguna',
        field: 'komentar',
        sortable: true,
        nowrap: false,
        width: '50%',
        multiline: true
    },
    {
        label: 'Waktu',
        field: 'created_at',
        sortable: true,
        width: '20%'
    },
    {
        label: 'Status',
        field: 'is_approved',
        sortable: false,
        width: '15%'
    },
    {
        label: 'Aksi',
        field: 'actions',
        sortable: false,
        width: '15%'
    }
];

const approveComment = (commentId) => {
    if (confirm("Are you sure you want to approve this comment?")) {
        form.patch(route("dashboard.produk-hukum.index-comments.approve", commentId), {
            preserveScroll: true,
        });
    }
};

const deleteComment = (commentId) => {
    if (confirm("Are you sure you want to delete this comment?")) {
        form.delete(route("dashboard.produk-hukum.index-comments.destroy", commentId), {
            preserveScroll: true,
        });
    }
};

const isPreviewable = (filePath) => {
    const previewableExtensions = [".pdf", ".jpg", ".jpeg", ".png"];
    const fileExtension = filePath.split(".").pop().toLowerCase();
    return previewableExtensions.includes(`.${fileExtension}`);
};

// Add pagination handling function
const handlePageChanged = (page) => {
    const params = {
        ...filterState.value,
        sort: sortField.value,
        direction: sortDirection.value,
        page: page
    };

    if (searchQuery.value) {
        params.search = searchQuery.value;
    }

    // Add current tab's is_approved filter if not 'all'
    if (activeTab.value === 'pending') {
        params.is_approved = 'pending';
    } else if (activeTab.value === 'approved') {
        params.is_approved = 'approved';
    }

    router.get(route('dashboard.produk-hukum.index-comments.edit', props.produkHukum.id), params, {
        preserveState: true,
        preserveScroll: true,
    });
};

const formatDate = (date) => {
    return new Date(date).toLocaleString("id-ID", {
        year: "numeric",
        month: "long",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
};

// Get document file if available
const getDocumentFile = () => {
    return props.produkHukum.file_path || null;
};
</script>

<template>

    <Head :title="`Moderasi Komentar Produk Hukum`" />

    <AuthenticatedLayout>
        <template #header>
            <HeadingMenu :title="`Moderasi Komentar Produk Hukum`" :hasRightContent="false">
                <template #icon>
                    <svg xmlns="http://www.w3.org/2000/svg" width="37" height="38" viewBox="0 0 37 38" fill="none">
                        <mask id="mask0_1448_7519" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0"
                            width="37" height="38">
                            <rect y="0.5" width="37" height="37" fill="#D9D9D9" />
                        </mask>
                        <g mask="url(#mask0_1448_7519)">
                            <path
                                d="M27.75 32.8753L25.5917 30.717L28.0198 28.2503H21.5833V25.167H28.0198L25.5917 22.7003L27.75 20.542L33.9167 26.7087L27.75 32.8753ZM4.625 32.8753V9.75033C4.625 8.90241 4.92691 8.17654 5.53073 7.57272C6.13455 6.9689 6.86042 6.66699 7.70833 6.66699H26.2083C27.0562 6.66699 27.7821 6.9689 28.3859 7.57272C28.9898 8.17654 29.2917 8.90241 29.2917 9.75033V17.5743C29.0347 17.5229 28.7778 17.4908 28.5208 17.4779C28.2639 17.4651 28.0069 17.4587 27.75 17.4587C25.1549 17.4587 22.9644 18.358 21.1786 20.1566C19.3929 21.9552 18.5 24.1392 18.5 26.7087C18.5 26.9656 18.5064 27.2225 18.5193 27.4795C18.5321 27.7364 18.5642 27.9934 18.6156 28.2503H9.25L4.625 32.8753Z"
                                fill="#4C7660" />
                        </g>
                    </svg>
                </template>
            </HeadingMenu>
        </template>

        <template #search-area>
            <Link :href="route('dashboard.produk-hukum.index-documents')">
            <PrimaryButton>
                SELESAI
            </PrimaryButton>
            </Link>
        </template>

        <template #edit-comment>
            <!-- Trigger the layout to use edit-comment mode -->
        </template>

        <template #news-content-comment>
            <div class="px-2">
                <div class="bg-menuBG rounded-lg shadow-sm border p-6">
                    <div class="space-y-6">
                        <div class="flex col-span-3 gap-4 text-selectedText text-[8px] sm:text-[9px] md:text-[10px] lg:text-[11px] xl:text-[12px] 2xl:text-[13px] 3xl:text-[15px] 4xl:text-[17px]">
                            <div class="flex-shrink-0">
                                <div>Kategori</div>
                                <div>Judul</div>
                                <div>Tanggal</div>
                                <div>Deskripsi</div>
                                <div>Link</div>
                            </div>
                            <div class="flex-shrink-0">
                                <div>:</div>
                                <div>:</div>
                                <div>:</div>
                                <div>:</div>
                                <div>:</div>
                            </div>
                            <div class="flex-1">
                                <div>{{ produkHukum.category?.name || 'Tidak berkategori' }}</div>
                                <div>{{ produkHukum.title }}</div>
                                <div>{{ produkHukum.date }}</div>
                                <div>{{ produkHukum.description }}</div>
                                <div>{{ produkHukum.link || 'Tidak tersedia' }}</div>
                            </div>
                        </div>

                        <div v-if="produkHukum.file && isPreviewable(produkHukum.file)" class="">
                            <iframe :src="`/storage/${produkHukum.file}`" 
                                class="w-full h-screen max-h-screen"></iframe>
                        </div>

                        <div class="pb-8"></div>
                    </div>
                </div>
            </div>
        </template>

        <template #table-content-comment>
            <div class="h-full">
                <div class="overflow-hidden bg-white shadow-sm h-full">
                    <Tabel :headers="tableHeaders" :items="commentItems" :pagination="true"
                        :items-per-page="pagination?.perPage || 25" :current-page="pagination?.currentPage || 1"
                        :total-pages="pagination?.lastPage || 1" :total-items="pagination?.total || 0"
                        :filters="filterState" :sort-field="sortField" :sort-direction="sortDirection"
                        :white-background="true" empty-state-message="No comments found"
                        @page-changed="handlePageChanged">

                        <template #['item.Komentar_Pengguna']="{ item }">
                            <Comments :user-name="item.nama_user" :email-user="item.email_user" :comment="item.komentar"
                                :parent-comment="item.parent?.komentar || null"
                                :parent-comment-username="item.parent?.nama_user || null" :news-title="null" />
                        </template>

                        <template #['item.Waktu']="{ item }">
                            <div class="flex flex-col">
                                <span
                                    class="text-[#404DBD] text-[8px] sm:text-[9px] md:text-[10px] lg:text-[11px] xl:text-[12px] 2xl:text-[14px] 3xl:text-[16px] 4xl:text-[18px] font-normal">
                                    {{ formatDate(item.created_at) }}
                                </span>
                            </div>
                        </template>

                        <template #['item.Status']="{ item }">
                            <Labels :type="item.is_approved ? 'ditanggapi' : 'menunggu'"
                                :text="item.is_approved ? 'Disetujui' : 'Menunggu'" />
                        </template>

                        <template #['item.Aksi']="{ item }">
                            <div class="flex gap-2">
                                <button @click="deleteComment(item.id)" class="hover:opacity-75 transition-opacity">
                                    <IconAksi type="delete" />
                                </button>
                                <button v-if="!item.is_approved" @click="approveComment(item.id)"
                                    class="hover:opacity-75 transition-opacity">
                                    <IconAksi type="accept" />
                                </button>
                            </div>
                        </template>
                    </Tabel>
                </div>
            </div>
        </template>
    </AuthenticatedLayout>
</template>
