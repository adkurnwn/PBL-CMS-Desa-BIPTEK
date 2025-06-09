import { ref } from "vue";

export default function useFilter(fetchCallback) {
    const searchQuery = ref("");
    const selectedCategory = ref("Semua");

    const handleSearch = () => {
        fetchCallback({
            search: searchQuery.value,
            category:
                selectedCategory.value !== "Semua" && selectedCategory.value !== ""
                    ? selectedCategory.value
                    : null,
        });
    };

    const selectCategory = (category) => {
        selectedCategory.value = category;
        fetchCallback({
            search: searchQuery.value,
            category: category !== "Semua" && category !== "" ? category : null,
        });
    };

    const goToPage = (page) => {
        fetchCallback({
            search: searchQuery.value,
            category:
                selectedCategory.value !== "Semua" && selectedCategory.value !== ""
                    ? selectedCategory.value
                    : null,
            page,
        });
        window.scrollTo(0, 0);
    };

    return {
        searchQuery,
        selectedCategory,
        handleSearch,
        selectCategory,
        goToPage,
    };
}
