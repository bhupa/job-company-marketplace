import React from 'react';

import { Image, Spin } from 'antd';
import { useTranslation } from 'react-i18next';
import { useNavigate } from 'react-router-dom';

// Components
import {
  CustomButton,
  FileButton,
  HeaderWithBackButton,
  Title,
} from '@components/common';
import { DashboardWrapper } from '@templates';

// Hooks
import useUserProfile from '@customHooks/useUserProfile';
import useVerifyAccount from '@customHooks/useVerifyAccount';

// Others
import { AddCircle, Close, Edit } from '@assets/icons';
import { MAX_ACCOUNT_VERIFICATION_IMAGES } from '@utils/constants';

const VerifyAccount = () => {
  const { t } = useTranslation(['profile']);
  const navigate = useNavigate();

  const { isJobSeeker } = useUserProfile();

  const {
    images,
    uploadedFile,
    comment,
    isDataLoading,
    isUploading,
    isVerified,
    isVerificationPending,
    isVerificationRejected,
    isChanged,
    removeImage,
    updateDocuments,
    handleOpenPicker,
    handleSelectFile,
    handleRemoveFile,
  } = useVerifyAccount();

  const imageUri = images?.[0]?.image;

  /**
   * Rendered when documents are verified
   * @returns Component
   */
  const renderVerifiedComponent = () => {
    return (
      <>
        <Title type="body1" className={'text-GREEN_4EBE59'}>
          {t('userVerification.accountHasBeenVerified')}
        </Title>
        <Title type="body2">{t('userVerification.uploadedDocuments')}</Title>
        {images?.map(item => {
          return (
            <div
              key={item.id}
              className="relative flex flex-col border border-dashed h-[240px] justify-center items-center self-center rounded-md border-white shadow-md">
              <Image
                src={item.image}
                wrapperClassName="relative flex items-center w-[240px] h-[240px] rounded-md overflow-hidden"
                alt={'verified-image'}
              />
            </div>
          );
        })}
        {uploadedFile && (
          <FileButton
            url={uploadedFile.image}
            fileName={'Verification.' + uploadedFile.fileType}
            onDeletePressed={() => handleRemoveFile(uploadedFile.id)}
            hideDeleteButton
          />
        )}
      </>
    );
  };

  /**
   * Rendered when verification is pending or has been rejected
   * @returns Component
   */
  const renderPendingVerificationComponent = () => {
    return (
      <>
        <div className="flex flex-col gap-1">
          {isVerificationPending && (
            <Title type="body2" className={'text-ORANGE_EFC269'}>
              {t('userVerification.verificationPending')}
            </Title>
          )}
          {isVerificationRejected && (
            <>
              <Title type="body2" className={'text-RED_FF4D4D'}>
                {t('userVerification.documentsRejected')}
              </Title>
              <Title type="body2" className={'text-RED_FF4D4D'}>
                {comment}
              </Title>
              <Title type="body2">
                {t(
                  isJobSeeker
                    ? 'userVerification.jobseekerUploadTitle'
                    : 'userVerification.companyUploadTitle',
                )}
              </Title>
            </>
          )}
          <Title type="caption1" className={'text-BLACK_656565'}>
            *
            {t('userVerification.uploadHelperInfo', {
              MAX_ACCOUNT_VERIFICATION_IMAGES,
            })}
          </Title>
        </div>
        <Title type="body2">
          {t(
            isJobSeeker
              ? 'userVerification.citizenshipOrPassport'
              : 'userVerification.companyRegistrationDocument',
          )}
        </Title>
        {images?.map((verificationImage, index) => {
          const imageUrl = verificationImage.image;
          return (
            <div
              key={index.toString()}
              className={`relative flex flex-col border border-dashed h-[240px] justify-center items-center self-center rounded-md ${imageUrl ? 'border-white shadow-md' : ''}`}>
              <Image
                src={imageUrl}
                wrapperClassName="relative flex items-center w-[240px] h-[240px] rounded-md overflow-hidden"
                alt={'pending-verification-image'}
              />
              {imageUrl && (
                <button
                  className={`absolute flex flex-col justify-center items-center bg-RED_FF4D4D shadow-md rounded-[40px] w-7 h-7 -bottom-1 -right-1 ${imageUrl ? '-top-1 bg-white shadow-md' : ''}`}
                  onClick={() => {
                    removeImage(verificationImage.id, index);
                  }}>
                  <Close className={'text-GRAY_545454'} />
                </button>
              )}
            </div>
          );
        })}
        {images.length < MAX_ACCOUNT_VERIFICATION_IMAGES && (
          <button
            onClick={() => handleOpenPicker()}
            className="flex justify-center items-center">
            <AddCircle className={'text-BLUE_004D80'} height={36} width={36} />
            <Title type="body2" className={'text-BLUE_004D80'} bold>
              {t('userVerification.addImage')}
            </Title>
          </button>
        )}
        {uploadedFile ? (
          <FileButton
            url={uploadedFile.image}
            fileName={'Verification.' + uploadedFile.fileType}
            onDeletePressed={() => handleRemoveFile(uploadedFile.id)}
          />
        ) : (
          <CustomButton
            title={t('userVerification.addPdf')}
            onClick={() => handleSelectFile()}
          />
        )}
        {isChanged && (
          <CustomButton
            title={t('userVerification.updateDocument')}
            onClick={() => updateDocuments()}
          />
        )}
      </>
    );
  };

  return (
    <DashboardWrapper>
      <div className="flex flex-col gap-3 h-full overflow-scroll w-full mx-auto card px-6 py-4">
        <HeaderWithBackButton
          title={t('verifyAccount')}
          onBackPressed={() => navigate(-1)}
        />
        {isUploading && <Spin fullscreen />}
        {isDataLoading ? (
          <Spin />
        ) : (
          <div className="flex flex-col gap-4 self-center">
            {isVerified ? (
              renderVerifiedComponent()
            ) : (
              <>
                {isVerificationPending || isVerificationRejected ? (
                  renderPendingVerificationComponent()
                ) : (
                  <>
                    <div className="flex flex-col gap-1">
                      <Title type="body2">
                        {t(
                          isJobSeeker
                            ? 'userVerification.jobseekerUploadTitle'
                            : 'userVerification.companyUploadTitle',
                        )}
                      </Title>
                      <Title type="caption1" className={'text-BLACK_656565'}>
                        *
                        {t('userVerification.uploadHelperInfo', {
                          MAX_ACCOUNT_VERIFICATION_IMAGES,
                        })}
                      </Title>
                    </div>
                    <Title type="body2">
                      {t(
                        isJobSeeker
                          ? 'userVerification.citizenshipOrPassport'
                          : 'userVerification.companyRegistrationDocument',
                      )}
                    </Title>
                    <button
                      className={`flex flex-col relative border border-dashed h-[240px] justify-center items-center self-center rounded-md ${imageUri ? 'border-white shadow-md' : 'px-6'}`}
                      onClick={() => {
                        if (imageUri) {
                          // removeImage(images?.[0]?.id, 0);
                        } else {
                          handleOpenPicker();
                        }
                      }}>
                      {!imageUri ? (
                        <Title type="caption1">
                          {t('userVerification.uploadVerificationDocument')}
                        </Title>
                      ) : (
                        <Image
                          src={imageUri}
                          wrapperClassName="relative flex items-center w-[240px] h-[240px] rounded-md overflow-hidden"
                          alt="verification-image"
                        />
                      )}
                      <div
                        onClick={() =>
                          imageUri ? removeImage(images?.[0]?.id, 0) : {}
                        }
                        className={`absolute flex flex-col cursor-pointer justify-center items-center bg-RED_FF4D4D shadow-md rounded-[40px] w-7 h-7 -bottom-1 -right-1 ${imageUri ? '-top-1 bg-white' : ''}`}>
                        {imageUri ? (
                          <Close className={'text-GRAY_545454'} />
                        ) : (
                          <Edit className={'text-white'} />
                        )}
                      </div>
                    </button>
                    {images?.map((verificationImage, index) => {
                      if (index === 0) {
                        return null;
                      }
                      return (
                        <div
                          key={verificationImage.image + index.toString()}
                          className={`relative flex flex-col border border-dashed w-[240px] h-[240px] justify-center items-center self-center rounded-md ${imageUri ? 'border-white shadow-md' : ''}`}>
                          <Image
                            src={verificationImage.image}
                            wrapperClassName="relative flex items-center w-[240px] h-[240px] rounded-md overflow-hidden"
                            alt="verification-image"
                          />
                          {verificationImage.image ? (
                            <button
                              className={`absolute flex flex-col justify-center items-center bg-RED_FF4D4D shadow-md rounded-[40px] w-7 h-7 -bottom-1 -right-1 ${verificationImage.image ? '-top-1 bg-white' : ''}`}
                              onClick={() => {
                                removeImage(verificationImage.id, index);
                              }}>
                              <Close className={'text-GRAY_545454'} />
                            </button>
                          ) : null}
                        </div>
                      );
                    })}
                    {images.length > 0 &&
                      images.length < MAX_ACCOUNT_VERIFICATION_IMAGES && (
                        <button
                          onClick={() => handleOpenPicker()}
                          className="flex justify-center items-center">
                          <AddCircle
                            className={'text-BLUE_004D80'}
                            height={36}
                            width={36}
                          />
                          <Title
                            type="body2"
                            className={'text-BLUE_004D80'}
                            bold>
                            {t('userVerification.addImage')}
                          </Title>
                        </button>
                      )}
                    {uploadedFile ? (
                      <FileButton
                        url={uploadedFile.image}
                        fileName={'Verification.' + uploadedFile.fileType}
                        onDeletePressed={() =>
                          handleRemoveFile(uploadedFile.id)
                        }
                      />
                    ) : (
                      <CustomButton
                        title={t('userVerification.addPdf')}
                        onClick={() => handleSelectFile()}
                      />
                    )}
                    <CustomButton
                      title={t('userVerification.uploadDocument')}
                      onClick={() => updateDocuments()}
                    />
                  </>
                )}
              </>
            )}
          </div>
        )}
      </div>
    </DashboardWrapper>
  );
};

export default VerifyAccount;
