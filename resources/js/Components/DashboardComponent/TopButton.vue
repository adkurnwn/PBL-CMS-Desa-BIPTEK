<template>
  <button class="inline-flex items-center justify-center 
               h-[27px] sm:h-[30px] md:h-[35px] xl:h-[29px] 2xl:h-[35px] 3xl:h-[38px] 4xl:h-[40px]
               min-w-[35px] sm:min-w-[40px] md:min-w-[45px] xl:min-w-[50px] 2xl:min-w-[55px] 3xl:min-w-[65px] 4xl:min-w-[75px]
               rounded-[6px] sm:rounded-[7px] md:rounded-[8px] xl:rounded-[7px] 2xl:rounded-[8px] 3xl:rounded-[9px] 4xl:rounded-[10px] 
               bg-selectedText 
               px-[9px] sm:px-[10px] md:px-[11px] xl:px-[9px] 2xl:px-[12px] 3xl:px-[15px] 4xl:px-[19px] 
               py-[6px] sm:py-[7px] md:py-[8px] xl:py-[6px] 2xl:py-[8px] 3xl:py-[9px] 4xl:py-[11px] 
               text-[10px] sm:text-[11px] md:text-[12px] xl:text-[10px] 2xl:text-[12px] 3xl:text-[13px] 4xl:text-[15px] 
               font-medium text-white transition duration-150 ease-in-out hover:bg-opacity-90 active:bg-opacity-80">
    <!-- Icon based on type -->
    <span class="w-[13px] sm:w-[13px] md:w-[13px] xl:w-[13px] 2xl:w-[15px] 3xl:w-[18px] 4xl:w-[20px] 
                 h-[13px] sm:h-[13px] md:h-[13px] xl:h-[13px] 2xl:h-[15px] 3xl:h-[18px] 4xl:h-[20px] 
                 flex-shrink-0 flex items-center justify-center">
      <!-- Default "Tambah" icon -->
      <svg v-if="type === 'Tambah'" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 14 14" fill="none">
        <mask id="mask0_1540_150" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="15" height="14">
          <rect x="0.666992" y="0.666016" width="13.3333" height="13.3333" fill="#D9D9D9" />
        </mask>
        <g mask="url(#mask0_1540_150)">
          <path
            d="M6.77767 7.8878H3.44434V6.77669H6.77767V3.44336H7.88878V6.77669H11.2221V7.8878H7.88878V11.2211H6.77767V7.8878Z"
            fill="white" />
        </g>
      </svg>
      
      <!-- "Diarsipkan" icon -->
      <svg v-else-if="type === 'Diarsipkan'" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 -960 960 960" fill="white">
        <path d="m480-240 160-160-56-56-64 64v-168h-80v168l-64-64-56 56 160 160ZM200-640v440h560v-440H200Zm0 520q-33 0-56.5-23.5T120-200v-499q0-14 4.5-27t13.5-24l50-61q11-14 27.5-21.5T250-840h460q18 0 34.5 7.5T772-811l50 61q9 11 13.5 24t4.5 27v499q0 33-23.5 56.5T760-120H200Zm16-600h528l-34-40H250l-34 40Zm264 300Z"/>
      </svg>
    </span>
    
    <!-- Text based on type -->
    <span v-if="showText" class="ml-1 sm:ml-1 md:ml-1.5 xl:ml-1 2xl:ml-1.5 3xl:ml-1.5 4xl:ml-2 
                 whitespace-nowrap
                 max-sm:hidden max-md:hidden max-xl:hidden">
      {{ type }}
    </span>
  </button>
</template>

<script setup>
defineProps({
  /**
   * Button type that determines the icon and text
   * Available options: 'Tambah', 'Diarsipkan'
   * Default is 'Tambah'
   */
  type: {
    type: String,
    default: 'Tambah',
    validator: (value) => ['Tambah', 'Diarsipkan'].includes(value)
  },
  /**
   * Whether to show the text label
   * Default is true
   */
  showText: {
    type: Boolean,
    default: true
  }
});
</script>
