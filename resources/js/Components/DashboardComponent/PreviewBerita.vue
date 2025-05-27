<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 overflow-y-auto">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="closeModal"></div>
    
    <!-- Modal -->
    <div class="flex min-h-full items-center justify-center p-2 sm:p-4 lg:p-6" style="padding-top: 20px; padding-bottom: 20px;">
      <div class="relative w-full max-w-sm sm:max-w-md md:max-w-lg lg:max-w-2xl xl:max-w-4xl 2xl:max-w-5xl 3xl:max-w-6xl 4xl:max-w-7xl bg-type1 rounded-[10px] shadow-xl transform transition-all flex flex-col" style="height: calc(100vh - 80px);">
        
        <!-- Header -->
        <div class="flex items-center justify-between px-3 sm:px-4 xl:px-6 py-3 xl:py-4 border-b border-gray-200 flex-shrink-0 bg-white rounded-t-[10px]">
          <div class="flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-selectedText" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
            <h3 class="text-base sm:text-lg xl:text-xl font-semibold text-gray-900">Preview Berita</h3>
          </div>
          <button @click="closeModal" class="text-gray-400 hover:text-gray-600 transition-colors">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Content - Fixed scaling container -->
        <div class="flex-1 overflow-hidden font-merriweather">
          <div class="w-full h-full overflow-auto" style="transform: scale(0.5); transform-origin: top left; width: 200%; height: 200%;">
            <div class="px-8 lg:px-16 py-12" style="min-height: 200vh;">
              <!-- Category Header -->
              <div class="flex space-x-4 items-center mb-12">
                <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px" fill="currentColor" class="text-accent2">
                  <path d="M280-280q17 0 28.5-11.5T320-320q0-17-11.5-28.5T280-360q-17 0-28.5 11.5T240-320q0 17 11.5 28.5T280-280Zm-40-160h80v-240h-80v240Zm200 160h280v-80H440v80Zm0-160h280v-80H440v80Zm0-160h280v-80H440v80ZM160-120q-33 0-56.5-23.5T80-200v-560q0-33 23.5-56.5T160-840h640q33 0 56.5 23.5T880-760v560q0 33-23.5 56.5T800-120H160Zm0-80h640v-560H160v560Zm0 0v-560 560Z"/>
                </svg>
                <h1 class="text-2xl text-accent2 -mb-1 font-merriweather">
                  Berita {{ categoryName || 'Kategori' }}
                </h1>
              </div>

              <!-- Main Content Layout -->
              <div class="flex flex-col lg:flex-row">
                <!-- News Content (2/3 width) -->
                <div class="flex flex-col w-full lg:w-2/3">
                  <div class="flex flex-col bg-white rounded-lg shadow overflow-hidden mx-auto p-10 lg:p-12">
                    <!-- Title -->
                    <h1 class="flex text-2xl lg:text-6xl font-bold text-accent1 lg:mb-8 font-merriweather">
                      {{ newsData.title || 'Judul Berita' }}
                    </h1>

                    <!-- Metadata -->
                    <div class="flex items-center text-accent2 text-2xl mt-4 lg:mt-8 mb-8 lg:mb-16 space-x-6 lg:space-x-12">
                      <!-- Date -->
                      <div class="flex items-center space-x-4">
                        <svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="currentColor" class="text-accent2 lg:w-12 lg:h-12">
                          <path d="M200-80q-33 0-56.5-23.5T120-160v-560q0-33 23.5-56.5T200-800h40v-80h80v80h320v-80h80v80h40q33 0 56.5 23.5T840-720v560q0 33-23.5 56.5T760-80H200Zm0-80h560v-400H200v400Zm0-480h560v-80H200v80Zm0 0v-80 80Zm80 240v-80h400v80H280Zm0 160v-80h280v80H280Z"/>
                        </svg>
                        <h3 class="flex lg:-mb-2 text-lg lg:text-2xl">
                          {{ formatDate(new Date()) }}
                        </h3>
                      </div>
                      
                      <!-- Author -->
                      <div class="flex items-center space-x-4">
                        <svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="currentColor" class="text-accent2 lg:w-12 lg:h-12">
                          <path d="M480-480q-66 0-113-47t-47-113q0-66 47-113t113-47q66 0 113 47t47 113q0 66-47 113t-113 47ZM160-160v-112q0-34 17.5-62.5T224-378q62-31 126-46.5T480-440q66 0 130 15.5T736-378q29 15 46.5 43.5T800-272v112H160Zm80-80h480v-32q0-11-5.5-20T700-306q-54-27-109-40.5T480-360q-56 0-111 13.5T260-306q-9 5-14.5 14t-5.5 20v32Zm240-320q33 0 56.5-23.5T560-640q0-33-23.5-56.5T480-720q-33 0-56.5 23.5T400-640q0 33 23.5 56.5T480-560Zm0-80Zm0 400Z"/>
                        </svg>
                        <h3 class="flex lg:-mb-2 text-lg lg:text-2xl">
                          Admin
                        </h3>
                      </div>

                      <!-- Views -->
                      <div class="flex items-center space-x-4">
                        <svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="currentColor" class="text-accent2 lg:w-12 lg:h-12">
                          <path d="M480-320q75 0 127.5-52.5T660-500q0-75-52.5-127.5T480-680q-75 0-127.5 52.5T300-500q0 75 52.5 127.5T480-320Zm0-72q-45 0-76.5-31.5T372-500q0-45 31.5-76.5T480-608q45 0 76.5 31.5T588-500q0 45-31.5 76.5T480-392Zm0 192q-146 0-266-81.5T40-500q54-137 174-218.5T480-800q146 0 266 81.5T920-500q-54 137-174 218.5T480-200Zm0-300Zm0 220q113 0 207.5-59.5T832-500q-50-101-144.5-160.5T480-720q-113 0-207.5 59.5T128-500q50 101 144.5 160.5T480-280Z"/>
                        </svg>
                        <h3 class="flex lg:-mb-2 text-lg lg:text-2xl">
                          0 views
                        </h3>
                      </div>
                    </div>

                    <!-- Featured image - first image shown at the top -->
                    <div v-if="newsData.images && newsData.images.length > 0" class="relative min-h-96 lg:min-h-[48rem] mb-12">
                      <img 
                        v-if="getImageUrl(newsData.images[0])"
                        :src="getImageUrl(newsData.images[0])" 
                        :alt="getImageAlt(0)"
                        class="w-full h-full object-cover rounded-lg"
                        @error="handleImageError"
                      />
                      <div v-else class="w-full h-full flex items-center justify-center bg-gray-100 rounded-lg">
                        <div class="text-center text-gray-500">
                          <svg class="mx-auto h-24 w-24 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                          </svg>
                          <p class="text-lg">Preview not available</p>
                        </div>
                      </div>
                    </div>

                    <!-- Content -->
                    <div class="pt-0 lg:pt-4 pb-4">
                      <div class="text-lg lg:text-2xl text-semiBlack prose prose-lg max-w-none">
                        <div 
                          v-if="newsData.content"
                          class="leading-relaxed prose-content"
                          v-html="processedContentWithImages"
                        ></div>
                        
                        <div v-else class="text-gray-500 italic">
                          Konten berita akan ditampilkan di sini...
                        </div>
                      </div>
                    </div>

                    <!-- Video if any -->
                    <div v-if="newsData.video" class="mt-12">
                      <h3 class="text-2xl font-semibold mb-6">Video</h3>
                      <div class="bg-gray-100 rounded-lg p-8">
                        <div class="flex items-center gap-6">
                          <svg class="h-16 w-16 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                          </svg>
                          <div>
                            <p class="font-medium text-gray-900 text-xl">Video File</p>
                            <p class="text-lg text-gray-600">{{ newsData.video.name }}</p>
                            <p class="text-lg text-gray-500 mt-2">{{ formatFileSize(newsData.video.size) }}</p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Aside (1/3 width) -->
                <div class="flex flex-col w-full lg:w-1/3 px-2 lg:px-8 my-6 lg:my-0 font-merriweather">
                  <div class="flex bg-accent1 shadow-md rounded-sm p-6 space-x-4">
                    <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px" fill="currentColor" class="text-type1">
                      <path d="M160-120q-33 0-56.5-23.5T80-200v-280h80v280h360v80H160Zm160-160q-33 0-56.5-23.5T240-360v-280h80v280h360v80H320Zm160-160q-33 0-56.5-23.5T400-520v-240q0-33 23.5-56.5T480-840h320q33 0 56.5 23.5T880-760v240q0 33-23.5 56.5T800-440H480Zm0-80h320v-160H480v160Z"/>
                    </svg>
                    <h2 class="text-2xl font-semibold text-type1">Berita Lainnya</h2>
                  </div>

                  <!-- Tabs -->
                  <div class="flex flex-row bg-white w-full">
                    <button class="flex flex-col w-full bg-gray-200 hover:opacity-60 shadow-md p-2">
                      <h3 class="text-xl text-accent1">Terkini</h3>
                    </button>
                    <button class="flex flex-col w-full bg-white hover:opacity-60 shadow-md p-2">
                      <h3 class="text-xl text-accent1">Populer</h3>
                    </button>
                  </div>

                  <!-- Sample Related News -->
                  <div class="flex flex-col w-full bg-white shadow-md px-6">
                    <div class="flex flex-col w-full bg-white p-2">
                      <div class="flex flex-col">
                        <!-- Sample related news items -->
                        <div v-for="i in 3" :key="i" class="flex flex-row py-6 gap-4 hover:bg-gray-200 hover:shadow-sm rounded-md transition duration-300 ease-in-out">
                          <div class="flex w-1/2">
                            <div class="w-full h-40 bg-gray-200 rounded-md flex items-center justify-center">
                              <svg class="h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                              </svg>
                            </div>
                          </div>
                          <div class="flex flex-col w-full">
                            <h3 class="text-lg font-semibold text-semiBlack line-clamp-3">
                              Contoh Berita Terkait {{ i }}
                            </h3>
                            <p class="text-lg text-gray-500">
                              {{ formatDate(new Date()) }}
                            </p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Footer -->
        <div class="flex flex-col sm:flex-row items-center justify-end px-3 sm:px-4 xl:px-6 py-3 xl:py-4 border-t border-gray-200 space-y-2 sm:space-y-0 sm:space-x-3 flex-shrink-0 bg-white rounded-b-[10px]">
          <SecondaryButton @click="closeModal" class="w-full sm:w-auto">
            Tutup Preview
          </SecondaryButton>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import SecondaryButton from "@/Components/DashboardComponent/SecondaryButton.vue";

