<template>
  <UserLayout>
    <div class="min-h-screen bg-type1">
      <div class="max-w-screen-2xl mx-auto px-6 lg:px-28 py-6 lg:py-12">
        <!-- Loading State -->
        <div v-if="loading" class="flex justify-center items-center py-12">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="text-center py-12">
          <p class="text-red-600">{{ error }}</p>
        </div>

        <!-- Document Detail -->
          <div v-else class="mt-24 font-merriweather">
            <div class="flex space-x-2 items-center mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="currentColor" class="text-accent2">
                    <path d="M280-280q17 0 28.5-11.5T320-320q0-17-11.5-28.5T280-360q-17 0-28.5 11.5T240-320q0 17 11.5 28.5T280-280Zm-40-160h80v-240h-80v240Zm200 160h280v-80H440v80Zm0-160h280v-80H440v80Zm0-160h280v-80H440v80ZM160-120q-33 0-56.5-23.5T80-200v-560q0-33 23.5-56.5T160-840h640q33 0 56.5 23.5T880-760v560q0 33-23.5 56.5T800-120H160Zm0-80h640v-560H160v560Zm0 0v-560 560Z"/>
                </svg>
                <h1 class="lg:text-lg  text-accent2 -mb-0.5 font-merriweather ">
                    Peraturan Desa 
                </h1>
            </div>

            <!-- Title -->
            <h1 class="flex text-xl lg:text-3xl w-full mx-auto justify-start lg:justify-center items-center font-bold text-accent1 mb-4">
              Detail Produk Hukum
            </h1>
            
            
            <!-- Title -->
            <!-- <h2 class="text-3xl font-bold text-accent1 mb-4">
              {{ dokumen.title }}
            </h2> -->

            <!-- MetaData -->
            <div>
              <div class="flex flex-col gap-2.5 lg:gap-4 mb-4lg:mb-8 font-merriweather font-medium text-accent1">
                <div class="flex flex-row items-center">
                  <div class="flex flex-col w-1/5 font-bold text-sm lg:text-base">Kategori</div>
                    <div class="flex flex-row w-4/5">
                      <div class="flex flex-col mr-1 text-sm lg:text-base">:</div>
                      <p class="flex col-span-2 text-sm lg:text-base"> {{ dokumen.category }}</p>
                    </div>
                </div>
                <div class="flex flex-row items-start text-sm lg:text-base">
                  <div class="flex flex-col w-1/5 font-bold">Judul</div>
                    <div class="flex flex-row w-4/5">
                      <div class="flex flex-col mr-1">:</div>
                      <p class="flex col-span-2"> {{ dokumen.title}}</p>
                    </div>
                </div>
                <div class="flex flex-row items-start text-sm lg:text-base">
                  <div class="flex flex-col w-1/5 font-bold">Tanggal</div>
                    <div class="flex flex-row w-4/5">
                      <div class="flex flex-col mr-1">:</div>
                      <p class="flex col-span-2"> {{ formatDate(dokumen.date) }}</p>
                    </div>
                </div>
                <div class="flex flex-row items-start text-sm lg:text-base ">
                  <div class="flex flex-col w-1/5 font-bold">Deskripsi</div>
                    <div class="flex flex-row w-4/5">
                      <div class="flex flex-col mr-1">:</div>
                      <p class="flex col-span-2 text-justify lg:text-left"> {{ dokumen.description}}</p>
                    </div>
                </div>
                <div class="flex flex-row items-start text-sm lg:text-base">
                  <div class="flex flex-col w-1/5 font-bold">Link</div>
                    <div class="flex flex-row w-4/5">
                      <div class="flex flex-col mr-1">:</div>
                      <a v-if="dokumen.link" :href="dokumen.link" target="_blank" class="line-clamp-1 text-blue-600 hover:underline ">
                        {{ dokumen.link }}
                      </a>
                      <p v-else class="flex col-span-9"> Tidak Tersedia</p>
                    </div>
                </div>
                <div class="mt-4">
                <a v-if="dokumen.file"
                   :href="`/storage/${dokumen.file}`"
                   target="_blank"
                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-accent2 hover:bg-accent1"
                >
                  Download Document
                </a>
              </div>
                
              </div>
              <div v-if="dokumen.file && isPreviewable(dokumen.file)" class="m-1 pt-4">
                <iframe
                  :src="`/storage/${dokumen.file}`"
                  width="100%"
                  height="100%"
                  class="border rounded font-merriweather h-[350px] lg:h-[750px]"
                ></iframe>
              </div>
              
            </div>
            
            <!-- Metadata
            <div class="flex items-center text-sm text-gray-500 mb-8">
              <span>{{ formatDate(dokumen.date) }}</span>
            </div> -->

            <!-- Description -->
            <!-- <div class="prose max-w-none mb-8">
              {{ dokumen.description }}
            </div> -->

            <!-- Document Actions -->
            <!-- <div class="flex space-x-4">
              <a v-if="dokumen.file"
                 :href="`/storage/${dokumen.file}`"
                 target="_blank"
                 class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700"
              >
                Download Document
              </a>
              <a v-if="dokumen.link"
                 :href="dokumen.link"
                 target="_blank"
                 class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
              >
                View External Link
              </a>
            </div> -->

            <!-- Back Button -->
            <div class="mt-8">
              <Link href="/produk-hukum-test" class="text-accent1 hover:text-accent2 transition-color duration-300 font-medium">
                ← Back to Daftar Produk Hukum
              </Link>
            </div>
        </div>

        <div class="mt-12">
          <Comment :slug="props.slug" :news="dokumen" comment-api-base="/api/produk-hukum-comments" />
        </div>

      </div>
    </div>
  </UserLayout>
