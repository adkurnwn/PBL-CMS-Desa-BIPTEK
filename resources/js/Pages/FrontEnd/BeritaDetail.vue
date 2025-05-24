<script setup>
import UserLayout from "@/Layouts/UserLayout.vue";
import { ref, onMounted, watch } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import axios from "axios";
import Comment from "@/Components/Comment.vue";

const props = defineProps({
    slug: {
        type: String,
        required: true,
    },
});

const news = ref({});
const loading = ref(true);
const error = ref(null);
const comments = ref([]);
const submitting = ref(false);
const commentForm = ref({
    nama_user: "",
    komentar: "",
    id_berita: props.slug,
    captcha: "",
});
const replyForm = ref({
    nama_user: "",
    komentar: "",
    id_berita: props.slug,
    id_ref_komentar: null,
    captcha: "",
});
const activeReplyId = ref(null);
const mainCaptchaData = ref(null);
const replyCaptchaData = ref(null);
const mainCaptchaCode = ref("");
const replyCaptchaCode = ref("");

const formatDate = (date) => {
    return new Date(date).toLocaleDateString("id-ID", {
        year: "numeric",
        month: "long",
        day: "numeric",
    });
};

const truncateText = (text, length) => {
    if (!text) return "";
    return text.length > length ? text.substring(0, length) + "..." : text;
};

const getAvatar = (name) => {
    return `https://ui-avatars.com/api/?name=${encodeURIComponent(
        name
    )}&background=random`;
};

const activeTab = ref("terkini"); // Default to "Terkini"
const relatedNews = ref([]);
const relatedNewsLoading = ref(true);

const fetchNewsDetail = async () => {
    try {
        loading.value = true;
        const response = await axios.get(`/api/news/${props.slug}`);
        news.value = response.data.data;
    } catch (err) {
        error.value = "Failed to load news";
        console.error("Error fetching news:", err);
    } finally {
        loading.value = false;
    }
};

const fetchRelatedNews = async () => {
    try {
        relatedNewsLoading.value = true;
        const response = await axios.get("/api/news");
        let newsData = response.data.data;

        // Sort the news based on the active tab
        if (activeTab.value === "terkini") {
            // Sort by latest (descending by created_at)
            newsData = newsData.sort(
                (a, b) => new Date(b.created_at) - new Date(a.created_at)
            );
        } else if (activeTab.value === "populer") {
            // Sort by most views (descending by views)
            newsData = newsData.sort((a, b) => b.views - a.views);
        }

        relatedNews.value = Array.isArray(newsData) ? newsData.slice(0, 6) : [];
    } catch (err) {
        console.error("Error fetching related news:", err);
    } finally {
        relatedNewsLoading.value = false;
    }
};

watch(activeTab, fetchRelatedNews);
const fetchComments = async () => {
    try {
        const response = await axios.get(`/api/comments/${props.slug}`);
        // Only map top-level comments (those without parent)
        comments.value = response.data
            .filter((comment) => !comment.id_ref_komentar)
            .map((comment) => ({
                ...comment,
                showReplies: false,
                replies: [],
            }));
    } catch (err) {
        console.error("Error fetching comments:", err);
    }
};

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

const submitComment = async () => {
    try {
        submitting.value = true;
        const response = await axios.post("/api/comments", {
            ...commentForm.value,
            captcha: commentForm.value.captcha,
            code: mainCaptchaCode.value,
        });

        if (!news.value.is_moderate) {
            await fetchComments();
        }

        commentForm.value.komentar = "";
        commentForm.value.captcha = "";
        alert(response.data.message);
    } catch (err) {
        console.error("Error submitting comment:", err);
        await refreshCaptcha("main"); // Refresh on error
        commentForm.value.captcha = ""; // Clear invalid captcha
        alert(err.response?.data?.message || "Failed to submit comment");
    } finally {
        submitting.value = false;
    }
};

const showReplyForm = (commentId) => {
    activeReplyId.value = commentId;
    replyForm.value = {
        nama_user: "",
        komentar: "",
        id_berita: props.slug,
        id_ref_komentar: commentId,
        captcha: "",
    };
    refreshCaptcha("reply"); // Get new captcha for reply form
};

const cancelReply = () => {
    activeReplyId.value = null;
    replyForm.value = {
        nama_user: "",
        komentar: "",
        id_berita: props.slug,
        id_ref_komentar: null,
        captcha: "",
    };
};

