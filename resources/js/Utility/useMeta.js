import { computed, unref } from 'vue';

export function useMeta(options = {}) {
    const {
        title = 'CMS Waradesa',
        description = 'Website resmi pemerintah desa',
        type = 'website',
        image = null,
        url = null,
        siteName = 'CMS Waradesa',
        author = 'Admin',
        publishedTime = null,
        section = null,
        fallbackImage = '/storage/default-image.jpg'
    } = options;

    const metaTitle = computed(() => {
        const titleValue = unref(title);
        return titleValue || 'CMS Waradesa';
    });
    
    const metaDescription = computed(() => {
        const descValue = unref(description);
        
        if (typeof descValue === 'string') return descValue;
        
        // Handle content that might have HTML tags
        if (descValue && typeof descValue === 'object' && descValue.content) {
            const textContent = descValue.content.replace(/<[^>]*>/g, '');
            return textContent.length > 160 
                ? textContent.substring(0, 157) + '...' 
                : textContent;
        }
        
        return 'Website resmi pemerintah desa';
    });

    const metaImage = computed(() => {
        const imageValue = unref(image);
        
        if (imageValue) {
            // If it's already a full URL, return as is
            if (typeof imageValue === 'string' && imageValue.startsWith('http')) {
                return imageValue;
            }
            
            // If it's a path, prepend the domain
            return `${window.location.origin}/storage/${imageValue}`;
        }
        
        return `${window.location.origin}${fallbackImage}`;
    });

    const canonicalUrl = computed(() => {
        const urlValue = unref(url);
        return urlValue || window.location.href;
    });

    return {
        metaTitle,
        metaDescription,
        metaImage,
        canonicalUrl,
        metaType: unref(type),
        metaSiteName: unref(siteName),
        metaAuthor: computed(() => unref(author)),
        metaPublishedTime: computed(() => unref(publishedTime)),
        metaSection: computed(() => unref(section))
    };
}