</template>

<script setup>
import UserLayout from '@/Layouts/UserLayout.vue';
import { ref, onMounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import axios from 'axios';
import Comment from '@/Components/Comment.vue';

const props = defineProps({
  id: {
    type: String,
    required: true
  },
  slug: {
    type: String,
    required: true
  }
});

const dokumen = ref({});
const loading = ref(true);
const error = ref(null);

const comments = ref([]);
const submitting = ref(false);
const activeReplyId = ref(null);
const mainCaptchaData = ref(null);
const replyCaptchaData = ref(null);
const mainCaptchaCode = ref('');
const replyCaptchaCode = ref('');
const commentForm = ref({
  nama_user: '',
  komentar: '',
  id_produk_hukum: props.id,
  captcha: ''
});
const replyForm = ref({
  nama_user: '',
  komentar: '',
  id_produk_hukum: props.id,
  id_ref_komentar: null,
  captcha: ''
});

// Home data
// const home = ref({});
// const homeLoading = ref(false);

// const fetchHome = async () => {
//     try {
//         homeLoading.value = true;
//         const response = await axios.get("/api/home");
//         home.value = response.data;
//     } catch (error) {
//         console.error("Error fetching home data:", error);
//     } finally {
//         homeLoading.value = false;
//     }
// };
const formatDate = (date) => {
  return new Date(date).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
};

const isPreviewable = (filePath) => {
  const previewableExtensions = ['.pdf', '.jpg', '.jpeg', '.png'];
  const fileExtension = filePath.split('.').pop().toLowerCase();
  return previewableExtensions.includes(`.${fileExtension}`);
};

const getAvatar = (name) => {
  return `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=random`;
};

const truncateText = (text, length) => {
  if (!text) return '';
  return text.length > length ? text.substring(0, length) + '...' : text;
};

const fetchDokumen = async () => {
  try {
    loading.value = true;
    const response = await axios.get(`/api/produk-hukum/${props.slug}`);
    dokumen.value = response.data.data;
  } catch (err) {
    error.value = 'Gagal memuat dokumen';
    console.error('Error fetching document:', err);
  } finally {
    loading.value = false;
  }
};

const fetchComments = async () => {
  try {
    const response = await axios.get(`/api/produk-hukum-comments/${props.id}`);
    comments.value = response.data.filter(comment => !comment.id_ref_komentar).map(comment => ({
      ...comment,
      showReplies: false,
      replies: []
    }));
  } catch (err) {
    console.error('Error fetching comments:', err);
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
    const response = await axios.post('/api/produk-hukum-comments', {
      ...commentForm.value,
      code: mainCaptchaCode.value
    });
    
    commentForm.value.komentar = '';
    commentForm.value.nama_user = '';
    commentForm.value.captcha = '';
    
    if (!dokumen.value.is_moderate) {
      await fetchComments();
    }
    
    alert(response.data.message);
  } catch (err) {
    console.error('Error submitting comment:', err);
    commentForm.value.captcha = ''; // Clear captcha input
    alert(err.response?.data?.message || 'Gagal mengirim komentar');
  } finally {
    submitting.value = false;
    refreshCaptcha('main'); // Always refresh captcha after submit attempt
  }
};

const showReplyForm = (commentId) => {
  activeReplyId.value = commentId;
  replyForm.value = {
    nama_user: '',
    komentar: '',
    id_produk_hukum: props.id,
    id_ref_komentar: commentId,
    captcha: ''
  };
  refreshCaptcha('reply');
};

const cancelReply = () => {
  activeReplyId.value = null;
  replyForm.value = {
    nama_user: '',
    komentar: '',
    id_produk_hukum: props.id,
    id_ref_komentar: null,
    captcha: ''
  };
};

const submitReply = async (commentId) => {
  try {
    submitting.value = true;
    const response = await axios.post('/api/produk-hukum-comments', {
      ...replyForm.value,
      code: replyCaptchaCode.value
    });
    
    if (!dokumen.value.is_moderate) {
      const comment = comments.value.find(c => c.id === replyForm.value.id_ref_komentar);
      if (comment && comment.showReplies) {
        await loadReplies(comment);
      } else {
        await fetchComments();
      }
    }
    
    cancelReply();
    alert(response.data.message);
  } catch (err) {
    console.error('Error submitting reply:', err);
    replyForm.value.captcha = ''; // Clear captcha input
    alert(err.response?.data?.message || 'Gagal mengirim balasan');
  } finally {
    submitting.value = false;
    refreshCaptcha('reply'); // Always refresh captcha after submit attempt
  }
};

const loadReplies = async (comment) => {
  if (comment.showReplies) {
    comment.showReplies = false;
    comment.replies = [];
    return;
  }

  try {
    const response = await axios.get(
      `${props.commentApiBase}/${props.slug}/replies/${comment.id}`
    );

    // Build reply tree (same as in Comment.vue)
    comment.replies = [];
    const repliesMap = new Map();
    response.data.forEach((reply) => {
      repliesMap.set(reply.id, { ...reply, replies: [] });
    });

    response.data.forEach((reply) => {
      const replyObj = repliesMap.get(reply.id);
      if (reply.id_ref_komentar === comment.id) {
        comment.replies.push(replyObj);
      } else {
        const parentReply = repliesMap.get(reply.id_ref_komentar);
        if (parentReply) {
          parentReply.replies.push(replyObj);
        }
      }
    });

    comment.showReplies = true;
  } catch (err) {
    console.error('Error loading replies:', err);
    alert('Gagal memuat balasan');
  }
};

const getAllDeeperReplies = (reply) => {
  let allReplies = [];
  
  const collectReplies = (r) => {
    if (r.replies && Array.isArray(r.replies)) {
      r.replies.forEach(nestedReply => {
        allReplies.push(nestedReply);
        collectReplies(nestedReply);
      });
    }
  };
  
  collectReplies(reply);
  return allReplies;
};

onMounted(() => {
  fetchDokumen();
  fetchComments();
  refreshCaptcha('main');
});
</script>