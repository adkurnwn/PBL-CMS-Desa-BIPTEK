<script setup>
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';
import { setCookie, getCookie } from '@/Helpers/cookieHelper';

const props = defineProps({
    type: {
        type: String,
        required: true,
        validator: (value) => ['berita', 'produk-hukum'].includes(value)
    },
    identifier: {
        type: String,
        required: true
    },
    content: {
        type: Object,
        required: true
    }
});

const comments = ref([]);
const submitting = ref(false);
const commentForm = ref({
    nama_user: getCookie('commenter_name') || '',
    email: getCookie('commenter_email') || '',
    komentar: "",
    captcha: "",
});
const replyForm = ref({
    nama_user: getCookie('commenter_name') || '',
    email: getCookie('commenter_email') || '',
    komentar: '',
    captcha: '',
});
const activeReplyId = ref(null);
const mainCaptchaData = ref(null);
const replyCaptchaData = ref(null);
const mainCaptchaCode = ref('');
const replyCaptchaCode = ref('');

// Computed endpoints based on type
const getEndpoints = () => {
    if (props.type === 'berita') {
        return {
            list: `/api/comments/${props.identifier}`,
            submit: '/api/comments',
            replies: `/api/comments/${props.identifier}/replies/`
        };
    } else {
        return {
            list: `/api/produk-hukum-comments/${props.identifier}`,
            submit: '/api/produk-hukum-comments',
            replies: `/api/produk-hukum-comments/${props.identifier}/replies/`
        };
    }
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString("id-ID", {
        year: "numeric",
        month: "long",
        day: "numeric",
    });
};

const getAvatar = (name) => {
    return `https://ui-avatars.com/api/?name=${encodeURIComponent(
        name
    )}&background=random`;
};

const fetchComments = async () => {
    try {
        const endpoints = getEndpoints();
        const response = await axios.get(endpoints.list);
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

onMounted(() => {
    refreshCaptcha('main');
    fetchComments();
});

const submitComment = async () => {
    try {
        submitting.value = true;
        const endpoints = getEndpoints();
        
        const payload = {
            ...commentForm.value,
            captcha: commentForm.value.captcha,
            code: mainCaptchaCode.value
        };

        // Add type-specific fields
        if (props.type === 'berita') {
            payload.id_berita = props.identifier;
        } else {
            payload.id_produk_hukum = props.identifier;
        }

        const response = await axios.post(endpoints.submit, payload);

        // Save user data to cookies
        setCookie('commenter_name', commentForm.value.nama_user);
        setCookie('commenter_email', commentForm.value.email);

        if (!props.content.is_moderate) {
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
    replyForm.value = {
        nama_user: getCookie('commenter_name') || '',
        email: getCookie('commenter_email') || '',
        komentar: '',
        captcha: '',
    };
    activeReplyId.value = commentId;
    refreshCaptcha('reply');
};

const cancelReply = () => {
    activeReplyId.value = null;
    replyForm.value = {
        nama_user: getCookie('commenter_name') || '',
        email: getCookie('commenter_email') || '',
        komentar: '',
        captcha: '',
    };
};

const submitReply = async (commentId) => {
    try {
        submitting.value = true;
        const endpoints = getEndpoints();
        
        const payload = {
            ...replyForm.value,
            captcha: replyForm.value.captcha,
            code: replyCaptchaCode.value,
            id_ref_komentar: commentId
        };

        // Add type-specific fields
        if (props.type === 'berita') {
            payload.id_berita = props.identifier;
        } else {
            payload.id_produk_hukum = props.identifier;
        }

        const response = await axios.post(endpoints.submit, payload);

        setCookie('commenter_name', replyForm.value.nama_user);
        setCookie('commenter_email', replyForm.value.email);

        if (!props.content.is_moderate) {
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
        const endpoints = getEndpoints();
        const response = await axios.get(
            `${endpoints.replies}${comment.id}`
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

// Watch for changes in identifier prop to reload comments
watch(() => props.identifier, () => {
    fetchComments();
});
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
                <input
                    type="email"
                    v-model="commentForm.email"
                    class="w-full p-2 border rounded"
                    placeholder="Email"
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
                            <div v-if="activeReplyId" class="mb-3">
                                <input
                                    type="email"
                                    v-model="replyForm.email"
                                    class="w-full p-2 border rounded"
                                    placeholder="Email"
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