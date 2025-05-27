export function parseJson(value) {
    if (typeof value === "string") {
        try {
            return JSON.parse(value);
        } catch (e) {
            console.error("JSON parse error:", e);
            return [];
        }
    }
    return Array.isArray(value) || typeof value === "object" ? value : [];
}

export function parseFasilitas(fasilitas) {
    return parseJson(fasilitas);
}

export function parseMisi(misi) {
    if (typeof misi === "string") {
        return misi.split(/\d+\.\s/).filter((item) => item.trim() !== "");
    }
    return [];
}
