import React, { useEffect, useState } from 'react';

import { Spin } from 'antd';
import { useFormik } from 'formik';
import { useTranslation } from 'react-i18next';
import { useNavigate } from 'react-router-dom';

// Components
import { AuthProfileWrapper } from '@templates';
import { ProfilePicker } from '@components/profile';
import {
  CustomButton,
  CustomInput,
  DateInput,
  HeaderWithBackButton,
  HelperText,
} from '@components/common';

// Hooks
import useJobSeekerProfileInput from '@customHooks/useJobSeekerProfileInput';

// Redux
import { useAppDispatch } from '@redux/hook';
import { saveAccessToken } from '@redux/reducers/auth';
import { useRefreshTokenQuery } from '@redux/services/authApi';

// Utils
import { ProfileDetailValues, profileDetailSchema } from './schema';

const initialValues: ProfileDetailValues = {
  firstName: '',
  lastName: '',
  dob: new Date(),
};

const ProfileDetail = () => {
  const { t } = useTranslation(['jobseeker']);
  const dispatch = useAppDispatch();
  const navigate = useNavigate();

  const { handleSetProfileData, profileInput } = useJobSeekerProfileInput();

  const { data: refreshData, isSuccess: isRefreshSuccess } =
    useRefreshTokenQuery();

  const [isInitialLoading, setInitialLoading] = useState<boolean>(true);

  useEffect(() => {
    if (isRefreshSuccess) {
      dispatch(saveAccessToken(refreshData.data.token));
    }
  }, [isRefreshSuccess]);

  useEffect(() => {
    if (profileInput) {
      setFieldValue('firstName', profileInput.firstName);
      setFieldValue('lastName', profileInput.lastName);
    }
    setInitialLoading(false);
  }, []);

  useEffect(() => {
    if (profileInput.profileImg) {
      setFieldValue('profileImage', [profileInput.profileImg]);
    } else {
      setFieldValue('profileImage', '');
    }
  }, [profileInput]);

  /**
   * Add profile detail to redux state
   */
  const handleAddProfileDetail = () => {
    const { firstName, lastName, dob } = values;
    handleSetProfileData({
      firstName,
      lastName,
      birthday: dob?.toString() ?? '',
      isCompleted: false,
    });
    navigate('/gender');
  };

  const {
    handleBlur,
    handleChange,
    handleSubmit,
    setFieldValue,
    setTouched,
    values,
    touched,
    errors,
  } = useFormik({
    initialValues: initialValues,
    validationSchema: profileDetailSchema,
    onSubmit: handleAddProfileDetail,
  });

  return (
    <AuthProfileWrapper>
      {isInitialLoading ? (
        <Spin />
      ) : (
        <div className="flex flex-col gap-4">
          <HeaderWithBackButton
            title={t('profileDetail')}
            hasBackButton={false}
          />
          <ProfilePicker
            imageUrl={
              profileInput.profileImg
                ? URL.createObjectURL(profileInput.profileImg)
                : undefined
            }
          />
          <CustomInput
            label={t('firstName')}
            placeholder="First Name"
            onChange={handleChange('firstName')}
            onBlur={handleBlur('firstName')}
            value={values.firstName}
            error={
              errors.firstName && touched.firstName ? errors.firstName : null
            }
            autoCapitalize="words"
            required
          />
          <CustomInput
            label={t('lastName')}
            placeholder="Last Name"
            onChange={handleChange('lastName')}
            onBlur={handleBlur('lastName')}
            value={values.lastName}
            error={errors.lastName && touched.lastName ? errors.lastName : null}
            autoCapitalize="words"
            required
          />
          <DateInput
            placeholder={t('chooseBirthdayDate')}
            date={touched.dob ? values.dob : undefined}
            setDate={dob => {
              setTouched({ dob: true });
              setFieldValue('dob', dob);
            }}
            maximumDate={new Date().setFullYear(new Date().getFullYear() - 18)}
            error={errors.dob && touched.dob ? (errors.dob as string) : null}
          />
          <HelperText message={'validation.changeProfileDetails'} />
          <CustomButton
            title={t('next', { ns: 'common' })}
            onClick={() => handleSubmit()}
          />
        </div>
      )}
    </AuthProfileWrapper>
  );
};

export default ProfileDetail;
