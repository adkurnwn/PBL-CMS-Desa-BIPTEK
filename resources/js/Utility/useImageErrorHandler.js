import { ref } from 'vue';
import notFoundImage from '../../images/not-found.png';

export function useImageErrorHandler() {
  const imageErrors = ref(new Map());

  const handleImageError = (imageSrc) => {
    imageErrors.value.set(imageSrc, true);
  };

  const getImageSrc = (originalSrc) => {
    return imageErrors.value.get(originalSrc) ? notFoundImage : originalSrc;
  };

  const resetImageError = (imageSrc) => {
    imageErrors.value.delete(imageSrc);
  };

  const resetAllImageErrors = () => {
    imageErrors.value.clear();
  };

  return {
    imageErrors,
    handleImageError,
    getImageSrc,
    resetImageError,
    resetAllImageErrors
  };
}
