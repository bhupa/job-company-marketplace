import { useEffect, useState } from 'react';

import { useTranslation } from 'react-i18next';

// Hooks
import useCamera from './useCamera';
import { useShowMessage } from './useShowMessage';

// Redux
import {
  VerificationStatus,
  useGetVerificationDetailQuery,
  useVerifyAccountMutation,
} from '@redux/services/authApi';

// Others
import { IUserImage } from '@redux/services/jobSeekerApi';
import { MAX_ACCOUNT_VERIFICATION_IMAGES } from '@utils/constants';

interface IImage extends IUserImage {
  imageObj?: File;
}

interface IDocument extends IUserImage {
  imageObj?: File;
}

const useVerifyAccount = () => {
  const { t } = useTranslation(['messages']);
  const { showSuccess, showWarning, showError } = useShowMessage();
  const [verifyAccount, { isLoading, isSuccess: isUploadSuccess }] =
    useVerifyAccountMutation();
  const { isLoading: isDataLoading, data: verificationData } =
    useGetVerificationDetailQuery();

  const [removedImageIds, setRemovedImageIds] = useState<number[]>([]);
  const [images, setImages] = useState<IImage[]>([]);
  const [isChanged, setChanged] = useState<boolean>(false);
  const [uploadedFile, setUploadedFile] = useState<IDocument | null>(null);

  const verificationDocuments = verificationData?.data.verification.documents;
  const imagesLength = verificationDocuments?.length ?? 0;

  const { image, maxImageCount, setMaxImageCount, handleOpenPicker } =
    useCamera(false, MAX_ACCOUNT_VERIFICATION_IMAGES - imagesLength);

  useEffect(() => {
    if (isUploadSuccess) {
      setChanged(false);
      return showSuccess(t('accountVerification.success'));
    }
  }, [isUploadSuccess]);

  useEffect(() => {
    if (image) {
      console.log('image', image);
      const modifiedImages = image.map(item => {
        return {
          id: Date.now().toString(),
          fileType: item.type,
          image: URL.createObjectURL(item),
          imageObj: item,
        };
      });
      setImages([...images, ...modifiedImages]);
      setMaxImageCount(maxImageCount - image.length);
      setChanged(true);
    }
  }, [image]);

  /**
   * Set initial images if there are already uploaded images
   */
  useEffect(() => {
    if (verificationDocuments?.length) {
      const verificationImages = verificationDocuments?.filter(
        item => item.fileType !== 'pdf',
      );
      const verificationFile = verificationDocuments?.filter(
        item => item.fileType === 'pdf',
      );
      setImages(verificationImages);
      if (verificationFile?.[0]) {
        setUploadedFile(verificationFile[0]);
      }
    }
  }, [verificationDocuments]);

  /**
   * Remove image from given index
   * @param imageId
   * @param index
   */
  const removeImage = (imageId: string, index: number) => {
    if (!images[index]?.imageObj) {
      // If imageObj is present then it means we haven't added it to our server so no need to add on removed list
      setRemovedImageIds([...removedImageIds, Number(imageId)]);
    }
    const filteredImages = images.filter((_, imgIdx) => imgIdx !== index);
    setMaxImageCount(maxImageCount + 1);
    setImages(filteredImages);
    setChanged(true);
  };

  // Update documents
  const updateDocuments = () => {
    const uploadedImages = images
      .filter(item => item.imageObj)
      .map(item => item.imageObj) as File[];
    const noChangedImages =
      (!uploadedImages.length && !removedImageIds.length) || !images.length;
    const noChangedFile = !uploadedFile?.imageObj;
    if (noChangedImages && noChangedFile) {
      return showWarning(t('accountVerification.empty'));
    }
    const formData = new FormData();
    if (!noChangedImages) {
      uploadedImages?.forEach(imageItem => {
        formData.append('images[]', imageItem);
      });
    }
    if (uploadedFile?.imageObj) {
      const fileObj = uploadedFile.imageObj;
      formData.append('images[]', fileObj);
    }
    removedImageIds?.forEach(keyValue => {
      formData.append('remove[]', keyValue.toString());
    });
    console.log('formData', formData);
    verifyAccount(formData);
  };

  const handleSelectFile = async () => {
    try {
      const inputElement = document.createElement('input');
      inputElement.type = 'file';
      inputElement.accept = 'application/pdf';
      inputElement.style.display = 'none';
      inputElement.onchange = e => {
        const file = (e.target as HTMLInputElement).files?.[0];
        if (file) {
          console.log('Selected file:', file);
          const oldFile = uploadedFile || {};
          setUploadedFile({
            ...oldFile,
            id: Date.now().toString(),
            image: URL.createObjectURL(file),
            fileType: file.type,
            imageObj: file,
          });
          setChanged(true);
        }
        // Remove the input element after file selection
        inputElement.remove();
      };
      // Append the input element to the body
      document.body.appendChild(inputElement);

      // Trigger the click event on the input element
      inputElement.click();
    } catch (err) {
      console.log('Document picker error', err);
      showError(t('somethingWrong', { ns: 'messages' }));
    }
  };

  const handleRemoveFile = (fileId: string) => {
    setRemovedImageIds([...removedImageIds, Number(fileId)]);
    const filteredImages = images.filter(item => item.id !== fileId);
    setImages(filteredImages);
    setUploadedFile(null);
    setChanged(true);
  };

  const verificationStatus = verificationData?.data?.verificationStatus;

  return {
    images,
    uploadedFile,
    removeImage,
    updateDocuments,
    handleOpenPicker,
    handleSelectFile,
    handleRemoveFile,
    isUploading: isLoading,
    isDataLoading,
    comment: verificationData?.data.verification.comment,
    isVerified: verificationStatus === VerificationStatus.APPROVED,
    isVerificationPending: verificationStatus === VerificationStatus.PENDING,
    isVerificationRejected: verificationStatus === VerificationStatus.REJECTED,
    // isChanged is used to show update button
    isChanged: isChanged,
  };
};

export default useVerifyAccount;
