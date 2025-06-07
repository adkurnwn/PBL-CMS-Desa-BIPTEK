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
  colors: {
    type: Array,
    default: () => [
      '#fee2e2', // red-100
      '#fef3c7', // yellow-100
      '#dcfce7', // green-100
      '#dbeafe', // blue-100
    ]
  }
});

const PieChart = ref(null);
let chartInstance = null;

const fetchData = async () => {
  try {
    // Fetch the preview data (assume it returns an array, take the latest by tahun)
    const response = await fetch('/api/demografi/preview');
    const result = await response.json();

    return [
      { label: 'Lansia', value: result.jumlah_lansia ?? 0 },
      { label: 'Dewasa', value: result.jumlah_dewasa ?? 0 },
      { label: 'Remaja', value: result.jumlah_remaja ?? 0 },
      { label: 'Anak', value: result.jumlah_anak ?? 0 }
    ];
  } catch (error) {
    console.error('Error fetching data:', error);
    return [];
  }
};

const renderChart = (data) => {
  const labels = data.map(item => item.label);
  const values = data.map(item => item.value);
  const backgroundColors = props.colors.slice(0, data.length);

  if (chartInstance) {
    chartInstance.destroy();
  }

  const ctx = PieChart.value.getContext('2d');
  chartInstance = new Chart(ctx, {
    type: 'pie', // Changed to pie
    data: {
      labels,
      datasets: [{
        data: values,
        backgroundColor: backgroundColors,
        borderColor: '#fff',
        borderWidth: 2,
        hoverBorderWidth: 3
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'right',
          labels: {
            usePointStyle: true,
            padding: 20,
            font: {
              size: 12
            }
          }
        },
        tooltip: {
          callbacks: {
            label: function (context) {
              const label = context.label || '';
              const value = context.parsed;
              const total = context.dataset.data.reduce((a, b) => a + b, 0);
              const percentage = ((value / total) * 100).toFixed(1);
              return `${label}: ${value} orang (${percentage}%)`;
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
}
</style>
