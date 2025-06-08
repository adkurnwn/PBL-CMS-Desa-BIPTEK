<template>
  <div class="w-full flex justify-center items-center">
    <canvas ref="lineChart" width="900" height="400"></canvas>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { Chart, registerables } from 'chart.js';

Chart.register(...registerables);

const props = defineProps({
  data: {
    type: Object,
    required: true
  },
  title: {
    type: String,
    default: 'Chart'
  },
  color: {
    type: String,
    default: '#3B614A'
  },
});

const lineChart = ref(null);
let chartInstance = null;

const fetchData = async () => {
  try {
    const response = await fetch('/api/demografi');
    const result = await response.json();
    // The API returns an array of objects in result.data
    // We'll use the index as the year label (or you can add a year field in your backend)
    return result.data.map((item, idx) => ({
      year: (new Date().getFullYear() - (result.data.length + 1 - idx)).toString(),
      total: item.penduduk ?? item.jumlah_penduduk ?? 0,
    }));
  } catch (error) {
    console.error('Error fetching data:', error);
    return [];
  }
};

function getAccent1Color() {
  const el = document.getElementById('app-accent-root');
  if (el) {
    return getComputedStyle(el).getPropertyValue('--accent1') || '#244031';
  }
  return '#244031';
}

console.log(getAccent1Color())
const renderChart = (data) => {
  const labels = data.map(item => item.year);
  const values = data.map(item => item.total);

  if (chartInstance) {
    chartInstance.destroy();
  }

  // Use the accent1 color
  const accent1 = getAccent1Color().trim();

  const ctx = lineChart.value.getContext('2d');
  chartInstance = new Chart(ctx, {
    type: 'line',
    data: {
      labels: labels,
      datasets: [{
        label: 'Total Penduduk',
        data: values,
        borderColor: accent1,
        backgroundColor: accent1 + '50',
        borderWidth: 2,
        fill: true,
        tension: 0.4,
        pointBackgroundColor: accent1,
        pointBorderColor: '#fff',
        pointBorderWidth: 2,
        pointRadius: 4,
        pointHoverRadius: 10
      }],
    },
    options: {
      responsive: true,
      plugins: {
        legend: { display: false },
        title: { display: false }
      },
      scales: {
        y: {
          beginAtZero: false,
          ticks: { precision: 0 }
        },
      },
      interaction: {
        mode: 'nearest',
        axis: 'x',
        intersect: false
      }
    },
  });
};

onMounted(async () => {
  const data = await fetchData();
  if (data && data.length) {
    renderChart(data);
  }
});
</script>

<style scoped>
canvas {
  max-width: 100%;
  margin: 0 auto;

}
</style>