import { ref } from "vue";
import axios from "axios";

export function useApiCache(cacheKey, url, duration = 30 * 60 * 1000) {
    const data = ref(null);
    const loading = ref(false);
    const error = ref(null);
    const now = Date.now();
    const CACHE_EXPIRY_KEY = `${cacheKey}_expiry`;

    const getFromCache = () => {
        const cached = sessionStorage.getItem(cacheKey);
        const expiry = sessionStorage.getItem(CACHE_EXPIRY_KEY);
        if (cached && expiry && now < parseInt(expiry)) {
            return JSON.parse(cached);
        }
        return null;
    };

    const saveToCache = (fetchedData) => {
        sessionStorage.setItem(cacheKey, JSON.stringify(fetchedData));
        sessionStorage.setItem(CACHE_EXPIRY_KEY, (now + duration).toString());
    };

    const fetchWithCache = async () => {
        loading.value = true;
        error.value = null;

        const cached = getFromCache();
        if (cached) {
            data.value = cached;
            loading.value = false;
            return;
        }

        try {
            const res = await axios.get(url);

            if (res.data.success) {
                data.value = res.data.data;
                saveToCache(res.data.data);
            } else {
                error.value = res.data.message || "Gagal memuat data.";
            }
        } catch (err) {
            error.value =
                err.response?.data?.message || "Terjadi kesalahan jaringan.";
        } finally {
            loading.value = false;
        }
    };

    return {
        data,
        loading,
        error,
        fetchWithCache,
    };
}
