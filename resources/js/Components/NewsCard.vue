<template>
  <!-- <div class="news-card">
    <img :src="image" alt="News Image" class="news-card__image" />
    <div class="news-card__content">
      <h2 class="news-card__title">{{ title }}</h2>
      <p class="news-card__description">{{ description }}</p>
    </div>
  </div> -->
  <div class="w-full md:w-1/3 ">
      <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-lg transition-colors duration-300 h-">
        <img src="../../images/berita.png" alt="News Image" class="w-full object-cover p-4" />
        <div class="px-2">
          <div class="flex flex-row p-4 pt-1 space-x-2">
            <img src="../../images/person.png" alt="" class="w-[16px] h-[16px] object-contain " />
            <h2 class="pr-2 text-sm font-thin text-type2 ">M.Andi Sulaiman</h2>
          </div>
          <div>
            <h2 class="px-4 text-lg font-semibold text-type2">Pengukuhan Kader Posyandu Desa Pao-Pao Periode 2025-2030</h2>
            <p class="pt-2 px-4 text-sm font-thin text-type2 line-clamp-3 overflow-hidden text-ellipsis text-justify">Kepala Desa Pao-Pao, Kecamatan Tanete Rilau, Kabupaten Barru, Sulawesi Selatan SYAMSUL BAHRI, S.E., M.M. melakukan penandatanganan kerjasama yang signifikan antara Ikatan fhujediwshnfcujdewsnfhc hjfujedsiwhnfcddnb</p>
          </div>
          <div class="flex flex-row justify-between p-3 py-5">
            <h2 class="text-xs">5 March 2025</h2>
            <div class="flex flex-row space-x-3 mr-2">
              <div class="flex flex-row space-x-2 ">
                <img src="../../images/visibility.png" alt="" class="-mt-0.5"/>
                <h2 class="text-xs">0</h2>
              </div>
              <div class="flex flex-row space-x-2">
                <img src="../../images/3p.png" alt="">
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