<template>
  <div class="w-full h-96">
    <canvas ref="chartCanvas"></canvas>
  </div>
</template>

<script setup>
import { ref, onMounted, watch, nextTick, onBeforeUnmount } from 'vue';
import {
  Chart as ChartJS,
  ArcElement,
  Tooltip,
  Legend,
  DoughnutController
} from 'chart.js';

// Register required Chart.js components for doughnut chart
ChartJS.register(
  ArcElement,
  Tooltip,
  Legend,
  DoughnutController
);

const props = defineProps({
  data: {
    type: Array,
    required: true
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
      '#f3e8ff', // purple-100
      '#fce7f3', // pink-100
      '#ccfbf1', // teal-100
      '#fed7aa', // orange-100
      '#f1f5f9', // slate-100
      '#f3f4f6', // gray-100
      '#e0f2fe', // sky-100
      '#ecfccb', // lime-100
      '#fef7cd', // amber-100
      '#f0f4ff', // indigo-100
      '#fdf4ff', // fuchsia-100
      '#f0fdf0'  // emerald-100
    ]
  }
});

const chartCanvas = ref(null);
let chartInstance = null;

const destroyChart = () => {
  if (chartInstance) {
    chartInstance.destroy();
    chartInstance = null;
  }
};

const createChart = () => {
  if (!chartCanvas.value || !props.data || props.data.length === 0) return;
  
  // Destroy existing chart first
  destroyChart();

  const ctx = chartCanvas.value.getContext('2d');

  const labels = props.data.map(item => item.feature_name);
  const values = props.data.map(item => item.visits);
  const backgroundColors = props.colors.slice(0, props.data.length);

  chartInstance = new ChartJS(ctx, {
    type: 'doughnut',
    data: {
      labels: labels,
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
            label: function(context) {
              const label = context.label || '';
              const value = context.parsed;
              const total = context.dataset.data.reduce((a, b) => a + b, 0);
              const percentage = ((value / total) * 100).toFixed(1);
              return `${label}: ${value} kunjungan (${percentage}%)`;
            }
          }
        }
      },
      cutout: '60%'
    }
  });
};

onMounted(() => {
  nextTick(() => {
    createChart();
  });
});

// Watch for data changes
watch(() => props.data, () => {
  if (props.data && props.data.length > 0) {
    createChart();
  }
}, { deep: true });

// Watch for title changes
watch(() => props.title, () => {
  createChart();
});

// Cleanup on unmount
onBeforeUnmount(() => {
  destroyChart();
});
</script>
