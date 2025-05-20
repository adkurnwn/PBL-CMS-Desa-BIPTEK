<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import HeadingMenu from "@/Components/DashboardComponent/HeadingMenu.vue";
import { Head, useForm, Link, router } from "@inertiajs/vue3";
import { ref, computed, watch } from "vue";
import PrimaryButton from "@/Components/DashboardComponent/PrimaryButton.vue";
import Search from "@/Components/DashboardComponent/Search.vue";
import TabDevider from "@/Components/DashboardComponent/TabDevider.vue";
import Tabel from "@/Components/DashboardComponent/Tabel.vue";
import TopButton from "@/Components/DashboardComponent/TopButton.vue";
import Labels from "@/Components/DashboardComponent/Labels.vue";
import SwitchToggle from "@/Components/DashboardComponent/SwitchToggle.vue";

const props = defineProps({
    users: Object, // Change from Array to Object to match the paginated response format
    flash: Object,
    search: {
        type: String,
        default: ''
    }
});

// Extract pagination info from the users object
const pagination = computed(() => {
    if (!props.users) return null;

    return {
        currentPage: props.users.current_page,
        lastPage: props.users.last_page,
        perPage: props.users.per_page,
        total: props.users.total,
    };
});

// Extract the user data from the paginated response
const userItems = computed(() => {
    const users = props.users?.data || [];
    return users.map(user => ({
        ...user,
        avatarUrl: getAvatarUrl(user)
    }));
});

const form = useForm({});
// Update searchQuery to use the prop
const searchQuery = ref(props.search || '');

const toggleUserStatus = (userId) => {
    form.delete(route("dashboard.users.destroy", userId), {
        preserveScroll: true,
    });
};

const tableHeaders = [
    {
        label: 'Pengguna',
        field: 'name',
        sortable: true,
        nowrap: true,
        width: '320px',
        style: {
            fontSize: '18px',
            fontStyle: 'normal',
            fontWeight: '400',
            lineHeight: 'normal'
        },
        avatar: {
            field: 'avatarUrl',
            size: 24,
            gap: 12,
            fallback: 'initials'
        }
    },
    {
        label: 'Kontak',
        field: 'email',
        sortable: false,
        width: '400px'
    },
    {
        label: 'Position',
        field: 'position.name',
        sortable: true,
        width: '300px'
    },
    {
        label: 'Status',
        field: 'status',
        sortable: true,
        width: '264px'
    },
    {
        label: 'Actions',
        field: 'actions',
        sortable: false,
        width: '140px'
    },
];

// Implement search functionality
const handleSearch = (query) => {
    router.get(route('dashboard.users.index'), {
        search: query
    }, {
        preserveScroll: true,
        preserveState: true,
        only: ['users']
    });
};

const getAvatarUrl = (user) => {
    if (!user || !user.photo_profile) return null;
    return `/storage/${user.photo_profile}`;
};

// Watch for external search changes
watch(() => props.search, (newValue) => {
    if (newValue !== searchQuery.value) {
        searchQuery.value = newValue;
    }
}, { immediate: true });
</script>

