import React, { useEffect } from 'react';

import { Card } from 'antd';
import { useFormik } from 'formik';
import { useTranslation } from 'react-i18next';
import { useNavigate } from 'react-router-dom';

// Components
import {
  CustomButton,
  HeaderWithBackButton,
  PasswordInput,
} from '@components/common';
import { DashboardWrapper } from '@templates';

// Hooks
import useCompany from '@customHooks/useCompany';
import useJobSeeker from '@customHooks/useJobSeeker';
import useUserProfile from '@customHooks/useUserProfile';
import { useShowMessage } from '@customHooks/useShowMessage';

// Redux
import {
  IChangePasswordParams,
  useChangePasswordMutation,
} from '@redux/services/authApi';

// Others
import {
  ChangePasswordValues,
  changePasswordSchema,
  enterPasswordSchema,
} from '@pages/auth/schema';

const initialValues: ChangePasswordValues = {
  currentPassword: '',
  password: '',
  confirmPassword: '',
};

const ChangePassword = () => {
  const { showSuccess } = useShowMessage();
  const navigate = useNavigate();
  const { t } = useTranslation(['auth', 'messages']);
  const [changePassword, { isSuccess, isLoading }] =
    useChangePasswordMutation();

  const { isJobSeeker } = useUserProfile();
  const useProfileData = isJobSeeker ? useJobSeeker : useCompany;
  const { user } = useProfileData();
  const { isPasswordSet } = user ?? {};

  useEffect(() => {
    if (isSuccess) {
      showSuccess(t('password.resetSuccess', { ns: 'messages' }));
      navigate(-1);
    }
  }, [isSuccess]);

  const handleSubmitPassword = () => {
    const { currentPassword, password, confirmPassword } = values;
    let formData: IChangePasswordParams = {
      password,
      password_confirmation: confirmPassword,
    };
    if (isPasswordSet) {
      formData = { ...formData, old_password: currentPassword };
    }
    changePassword(formData);
  };

  const { handleBlur, handleChange, handleSubmit, values, touched, errors } =
    useFormik({
      initialValues: initialValues,
      validationSchema: !isPasswordSet
        ? enterPasswordSchema
        : changePasswordSchema,
      onSubmit: handleSubmitPassword,
    });

  return (
    <DashboardWrapper>
      <Card className="w-full">
        <HeaderWithBackButton
          title={t('changePassword', { ns: 'profile' })}
          onBackPressed={() => navigate(-1)}
        />
        <div className="flex flex-col gap-4 mt-4 max-w-lg mx-auto">
          {isPasswordSet && (
            <PasswordInput
              label={t('currentPassword')}
              placeholder="**********"
              name="currentPassword"
              onChange={handleChange('currentPassword')}
              onBlur={handleBlur('currentPassword')}
              value={values.currentPassword}
              error={
                errors.currentPassword && touched.currentPassword
                  ? errors.currentPassword
                  : null
              }
              required
            />
          )}
          <PasswordInput
            label={t('password')}
            placeholder="**********"
            name="password"
            onChange={handleChange('password')}
            onBlur={handleBlur('password')}
            value={values.password}
            error={errors.password && touched.password ? errors.password : null}
            required
          />
          <PasswordInput
            label={t('confirmPassword')}
            name="confirmPassword"
            placeholder="**********"
            onChange={handleChange('confirmPassword')}
            onBlur={handleBlur('confirmPassword')}
            value={values.confirmPassword}
            error={
              errors.confirmPassword && touched.confirmPassword
                ? errors.confirmPassword
                : null
            }
            required
          />
          <CustomButton
            title={t('submit', { ns: 'common' })}
            className="mt-4"
            onClick={() => handleSubmit()}
            loading={isLoading}
          />
        </div>
      </Card>
    </DashboardWrapper>
  );
};

export default ChangePassword;
