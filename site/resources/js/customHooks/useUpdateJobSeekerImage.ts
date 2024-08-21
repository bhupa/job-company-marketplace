import { useEffect, useState } from 'react';

// Hooks
import useCamera from './useCamera';
import useJobSeeker from './useJobSeeker';
import useJobSeekerProfileInput from './useJobSeekerProfileInput';

// Redux
import { useAppDispatch } from '@redux/hook';
import { setProfilePickerVisible } from '@redux/reducers/profile';

// Others
import { MAX_UPLOAD_IMAGES } from '@utils/constants';
import { IUserImage } from '@redux/services/jobSeekerApi';

interface IImage extends IUserImage {
  imageObj?: File;
}

/**
 * Uses image data from reducer during profile creation
 * Uses image data from server during update
 * @returns
 */
const useUpdateJobSeekerImage = () => {
  const dispatch = useAppDispatch();
  const {
    image: jobSeekerImage = [],
    profileImg = '',
    handleUpdateJobSeeker,
    id,
  } = useJobSeeker();
  const { handleSetProfileData, profileInput } = useJobSeekerProfileInput();
  const { image, handleOpenPicker } = useCamera(true);

  const [isLoading, setIsLoading] = useState<boolean>(true);
  const [imageIndex, setImageIndex] = useState<number | null>(null);
  const [removedImageIds, setRemovedImageIds] = useState<number[]>([]);
  const [images, setImages] = useState<IImage[]>([]);
  const [profilePicture, setProfilePicture] = useState<File | null>(null);
  const [isOriginalProfilePictureRemoved, setOriginalProfilePictureRemoved] =
    useState<boolean>(false);

  useEffect(() => {
    // Id there is id, it means user is already created so make data for update purpose
    // If no id, use from our redux state
    const initialProfileImage = id
      ? {
          id: 0,
          image: profileImg,
        }
      : {
          id: 0,
          image: profileInput.profileImg
            ? URL.createObjectURL(profileInput.profileImg)
            : null,
          imageObj: profileInput.profileImg,
        };
    const initialImages = id
      ? jobSeekerImage
      : profileInput.image
        ? profileInput.image.map((item: any) => {
            return {
              id: Date.now().toString(),
              image: item ? URL.createObjectURL(item) : null,
              imageObj: item,
            };
          })
        : [];

    let userImages = [initialProfileImage, ...initialImages] as IUserImage[];

    const totalImages = userImages.length;
    const remainingImages = MAX_UPLOAD_IMAGES - totalImages;

    for (let i = 0; i < remainingImages; i++) {
      const item = { id: Date.now().toString(), image: '', fileType: '' };
      userImages = [...userImages, item];
    }
    setImages(userImages);
    setTimeout(() => setIsLoading(false), 500);
  }, [profileImg]);

  useEffect(() => {
    if (image && imageIndex !== null) {
      if (imageIndex === 0) {
        setProfilePicture(image?.[0]);
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
     * For user first index is for profile picture,
     * so don't add to removedIds as profile picture is updated if we send new image
     */
    if (index === 0) {
      profilePicture && setProfilePicture(null);
      // If there was originally added profile picture and it was removed, set below state to true
      profileImg && setOriginalProfilePictureRemoved(true);
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
    // isOriginalProfilePictureRemoved = false means profile picture was not removed
    if (
      !uploadedImages.length &&
      !removedImageIds.length &&
      !isOriginalProfilePictureRemoved
    ) {
      return dispatch(setProfilePickerVisible(false));
    }
    // If jobseeker is being created then there won't be id
    if (!id) {
      const selectedProfileImg = uploadedImages[0];
      uploadedImages.shift();
      handleSetProfileData({
        profileImg: selectedProfileImg as File,
        image: uploadedImages as File[],
      });
      return dispatch(setProfilePickerVisible(false));
    }
    profilePicture && uploadedImages.shift();
    handleUpdateJobSeeker({
      image_ids: removedImageIds,
      profile_img: profilePicture
        ? profilePicture
        : isOriginalProfilePictureRemoved
          ? ''
          : null, // If profilePicture file value is present, it means picture was changed otherwise check isOriginalProfilePictureRemoved for removal of picture
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
    isUpdating: false,
    isLoading,
  };
};

export default useUpdateJobSeekerImage;
