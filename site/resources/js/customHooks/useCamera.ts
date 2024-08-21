import { useState } from 'react';

// Hooks
import { useShowMessage } from './useShowMessage';

// Utils
import { MAX_UPLOAD_IMAGES } from '@utils/constants';

const useCamera = (
  pickSingleImage: boolean = false,
  maxImages: number = MAX_UPLOAD_IMAGES,
) => {
  const { showError } = useShowMessage();
  const [image, setImage] = useState<File[] | null>(null);
  const [maxImageCount, setMaxImageCount] = useState<number>(maxImages);

  /**
   * If multiple then Imagepicker will return array otherwise single object
   * So convert to array when multiple is false
   */
  const handleOpenPicker = async () => {
    try {
      let imageData = null;
      const inputElement = document.createElement('input');
      inputElement.type = 'file';
      inputElement.accept = 'image/jpeg,image/png,image/jpg';
      inputElement.multiple = !pickSingleImage; // Allow multiple file selection
      inputElement.max = maxImageCount.toString(); // Set maximum number of files to select
      inputElement.style.display = 'none';
      inputElement.onchange = e => {
        const files = (e.target as HTMLInputElement).files;
        if (files) {
          if (files.length > maxImageCount) {
            return showError('Maximum 3 files are allowed!');
          }
          imageData = pickSingleImage
            ? [files?.[0] ?? {}]
            : Object.values(files);
          setImage(imageData as File[]);
        }
        // Remove the input element after file selection
        inputElement.remove();
      };
      // Append the input element to the body
      document.body.appendChild(inputElement);

      // Trigger the click event on the input element
      inputElement.click();
    } catch (e) {
      console.log('Image upload error', e);
    }
  };

  return {
    image,
    setImage,
    handleOpenPicker,
    maxImageCount,
    setMaxImageCount,
  };
};

export default useCamera;