const submitReply = async (commentId) => {
    try {
        submitting.value = true;
        const response = await axios.post("/api/comments", {
            ...replyForm.value,
            captcha: replyForm.value.captcha,
            code: replyCaptchaCode.value,
        });

        if (!news.value.is_moderate) {
            await fetchComments();
        }

        cancelReply();
        alert(response.data.message);
    } catch (err) {
        console.error("Error submitting reply:", err);
        await refreshCaptcha("reply"); // Refresh on error
        replyForm.value.captcha = ""; // Clear invalid captcha
        alert(err.response?.data?.message || "Failed to submit reply");
    } finally {
        submitting.value = false;
    }
};

const getParentUser = (reply) => {
    return reply.parent_user || "Unknown";
};

const loadReplies = async (comment) => {
    if (comment.showReplies) {
        comment.showReplies = false;
        return;
    }

    try {
        const response = await axios.get(
            `/api/comments/${props.slug}/replies/${comment.id}`
        );

        // Create a map of all replies
        const repliesMap = new Map();
        response.data.forEach((reply) => {
            repliesMap.set(reply.id, { ...reply, replies: [] });
        });

        // Build reply tree
        response.data.forEach((reply) => {
            const replyObj = repliesMap.get(reply.id);
            if (reply.id_ref_komentar === comment.id) {
                // Direct replies to main comment
                if (!comment.replies) comment.replies = [];
                comment.replies.push(replyObj);
            } else {
                // Nested replies
                const parentReply = repliesMap.get(reply.id_ref_komentar);
                if (parentReply) {
                    if (!parentReply.replies) parentReply.replies = [];
                    parentReply.replies.push(replyObj);
                }
            }
        });

        comment.showReplies = true;
    } catch (err) {
        console.error("Error loading replies:", err);
        alert("Failed to load replies");
    }
};

const getAllDeeperReplies = (reply) => {
    let allReplies = [];

    const collectReplies = (r) => {
        if (r.replies?.length) {
            r.replies.forEach((nestedReply) => {
                allReplies.push(nestedReply);
                collectReplies(nestedReply);
            });
        }
    };

    collectReplies(reply);
    return allReplies;
};

onMounted(() => {
    fetchNewsDetail();
    fetchRelatedNews();
    fetchComments();
    refreshCaptcha("main");
});
</script>

