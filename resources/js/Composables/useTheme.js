import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

export function useTheme() {
    const page = usePage();
    
    const primaryColor = computed(() => {
        return page.props.profile?.primary_color || '#244031';
    });
    
    const secondaryColor = computed(() => {
        return page.props.profile?.secondary_color || '#3B614A';
    });
    
    return {
        primaryColor,
        secondaryColor
    };
}
