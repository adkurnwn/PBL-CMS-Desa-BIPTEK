import { ref } from 'vue';
import notFoundImage from '../../images/not-found.png';

export function useImageErrorHandler() {
  const imageError = ref(false);

  const handleImageError = () => {
    imageError.value = true;
  };

  const getImageSrc = (originalSrc) => {
    return imageError.value ? notFoundImage : originalSrc;
  };

  const resetImageError = () => {
    imageError.value = false;
  };

  return {
    imageError,
    handleImageError,
    getImageSrc,
    resetImageError
  };
}
