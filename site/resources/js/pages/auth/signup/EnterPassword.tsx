import React, { useEffect } from 'react';

import { useFormik } from 'formik';
import { useTranslation } from 'react-i18next';
import { useLocation } from 'react-router-dom';

// Components
import { AuthWrapper } from '@templates';
import { CustomButton, PasswordInput, Title } from '@components/common';

// Hooks
import useUserProfile from '@customHooks/useUserProfile';
import { useShowMessage } from '@customHooks/useShowMessage';

// Redux
import { useAppDispatch } from '@redux/hook';
import { setSignupData } from '@redux/reducers/auth';
import { useRegisterUserMutation } from '@redux/services/authApi';

// Others
import { EnterPasswordValues, enterPasswordSchema } from '../schema';

const initialValues: EnterPasswordValues = {
  password: '',
  confirmPassword: '',
};

const EnterPassword = () => {
  const dispatch = useAppDispatch();
  const { t } = useTranslation(['auth']);
  const { showSuccess } = useShowMessage();

  const route = useLocation();
  const params = route.state?.params ?? {};

  const { userType, isJobSeeker, signupData, handleSetAuthData } =
    useUserProfile();
  const { email, countryCode, phone } = signupData;
  const [registerUser, { isLoading, isSuccess, data }] =
    useRegisterUserMutation();

  useEffect(() => {
    if (isSuccess && data) {
      handleSetAuthData({
        isLoggedIn: false,
        accessToken: data.data.token,
        user: data.data.user,
        provider: data.data.provider,
      });
      showSuccess(t('register.success', { ns: 'messages' }));
    }
  }, [isSuccess, data, isJobSeeker]);

  const handleRegisterUser = () => {
    const { password, confirmPassword } = values;
    registerUser({
      user_type: userType,
      email,
      password,
      password_confirmation: confirmPassword,
      phone,
      country_code: countryCode,
    });
  };

  const handleSubmitPassword = () => {
    const { password, confirmPassword } = values;
    dispatch(setSignupData({ password, confirmPassword }));
    handleRegisterUser();
  };

  const { handleBlur, handleChange, handleSubmit, values, touched, errors } =
    useFormik({
      initialValues: initialValues,
      validationSchema: enterPasswordSchema,
      onSubmit: handleSubmitPassword,
    });

  // If user directly entered url on browser, then show nothing
  if (!params.isAllowed) {
    return null;
  }

  return (
    <AuthWrapper>
      <div className="flex flex-col gap-2 bg-white shadow-lg border-GRAY_ACACAC rounded-md p-4 sm:w-3/4 md:w-1/2 lg:w-2/5">
        <Title type="heading2" className="text-center" bold>
          {t('enterYourPassword')}
        </Title>
        <Title
          type="body2"
          className="text-center text-GRAY_5E5E5E leading-5 my-2">
          {t('enterPasswordDescription')}
        </Title>
        <PasswordInput
          label={t('password')}
          placeholder="**********"
          onChange={handleChange('password')}
          onBlur={handleBlur('password')}
          value={values.password}
          error={errors.password && touched.password ? errors.password : null}
          required
        />
        <PasswordInput
          label={t('confirmPassword')}
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
          title={t('next', { ns: 'common' })}
          className="my-6"
          onClick={() => handleSubmit()}
          loading={isLoading}
        />
      </div>
    </AuthWrapper>
  );
};

export default EnterPassword;
