import { useEffect } from 'react';

import { useTranslation } from 'react-i18next';
import { useNavigate } from 'react-router-dom';

// Hooks
import useUserProfile from './useUserProfile';
import { useShowMessage } from './useShowMessage';

// Redux
import {
  ICompany,
  useGetCompanyQuery,
  useUpdateCompanyMutation,
} from '@redux/services/companyApi';
import { useAppDispatch, useAppSelector } from '@redux/hook';
import { setProfilePickerVisible } from '@redux/reducers/profile';

const useCompany = () => {
  const navigate = useNavigate();
  const { t, i18n } = useTranslation('messages');
  const { showSuccess } = useShowMessage();
  const { isProfileComplete } = useUserProfile();
  const {
    data: companyData,
    isFetching,
    refetch: refetchCompanyDetail,
  } = useGetCompanyQuery(undefined, {
    skip: !isProfileComplete,
  });
  const profile = companyData?.data as ICompany;
  const [updateCompany, { isLoading: isUpdating, isSuccess: isUpdateSuccess }] =
    useUpdateCompanyMutation();

  const dispatch = useAppDispatch();
  const { isProfilePickerVisible } = useAppSelector(state => state.profile);

  useEffect(() => {
    const userLanguage = profile?.user?.language ?? 'en';
    i18n.changeLanguage(userLanguage);
  }, [profile]);

  useEffect(() => {
    if (isUpdateSuccess) {
      showSuccess(t('profile.updateSuccess'));
      if (isProfilePickerVisible) {
        dispatch(setProfilePickerVisible(false));
      } else {
        navigate(-1);
      }
    }
  }, [isUpdateSuccess]);

  const handleUpdateCompany = (params: ICompanyUpdateParams) => {
    const formData: any = new FormData();
    Object.keys(params).forEach(updateKey => {
      const key = updateKey as keyof typeof params;
      if (key === 'logo' && params.logo) {
        const logo = params.logo;
        formData.append('logo', logo);
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
        formData.append(key, params[key]);
      }
    });
    if (profile) {
      updateCompany({ formData, id: profile.id });
    }
  };

  return {
    ...profile,
    verificationStatus: profile?.user?.verificationStatus,
    isRefetchingCompanyDetail: isFetching,
    refetchCompanyDetail,
    isUpdating,
    handleUpdateCompany,
  };
};

export default useCompany;

export interface ICompanyUpdateParams {
  company_name?: string;
  about_company?: string;
  about_company_ja?: string;
  address?: string;
  image_ids?: number[];
  image?: any[];
  logo?: any | null;
  intro_video?: File | null;
  isIntroVideoDeleted?: boolean;
}
