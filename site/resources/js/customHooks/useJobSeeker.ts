import { useEffect } from 'react';

import { useTranslation } from 'react-i18next';
import { useNavigate } from 'react-router-dom';

// Hooks
import useUserProfile from './useUserProfile';
import { useShowMessage } from './useShowMessage';

// Redux
import {
  IJobSeekerProfile,
  IJobSeekerProfileParams,
  useGetJobSeekerQuery,
  useUpdateJobSeekerMutation,
} from '@redux/services/jobSeekerApi';
import { useAppDispatch, useAppSelector } from '@redux/hook';
import { setProfilePickerVisible } from '@redux/reducers/profile';

const useJobSeeker = () => {
  const { t, i18n } = useTranslation('messages');
  const { showSuccess } = useShowMessage();
  const navigate = useNavigate();
  const { isProfileComplete } = useUserProfile();
  const { data, isFetching, refetch } = useGetJobSeekerQuery(undefined, {
    skip: !isProfileComplete,
  });
  const profile = data?.data as IJobSeekerProfile;

  const dispatch = useAppDispatch();
  const { isProfilePickerVisible } = useAppSelector(state => state.profile);

  const [
    updateJobSeeker,
    { isLoading: isUpdating, isSuccess: isUpdateSuccess },
  ] = useUpdateJobSeekerMutation();

  useEffect(() => {
    const userLanguage = profile?.user?.language ?? 'en';
    i18n.changeLanguage(userLanguage);
  }, [profile]);

  useEffect(() => {
    if (isUpdateSuccess) {
      showSuccess(t('profile.updateSuccess'));
      if (!isProfilePickerVisible) {
        navigate(-1);
      }
    }
  }, [isUpdateSuccess]);

  const handleUpdateJobSeeker = (params: IJobSeekerUpdateParams) => {
    const formData: any = new FormData();
    Object.keys(params).forEach(updateKey => {
      const key = updateKey as keyof typeof params;
      if (key === 'profile_img' && params.profile_img) {
        const logo = params.profile_img;
        formData.append('profile_img', logo);
        return;
      }
      if (key === 'image' && params.image) {
        const image = params.image;
        image?.forEach(imageItem => {
          formData.append('image[]', imageItem);
        });
        return;
      }
      if (Array.isArray(params[key])) {
        const arrayData = params[key] as [];
        arrayData?.forEach(keyValue => {
          formData.append(`${key}[]`, keyValue);
        });
        return;
      } else {
        if (params[key] !== null) {
          formData.append(key, params[key]);
        }
      }
    });
    if (profile) {
      if (isProfilePickerVisible) {
        dispatch(setProfilePickerVisible(false));
      }
      updateJobSeeker({ formData, id: profile.id });
    }
  };

  return {
    ...profile,
    isUpdating,
    isFetching,
    verificationStatus: profile?.user?.verificationStatus,
    refetch,
    handleUpdateJobSeeker,
  };
};

export default useJobSeeker;

export interface IJobSeekerUpdateParams extends IJobSeekerProfileParams {
  image_ids?: number[];
}
