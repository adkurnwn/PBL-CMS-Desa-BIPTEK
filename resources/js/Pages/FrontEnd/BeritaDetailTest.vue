<script setup>
import UserLayout from "@/Layouts/UserLayout.vue";
import { ref, onMounted } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import axios from "axios";

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
const mainCaptchaCode = ref('');
const replyCaptchaCode = ref('');

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

const fetchNews = async () => {
    try {
        loading.value = true;
        const response = await axios.get(`/api/news/${props.slug}`);
        news.value = response.data;
    } catch (err) {
        error.value = "Failed to load news";
        console.error("Error fetching news:", err);
    } finally {
        loading.value = false;
    }
};

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

const refreshCaptcha = async (type = 'main') => {
    try {
        const response = await axios.get('/captcha/api/default');
        if (type === 'main') {
            mainCaptchaData.value = response.data;
            mainCaptchaCode.value = response.data.code;
        } else {
            replyCaptchaData.value = response.data;
            replyCaptchaCode.value = response.data.code;
        }
    } catch (err) {
        console.error('Error loading captcha:', err);
    }
};

const submitComment = async () => {
    try {
        submitting.value = true;
        const response = await axios.post("/api/comments", {
            ...commentForm.value,
            captcha: commentForm.value.captcha,
            code: mainCaptchaCode.value
        });

        if (!news.value.is_moderate) {
            await fetchComments();
        }

        commentForm.value.komentar = '';
        commentForm.value.captcha = '';
        alert(response.data.message);
    } catch (err) {
        console.error("Error submitting comment:", err);
        await refreshCaptcha('main'); // Refresh on error
        commentForm.value.captcha = ''; // Clear invalid captcha
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
    refreshCaptcha('reply'); // Get new captcha for reply form
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
            code: replyCaptchaCode.value
        });

        if (!news.value.is_moderate) {
            await fetchComments();
        }

        cancelReply();
        alert(response.data.message);
    } catch (err) {
        console.error("Error submitting reply:", err);
        await refreshCaptcha('reply'); // Refresh on error
        replyForm.value.captcha = ''; // Clear invalid captcha
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
    fetchNews();
    fetchComments();
    refreshCaptcha('main');
});
</script>
<template>
    <UserLayout>
        <div class="min-h-screen bg-gray-100">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
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

                <!-- News Detail -->
                <div
                    v-else
                    class="bg-white rounded-lg shadow-lg overflow-hidden"
                >
                    <!-- Image Gallery -->
                    <div v-if="news.images?.length > 0" class="relative h-96">
                        <img
                            :src="`/storage/${news.images[0].path}`"
                            :alt="news.images[0].alt"
                            class="w-full h-full object-cover"
                        />
                    </div>

                    <div class="p-8">
                        <!-- Category Badge -->
                        <span
                            v-if="news.category"
                            class="inline-block px-3 py-1 text-sm font-semibold text-blue-600 bg-blue-100 rounded-full mb-4"
                        >
                            {{ news.category.name }}
                        </span>

                        <!-- Title -->
                        <h1 class="text-3xl font-bold text-gray-900 mb-4">
                            {{ news.title }}
                        </h1>

                        <!-- Metadata -->
                        <div
                            class="flex items-center text-sm text-gray-500 mb-8"
                        >
                            <span>{{ formatDate(news.created_at) }}</span>
                            <span class="mx-2">•</span>
                            <span>{{ news.user?.name || "Admin" }}</span>
                            <span class="mx-2">•</span>
                            <span>{{ news.views }} views</span>
                        </div>

                        <!-- Content -->
                        <div class="prose max-w-none">
                            {{ news.content }}
                        </div>

                        <!-- Back Button -->
                        <div class="mt-8">
                            <Link
                                href="/berita-test"
                                class="text-blue-600 hover:text-blue-800 font-medium"
                            >
                                ← Back to News List
                            </Link>
                        </div>

                        <!-- Comments Section -->
                        <div class="mt-12">
                            <h2 class="text-2xl font-bold mb-6">Komentar</h2>

                            <!-- Comment Form -->
                            <form @submit.prevent="submitComment" class="mb-8">
                                <div class="mb-4">
                                    <input
                                        v-model="commentForm.nama_user"
                                        type="text"
                                        placeholder="Masukkan Nama..."
                                        class="w-full p-2 border rounded"
                                        required
                                    />
                                </div>
                                <div class="mb-4">
                                    <textarea
                                        v-model="commentForm.komentar"
                                        placeholder="Masukkan Komentar..."
                                        rows="4"
                                        class="w-full p-2 border rounded"
                                        required
                                    ></textarea>
                                </div>
                                <div class="mb-4">
                                    <div class="flex items-center gap-4 mb-2">
                                        <img 
                                            :src="mainCaptchaData?.image" 
                                            alt="CAPTCHA" 
                                            class="h-10"
                                        />
                                        <button 
                                            type="button" 
                                            @click="() => refreshCaptcha('main')"
                                            class="text-blue-600 hover:text-blue-800"
                                        >
                                            Refresh Captcha
                                        </button>
                                    </div>
                                    <input
                                        v-model="commentForm.captcha"
                                        type="text"
                                        placeholder="Enter captcha..."
                                        class="w-full p-2 border rounded"
                                        required
                                    />
                                </div>
                                <button
                                    type="submit"
                                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
                                    :disabled="submitting"
                                >
                                    {{
                                        submitting
                                            ? "Mengirim..."
                                            : "Kirim Komentar"
                                    }}
                                </button>
                            </form>

                            <!-- Comments List -->
                            <div class="space-y-6">
                                <template
                                    v-for="comment in comments"
                                    :key="comment.id"
                                >
                                    <div class="bg-gray-50 p-4 rounded">
                                        <!-- Main Comment -->
                                        <div class="flex gap-3">
                                            <img
                                                :src="
                                                    getAvatar(comment.nama_user)
                                                "
                                                alt="User Avatar"
                                                class="w-8 h-8 rounded-full"
                                            />
                                            <div class="flex-1">
                                                <div class="font-semibold">
                                                    {{ comment.nama_user }}
                                                </div>
                                                <div class="text-gray-600 mt-1">
                                                    {{ comment.komentar }}
                                                </div>
                                                <div
                                                    class="flex items-center justify-between mt-2"
                                                >
                                                    <div
                                                        class="flex items-center gap-4"
                                                    >
                                                        <div
                                                            class="text-sm text-gray-500"
                                                        >
                                                            {{
                                                                formatDate(
                                                                    comment.created_at
                                                                )
                                                            }}
                                                        </div>
                                                        <!-- Add Show Replies button if has replies -->
                                                        <button
                                                            v-if="
                                                                comment.replies_count >
                                                                0
                                                            "
                                                            @click="
                                                                loadReplies(
                                                                    comment
                                                                )
                                                            "
                                                            class="text-sm text-gray-600 hover:text-gray-800"
                                                        >
                                                            {{
                                                                comment.showReplies
                                                                    ? "Sembunyikan"
                                                                    : "Tampilkan"
                                                            }}
                                                            {{
                                                                comment.replies_count
                                                            }}
                                                            {{ "balasan" }}
                                                        </button>
                                                    </div>
                                                    <button
                                                        @click="
                                                            showReplyForm(
                                                                comment.id
                                                            )
                                                        "
                                                        class="text-blue-600 text-sm hover:text-blue-800"
                                                    >
                                                        Balas
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Reply Form -->
                                        <div
                                            v-if="activeReplyId === comment.id"
                                            class="mt-4 ml-8"
                                        >
                                            <form
                                                @submit.prevent="
                                                    submitReply(comment.id)
                                                "
                                                class="space-y-4"
                                            >
                                                <div>
                                                    <input
                                                        v-model="
                                                            replyForm.nama_user
                                                        "
                                                        type="text"
                                                        placeholder="Masukkan Nama..."
                                                        class="w-full p-2 border rounded"
                                                        required
                                                    />
                                                </div>
                                                <div>
                                                    <textarea
                                                        v-model="
                                                            replyForm.komentar
                                                        "
                                                        placeholder="Masukkan Balasan..."
                                                        rows="3"
                                                        class="w-full p-2 border rounded"
                                                        required
                                                    ></textarea>
                                                </div>
                                                <div class="mb-4">
                                                    <div class="flex items-center gap-4 mb-2">
                                                        <img 
                                                            :src="replyCaptchaData?.image" 
                                                            alt="CAPTCHA" 
                                                            class="h-10"
                                                        />
                                                        <button 
                                                            type="button" 
                                                            @click="() => refreshCaptcha('reply')"
                                                            class="text-blue-600 hover:text-blue-800"
                                                        >
                                                            Refresh Captcha
                                                        </button>
                                                    </div>
                                                    <input
                                                        v-model="
                                                            replyForm.captcha
                                                        "
                                                        type="text"
                                                        placeholder="Enter captcha..."
                                                        class="w-full p-2 border rounded"
                                                        required
                                                    />
                                                </div>
                                                <div class="flex gap-2">
                                                    <button
                                                        type="submit"
                                                        class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700"
                                                        :disabled="submitting"
                                                    >
                                                        {{
                                                            submitting
                                                                ? "Membalas..."
                                                                : "Kirim Balasan"
                                                        }}
                                                    </button>
                                                    <button
                                                        type="button"
                                                        @click="cancelReply"
                                                        class="text-gray-600 px-4 py-2 rounded text-sm hover:bg-gray-100"
                                                    >
                                                        Batal
                                                    </button>
                                                </div>
                                            </form>
                                        </div>

                                        <!-- Replies Section -->
                                        <div
                                            v-if="
                                                comment.showReplies &&
                                                comment.replies?.length
                                            "
                                            class="mt-4 space-y-4"
                                        >
                                            <template
                                                v-for="reply in comment.replies"
                                                :key="reply.id"
                                            >
                                                <div
                                                    class="ml-8 bg-white p-3 rounded border border-gray-100"
                                                >
                                                    <!-- Reply Content -->
                                                    <div class="flex gap-3">
                                                        <img
                                                            :src="
                                                                getAvatar(
                                                                    reply.nama_user
                                                                )
                                                            "
                                                            alt="User Avatar"
                                                            class="w-8 h-8 rounded-full"
                                                        />
                                                        <div class="flex-1">
                                                            <div
                                                                class="flex items-center gap-1"
                                                            >
                                                                <div
                                                                    class="font-semibold"
                                                                >
                                                                    {{
                                                                        reply.nama_user
                                                                    }}
                                                                </div>
                                                                <div
                                                                    class="font-light italic text-gray-400"
                                                                >
                                                                    membalas
                                                                </div>
                                                                <div
                                                                    class="font-semibold"
                                                                >
                                                                    {{
                                                                        reply.parent_user
                                                                    }}
                                                                </div>
                                                            </div>
                                                            <div
                                                                class="text-gray-600 mt-2"
                                                            >
                                                                {{
                                                                    reply.komentar
                                                                }}
                                                            </div>
                                                            <div
                                                                class="flex items-center justify-between mt-2"
                                                            >
                                                                <div
                                                                    class="text-sm text-gray-500"
                                                                >
                                                                    {{
                                                                        formatDate(
                                                                            reply.created_at
                                                                        )
                                                                    }}
                                                                </div>
                                                                <button
                                                                    @click="
                                                                        showReplyForm(
                                                                            reply.id
                                                                        )
                                                                    "
                                                                    class="text-blue-600 text-sm hover:text-blue-800"
                                                                >
                                                                    Balas
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Reply Form for Level 2 -->
                                                    <div
                                                        v-if="
                                                            activeReplyId ===
                                                            reply.id
                                                        "
                                                        class="mt-4 ml-8"
                                                    >
                                                        <form
                                                            @submit.prevent="
                                                                submitReply(
                                                                    reply.id
                                                                )
                                                            "
                                                            class="space-y-4"
                                                        >
                                                            <div>
                                                                <input
                                                                    v-model="
                                                                        replyForm.nama_user
                                                                    "
                                                                    type="text"
                                                                    placeholder="Masukkan Nama..."
                                                                    class="w-full p-2 border rounded"
                                                                    required
                                                                />
                                                            </div>
                                                            <div>
                                                                <textarea
                                                                    v-model="
                                                                        replyForm.komentar
                                                                    "
                                                                    placeholder="Masukkan Balasan..."
                                                                    rows="3"
                                                                    class="w-full p-2 border rounded"
                                                                    required
                                                                ></textarea>
                                                            </div>
                                                            <div class="mb-4">
                                                                <div class="flex items-center gap-4 mb-2">
                                                                    <img 
                                                                        :src="replyCaptchaData?.image" 
                                                                        alt="CAPTCHA" 
                                                                        class="h-10"
                                                                    />
                                                                    <button 
                                                                        type="button" 
                                                                        @click="() => refreshCaptcha('reply')"
                                                                        class="text-blue-600 hover:text-blue-800"
                                                                    >
                                                                        Refresh Captcha
                                                                    </button>
                                                                </div>
                                                                <input
                                                                    v-model="
                                                                        replyForm.captcha
                                                                    "
                                                                    type="text"
                                                                    placeholder="Enter captcha..."
                                                                    class="w-full p-2 border rounded"
                                                                    required
                                                                />
                                                            </div>
                                                            <div
                                                                class="flex gap-2"
                                                            >
                                                                <button
                                                                    type="submit"
                                                                    class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700"
                                                                    :disabled="
                                                                        submitting
                                                                    "
                                                                >
                                                                    {{
                                                                        submitting
                                                                            ? "Mengirim..."
                                                                            : "Kirim"
                                                                    }}
                                                                </button>
                                                                <button
                                                                    type="button"
                                                                    @click="
                                                                        cancelReply
                                                                    "
                                                                    class="text-gray-600 px-4 py-2 rounded text-sm hover:bg-gray-100"
                                                                >
                                                                    Cancel
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>

                                                    <!-- Nested Replies (Level 3+) -->
                                                    <div
                                                        v-if="
                                                            reply.replies
                                                                ?.length
                                                        "
                                                        class="mt-4 space-y-4"
                                                    >
                                                        <div
                                                            v-for="deepReply in getAllDeeperReplies(
                                                                reply
                                                            )"
                                                            :key="deepReply.id"
                                                            class="ml-8 bg-white p-3 rounded border border-gray-100"
                                                        >
                                                            <!-- Reply Content with Referenced Comment -->
                                                            <div
                                                                class="flex gap-3"
                                                            >
                                                                <img
                                                                    :src="
                                                                        getAvatar(
                                                                            deepReply.nama_user
                                                                        )
                                                                    "
                                                                    alt="User Avatar"
                                                                    class="w-8 h-8 rounded-full"
                                                                />
                                                                <div
                                                                    class="flex-1"
                                                                >
                                                                    <div
                                                                        class="flex items-center gap-2"
                                                                    >
                                                                        <div
                                                                            class="flex items-center gap-1"
                                                                        >
                                                                            <div
                                                                                class="font-semibold"
                                                                            >
                                                                                {{
                                                                                    deepReply.nama_user
                                                                                }}
                                                                            </div>
                                                                            <div
                                                                                class="font-light italic text-gray-400"
                                                                            >
                                                                                membalas
                                                                            </div>
                                                                            <div
                                                                                class="font-semibold"
                                                                            >
                                                                                {{
                                                                                    deepReply.parent_user
                                                                                }}
                                                                            </div>
                                                                            <div
                                                                                class="text-gray-400 italic text-sm"
                                                                            >
                                                                                ("{{
                                                                                    truncateText(
                                                                                        deepReply
                                                                                            .parent
                                                                                            ?.komentar,
                                                                                        30
                                                                                    )
                                                                                }}")
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div
                                                                        class="text-gray-600 mt-2"
                                                                    >
                                                                        {{
                                                                            deepReply.komentar
                                                                        }}
                                                                    </div>
                                                                    <div
                                                                        class="flex items-center justify-between mt-2"
                                                                    >
                                                                        <div
                                                                            class="text-sm text-gray-500"
                                                                        >
                                                                            {{
                                                                                formatDate(
                                                                                    deepReply.created_at
                                                                                )
                                                                            }}
                                                                        </div>
                                                                        <button
                                                                            @click="
                                                                                showReplyForm(
                                                                                    deepReply.id
                                                                                )
                                                                            "
                                                                            class="text-blue-600 text-sm hover:text-blue-800"
                                                                        >
                                                                            Balas
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <!-- Reply Form -->
                                                            <div
                                                                v-if="
                                                                    activeReplyId ===
                                                                    deepReply.id
                                                                "
                                                                class="mt-4"
                                                            >
                                                                <form
                                                                    @submit.prevent="
                                                                        submitReply(
                                                                            deepReply.id
                                                                        )
                                                                    "
                                                                    class="space-y-4"
                                                                >
                                                                    <div>
                                                                        <input
                                                                            v-model="
                                                                                replyForm.nama_user
                                                                            "
                                                                            type="text"
                                                                            placeholder="Masukkan Nama..."
                                                                            class="w-full p-2 border rounded"
                                                                            required
                                                                        />
                                                                    </div>
                                                                    <div>
                                                                        <textarea
                                                                            v-model="
                                                                                replyForm.komentar
                                                                            "
                                                                            placeholder="Masukkan Balasan..."
                                                                            rows="3"
                                                                            class="w-full p-2 border rounded"
                                                                            required
                                                                        ></textarea>
                                                                    </div>
                                                                    <div class="mb-4">
                                                                        <div class="flex items-center gap-4 mb-2">
                                                                            <img 
                                                                                :src="replyCaptchaData?.image" 
                                                                                alt="CAPTCHA" 
                                                                                class="h-10"
                                                                            />
                                                                            <button 
                                                                                type="button" 
                                                                                @click="() => refreshCaptcha('reply')"
                                                                                class="text-blue-600 hover:text-blue-800"
                                                                            >
                                                                                Refresh Captcha
                                                                            </button>
                                                                        </div>
                                                                        <input
                                                                            v-model="
                                                                                replyForm.captcha
                                                                            "
                                                                            type="text"
                                                                            placeholder="Enter captcha..."
                                                                            class="w-full p-2 border rounded"
                                                                            required
                                                                        />
                                                                    </div>
                                                                    <div
                                                                        class="flex gap-2"
                                                                    >
                                                                        <button
                                                                            type="submit"
                                                                            class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700"
                                                                            :disabled="
                                                                                submitting
                                                                            "
                                                                        >
                                                                            {{
                                                                                submitting
                                                                                    ? "Mengirim..."
                                                                                    : "Kirim Balasan"
                                                                            }}
                                                                        </button>
                                                                        <button
                                                                            type="button"
                                                                            @click="
                                                                                cancelReply
                                                                            "
                                                                            class="text-gray-600 px-4 py-2 rounded text-sm hover:bg-gray-100"
                                                                        >
                                                                            Cancel
                                                                        </button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </UserLayout>
</template>
