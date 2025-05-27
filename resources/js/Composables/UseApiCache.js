import axios from "axios";

export function useApiCache(cacheKey, url, duration = 30 * 60 * 1000) {
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

    const saveToCache = (data) => {
        sessionStorage.setItem(cacheKey, JSON.stringify(data));
        sessionStorage.setItem(CACHE_EXPIRY_KEY, (now + duration).toString());
    };

    const fetchWithCache = async () => {
        const cached = getFromCache();
        if (cached) return cached;

        const res = await axios.get(url);
        const data = res.data.data;
        saveToCache(data);
        return data;
    };

    return { fetchWithCache };
}
