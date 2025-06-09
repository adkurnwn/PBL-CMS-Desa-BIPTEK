<template>
  <div class="w-full flex justify-center items-center">
    <canvas ref="PieChart" width="900" height="400"></canvas>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { Chart, registerables } from 'chart.js';

Chart.register(...registerables);

const props = defineProps({
  data: {
    type: Object,
    required: false,
    default: () => ({})
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

const PieChart = ref(null);
let chartInstance = null;

const fetchData = async () => {
  try {
    const response = await fetch('/api/demografi/preview');
    const result = await response.json();
    const data = result.data || {};

    return [
      { label: 'SD', value: data.tamat_sd ?? 0 },
      { label: 'SMP', value: data.tamat_smp ?? 0 },
      { label: 'SMA', value: data.tamat_sma ?? 0 },
      { label: 'Perguruan Tinggi', value: data.tamat_pt ?? 0 }
    ];
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

const renderChart = (data) => {
  const labels = data.map(item => item.label);
  const values = data.map(item => item.value);

  if (chartInstance) {
    chartInstance.destroy();
  }
  // Use the accent1 color
  const accent1 = getAccent1Color().trim();
  const ctx = PieChart.value.getContext('2d');
  chartInstance = new Chart(ctx, {
    type: 'bar',
    data: {
      labels,
      datasets: [{
        label: props.title,
        data: values,
        backgroundColor: accent1 + '90',
        borderColor: '#000',
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            precision: 3
          }
        }
      },
      plugins: {
        legend: {
          display: false
        },
        tooltip: {
          callbacks: {
            label: function (context) {
              const value = context.parsed.y;
              return `${context.label}: ${value} orang`;
            }
          }
        }
      }
    }
  });
};

onMounted(async () => {
  const data = await fetchData();
  if (data.length) {
    renderChart(data);
  }
});
</script>

<style scoped>
canvas {
  max-width: 100%;
  margin: 0 auto;
  height: 400px !important;
}
</style>
