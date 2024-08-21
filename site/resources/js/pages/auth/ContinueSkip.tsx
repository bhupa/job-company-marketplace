import React, { useEffect } from 'react';

import { Spin } from 'antd';
import { useAppDispatch } from '@redux/hook';
import { useTranslation } from 'react-i18next';
import { useNavigate } from 'react-router-dom';

// Components
import { AuthProfileWrapper } from '@templates';
import { CustomButton, HeaderWithBackButton, Title } from '@components/common';

// Hooks
import useUserProfile from '@customHooks/useUserProfile';
import { useShowMessage } from '@customHooks/useShowMessage';
import useCompanyProfileInput from '@customHooks/useCompanyProfileInput';
import useJobSeekerProfileInput from '@customHooks/useJobSeekerProfileInput';

// Redux
import { setLoggedIn } from '@redux/reducers/auth';
import { initialJobSeekerProfileInput } from '@redux/reducers/jobSeeker';

const ContinueSkip = () => {
  const navigate = useNavigate();
  const dispatch = useAppDispatch();
  const { t } = useTranslation(['messages', 'auth']);

  const { showSuccess } = useShowMessage();
  const { isJobSeeker } = useUserProfile();

  /////////////////////////////////
  // Jobseeker
  /////////////////////////////////
  const {
    handleSetProfileData,
    handleCreateProfile,
    isLoading,
    isSuccess,
    data,
    profileInput,
  } = useJobSeekerProfileInput();

  useEffect(() => {
    if (isSuccess && data) {
      showSuccess(t('profile.success'));
      handleSetProfileData(initialJobSeekerProfileInput);
      dispatch(setLoggedIn(true));
    }
  }, [isSuccess, data]);

  useEffect(() => {
    if (profileInput.isCompleted) {
      handleCreateProfile();
    }
  }, [profileInput]);

  /////////////////////////////////
  // Company
  /////////////////////////////////
  const {
    handleCreateProfile: handleCreateCompanyProfile,
    handleSetCompanyProfileInputData,
    isLoading: isCompanyCreateLoading,
    isSuccess: isCompanyCreateSuccess,
    data: companyData,
    profile,
  } = useCompanyProfileInput();

  useEffect(() => {
    if (profile?.isCompleted) {
      handleSetCompanyProfileInputData({
        isCompleted: false,
      });
      /**
       * When user is not logged in, create company profile as well as first job
       */
      handleCreateCompanyProfile({ skip: true });
    }
  }, [profile]);

  /**
   * When company profile and job create is success
   */
  useEffect(() => {
    if (isCompanyCreateSuccess && companyData) {
      showSuccess(t('profile.success'));
      dispatch(setLoggedIn(true));
    }
  }, [isCompanyCreateSuccess, companyData]);

  // Continue registration flow
  const handleContinue = () => {
    navigate(isJobSeeker ? '/country' : '/jobs/policy', { replace: true });
  };

  // Skip registration flow and create profile
  const handleSubmit = () => {
    if (isJobSeeker) {
      handleSetProfileData({
        isCompleted: true,
      });
    } else {
      handleSetCompanyProfileInputData({
        isCompleted: true,
      });
    }
  };

  return (
    <AuthProfileWrapper>
      <div className="flex flex-col gap-4 w-full max-w-md">
        <HeaderWithBackButton title={''} hasBackButton={false} />
        {isLoading || isCompanyCreateLoading ? (
          <Spin />
        ) : (
          <div className="flex flex-col gap-4 w-full h-full justify-center items-center card px-6">
            <Title type="body1" className="text-center">
              {t('wouldYouFillAllDetails', { ns: 'auth' })}
            </Title>
            <div className="flex gap-4 w-full justify-center">
              <CustomButton
                title={'Now'}
                className="my-6"
                onClick={() => handleContinue()}
              />
              <CustomButton
                title={'Later'}
                className="my-6"
                onClick={() => handleSubmit()}
              />
            </div>
          </div>
        )}
      </div>
    </AuthProfileWrapper>
  );
};

export default ContinueSkip;
