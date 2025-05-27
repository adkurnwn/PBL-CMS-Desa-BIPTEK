import axios from "axios";
import { ref } from "vue";

export default function useApiFetch() {
    const data = ref(null);
    const error = ref(null);
    const loading = ref(false);
    const pagination = ref(null);

    const fetchData = async (url, config = {}) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await axios.get(url, config);
            const result = response.data;

            if (result.success) {
                data.value = result.data;
                if (result.meta) {
                    pagination.value = {
                        current_page: result.meta.current_page,
                        last_page: result.meta.last_page,
                        total: result.meta.total,
                    };
                }
            } else {
                error.value = result.message || "Gagal memuat data";
            }
        } catch (err) {
            error.value =
                err.response?.data?.message || "Terjadi kesalahan jaringan";
        } finally {
            loading.value = false;
        }
    };

    return {
        data,
        error,
        loading,
        pagination,
        fetchData,
    };
}
