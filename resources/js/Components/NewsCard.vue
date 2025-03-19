<template>
  <!-- <div class="news-card">
    <img :src="image" alt="News Image" class="news-card__image" />
    <div class="news-card__content">
      <h2 class="news-card__title">{{ title }}</h2>
      <p class="news-card__description">{{ description }}</p>
    </div>
  </div> -->
  <div class="w-full p-2 ">
    <div class="flex flex-col bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-lg transition-colors duration-300 h-">
        <div class="flex">
          <img src="../../images/berita.png" alt="News Image" class="w-full object-cover p-4" />
        </div>      
        <div class="flex flex-col px-2">
          <div class="flex flex-row p-4 pt-1 space-x-2">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="currentColor" class="text-type2">
              <path d="M480-480q-66 0-113-47t-47-113q0-66 47-113t113-47q66 0 113 47t47 113q0 66-47 113t-113 47ZM160-160v-112q0-34 17.5-62.5T224-378q62-31 126-46.5T480-440q66 0 130 15.5T736-378q29 15 46.5 43.5T800-272v112H160Zm80-80h480v-32q0-11-5.5-20T700-306q-54-27-109-40.5T480-360q-56 0-111 13.5T260-306q-9 5-14.5 14t-5.5 20v32Zm240-320q33 0 56.5-23.5T560-640q0-33-23.5-56.5T480-720q-33 0-56.5 23.5T400-640q0 33 23.5 56.5T480-560Zm0-80Zm0 400Z"/></svg>
            <h2 class="pr-2 text-sm font-thin text-type2 mt-0.5 ">M.Andi Sulaiman</h2>
          </div>
          <div>
            <h2 class="px-4 text-lg font-semibold text-type2 line-clamp-2">Pengukuhan Kader Posyandu Desa Pao-Pao Periode 2025-2030</h2>
            <p class="pt-2 px-4 text-sm font-thin text-type2 line-clamp-3 overflow-hidden text-ellipsis text-justify">Kepala Desa Pao-Pao, Kecamatan Tanete Rilau, Kabupaten Barru, Sulawesi Selatan SYAMSUL BAHRI, S.E., M.M. melakukan penandatanganan kerjasama yang signifikan antara Ikatan fhujediwshnfcujdewsnfhc hjfujedsiwhnfcddnb</p>
          </div>
          <div class="flex flex-row justify-between p-3 py-5">
            <h2 class="text-xs">5 March 2025</h2>
            <div class="flex flex-row space-x-3 mr-2">
              <div class="flex flex-row space-x-2 justify-center items-center"> 
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="currentColor" class="text-semiBlack">
                  <path d="M480-320q75 0 127.5-52.5T660-500q0-75-52.5-127.5T480-680q-75 0-127.5 52.5T300-500q0 75 52.5 127.5T480-320Zm0-72q-45 0-76.5-31.5T372-500q0-45 31.5-76.5T480-608q45 0 76.5 31.5T588-500q0 45-31.5 76.5T480-392Zm0 192q-146 0-266-81.5T40-500q54-137 174-218.5T480-800q146 0 266 81.5T920-500q-54 137-174 218.5T480-200Zm0-300Zm0 220q113 0 207.5-59.5T832-500q-50-101-144.5-160.5T480-720q-113 0-207.5 59.5T128-500q50 101 144.5 160.5T480-280Z"/>
                </svg>
                <h2 class="text-xs">0</h2>
              </div>
              <div class="flex flex-row space-x-2 justify-center items-center">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="currentColor" class="text-semiBlack">
                  <path d="M320-400h320v-22q0-44-44-71t-116-27q-72 0-116 27t-44 71v22Zm160-160q33 0 56.5-23.5T560-640q0-33-23.5-56.5T480-720q-33 0-56.5 23.5T400-640q0 33 23.5 56.5T480-560ZM80-80v-720q0-33 23.5-56.5T160-880h640q33 0 56.5 23.5T880-800v480q0 33-23.5 56.5T800-240H240L80-80Zm126-240h594v-480H160v525l46-45Zm-46 0v-480 480Z"/>
                </svg>
                <h2 class="text-xs">3</h2>
              </div>
            </div>
          </div>
        </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'NewsCard',
  props: {
    image: {
      type: String,
      required: true
    },
    title: {
      type: String,
      required: true
    },
    description: {
      type: String,
      required: true
    }
  }
}
</script>

<style scoped>
.news-card {
  border: 1px solid #ddd;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  transition: box-shadow 0.3s ease;
}

.news-card:hover {
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.news-card__image {
  width: 100%;
  height: auto;
}

.news-card__content {
  padding: 16px;
}

.news-card__title {
  font-size: 1.5em;
  margin: 0 0 8px;
}

.news-card__description {
  font-size: 1em;
  color: #555;
}
</style>