<script setup>
import { ref } from 'vue';
import axios from 'axios';

const props = defineProps({
    slug: {
        type: String,
        required: true,
    },
    news: {
        type: Object,
        required: true,
    }
});

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

const fetchComments = async () => {
    try {
        const response = await axios.get(`/api/comments/${props.slug}`);
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

        if (!props.news.is_moderate) {
            await fetchComments();
        }

        commentForm.value.komentar = '';
        commentForm.value.captcha = '';
        alert(response.data.message);
    } catch (err) {
        console.error("Error submitting comment:", err);
        await refreshCaptcha('main');
        commentForm.value.captcha = '';
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
    refreshCaptcha('reply');
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

        if (!props.news.is_moderate) {
            await fetchComments();
        }

        cancelReply();
        alert(response.data.message);
    } catch (err) {
        console.error("Error submitting reply:", err);
        await refreshCaptcha('reply');
        replyForm.value.captcha = '';
        alert(err.response?.data?.message || "Failed to submit reply");
    } finally {
        submitting.value = false;
    }
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

        const repliesMap = new Map();
        response.data.forEach((reply) => {
            repliesMap.set(reply.id, { ...reply, replies: [] });
        });

        response.data.forEach((reply) => {
            const replyObj = repliesMap.get(reply.id);
            if (reply.id_ref_komentar === comment.id) {
                if (!comment.replies) comment.replies = [];
                comment.replies.push(replyObj);
            } else {
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

// Initialize the component
refreshCaptcha('main');
fetchComments();
</script>

<template>
    <div class="flex flex-col mt-12 bg-white rounded-lg shadow-lg overflow-hidden p-8 font-merriweather">
        <!-- Share Form -->
        <div>
            <h2 class="text-lg text-accent2 font-bold">
                Bagikan Artikel Ini:
            </h2>
            <div class="my-4 flex items-center">
                <i class="bi bi-instagram md:text-xl relative mr-4 text-accent2"></i>
                <i class="bi bi-twitter-x md:text-xl relative mr-4 text-accent2"></i>
                <i class="bi bi-facebook md:text-xl relative mr-4 text-accent2"></i>
                <i class="bi bi-whatsapp md:text-xl relative mr-4 text-accent2"></i>
            </div>
        </div>

        <h2 class="text-2xl text-accent1 font-bold mb-6">Komentar</h2>

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
                    placeholder="Masukkan kode captcha..."
                    class="w-full p-2 border rounded"
                    required
                />
            </div>
            <button 
                type="submit"
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
                :disabled="submitting"
            >
                {{ submitting ? 'Mengirim...' : 'Kirim Komentar' }}
            </button>
        </form>

        <!-- Comments List -->
        <div class="space-y-6">
            <template v-for="comment in comments" :key="comment.id">
                <div class="bg-gray-50 p-4 rounded">
                    <!-- Main Comment -->
                    <div class="flex gap-3">
                        <img :src="getAvatar(comment.nama_user)" alt="User Avatar" class="w-8 h-8 rounded-full">
                        <div class="flex-1">
                            <div class="font-semibold">{{ comment.nama_user }}</div>
                            <div class="text-gray-600 mt-1">{{ comment.komentar }}</div>
                            <div class="flex items-center justify-between mt-2">
                                <div class="flex items-center gap-4">
                                    <div class="text-sm text-gray-500">
                                        {{ formatDate(comment.created_at) }}
                                    </div>
                                    <button 
                                        v-if="comment.replies_count > 0"
                                        @click="loadReplies(comment)" 
                                        class="text-sm text-gray-600 hover:text-gray-800"
                                    >
                                        {{ comment.showReplies ? 'Sembunyikan' : 'Tampilkan' }} 
                                        {{ comment.replies_count }} {{ 'balasan'}}
                                    </button>
                                </div>
                                <button 
                                    @click="showReplyForm(comment.id)" 
                                    class="text-blue-600 text-sm hover:text-blue-800"
                                >
                                    Balas
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Reply Form -->
                    <div v-if="activeReplyId === comment.id" class="mt-4 ml-8">
                        <form @submit.prevent="submitReply(comment.id)" class="space-y-4">
                            <div>
                                <input 
                                    v-model="replyForm.nama_user"
                                    type="text"
                                    placeholder="Masukkan Nama..."
                                    class="w-full p-2 border rounded"
                                    required
                                />
                            </div>
                            <div>
                                <textarea
                                    v-model="replyForm.komentar"
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
                                    v-model="replyForm.captcha"
                                    type="text"
                                    placeholder="Masukkan kode captcha..."
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
                                    {{ submitting ? 'Membalas...' : 'Kirim Balasan' }}
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
                    <div v-if="comment.showReplies && comment.replies?.length" class="mt-4 space-y-4">
                        <template v-for="reply in comment.replies" :key="reply.id">
                            <div class="ml-8 bg-white p-3 rounded border border-gray-100">
                                <!-- Reply Content -->
                                <div class="flex gap-3">
                                    <img :src="getAvatar(reply.nama_user)" alt="User Avatar" class="w-8 h-8 rounded-full">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-1">
                                            <div class="font-semibold">{{ reply.nama_user }}</div>
                                            <div class="font-light italic text-gray-400">membalas</div>
                                            <div class="font-semibold">{{ reply.parent_user }}</div>
                                        </div>
                                        <div class="text-gray-600 mt-2">{{ reply.komentar }}</div>
                                        <div class="flex items-center justify-between mt-2">
                                            <div class="text-sm text-gray-500">
                                                {{ formatDate(reply.created_at) }}
                                            </div>
                                            <button 
                                                @click="showReplyForm(reply.id)" 
                                                class="text-blue-600 text-sm hover:text-blue-800"
                                            >
                                                Balas
                                            </button>
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
</template>