export default {
  name: 'PreviewBerita',
  components: {
    SecondaryButton
  },
  props: {
    isOpen: {
      type: Boolean,
      default: false
    },
    newsData: {
      type: Object,
      default: () => ({})
    },
    categories: {
      type: Array,
      default: () => []
    }
  },
  computed: {
    categoryName() {
      if (!this.newsData.category?.category_id) return 'Kategori';
      
      const category = this.categories.find(cat => cat.id == this.newsData.category.category_id);
      return category?.name || this.newsData.category.new_category || 'Kategori';
    },
    processedContentWithImages() {
      if (!this.newsData.content) return '';
      
      let content = this.newsData.content;
      let imageIndex = 0;
      
      // Replace blob URLs with actual image previews
      content = content.replace(/<img[^>]*src="blob:[^"]*"[^>]*>/g, (match) => {
        if (this.newsData.images && this.newsData.images[imageIndex]) {
          const imageUrl = this.getImageUrl(this.newsData.images[imageIndex]);
          const altText = this.getImageAlt(imageIndex);
          
          if (imageUrl) {
            const imageHtml = `
              <div class="my-4">
                <img 
                  src="${imageUrl}" 
                  alt="${altText}" 
                  class="w-full h-auto rounded-lg shadow-sm"
                  style="max-height: 400px; object-fit: contain;"
                />
                ${altText ? `<p class="text-sm text-gray-600 italic text-center mt-2">${altText}</p>` : ''}
              </div>
            `;
            imageIndex++;
            return imageHtml;
          }
        }
        
        // Fallback placeholder
        const placeholder = `
          <div class="my-4 bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg p-8">
            <div class="text-center text-gray-500">
              <svg class="mx-auto h-12 w-12 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
              </svg>
              <p class="text-sm">Gambar akan muncul setelah berita diterbitkan</p>
            </div>
          </div>
        `;
        imageIndex++;
        return placeholder;
      });
      
      // Replace any remaining placeholders with actual images
      content = content.replace(/\{\{IMAGE_(\d+)\}\}/g, (match, index) => {
        const imgIndex = parseInt(index);
        if (this.newsData.images && this.newsData.images[imgIndex]) {
          const imageUrl = this.getImageUrl(this.newsData.images[imgIndex]);
          const altText = this.getImageAlt(imgIndex);
          
          if (imageUrl) {
            return `
              <div class="my-4">
                <img 
                  src="${imageUrl}" 
                  alt="${altText}" 
                  class="w-full h-auto rounded-lg shadow-sm"
                  style="max-height: 400px; object-fit: contain;"
                />
                ${altText ? `<p class="text-sm text-gray-600 italic text-center mt-2">${altText}</p>` : ''}
              </div>
            `;
          }
        }
        
        return `
          <div class="my-4 bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg p-8">
            <div class="text-center text-gray-500">
              <svg class="mx-auto h-12 w-12 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
              </svg>
              <p class="text-sm">Gambar akan muncul setelah berita diterbitkan</p>
            </div>
          </div>
        `;
      });
      
      return content;
    }
  },
  methods: {
    closeModal() {
      this.$emit('close');
    },
    formatDate(date) {
      return new Intl.DateTimeFormat('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      }).format(date);
    },
    getImageUrl(image) {
      try {
        if (!image) return null;
        
        // Check if it's a File object (from form input)
        if (image instanceof File) {
          return window.URL.createObjectURL(image);
        }
        
        // Check if it's a string (existing image path)
        if (typeof image === 'string') {
          return image;
        }
        
        return null;
      } catch (error) {
        console.warn('Error creating image URL:', error);
        return null;
      }
    },
    getImageAlt(index) {
      if (this.newsData.alts && this.newsData.alts[index]) {
        return this.newsData.alts[index];
      }
      return `Image ${index + 1}`;
    },
    handleImageError(event) {
      console.warn('Image failed to load:', event.target.src);
      // Hide the broken image
      event.target.style.display = 'none';
      // Show placeholder in parent container
      const container = event.target.parentElement;
      if (container) {
        container.innerHTML = `
          <div class="w-full h-full flex items-center justify-center bg-gray-100">
            <div class="text-center text-gray-500">
              <svg class="mx-auto h-12 w-12 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              <p class="text-sm">Image preview failed</p>
            </div>
          </div>
        `;
      }
    },
    formatFileSize(bytes) {
      if (!bytes) return '0 Bytes';
      
      const k = 1024;
      const sizes = ['Bytes', 'KB', 'MB', 'GB'];
      const i = Math.floor(Math.log(bytes) / Math.log(k));
      
      return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
  },
  emits: ['close'],
  beforeUnmount() {
    // Clean up any object URLs when component is destroyed
    if (this.newsData.images) {
      this.newsData.images.forEach(image => {
        if (image instanceof File) {
          try {
            const url = this.getImageUrl(image);
            if (url) {
              window.URL.revokeObjectURL(url);
            }
          } catch (error) {
            // Ignore cleanup errors
          }
        }
      });
    }
  }
}
</script>

<style scoped>
/* Custom scrollbar for the modal */
::-webkit-scrollbar {
  width: 6px;
}

::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 3px;
}

::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
  background: #a1a1a1;
}

/* Ensure proper styling for dynamic content */
:deep(.prose-content img) {
  max-width: 100%;
  height: auto;
  border-radius: 16px;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
}

:deep(.prose-content h1, .prose-content h2, .prose-content h3) {
  color: #1f2937;
}

:deep(.prose-content p) {
  margin-bottom: 1rem;
}

:deep(.prose-content ul, .prose-content ol) {
  padding-left: 1.5rem;
}

:deep(.prose-content blockquote) {
  border-left: 4px solid #e5e7eb;
  padding-left: 1rem;
  margin: 1.5rem 0;
  background-color: #f9fafb;
  padding: 1rem;
  border-radius: 4px;
}

/* Style for image placeholders - scaled up */
:deep(.prose-content .my-4) {
  margin: 2rem 0;
}

/* Ensure images are responsive and centered - scaled up */
:deep(.prose-content img) {
  display: block;
  margin-left: auto;
  margin-right: auto;
  max-height: 800px;
  object-fit: contain;
}

/* Line clamp utility */
.line-clamp-3 {
  overflow: hidden;
  display: -webkit-box;
  -webkit-box-orient: vertical;
  -webkit-line-clamp: 3;
}
</style>
