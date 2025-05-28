import { ref } from "vue";
import axios from "axios";

export const formatDate = (date) => {
    return new Date(date).toLocaleDateString("id-ID", {
        year: "numeric",
        month: "long",
        day: "numeric",
    });
};

export default function useComments(type, itemId, isModerated = false) {
    const comments = ref([]);
    const submitting = ref(false);
    const activeReplyId = ref(null);
    const mainCaptchaData = ref(null);
    const replyCaptchaData = ref(null);
    const mainCaptchaCode = ref("");
    const replyCaptchaCode = ref("");

    // Initialize form data based on the content type
    const commentForm = ref({
        nama_user: "",
        komentar: "",
        captcha: "",
    });

    const replyForm = ref({
        nama_user: "",
        komentar: "",
        id_ref_komentar: null,
        captcha: "",
    });

    // Set the appropriate ID field based on content type
    if (type === "news") {
        commentForm.value.id_berita = itemId;
        replyForm.value.id_berita = itemId;
    } else if (type === "produk-hukum") {
        commentForm.value.id_produk_hukum = itemId;
        replyForm.value.id_produk_hukum = itemId;
    }

    // Helper methods
    const getAvatar = (name) => {
        return `https://ui-avatars.com/api/?name=${encodeURIComponent(
            name
        )}&background=random`;
    };

    const truncateText = (text, length) => {
        if (!text) return "";
        return text.length > length ? text.substring(0, length) + "..." : text;
    };
    // API endpoints based on content type
    const getEndpoints = () => {
        if (type === "news") {
            return {
                fetchComments: `/api/comments/${itemId}`,
                submitComment: "/api/comments",
                fetchReplies: (commentId) =>
                    `/api/comments/${itemId}/replies/${commentId}`,
            };
        } else if (type === "produk-hukum") {
            return {
                fetchComments: `/api/produk-hukum-comments/${itemId}`,
                submitComment: "/api/produk-hukum-comments",
                fetchReplies: (commentId) =>
                    `/api/produk-hukum-comments/${itemId}/replies/${commentId}`,
            };
        }
    };

    const endpoints = getEndpoints();

    // Fetch comments
    const fetchComments = async () => {
        try {
            const response = await axios.get(endpoints.fetchComments);
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

    // Refresh captcha
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

    // Submit comment
    const submitComment = async () => {
        try {
            submitting.value = true;
            const response = await axios.post(endpoints.submitComment, {
                ...commentForm.value,
                captcha: commentForm.value.captcha,
                code: mainCaptchaCode.value,
            });

            if (!isModerated) {
                await fetchComments();
            }

            commentForm.value.komentar = "";
            commentForm.value.nama_user = "";
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

    // Show reply form
    const showReplyForm = (commentId) => {
        activeReplyId.value = commentId;
        replyForm.value = {
            nama_user: "",
            komentar: "",
            id_ref_komentar: commentId,
            captcha: "",
        };

        // Add the appropriate ID field based on content type
        if (type === "news") {
            replyForm.value.id_berita = itemId;
        } else if (type === "produk-hukum") {
            replyForm.value.id_produk_hukum = itemId;
        }

        refreshCaptcha("reply");
    };

    // Cancel reply
    const cancelReply = () => {
        activeReplyId.value = null;
        replyForm.value = {
            nama_user: "",
            komentar: "",
            id_ref_komentar: null,
            captcha: "",
        };

        // Add the appropriate ID field based on content type
        if (type === "news") {
            replyForm.value.id_berita = itemId;
        } else if (type === "produk-hukum") {
            replyForm.value.id_produk_hukum = itemId;
        }
    };

    // Submit reply
    const submitReply = async (commentId) => {
        try {
            submitting.value = true;
            const response = await axios.post(endpoints.submitComment, {
                ...replyForm.value,
                captcha: replyForm.value.captcha,
                code: replyCaptchaCode.value,
            });

            if (!isModerated) {
                const comment = comments.value.find(
                    (c) => c.id === replyForm.value.id_ref_komentar
                );
                if (comment && comment.showReplies) {
                    await loadReplies(comment);
                } else {
                    await fetchComments();
                }
            }

            cancelReply();
            alert(response.data.message);
        } catch (err) {
            console.error("Error submitting reply:", err);
            await refreshCaptcha("reply");
            replyForm.value.captcha = "";
            alert(err.response?.data?.message || "Failed to submit reply");
        } finally {
            submitting.value = false;
        }
    };

    // Load replies
    const loadReplies = async (comment) => {
        if (comment.showReplies) {
            comment.showReplies = false;
            comment.replies = [];
            return;
        }

        try {
            const response = await axios.get(
                endpoints.fetchReplies(comment.id)
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

    // Get all deeper replies
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

    // Initialize
    const initialize = () => {
        fetchComments();
        refreshCaptcha("main");
    };

    return {
        comments,
        commentForm,
        replyForm,
        submitting,
        activeReplyId,
        mainCaptchaData,
        replyCaptchaData,
        getAvatar,
        truncateText,
        formatDate,
        fetchComments,
        refreshCaptcha,
        submitComment,
        showReplyForm,
        cancelReply,
        submitReply,
        loadReplies,
        getAllDeeperReplies,
        initialize,
    };
}
