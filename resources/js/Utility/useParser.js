export function parseJson(value) {
    // Check for null, undefined, or empty string
    if (!value || value.trim() === "") {
        return [];
    }

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
    if (!misi || typeof misi !== "string") {
        return [];
    }
    if (typeof misi === "string") {
        return misi.split(/\d+\.\s/).filter((item) => item.trim() !== "");
    }
    return [];
}

export function parseBatas(batas_desa) {
    const parsed = parseJson(batas_desa);
    return {
        utara: parsed[0] || '-',
        timur: parsed[1] || '-', 
        selatan: parsed[2] || '-',
        barat: parsed[3] || '-'
    };
}

export function parseMediaSosial(media_sosial) {
    const parsed = parseJson(media_sosial);
    // Order: [instagram, x, facebook, youtube]
    const baseUrls = {
        instagram: 'https://instagram.com/',
        x: 'https://x.com/',
        facebook: 'https://facebook.com/',
        youtube: 'https://youtube.com/@'
    };
    
    return {
        instagram: {
            username: parsed[0] || '',
            url: parsed[0] ? `${baseUrls.instagram}${parsed[0].replace('@', '')}` : ''
        },
        x: {
            username: parsed[1] || '',
            url: parsed[1] ? `${baseUrls.x}${parsed[1].replace('@', '')}` : ''
        },
        facebook: {
            username: parsed[2] || '',
            url: parsed[2] ? `${baseUrls.facebook}${parsed[2].replace('@', '')}` : ''
        },
        youtube: {
            username: parsed[3] || '',
            url: parsed[3] ? `${baseUrls.youtube}${parsed[3].replace('@', '')}` : ''
        }
    };
}
