<script setup>
import { ref, computed, onMounted, inject } from "vue";
import useComments from "@/Utility/useComment";

const props = defineProps({
    slug: {
        type: String,
        required: true,
    },
    data: {
        type: Object,
        required: true,
    },
    commentApiBase: {
        type: String,
        default: "/api/comments",
    },
    showShare: {
        type: Boolean,
        default: false,
    },
});
const showToast = inject("showToast");
// Determine content type from commentApiBase
const contentType = computed(() => {
    return props.commentApiBase.includes("produk-hukum")
        ? "produk-hukum"
        : "news";
});

// Use the comments utility
const {
    comments,
    commentForm,
    replyForm,
    submitting,
    activeReplyId,
    mainCaptchaData,
    replyCaptchaData,
    getAvatar,
    refreshCaptcha,
    truncateText,
    formatDate,
    submitComment,
    showReplyForm,
    cancelReply,
    submitReply,
    loadReplies,
    getAllDeeperReplies: getAllDeeperRepliesSorted,
    initialize,
} = useComments(contentType.value, props.slug, props.data?.is_moderate);

// Calculate relative time for better UX
const timeAgo = (date) => {
    const now = new Date();
    const past = new Date(date);
    const diff = Math.floor((now - past) / 1000);

    if (diff < 60) return `${diff} detik lalu`;
    if (diff < 3600) return `${Math.floor(diff / 60)} menit lalu`;
    if (diff < 86400) return `${Math.floor(diff / 3600)} jam lalu`;
    if (diff < 2592000) return `${Math.floor(diff / 86400)} hari lalu`;
    if (diff < 31104000) return `${Math.floor(diff / 2592000)} bulan lalu`;
    return `${Math.floor(diff / 31104000)} tahun lalu`;
};
const fullUrl = computed(() => {
    // Adjust the base URL as needed for your deployment
    return `${window.location.origin}/berita/${props.data.slug || props.slug}`;
});
const generateShareId = () => {
    // Simple random string, you can use a better generator if needed
    return Math.random().toString(36).substring(2, 10);
};

const shareId = ref(generateShareId());

const mobileShareUrl = computed(() => {
    return `${window.location.origin}/berita/${props.data.slug || props.slug}?utm_source=Mobile&utm_medium=copy-to-clipboard&shareID=${shareId.value}`;
});

const copyToClipboard = async () => {
    if (!navigator.clipboard) {
        alert('Fitur salin link tidak didukung di browser ini.');
        return;
    }
    try {
        await navigator.clipboard.writeText(mobileShareUrl.value);
        alert('Link berhasil disalin!');
    } catch (e) {
        alert('Gagal menyalin link');
        console.error('Gagal menyalin link:', e);
    }
};
onMounted(() => {
    initialize();
});
</script>

