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
  },
  multiDataset: {
    type: Boolean,
    default: false
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

function getAccent1Color() {
  const el = document.getElementById('app-accent-root');
  if (el) {
    return getComputedStyle(el).getPropertyValue('--accent1') || '#244031';
  }
  return '#244031';
}

const createChart = () => {
  if (!chartCanvas.value || !props.data) return;
  
  // Destroy existing chart first
  destroyChart();

  const ctx = chartCanvas.value.getContext('2d');

  let datasets = [];
  // Use the accent1 color
  const accent1 = getAccent1Color().trim();
  
  if (props.multiDataset && props.data.datasets) {
    // Multi-dataset configuration
    const colors = [
      '#3B4659', // Blue for Berita
      '#9F4941', // Red for Produk Hukum  
      '#3B614A', // Green for Dana Desa
      '#F59E0B'  // Orange for Produk Desa
    ];
    
    datasets = props.data.datasets.map((dataset, index) => ({
      label: dataset.label,
      data: dataset.data,
      borderColor: colors[index] || props.color,
      backgroundColor: (colors[index] || props.color) + '20',
      fill: false,
      tension: 0.4,
      pointBackgroundColor: colors[index] || props.color,
      pointBorderColor: '#fff',
      pointBorderWidth: 2,
      pointRadius: 4,
      pointHoverRadius: 6
    }));
  } else {
    // Single dataset configuration
    datasets = [{
      label: props.title,
      data: props.data.values || [],
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
    }];
  }

  chartInstance = new ChartJS(ctx, {
    type: 'line',
    data: {
      labels: props.data.labels || [],
      datasets: datasets
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        title: {
          display: false
        },
        legend: {
          display: props.multiDataset,
          position: 'top'
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
            text: props.multiDataset ? 'Jumlah Views' : 'Pengunjung'
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
