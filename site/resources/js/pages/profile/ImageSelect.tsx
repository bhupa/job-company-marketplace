import React from 'react';

import { Image, Modal, Spin } from 'antd';
import { useTranslation } from 'react-i18next';

// Components
import { HeaderWithBackButton, InputLabel, Title } from '@components/common';

// Hooks
import useUserProfile from '@customHooks/useUserProfile';
import useUpdateCompanyImage from '@customHooks/useUpdateCompanyImage';
import useUpdateJobSeekerImage from '@customHooks/useUpdateJobSeekerImage';

// Others
import { Close, Edit } from '@assets/icons';
import { UserType } from '@redux/reducers/auth';

interface Props {
  closeModal: () => void;
}

const ImageSelect = ({ closeModal }: Props) => {
  const { t } = useTranslation(['common', 'messages']);

  const { userType } = useUserProfile();
  const isCompany = userType === UserType.Company;

  const useGetData = isCompany
    ? useUpdateCompanyImage
    : useUpdateJobSeekerImage;
  const {
    images = [],
    addImage,
    removeImage,
    updateImage,
    isUpdating,
    isLoading,
  } = useGetData();

  return (
    <Modal open footer={null} centered closable={false}>
      <div className="flex flex-col gap-3 h-full overflow-scroll w-full mx-auto pb-4 px-2">
        {isLoading ? (
          <Spin />
        ) : (
          <>
            {isUpdating && <Spin fullscreen />}
            <HeaderWithBackButton
              title={t('editImage')}
              onBackPressed={() => closeModal()}
              rightBtn={
                <Title
                  type="body1"
                  className={
                    isUpdating ? 'text-GRAY_ADAFBB' : 'text-BLUE_004D80'
                  }>
                  {t('done')}
                </Title>
              }
              onRightButtonPress={() => !isUpdating && updateImage()}
            />
            {isCompany ? (
              <InputLabel label={t('photoOfTeamMembers', { ns: 'profile' })} />
            ) : null}
            <div
              className={`grid ${isCompany ? 'grid-cols-1 justify-items-center' : 'grid-cols-3'} gap-4`}>
              {images.map((item, index) => {
                const { image, id } = item;
                return (
                  <button
                    onClick={() => {
                      image ? {} : addImage(index);
                    }}
                    key={id + index.toString()}
                    className={`flex relative rounded-md border-2 border-dashed bg-WHITE_EFF0F2 justify-center items-center ${isCompany ? 'h-[240px] w-[240px]' : 'h-[160px]'} ${image ? 'border-white' : 'border-GRAY_ADAFBB'}`}>
                    {index === 0 && !image ? (
                      <Title
                        className="text-center text-RED_FF4D4D mx-2"
                        type="caption2">
                        {isCompany ? '' : t('profilePicture')}
                      </Title>
                    ) : image ? (
                      <Image
                        src={image}
                        wrapperClassName={'rounded-md w-full h-full'}
                        style={{
                          objectFit: 'cover',
                          height: isCompany ? 240 : 160,
                        }}
                      />
                    ) : null}
                    <div
                      onClick={() => {
                        image ? removeImage(id, index) : {};
                      }}
                      className={`absolute flex cursor-pointer -right-2 -bottom-2 w-7 h-7 rounded-full items-center justify-center shadow-md ${image ? 'bg-white' : 'bg-RED_FF4D4D'}`}>
                      {image ? (
                        <Close className={'text-GRAY_545454'} />
                      ) : (
                        <Edit className={'text-white'} />
                      )}
                    </div>
                  </button>
                );
              })}
            </div>
          </>
        )}
      </div>
    </Modal>
  );
};

export default ImageSelect;
