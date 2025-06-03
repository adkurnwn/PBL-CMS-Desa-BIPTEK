<template>
  <div class="w-full h-96">
    <canvas ref="chartCanvas"></canvas>
  </div>
</template>

<script setup>
import { ref, onMounted, watch, nextTick, onBeforeUnmount } from 'vue';
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  LineController,
  Title,
  Tooltip,
  Legend,
  Filler
} from 'chart.js';

// Register all required Chart.js components including LineController
ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  LineController,
  Title,
  Tooltip,
  Legend,
  Filler
);

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
  if (!chartCanvas.value || !props.data) return;
  
  // Destroy existing chart first
  destroyChart();

  const ctx = chartCanvas.value.getContext('2d');

  chartInstance = new ChartJS(ctx, {
    type: 'line',
    data: {
      labels: props.data.labels || [],
      datasets: [{
        label: props.title,
        data: props.data.values || [],
        borderColor: props.color,
        backgroundColor: props.color + '20',
        fill: true,
        tension: 0.4,
        pointBackgroundColor: props.color,
        pointBorderColor: '#fff',
        pointBorderWidth: 2,
        pointRadius: 4,
        pointHoverRadius: 6
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        title: {
          display: false // Hide title since we show it above the chart
        },
        legend: {
          display: false
        },
        tooltip: {
          mode: 'index',
          intersect: false,
        }
      },
      scales: {
        x: {
          display: true,
          title: {
            display: true,
            text: 'Periode'
          }
        },
        y: {
          display: true,
          title: {
            display: true,
            text: 'Pengunjung'
          },
          beginAtZero: true,
          ticks: {
            stepSize: 1,
            callback: function(value) {
              return Number.isInteger(value) ? value : '';
            }
          }
        }
      },
      interaction: {
        mode: 'nearest',
        axis: 'x',
        intersect: false
      }
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
  if (props.data && (props.data.labels?.length > 0 || props.data.values?.length > 0)) {
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