<template>
    <UserLayout>
        <div class="min-h-screen bg-type1">
            <div
                class="max-w-screen-2xl w-full px-4 lg:px-20 mx-auto py-0 pt-32 pb-6 lg:pt-36"
            >
                <!-- Loading State -->
                <div
                    v-if="loading"
                    class="flex justify-center items-center py-12"
                >
                    <div
                        class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"
                    ></div>
                </div>

                <!-- Error State -->
                <div v-else-if="error" class="text-center py-12">
                    <p class="text-red-600">{{ error }}</p>
                </div>

                <!-- News Part -->
                <div v-else class="flex flex-col rounded-lg overflow-hidden">
                    <div class="flex space-x-2 items-center mb-6">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            height="24px"
                            viewBox="0 -960 960 960"
                            width="24px"
                            fill="currentColor"
                            class="text-accent2"
                        >
                            <path
                                d="M280-280q17 0 28.5-11.5T320-320q0-17-11.5-28.5T280-360q-17 0-28.5 11.5T240-320q0 17 11.5 28.5T280-280Zm-40-160h80v-240h-80v240Zm200 160h280v-80H440v80Zm0-160h280v-80H440v80Zm0-160h280v-80H440v80ZM160-120q-33 0-56.5-23.5T80-200v-560q0-33 23.5-56.5T160-840h640q33 0 56.5 23.5T880-760v560q0 33-23.5 56.5T800-120H160Zm0-80h640v-560H160v560Zm0 0v-560 560Z"
                            />
                        </svg>
                        <h1
                            class="text-lg font- text-accent2 -mb-0.5 font-merriweather"
                        >
                            Berita {{ news.category_name }}
                        </h1>
                    </div>

                    <div class="flex flex-row">
                        <div class="flex flex-col w-2/3">
                            <!-- News Detail -->
                            <div
                                class="flex flex-col bg-white rounded-lg shadow overflow-hidden mx-auto p-6"
                            >
                                <!-- Title -->
                                <h1
                                    class="flex text-3xl font-bold text-accent1 mb-4 font-merriweather"
                                >
                                    {{ news.title }}
                                </h1>

                                <!-- Metadata -->
                                <div
                                    class="flex items-center text-accent2 text-lg mt-2 lg:mt-4 mb-4 lg:mb-8 space-x-3 lg:space-x-6"
                                >
                                    <!-- TANGGAL -->
                                    <div class="flex items-center space-x-2">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            height="20px"
                                            viewBox="0 -960 960 960"
                                            width="20px"
                                            fill="currentColor"
                                            class="text-accent2 lg:w-6 lg:h-6"
                                        >
                                            <path
                                                d="M200-80q-33 0-56.5-23.5T120-160v-560q0-33 23.5-56.5T200-800h40v-80h80v80h320v-80h80v80h40q33 0 56.5 23.5T840-720v560q0 33-23.5 56.5T760-80H200Zm0-80h560v-400H200v400Zm0-480h560v-80H200v80Zm0 0v-80 80Zm80 240v-80h400v80H280Zm0 160v-80h280v80H280Z"
                                            />
                                        </svg>
                                        <h3 class="flex -mb-1">
                                            {{ formatDate(news.created_at) }}
                                        </h3>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            height="24px"
                                            viewBox="0 -960 960 960"
                                            width="24px"
                                            fill="currentColor"
                                            class="text-accent2"
                                        >
                                            <path
                                                d="M480-480q-66 0-113-47t-47-113q0-66 47-113t113-47q66 0 113 47t47 113q0 66-47 113t-113 47ZM160-160v-112q0-34 17.5-62.5T224-378q62-31 126-46.5T480-440q66 0 130 15.5T736-378q29 15 46.5 43.5T800-272v112H160Zm80-80h480v-32q0-11-5.5-20T700-306q-54-27-109-40.5T480-360q-56 0-111 13.5T260-306q-9 5-14.5 14t-5.5 20v32Zm240-320q33 0 56.5-23.5T560-640q0-33-23.5-56.5T480-720q-33 0-56.5 23.5T400-640q0 33 23.5 56.5T480-560Zm0-80Zm0 400Z"
                                            />
                                        </svg>
                                        <h3 class="flex -mb-1">
                                            {{ news.user?.name || "Admin" }}
                                        </h3>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            height="24px"
                                            viewBox="0 -960 960 960"
                                            width="24px"
                                            fill="currentColor"
                                            class="text-accent2"
                                        >
                                            <path
                                                d="M480-320q75 0 127.5-52.5T660-500q0-75-52.5-127.5T480-680q-75 0-127.5 52.5T300-500q0 75 52.5 127.5T480-320Zm0-72q-45 0-76.5-31.5T372-500q0-45 31.5-76.5T480-608q45 0 76.5 31.5T588-500q0 45-31.5 76.5T480-392Zm0 192q-146 0-266-81.5T40-500q54-137 174-218.5T480-800q146 0 266 81.5T920-500q-54 137-174 218.5T480-200Zm0-300Zm0 220q113 0 207.5-59.5T832-500q-50-101-144.5-160.5T480-720q-113 0-207.5 59.5T128-500q50 101 144.5 160.5T480-280Z"
                                            />
                                        </svg>
                                        <h3
                                            class="flex lg:-mb-1 text-sm lg:text-lg"
                                        >
                                            {{ news.views }} views
                                        </h3>
                                    </div>
                                </div>

                                <!-- Image Gallery -->
                                <div
                                    v-if="news.images?.length > 0"
                                    class="relative min-h-48 lg:min-h-96"
                                >
                                    <img
                                        :src="`/storage/${news.images[0].path}`"
                                        :alt="news.images[0].alt"
                                        class="w-full h-full object-cover"
                                    />
                                </div>
                                <!-- <div class="flex relative h-full w-full p-2">
                                <img src="../../../images/profile.jpg" alt="" class="w-full h-full object-cover rounded-xl">
                            </div> -->
                                <div class="pt-6 lg:pt-8 pb-2">
                                    <!-- Category Badge -->
                                    <!-- <span
                                    v-if="news.category"
                                    class="inline-block px-3 py-1 text-sm font-semibold text-blue-600 bg-blue-100 rounded-full mb-4"
                                >
                                    {{ news.category.name }}
                                </span> -->

                                    <!-- Content -->
                                    <div
                                        class="text-sm lg:text-lg text-semiBlack"
                                    >
                                        {{ news.content }}
                                    </div>

                                    <!-- Back Button -->
                                    <!-- <div class="mt-8">
                                    <Link
                                        href="/berita"
                                        class="text-blue-600 hover:text-blue-800 font-medium"
                                    >
                                        ← Back to News List
                                    </Link>
                                </div> -->
                                </div>
                            </div>

                            <!-- Comments Section -->
                            <div
                                class="flex flex-col mt-4 lg:mt-12 bg-white rounded-lg shadow-lg overflow-hidden font-merriweather"
                            >
                                <Comment
                                    :slug="props.slug"
                                    :news="news"
                                    :show-share="true"
                                    :loading="loading"
                                    and
                                    :error="error"
                                />
                            </div>
                        </div>
                        <!-- Aside -->
                        <div
                            class="flex flex-col w-full lg:w-1/3 px-1 lg:px-4 my-3 lg:my-0 font-merriweather"
                        >
                            <div
                                class="flex bg-accent1 shadow-md rounded-sm p-3 space-x-2"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    height="24px"
                                    viewBox="0 -960 960 960"
                                    width="24px"
                                    fill="currentColor"
                                    class="text-type1"
                                >
                                    <path
                                        d="M160-120q-33 0-56.5-23.5T80-200v-280h80v280h360v80H160Zm160-160q-33 0-56.5-23.5T240-360v-280h80v280h360v80H320Zm160-160q-33 0-56.5-23.5T400-520v-240q0-33 23.5-56.5T480-840h320q33 0 56.5 23.5T880-760v240q0 33-23.5 56.5T800-440H480Zm0-80h320v-160H480v160Z"
                                    />
                                </svg>
                                <h2 class="text-lg font-semibold text-type1">
                                    Berita Lainnya
                                </h2>
                            </div>
                            <!-- Tabs -->
                            <div class="flex flex-row bg-white w-full">
                                <button
                                    class="flex flex-col w-full bg-white hover:opacity-60 shadow-md p-1"
                                    :class="{
                                        'bg-gray-500': activeTab === 'terkini',
                                    }"
                                    @click="activeTab = 'terkini'"
                                >
                                    <h3 class="text-md text-accent1">
                                        Terkini
                                    </h3>
                                </button>
                                <button
                                    class="flex flex-col w-full bg-white hover:opacity-60 shadow-md p-1"
                                    :class="{
                                        'bg-gray-200': activeTab === 'populer',
                                    }"
                                    @click="activeTab = 'populer'"
                                >
                                    <h3 class="text-md text-accent1">
                                        Populer
                                    </h3>
                                </button>
                            </div>

                            <!-- News List -->
                            <div
                                class="flex flex-col w-full bg-white shadow-md px-3"
                            >
                                <div
                                    v-if="relatedNewsLoading"
                                    class="flex justify-center py-8"
                                >
                                    <div
                                        class="animate-spin rounded-full h-12 w-12 border-b-2 border-accent2"
                                    ></div>
                                </div>
                                <div
                                    v-else
                                    class="flex flex-col w-full bg-white p-1"
                                >
                                    <div class="flex flex-col">
                                        <Link
                                            v-for="item in relatedNews"
                                            :key="item.id"
                                            :href="`/berita/${item.slug}`"
                                            class="flex flex-row py-3 gap-2 hover:bg-gray-200 hover:shadow-sm rounded-md transition duration-300 ease-in-out cursor-pointer"
                                        >
                                            <div class="flex w-1/2">
                                                <img
                                                    :src="`/storage/${
                                                        item.thumbnail ||
                                                        'default.jpg'
                                                    }`"
                                                    alt="News Image"
                                                    class="w-full h-20 object-cover rounded-md"
                                                />
                                            </div>

                                            <div class="flex flex-col w-full">
                                                <h3
                                                    class="text-sm font-semibold text-semiBlack line-clamp-3"
                                                >
                                                    {{ item.title }}
                                                </h3>
                                                <p
                                                    class="text-xs text-gray-500"
                                                >
                                                    <span
                                                        v-if="
                                                            activeTab ===
                                                            'terkini'
                                                        "
                                                    >
                                                        {{
                                                            formatDate(
                                                                item.created_at
                                                            )
                                                        }}
                                                    </span>
                                                    <span
                                                        v-else-if="
                                                            activeTab ===
                                                            'populer'
                                                        "
                                                    >
                                                        {{ item.views }} views
                                                    </span>
                                                </p>
                                            </div>
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </UserLayout>
</template>