<template>

    <Head title="Manajemen Pengguna" />
    <AuthenticatedLayout>
        <template #header>
            <HeadingMenu title="Manajemen Pengguna" :hasRightContent="false">
                <template #icon>
                    <svg xmlns="http://www.w3.org/2000/svg" width="25" height="26" viewBox="0 0 25 26" fill="none">
                        <path
                            d="M12.4998 13.0003C10.804 13.0003 9.35227 12.3965 8.14463 11.1889C6.93699 9.98123 6.33317 8.52949 6.33317 6.83366C6.33317 5.13783 6.93699 3.68609 8.14463 2.47845C9.35227 1.27081 10.804 0.666992 12.4998 0.666992C14.1957 0.666992 15.6474 1.27081 16.855 2.47845C18.0627 3.68609 18.6665 5.13783 18.6665 6.83366C18.6665 8.52949 18.0627 9.98123 16.855 11.1889C15.6474 12.3965 14.1957 13.0003 12.4998 13.0003ZM0.166504 25.3337V21.017C0.166504 20.1434 0.39133 19.3404 0.840983 18.6081C1.29064 17.8758 1.88803 17.317 2.63317 16.9316C4.22623 16.135 5.84498 15.5377 7.48942 15.1394C9.13386 14.7411 10.804 14.542 12.4998 14.542C14.1957 14.542 15.8658 14.7411 17.5103 15.1394C19.1547 15.5377 20.7734 16.135 22.3665 16.9316C23.1116 17.317 23.709 17.8758 24.1587 18.6081C24.6083 19.3404 24.8332 20.1434 24.8332 21.017V25.3337H0.166504Z"
                            fill="currentColor" />
                    </svg>
                </template>
            </HeadingMenu>
        </template>

        <template #search-area>
            <Search v-model="searchQuery" placeholder="Cari pengguna..." @search="handleSearch" />
            <Link :href="route('dashboard.users.create')">
            <TopButton :icon="true">
                <template #icon>
                    <svg xmlns="http://www.w3.org/2000/svg" width="21" height="20" viewBox="0 0 21 20" fill="none">
                        <mask id="mask0_1642_3445" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0"
                            width="21" height="20">
                            <rect x="0.5" width="20" height="20" fill="#D9D9D9" />
                        </mask>
                        <g mask="url(#mask0_1642_3445)">
                            <path
                                d="M9.6665 10.8337H4.6665V9.16699H9.6665V4.16699H11.3332V9.16699H16.3332V10.8337H11.3332V15.8337H9.6665V10.8337Z"
                                fill="white" />
                        </g>
                    </svg>
                </template>
                Create
            </TopButton>
            </Link>
        </template>

        <template #tab-devider>
            <TabDevider :tabs="[
                { name: 'Pengguna', href: route('dashboard.users.index'), active: true },
                { name: 'Posisi', href: route('dashboard.positions.index'), active: false },
            ]" />
        </template>

        <div class="dashboard-container">
            <div class="h-full">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg h-full">
                    <Tabel :headers="tableHeaders" :items="userItems" :pagination="true"
                        :items-per-page="pagination?.perPage || 25" :current-page="pagination?.currentPage || 1"
                        :total-pages="pagination?.lastPage || 1" :total-items="pagination?.total || 0"
                        empty-state-message="No users found">
                        <!-- Add a template for the Kontak column -->
                        <template #['item.Kontak']="{ item }">
                            <div class="flex flex-col">
                                <span class="text-[18px] italic text-TableText">{{ item.phone || '-' }}</span>
                                <span class="italic text-[18px] text-emailText">{{ item.email }}</span>
                            </div>
                        </template>

                        <!-- Fix the Position column template -->
                        <template #['item.Position']="{ item }">
                            <Labels
                                v-if="item.position"
                                :type="item.position.name.toLowerCase().includes('kepala') ? 'kepala desa' : 
                                       item.position.name.toLowerCase().includes('sekretaris') ? 'sekretaris desa' : 
                                       item.position.name.toLowerCase().includes('bendahara') ? 'bendahara desa' : 'other'"
                                :text="item.position.name"
                            />
                            <span v-else>-</span>
                        </template>

                        <!-- Fix the slot syntax for Status column -->
                        <template #['item.Status']="{ item }">
                            <div @click.stop="toggleUserStatus(item.id)" class="cursor-pointer">
                                <SwitchToggle 
                                    :type="item.status"
                                    :text="item.status ? 'Aktif' : 'Nonaktif'"
                                />
                            </div>
                        </template>

                        <!-- Fix the slot syntax for Actions column -->
                        <template #['item.Actions']="{ item }">
                            <div class="flex gap-x-2 justify-center">
                                <Link :href="route('dashboard.users.edit', item.id)">
                                <button class="w-[21px] h-[21px] flex items-center justify-center hover:opacity-80 transition-opacity">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="21" height="22" viewBox="0 0 21 22" fill="none">
                                        <mask id="mask0_1659_4389" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="21" height="22">
                                            <rect y="0.5" width="21" height="21" fill="#D9D9D9"/>
                                        </mask>
                                        <g mask="url(#mask0_1659_4389)">
                                            <path d="M1.75 21.5V18H19.25V21.5H1.75ZM5.25 14.5H6.475L13.3 7.69688L12.0531 6.45L5.25 13.275V14.5ZM3.5 16.25V12.5312L13.3 2.75313C13.4604 2.59271 13.6464 2.46875 13.8578 2.38125C14.0693 2.29375 14.2917 2.25 14.525 2.25C14.7583 2.25 14.9844 2.29375 15.2031 2.38125C15.4219 2.46875 15.6188 2.6 15.7938 2.775L16.9969 4C17.1719 4.16042 17.2995 4.35 17.3797 4.56875C17.4599 4.7875 17.5 5.01354 17.5 5.24688C17.5 5.46563 17.4599 5.68073 17.3797 5.89219C17.2995 6.10365 17.1719 6.29688 16.9969 6.47188L7.21875 16.25H3.5Z" fill="#FFC107"/>
                                        </g>
                                    </svg>
                                </button>
                                </Link>
                            </div>
                        </template>
                    </Tabel>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* Dashboard specific styles to manage scrolling */
.dashboard-container {
    position: relative;
    height: calc(100vh - 348px);
    min-height: 650px;
    overflow: hidden;
}
</style>
