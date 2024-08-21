import { useEffect, useState } from 'react';

// Hooks
import useCamera from './useCamera';
import useCompany from './useCompany';
import useCompanyProfileInput from './useCompanyProfileInput';

// Redux
import { useAppDispatch } from '@redux/hook';
import { setProfilePickerVisible } from '@redux/reducers/profile';

// Others
import { MAX_COMPANY_IMAGES } from '@utils/constants';
import { IUserImage } from '@redux/services/jobSeekerApi';

interface IImage extends IUserImage {
  imageObj?: File;
}

/**
 * Uses image data from reducer during profile creation
 * Uses image data from server during update
 * @returns
 */
const useUpdateCompanyImage = () => {
  const {
    logo = '',
    image: companyImage = [],
    handleUpdateCompany,
    isUpdating,
    id,
  } = useCompany();
  const { handleSetCompanyProfileInputData, profile } =
    useCompanyProfileInput();
  const { image, handleOpenPicker } = useCamera(true, MAX_COMPANY_IMAGES);

  const dispatch = useAppDispatch();

  const [isLoading, setIsLoading] = useState<boolean>(true);
  const [imageIndex, setImageIndex] = useState<number | null>(null);
  const [removedImageIds, setRemovedImageIds] = useState<number[]>([]);
  const [images, setImages] = useState<IImage[]>([]);
  const [companyLogo, setCompanyLogo] = useState<File | null>(null);
  const [isOriginalProfilePictureRemoved, setOriginalProfilePictureRemoved] =
    useState<boolean>(false);

  // Set array of images for creating total input fields for company images
  useEffect(() => {
    const initialLogo = id
      ? {
          id: 0,
          image: logo,
        }
      : {
          id: 0,
          image: profile.logo ? URL.createObjectURL(profile.logo) : null,
          imageObj: profile.logo,
        };
    const initialImages = id
      ? companyImage
      : profile.image
        ? profile.image.map(item => {
            return {
              id: Date.now().toString(),
              image: item ? URL.createObjectURL(item) : null,
              imageObj: item,
            };
          })
        : [];

    const userImages = [initialLogo, ...initialImages] as IUserImage[];

    const totalImages = userImages.length;
    const remainingImages = MAX_COMPANY_IMAGES - totalImages;

    for (let i = 0; i < remainingImages; i++) {
      const item = { id: Date.now().toString(), image: '', fileType: '' };
      userImages.push(item);
    }
    setImages(userImages);
    setTimeout(() => setIsLoading(false), 500);
  }, [logo]);

  useEffect(() => {
    if (image && imageIndex !== null) {
      if (imageIndex === 0) {
        setCompanyLogo(image?.[0]);
      }
      images.splice(imageIndex, 1, {
        id: Date.now().toString(),
        image: URL.createObjectURL(image?.[0]),
        fileType: 'jpeg',
        imageObj: image?.[0],
      });
      setImages(images);
      setImageIndex(null);
    }
  }, [image]);

  /**
   * Remove image from given index
   * @param imageId
   * @param index
   */
  const removeImage = (imageId: string, index: number) => {
    /**
     * For company first index is for company logo,
     * so don't add to removedIds as logo is updated if we send new image
     */
    if (index === 0) {
      companyLogo && setCompanyLogo(null);
      logo && setOriginalProfilePictureRemoved(true);
    } else if (!images[index]?.imageObj) {
      // If imageObj is present then it means we haven't added it to our server so no need to add on removed list
      setRemovedImageIds([...removedImageIds, Number(imageId)]);
    }
    images.splice(index, 1, {
      id: Date.now().toString(),
      image: '',
      fileType: '',
    });
    setImages([...images]);
  };

  // Update images to server
  const updateImage = () => {
    const uploadedImages = images
      .filter(item => item.imageObj)
      .map(item => item.imageObj);
    if (
      !uploadedImages.length &&
      !removedImageIds.length &&
      !isOriginalProfilePictureRemoved
    ) {
      return dispatch(setProfilePickerVisible(false));
    }
    // For creation time when company has not been created
    if (!id) {
      const selectedLogoImg = uploadedImages[0];
      uploadedImages.shift();
      handleSetCompanyProfileInputData({
        logo: selectedLogoImg as File,
        image: uploadedImages as File[],
      });
      return dispatch(setProfilePickerVisible(false));
    }
    // If company is already created update image
    companyLogo && uploadedImages.shift();
    handleSetCompanyProfileInputData({
      logo: companyLogo as File,
      image: uploadedImages as File[],
    });
    handleUpdateCompany({
      image_ids: removedImageIds,
      logo: companyLogo
        ? companyLogo
        : isOriginalProfilePictureRemoved
          ? ''
          : null,
      image: uploadedImages as File[],
    });
  };

  const addImage = (index: number) => {
    setImageIndex(index);
    handleOpenPicker();
  };

  return {
    images,
    removeImage,
    addImage,
    updateImage,
    isUpdating,
    isLoading,
  };
};

export default useUpdateCompanyImage;