<template>
    <div
        class="flex flex-col bg-white rounded-lg shadow-lg overflow-hidden p-4 lg:p-8 font-merriweather"
    >
        <!-- Share Form -->
        <div class="flex flex-col w-full justify-between" v-if="showShare">
            <h2 class="flex lg:text-lg mt-2 lg:mt-0 text-accent2 font-bold">
                Bagikan Artikel Ini
            </h2>
            <div class="ml-1 my-2 lg:my-4 flex items-center">
            <!-- Instagram (Instagram does not support direct sharing, so just link to Instagram home or your profile) -->
            <!-- <a
                href="https://www.instagram.com/"
                target="_blank"
                rel="noopener"
                class="mr-4"
                title="Bagikan ke Instagram"
            >
                <i class="bi bi-instagram md:text-xl relative text-accent2"></i>
            </a> -->
            <!-- X (Twitter) -->
            <a
                :href="`https://x.com/intent/post?text=${encodeURIComponent(props.data.title || '')}&url=${encodeURIComponent(fullUrl)}`"
                target="_blank"
                rel="noopener"
                class="mr-4"
                title="Bagikan ke X"
            >
                <i class="bi bi-twitter-x md:text-xl relative text-accent2"></i>
            </a>
            <!-- Facebook -->
            <a
                :href="`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(fullUrl)}`"
                target="_blank"
                rel="noopener"
                class="mr-4"
                title="Bagikan ke Facebook"
            >
                <i class="bi bi-facebook md:text-xl relative text-accent2"></i>
            </a>
            <!-- WhatsApp -->
            <a
                :href="`https://wa.me/?text=${encodeURIComponent(props.data.title || '')}%20${encodeURIComponent(fullUrl)}`"
                target="_blank"
                rel="noopener"
                class="mr-4"
                title="Bagikan ke WhatsApp"
            >
                <i class="bi bi-whatsapp md:text-xl relative text-accent2"></i>
            </a>
            <button
                @click="copyToClipboard"
                class="flex items-center text-accent2"
                title="Salin Link"
            >
                <i class="bi bi-clipboard md:text-xl relative"></i>
            </button>
        </div>
        </div>
        <div class="flex flex-col border-gray-300 mt-2 lg:mt-0 mb-2 lg:mb-6">
            <h2
                class="flex text-lg lg:text-2xl text-accent1 font-medium"
                :class="{ 'mb-6': props.data.is_moderate }"
            >
                Kirim Komentar
            </h2>
            <div class="flex border-l-accent2 border-l-4 bg-accent2/25">
                <p
                    v-if="props.data.is_moderate"
                    class="text-accent1 opacity-100 pl-2 lg:pl-4 py-2 text-xxs lg:text-base"
                >
                    Komentar Anda akan ditampilkan setelah moderasi.
                </p>
            </div>
        </div>

        <!-- Comment Form -->
        <form @submit.prevent="submitComment" class="mb-6 lg:mb-8">
            <div class="mb-2 lg:mb-4 shadow-inner">
                <input
                    v-model="commentForm.nama_user"
                    type="text"
                    placeholder="Masukkan Nama..."
                    class="w-full text-xs lg:text-base p-2 border-1 border-zinc-300 rounded shadow-inner bg-type1"
                    required
                />
            </div>
            <div class="mb-2 lg:mb-4 shadow-inner">
                <input
                    v-model="commentForm.email_user"
                    type="text"
                    placeholder="Masukkan Email..."
                    class="w-full text-xs lg:text-base p-2 border-1 border-zinc-300 rounded shadow-inner bg-type1"
                    required
                />
            </div>
            <div class="mb-0 lg:mb-4 shadow-inner">
                <textarea
                    v-model="commentForm.komentar"
                    placeholder="Masukkan Komentar..."
                    rows="6"
                    class="w-full text-xs lg:text-base p-2 border-1 border-zinc-300 rounded shadow-inner bg-type1"
                    required
                ></textarea>
            </div>
            <div class="mb-2 lg:mb-4">
                <div class="flex items-center gap-4 mb-2">
                    <img
                        :src="mainCaptchaData?.image"
                        alt="CAPTCHA"
                        class="h-7 lg:h-10"
                    />
                    <button
                        type="button"
                        @click="refreshCaptcha('main')"
                        class="text-xs lg:text-base text-blue-600 hover:text-blue-800"
                    >
                        Refresh Captcha
                    </button>
                </div>
                <input
                    v-model="commentForm.captcha"
                    type="text"
                    placeholder="Masukkan kode captcha..."
                    class="w-full lg:w-1/2 text-xs lg:text-base p-2 border-1 border-zinc-300 rounded shadow-inner bg-type1"
                    required
                />
            </div>
            <button
                type="submit"
                class="flex bg-secondary text-white px-2 lg:px-4 py-2 gap-1 rounded text-xs lg:text-sm hover:bg-secondary/75"
                :disabled="submitting"
            >
                {{ submitting ? "Mengirim..." : "Kirim Komentar" }}
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    height="18px"
                    viewBox="http://www.w3.org/2000/svg"
                    width="16px"
                    fill="#FFFFFF"
                    class="-mt-0.5 lg:w-6 lg:h-6"
                >
                    <path
                        d="M120-160v-640l760 320-760 320Zm80-120 474-200-474-200v140l240 60-240 60v140Zm0 0v-400 400Z"
                    />
                </svg>
            </button>
        </form>

        <!-- Comments List -->
        <div class="space-y-2 lg:space-y-4">
            <template v-for="comment in comments" :key="comment.id">
                <div class="bg-type1 shadow-md p-2 lg:p-4 py-4 lg:py-6 rounded">
                    <!-- Main Comment -->
                    <div class="flex gap-2 lg:gap-3">
                        <img
                            :src="getAvatar(comment.nama_user)"
                            alt="User Avatar"
                            class="w-7 h-7 lg:w-10 lg:h-10 rounded-full"
                        />
                        <div class="flex flex-col w-full">
                            <div
                                class="flex flex-row items-start sm:items-center gap-2 sm:gap-4 mt-1 lg:mt-1.5"
                            >
                                <div class="font-semibold text-sm lg:text-lg">
                                    <p>{{ comment.nama_user }}</p>
                                </div>
                                <div
                                    class="text-xs lg:text-sm text-zinc-400 mt-0.5 lg:mt-0"
                                >
                                    <p>{{ timeAgo(comment.created_at) }}</p>
                                </div>
                            </div>
                            <div
                                class="text-gray-600 mt-2 text-xs lg:text-sm font-medium break-words"
                            >
                                {{ comment.komentar }}
                            </div>
                            <div
                                class="flex flex-col sm:flex-row items-start sm:items-center w-full justify-between mt-2"
                            >
                                <div
                                    class="flex flex-col lg:flex-row items-start gap-2 lg:gap-4 mt-2"
                                >
                                    <div
                                        class="text-xs lg:text-sm text-zinc-400"
                                    >
                                        {{ formatDate(comment.created_at) }}
                                    </div>
                                    <button
                                        v-if="comment.replies_count > 0"
                                        @click="loadReplies(comment)"
                                        class="text-xs lg:text-sm text-zinc-500 hover:text-gray-800"
                                    >
                                        {{
                                            comment.showReplies
                                                ? "Sembunyikan"
                                                : "Tampilkan"
                                        }}
                                        {{ comment.replies_count }} balasan
                                    </button>
                                </div>
                                <button
                                    @click="
                                        activeReplyId === comment.id
                                            ? cancelReply()
                                            : showReplyForm(comment.id)
                                    "
                                    class="text-xs lg:text-sm mt-2 mr-2 lg:mr-10"
                                    :class="
                                        activeReplyId === comment.id
                                            ? 'text-red-600 hover:text-red-800'
                                            : 'text-blue-600 hover:text-blue-800'
                                    "
                                >
                                    {{
                                        activeReplyId === comment.id
                                            ? "Batal"
                                            : "Balas"
                                    }}
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Reply Form -->
                    <div
                        v-if="activeReplyId === comment.id"
                        class="mt-4 ml-2 lg:ml-12"
                    >
                        <form
                            @submit.prevent="submitReply(comment.id)"
                            class="space-y-4"
                        >
                            <div>
                                <input
                                    v-model="replyForm.nama_user"
                                    type="text"
                                    placeholder="Masukkan Nama..."
                                    class="w-full text-xs lg:text-base p-2 border-1 border-zinc-300 rounded shadow-inner bg-type1"
                                    required
                                />
                            </div>
                            <div>
                                <input
                                    v-model="replyForm.email_user"
                                    type="text"
                                    placeholder="Masukkan Email..."
                                    class="w-full text-xs lg:text-base p-2 border-1 border-zinc-300 rounded shadow-inner bg-type1"
                                    required
                                />
                            </div>
                            <div>
                                <textarea
                                    v-model="replyForm.komentar"
                                    placeholder="Masukkan Balasan..."
                                    rows="3"
                                    class="w-full text-xs lg:text-base p-2 border-1 border-zinc-300 rounded shadow-inner bg-type1"
                                    required
                                ></textarea>
                            </div>
                            <div class="mb-4">
                                <div class="flex items-center gap-4 mb-2">
                                    <img
                                        :src="replyCaptchaData?.image"
                                        alt="CAPTCHA"
                                        class="h-7 lg:h-10"
                                    />
                                    <button
                                        type="button"
                                        @click="refreshCaptcha('reply')"
                                        class="text-xs lg:text-base text-blue-600 hover:text-blue-800"
                                    >
                                        Refresh Captcha
                                    </button>
                                </div>
                                <input
                                    v-model="replyForm.captcha"
                                    type="text"
                                    placeholder="Masukkan kode captcha..."
                                    class="w-full lg:w-1/2 text-xs lg:text-base p-2 border-1 border-zinc-300 rounded shadow-inner bg-type1"
                                    required
                                />
                            </div>
                            <div class="flex gap-2">
                                <button
                                    type="submit"
                                    class="flex bg-secondary text-white px-2 lg:px-4 py-2 gap-1 rounded text-xs lg:text-sm hover:bg-secondary/75"
                                    :disabled="submitting"
                                >
                                    {{
                                        submitting
                                            ? "Mengirim..."
                                            : "Kirim Balasan"
                                    }}
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        height="18px"
                                        viewBox="http://www.w3.org/2000/svg"
                                        width="16px"
                                        fill="#FFFFFF"
                                        class="-mt-0.5 lg:w-6 lg:h-6"
                                    >
                                        <path
                                            d="M120-160v-640l760 320-760 320Zm80-120 474-200-474-200v140l240 60-240 60v140Zm0 0v-400 400Z"
                                        />
                                    </svg>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Replies Section -->
                    <div
                        v-if="comment.showReplies && comment.replies?.length"
                        class="mt-4 space-y-4"
                    >
                        <template
                            v-for="reply in comment.replies"
                            :key="reply.id"
                        >
                            <div
                                class="lg:ml-8 bg-white p-4 py-6 rounded border border-gray-100 shadow-inner"
                            >
                                <!-- Reply Content Level 2 -->
                                <div class="flex gap-2 lg:gap-3">
                                    <img
                                        :src="getAvatar(reply.nama_user)"
                                        alt="User Avatar"
                                        class="w-7 h-7 lg:w-10 lg:h-10 rounded-full"
                                    />
                                    <div class="flex flex-col w-full">
                                        <div
                                            class="flex flex-row items-start sm:items-center gap-2 sm:gap-4 mt-1 lg:mt-1.5"
                                        >
                                            <div
                                                class="font-semibold text-sm lg:text-lg"
                                            >
                                                <p>{{ reply.nama_user }}</p>
                                            </div>
                                            <div
                                                class="text-xs lg:text-sm text-zinc-400 mt-0.5 lg:mt-0"
                                            >
                                                <p>
                                                    {{
                                                        timeAgo(
                                                            reply.created_at
                                                        )
                                                    }}
                                                </p>
                                            </div>
                                        </div>
                                        <div
                                            class="text-gray-600 mt-2 text-xs lg:text-sm font-medium break-words"
                                        >
                                            {{ reply.komentar }}
                                        </div>
                                        <div
                                            class="flex flex-col sm:flex-row items-start sm:items-center w-full justify-between mt-2"
                                        >
                                            <div
                                                class="flex flex-col lg:flex-row items-start gap-2 lg:gap-4 mt-2"
                                            >
                                                <div
                                                    class="text-xs lg:text-sm text-zinc-400"
                                                >
                                                    {{
                                                        formatDate(
                                                            reply.created_at
                                                        )
                                                    }}
                                                </div>
                                                <button
                                                    v-if="
                                                        reply.replies_count > 0
                                                    "
                                                    @click="loadReplies(reply)"
                                                    class="text-xs lg:text-sm text-zinc-500 hover:text-gray-800"
                                                >
                                                    {{
                                                        reply.showReplies
                                                            ? "Sembunyikan"
                                                            : "Tampilkan"
                                                    }}
                                                    {{ reply.replies_count }}
                                                    balasan
                                                </button>
                                            </div>
                                            <button
                                                @click="
                                                    activeReplyId === reply.id
                                                        ? cancelReply()
                                                        : showReplyForm(
                                                              reply.id
                                                          )
                                                "
                                                class="text-xs lg:text-sm mt-2 mr-2 lg:mr-6"
                                                :class="
                                                    activeReplyId === reply.id
                                                        ? 'text-red-600 hover:text-red-800'
                                                        : 'text-blue-600 hover:text-blue-800'
                                                "
                                            >
                                                {{
                                                    activeReplyId === reply.id
                                                        ? "Batal"
                                                        : "Balas"
                                                }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <!-- Reply Form for Level 2 -->
                                <div
                                    v-if="activeReplyId === reply.id"
                                    class="mt-4 lg:ml-12"
                                >
                                    <form
                                        @submit.prevent="submitReply(reply.id)"
                                        class="space-y-4"
                                    >
                                        <div>
                                            <input
                                                v-model="replyForm.nama_user"
                                                type="text"
                                                placeholder="Masukkan Nama..."
                                                class="w-full text-xs lg:text-base p-2 border-1 border-zinc-300 rounded shadow-inner bg-type1"
                                                required
                                            />
                                        </div>
                                        <div>
                                            <input
                                                v-model="replyForm.email_user"
                                                type="text"
                                                placeholder="Masukkan Email..."
                                                class="w-full text-xs lg:text-base p-2 border-1 border-zinc-300 rounded shadow-inner bg-type1"
                                                required
                                            />
                                        </div>
                                        <div>
                                            <textarea
                                                v-model="replyForm.komentar"
                                                placeholder="Masukkan Balasan..."
                                                rows="3"
                                                class="w-full text-xs lg:text-base p-2 border-1 border-zinc-300 rounded shadow-inner bg-type1"
                                                required
                                            ></textarea>
                                        </div>
                                        <div class="mb-4">
                                            <div
                                                class="flex items-center gap-4 mb-2"
                                            >
                                                <img
                                                    :src="
                                                        replyCaptchaData?.image
                                                    "
                                                    alt="CAPTCHA"
                                                    class="h-7 lg:h-10"
                                                />
                                                <button
                                                    type="button"
                                                    @click="
                                                        refreshCaptcha('reply')
                                                    "
                                                    class="text-xs lg:text-base text-blue-600 hover:text-blue-800"
                                                >
                                                    Refresh Captcha
                                                </button>
                                            </div>
                                            <input
                                                v-model="replyForm.captcha"
                                                type="text"
                                                placeholder="Masukkan kode captcha..."
                                                class="w-full lg:w-1/2 text-xs lg:text-base p-2 border-1 border-zinc-300 rounded shadow-inner bg-type1"
                                                required
                                            />
                                        </div>
                                        <div class="flex gap-2">
                                            <button
                                                type="submit"
                                                class="flex bg-secondary text-white px-2 lg:px-4 py-2 gap-1 rounded text-xs lg:text-sm hover:bg-secondary/75"
                                                :disabled="submitting"
                                            >
                                                {{
                                                    submitting
                                                        ? "Mengirim..."
                                                        : "Kirim Balasan"
                                                }}
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    height="18px"
                                                    viewBox="http://www.w3.org/2000/svg"
                                                    width="16px"
                                                    fill="#FFFFFF"
                                                    class="-mt-0.5 lg:w-6 lg:h-6"
                                                >
                                                    <path
                                                        d="M120-160v-640l760 320-760 320Zm80-120 474-200-474-200v140l240 60-240 60v140Zm0 0v-400 400Z"
                                                    />
                                                </svg>
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                <!-- Nested Replies (Level 3+) -->
                                <div
                                    v-if="
                                        reply.showReplies &&
                                        reply.replies?.length
                                    "
                                    class="mt-4 space-y-4"
                                >
                                    <div
                                        v-for="deepReply in getAllDeeperRepliesSorted(
                                            reply
                                        )"
                                        :key="deepReply.id"
                                        class="lg:ml-12 bg-white p-2 lg:p-3 py-4 lg:py-3 rounded border border-gray-100 shadow-[inset_0px_0px_4px_0px_rgba(0,0,0,0.15)]"
                                    >
                                        <!-- Reply Content Level 3+ -->
                                        <div class="flex gap-2 lg:gap-3">
                                            <img
                                                :src="
                                                    getAvatar(
                                                        deepReply.nama_user
                                                    )
                                                "
                                                alt="User Avatar"
                                                class="w-7 h-7 lg:w-10 lg:h-10 rounded-full"
                                            />
                                            <div class="flex flex-col w-full">
                                                <div
                                                    class="flex flex-col lg:flex-row items-start sm:items-center sm:gap-4 mt-1 lg:mt-1.5"
                                                >
                                                    <div
                                                        class="font-semibold text-sm lg:text-lg"
                                                    >
                                                        <p>
                                                            {{
                                                                deepReply.nama_user
                                                            }}
                                                        </p>
                                                    </div>
                                                    <div
                                                        class="text-xs lg:text-sm text-zinc-400 mt-0.5 lg:mt-0"
                                                    >
                                                        <p>
                                                            {{
                                                                timeAgo(
                                                                    deepReply.created_at
                                                                )
                                                            }}
                                                        </p>
                                                    </div>
                                                </div>
                                                <div
                                                    class="flex flex-col w-full border-l-accent2 border-l-4 bg-accent2/25 pl-3 lg:pl-6 pr-2 py-3 my-3"
                                                    v-if="deepReply.parent_user"
                                                >
                                                    <div
                                                        class="line-clamp-1 text-gray-600 font-semibold text-xs lg:text-sm"
                                                    >
                                                        {{
                                                            deepReply.parent_user
                                                        }}
                                                    </div>
                                                    <div
                                                        class="line-clamp-2 lg:line-clamp-1 text-gray-600 italic text-xs lg:text-sm break-words"
                                                    >
                                                        "{{
                                                            truncateText(
                                                                deepReply.parent
                                                                    ?.komentar
                                                            )
                                                        }}"
                                                    </div>
                                                </div>
                                                <div
                                                    class="text-gray-600 lg:mt-2 text-xs lg:text-sm font-medium break-words"
                                                >
                                                    {{ deepReply.komentar }}
                                                </div>
                                                <div
                                                    class="flex flex-col sm:flex-row items-start sm:items-center w-full justify-between mt-2"
                                                >
                                                    <div
                                                        class="flex flex-col lg:flex-row items-start gap-2 lg:gap-4 mt-2"
                                                    >
                                                        <div
                                                            class="text-xs lg:text-sm text-zinc-400"
                                                        >
                                                            {{
                                                                formatDate(
                                                                    deepReply.created_at
                                                                )
                                                            }}
                                                        </div>
                                                    </div>
                                                    <button
                                                        @click="
                                                            activeReplyId ===
                                                            deepReply.id
                                                                ? cancelReply()
                                                                : showReplyForm(
                                                                      deepReply.id
                                                                  )
                                                        "
                                                        class="text-xs lg:text-sm mt-2 mr-2"
                                                        :class="
                                                            activeReplyId ===
                                                            deepReply.id
                                                                ? 'text-red-600 hover:text-red-800'
                                                                : 'text-blue-600 hover:text-blue-800'
                                                        "
                                                    >
                                                        {{
                                                            activeReplyId ===
                                                            deepReply.id
                                                                ? "Batal"
                                                                : "Balas"
                                                        }}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Reply Form -->
                                        <div
                                            v-if="
                                                activeReplyId === deepReply.id
                                            "
                                            class="mt-4 ml-2 lg:ml-12"
                                        >
                                            <form
                                                @submit.prevent="
                                                    submitReply(deepReply.id)
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
                                                        class="w-full text-xs lg:text-base p-2 border-1 border-zinc-300 rounded shadow-inner bg-type1"
                                                        required
                                                    />
                                                </div>
                                                <div>
                                                    <input
                                                        v-model="
                                                            replyForm.email_user
                                                        "
                                                        type="text"
                                                        placeholder="Masukkan Email..."
                                                        class="w-full text-xs lg:text-base p-2 border-1 border-zinc-300 rounded shadow-inner bg-type1"
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
                                                        class="w-full text-xs lg:text-base p-2 border-1 border-zinc-300 rounded shadow-inner bg-type1"
                                                        required
                                                    ></textarea>
                                                </div>
                                                <div class="mb-4">
                                                    <div
                                                        class="flex items-center gap-4 mb-2"
                                                    >
                                                        <img
                                                            :src="
                                                                replyCaptchaData?.image
                                                            "
                                                            alt="CAPTCHA"
                                                            class="h-7 lg:h-10"
                                                        />
                                                        <button
                                                            type="button"
                                                            @click="
                                                                refreshCaptcha(
                                                                    'reply'
                                                                )
                                                            "
                                                            class="text-xs lg:text-base text-blue-600 hover:text-blue-800"
                                                        >
                                                            Refresh Captcha
                                                        </button>
                                                    </div>
                                                    <input
                                                        v-model="
                                                            replyForm.captcha
                                                        "
                                                        type="text"
                                                        placeholder="Masukkan kode captcha..."
                                                        class="w-full lg:w-1/2 text-xs lg:text-base p-2 border-1 border-zinc-300 rounded shadow-inner bg-type1"
                                                        required
                                                    />
                                                </div>
                                                <div class="flex gap-2">
                                                    <button
                                                        type="submit"
                                                        class="flex bg-secondary text-white px-2 lg:px-4 py-2 gap-1 rounded text-xs lg:text-sm hover:bg-secondary/75"
                                                        :disabled="submitting"
                                                    >
                                                        {{
                                                            submitting
                                                                ? "Mengirim..."
                                                                : "Kirim Balasan"
                                                        }}
                                                        <svg
                                                            xmlns="http://www.w3.org/2000/svg"
                                                            height="18px"
                                                            viewBox="http://www.w3.org/2000/svg"
                                                            width="16px"
                                                            fill="#FFFFFF"
                                                            class="-mt-0.5 lg:w-6 lg:h-6"
                                                        >
                                                            <path
                                                                d="M120-160v-640l760 320-760 320Zm80-120 474-200-474-200v140l240 60-240 60v140Zm0 0v-400 400Z"
                                                            />
                                                        </svg>
                                                    </button>
                                                    <button
                                                        type="button"
                                                        @click="cancelReply"
                                                        class="text-gray-600 px-4 py-2 rounded text-xs lg:text-sm hover:bg-gray-100"
                                                    >
                                                        Batal
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
</template>
