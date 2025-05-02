<template>
  <UserLayout>
    <div class="min-h-screen bg-gray-100">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Loading State -->
        <div v-if="loading" class="flex justify-center items-center py-12">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="text-center py-12">
          <p class="text-red-600">{{ error }}</p>
        </div>

        <!-- Document Detail -->
        <div v-else class="bg-white rounded-lg shadow-lg overflow-hidden">
          <div class="p-8">
            <!-- Category Badge -->
            <span v-if="dokumen.category" 
                  class="inline-block px-3 py-1 text-sm font-semibold text-blue-600 bg-blue-100 rounded-full mb-4">
              {{ dokumen.category.name }}
            </span>

            <!-- Title -->
            <h1 class="text-3xl font-bold text-gray-900 mb-4">
              {{ dokumen.title }}
            </h1>

            <!-- Metadata -->
            <div class="flex items-center text-sm text-gray-500 mb-8">
              <span>{{ formatDate(dokumen.date) }}</span>
            </div>

            <!-- Description -->
            <div class="prose max-w-none mb-8">
              {{ dokumen.description }}
            </div>

            <!-- Document Actions -->
            <div class="flex space-x-4">
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
            </div>

            <!-- Back Button -->
            <div class="mt-8">
              <Link href="/produk-hukum-test" class="text-blue-600 hover:text-blue-800 font-medium">
                ← Back to Documents List
              </Link>
            </div>
          </div>
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
                            <!-- Add Show Replies button if has replies -->
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

                          <!-- Reply Form for Level 2 -->
                          <div v-if="activeReplyId === reply.id" class="mt-4 ml-8">
                            <form @submit.prevent="submitReply(reply.id)" class="space-y-4">
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
                                  {{ submitting ? 'Mengirim...' : 'Kirim' }}
                                </button>
                                <button 
                                  type="button"
                                  @click="cancelReply"
                                  class="text-gray-600 px-4 py-2 rounded text-sm hover:bg-gray-100"
                                >
                                  Cancel
                                </button>
                              </div>
                            </form>
                          </div>

                          <!-- Nested Replies (Level 3+) -->
                          <div v-if="reply.replies?.length" class="mt-4 space-y-4">
                            <div v-for="deepReply in getAllDeeperReplies(reply)" :key="deepReply.id" 
                                 class="ml-8 bg-white p-3 rounded border border-gray-100">
                              <!-- Reply Content with Referenced Comment -->
                              <div class="flex gap-3">
                                <img :src="getAvatar(deepReply.nama_user)" alt="User Avatar" class="w-8 h-8 rounded-full">
                                <div class="flex-1">
                                  <div class="flex items-center gap-2">
                                    <div class="flex items-center gap-1">
                                      <div class="font-semibold">{{ deepReply.nama_user }}</div>
                                      <div class="font-light italic text-gray-400">membalas</div>
                                      <div class="font-semibold">{{ deepReply.parent_user }}</div>
                                      <div class="text-gray-400 italic text-sm">
                                        ("{{ truncateText(deepReply.parent?.komentar, 30) }}")
                                      </div>
                                    </div>
                                  </div>
                                  <div class="text-gray-600 mt-2">{{ deepReply.komentar }}</div>
                                  <div class="flex items-center justify-between mt-2">
                                    <div class="text-sm text-gray-500">
                                      {{ formatDate(deepReply.created_at) }}
                                    </div>
                                    <button 
                                      @click="showReplyForm(deepReply.id)" 
                                      class="text-blue-600 text-sm hover:text-blue-800"
                                    >
                                      Balas
                                    </button>
                                  </div>
                                </div>
                              </div>

                              <!-- Reply Form -->
                              <div v-if="activeReplyId === deepReply.id" class="mt-4">
                                <form @submit.prevent="submitReply(deepReply.id)" class="space-y-4">
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
                                      {{ submitting ? 'Mengirim...' : 'Kirim Balasan' }}
                                    </button>
                                    <button 
                                      type="button"
                                      @click="cancelReply"
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
  </UserLayout>
</template>

<script setup>
import UserLayout from '@/Layouts/UserLayout.vue';
import { ref, onMounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import axios from 'axios';

const props = defineProps({
  id: {
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

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
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
    const response = await axios.get(`/api/produk-hukum/${props.id}`);
    dokumen.value = response.data;
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
      `/api/produk-hukum-comments/${props.id}/replies/${comment.id}`
    );
    
    comment.replies = response.data;
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