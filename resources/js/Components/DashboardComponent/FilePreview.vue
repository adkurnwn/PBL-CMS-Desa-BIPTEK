<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 overflow-y-auto">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="closeModal"></div>
    
    <!-- Modal -->
    <div class="flex min-h-full items-center justify-center p-2 sm:p-4 lg:p-6" style="padding-top: 20px; padding-bottom: 20px;">
      <div class="relative w-full max-w-sm sm:max-w-md md:max-w-lg lg:max-w-2xl xl:max-w-4xl 2xl:max-w-5xl 3xl:max-w-6xl 4xl:max-w-7xl bg-white rounded-[10px] shadow-xl transform transition-all flex flex-col" style="height: calc(100vh - 80px);">
        
        <!-- Content -->
        <div class="flex-1 flex flex-col">
          <div v-if="isPdf" class="w-full h-full flex flex-col">
            <!-- PDF Preview -->
            <div class="bg-gray-100 rounded-lg p-2 sm:p-3 md:p-3 lg:p-3 xl:p-4 text-center flex-shrink-0">
              <h4 class="text-sm sm:text-base md:text-base lg:text-base xl:text-lg font-medium text-gray-900 mb-2 flex items-center justify-center gap-2">
                <svg class="h-4 w-4 sm:h-5 sm:w-5 md:h-5 md:w-5 lg:h-5 lg:w-5 xl:h-6 xl:w-6 text-red-600" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M8.267 14.68c-.184 0-.308.018-.372.036v1.178c.076.018.171.023.302.023.479 0 .774-.242.774-.651 0-.366-.254-.586-.704-.586zm3.487.012c-.2 0-.33.018-.407.036v2.61c.077.018.201.018.313.018.817.006 1.349-.444 1.349-1.396.006-.83-.479-1.268-1.255-1.268z"/>
                  <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zM9.498 16.19c-.309.29-.765.42-1.296.42a2.23 2.23 0 0 1-.308-.018v1.426H7v-3.936A7.558 7.558 0 0 1 8.219 14c.557 0 .953.106 1.22.319.254.202.426.533.426.923-.001.392-.131.723-.367.948zm3.807 1.355c-.42.349-1.059.515-1.84.515-.468 0-.799-.03-1.024-.06v-3.917A7.947 7.947 0 0 1 11.66 14c.757 0 1.249.136 1.633.426.415.308.675.799.675 1.504 0 .763-.279 1.29-.663 1.615zM17 14.77h-1.532v.911H16.9v.734h-1.432v1.604h-.906V14.03H17v.74zM14 9h-1V4l5 5h-4z"/>
                </svg>
                <span class="truncate">Preview file: {{ fileName }}</span>
              </h4>
            </div>
            
            <!-- PDF Embed - Takes remaining space -->
            <div class="flex-1 bg-gray-100 px-2 sm:px-3 md:px-3 lg:px-3 xl:px-4 pb-2 sm:pb-3 md:pb-3 lg:pb-3 xl:pb-4">
              <div class="bg-white border rounded-lg overflow-hidden h-full">
                <iframe
                  :src="fileUrl + '#toolbar=1&navpanes=1&scrollbar=1'"
                  class="w-full h-full border-0"
                  type="application/pdf"
                >
                  <p class="p-4 text-center text-xs sm:text-sm xl:text-base">
                    Browser anda tidak mendukung preview PDF. 
                    <a :href="fileUrl" target="_blank" class="text-blue-600 hover:text-blue-800 underline">
                      Klik di sini untuk mengunduh file PDF.
                    </a>
                  </p>
                </iframe>
              </div>
            </div>
          </div>
          
          <!-- Not supported file type -->
          <div v-else class="flex-1 flex flex-col items-center justify-center text-center py-4 sm:py-6 xl:py-8 px-4">
            <svg class="mx-auto h-12 w-12 sm:h-14 sm:w-14 xl:h-16 xl:w-16 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
              <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zM16 18H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>
            </svg>
            <h4 class="text-base sm:text-base xl:text-lg font-medium text-gray-900 mb-2">Preview Not Available</h4>
            <p class="text-sm sm:text-base xl:text-base text-gray-600 mb-4">Preview is only available for PDF files.</p>
          </div>
        </div>
        
        <!-- Footer -->
        <div class="flex flex-col sm:flex-row items-center justify-end px-3 sm:px-4 xl:px-6 py-3 xl:py-4 border-t border-gray-200 space-y-2 sm:space-y-0 sm:space-x-3 flex-shrink-0">
          <a
            :href="fileUrl"
            target="_blank"
            class="no-underline w-full sm:w-auto"
          >
            <PrimaryButton :icon="true" class="w-full sm:w-auto">
              <template #icon>
              </template>
              Unduh
            </PrimaryButton>
          </a>
          <SecondaryButton @click="closeModal" class="w-full sm:w-auto">
            Close
          </SecondaryButton>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import SecondaryButton from './SecondaryButton.vue';
import PrimaryButton from './PrimaryButton.vue';

export default {
  name: 'FilePreview',
  components: {
    SecondaryButton,
    PrimaryButton
  },
  props: {
    isOpen: {
      type: Boolean,
      default: false
    },
    fileUrl: {
      type: String,
      required: true
    },
    fileName: {
      type: String,
      required: true
    }
  },
  computed: {
    isPdf() {
      return this.fileName.toLowerCase().endsWith('.pdf');
    }
  },
  methods: {
    closeModal() {
      this.$emit('close');
    }
  },
  emits: ['close']
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
</style>
