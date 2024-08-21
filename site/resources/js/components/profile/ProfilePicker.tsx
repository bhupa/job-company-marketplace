import React from 'react';

// Components
import { ImageSelect } from '@pages/profile';
import { ErrorText } from '@components/common';

// Hooks
import useUserProfile from '@customHooks/useUserProfile';

// Redux
import { useAppDispatch, useAppSelector } from '@redux/hook';
import { setProfilePickerVisible } from '@redux/reducers/profile';

// Others
import { Camera, Edit } from '@assets/icons';
import { UserType } from '@redux/reducers/auth';
import { CompanyPhoto, ProfilePhoto } from '@assets/images';

interface ProfilePickerProps {
  imageUrl?: string;
  error?: string | null;
  isEdit?: boolean;
  progress?: number;
}

const IMAGE_WIDTH = 100;
const IMAGE_CIRCLE_RADIUS = IMAGE_WIDTH / 2;
const CIRCLE_CIRCUMFERENCE = 2 * Math.PI * IMAGE_CIRCLE_RADIUS;
const STROKE_WIDTH = 10;

const ProfilePicker = (props: ProfilePickerProps) => {
  const { imageUrl, error, isEdit, progress = 0 } = props;

  const { userType } = useUserProfile();
  const dispatch = useAppDispatch();
  const { isProfilePickerVisible } = useAppSelector(state => state.profile);

  const handleOpenImageSelector = (isVisible: boolean) => {
    dispatch(setProfilePickerVisible(isVisible));
  };

  const showProgress = progress > 0;

  return (
    <div className="flex flex-col items-center gap-2">
      <div
        className="relative flex flex-col self-center items-center justify-center rounded-full cursor-pointer shadow-md"
        onClick={() => {
          handleOpenImageSelector(true);
        }}>
        {showProgress && (
          <svg height="120" width="120" className="absolute rotate-90">
            <circle
              cx="60"
              cy="60"
              r={IMAGE_CIRCLE_RADIUS}
              stroke={'#E0E2E4'}
              strokeWidth={STROKE_WIDTH}
              fill="transparent"
            />
            <circle
              cx="60"
              cy="60"
              r={IMAGE_CIRCLE_RADIUS}
              stroke={'#4EBE59'}
              strokeWidth={STROKE_WIDTH}
              strokeDasharray={`${(progress / 100) * CIRCLE_CIRCUMFERENCE} ${CIRCLE_CIRCUMFERENCE}`}
              fill="transparent"
            />
          </svg>
        )}
        <div className={'relative'}>
          <img
            src={
              imageUrl
                ? imageUrl
                : userType === UserType.Company
                  ? CompanyPhoto
                  : ProfilePhoto
            }
            className={
              'w-[100px] h-[100px] object-cover aspect-square rounded-full'
            }
          />
          <div
            className={`absolute rounded-full -right-0 -bottom-1 bg-white shadow-md ${isEdit ? 'p-1 border border-WHITE_EFF0F2' : ' p-[1px]'}`}>
            {isEdit ? (
              <Edit width={14} height={14} />
            ) : (
              <Camera width={24} height={24} />
            )}
          </div>
        </div>
      </div>
      {error && <ErrorText error={error} />}
      {isProfilePickerVisible && (
        <ImageSelect closeModal={() => handleOpenImageSelector(false)} />
      )}
    </div>
  );
};

export default ProfilePicker;
