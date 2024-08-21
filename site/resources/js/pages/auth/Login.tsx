import React, { useEffect, useState } from 'react';

import { Radio } from 'antd';
import { useFormik } from 'formik';
import { useTranslation } from 'react-i18next';
import { useNavigate } from 'react-router-dom';

// Components
import AuthWrapper from '@templates/AuthWrapper';
import {
  AppLogo,
  CustomButton,
  CustomInput,
  PasswordInput,
  Title,
} from '@components/common';
import { FacebookLoginButton, GoogleLoginButton } from '@components/auth';

// Hooks
import useUserProfile from '@customHooks/useUserProfile';
import { useShowMessage } from '../../customHooks/useShowMessage';

// Redux
import { useAppDispatch } from '@redux/hook';
import { UserType, setUserType } from '@redux/reducers/auth';
import { ILoginParams, useLoginMutation } from '@redux/services/authApi';

// Others
import { LoginValues, loginSchema } from './schema';

const initialValues: LoginValues = {
  email: '',
  password: '',
};

const Login = () => {
  const { t } = useTranslation(['auth']);
  const navigate = useNavigate();
  const { showSuccess } = useShowMessage();
  const [login, { data, isLoading, isSuccess }] = useLoginMutation();

  const dispatch = useAppDispatch();
  const { handleSetAuthData, userType } = useUserProfile();

  const [selectedUserType, setSelectedUserType] = useState<UserType>(userType);

  useEffect(() => {
    const urlParams = new URLSearchParams(window.location.search);
    const verified = urlParams.get('verified');
    if (verified === 'true') {
      showSuccess(t('emailVerified'));
      const url = new URL(window.location.href);
      url.searchParams.delete('verified');
      navigate(url.pathname + url.search);
    }
  }, []);

  useEffect(() => {
    if (isSuccess && data) {
      const { token, user, provider } = data.data;
      handleSetAuthData({
        isLoggedIn: true,
        user,
        accessToken: token,
        provider: provider,
      });
    }
  }, [isSuccess]);

  const handleLogin = () => {
    const { email, password } = values;
    const phoneRegex = /^\d{10,14}$/;
    const isPhoneNumber = phoneRegex.test(email);
    let params: ILoginParams = {
      email,
      password,
    };
    if (isPhoneNumber) {
      params = {
        phone: email,
        password,
      };
    }
    login(params);
  };

  const { handleBlur, handleChange, handleSubmit, values, touched, errors } =
    useFormik({
      initialValues: initialValues,
      validationSchema: loginSchema,
      onSubmit: handleLogin,
    });

  return (
    <AuthWrapper>
      <div className="flex flex-col gap-2 bg-white shadow-lg border-GRAY_ACACAC rounded-md p-4 w-full sm:w-3/4 md:w-1/2 lg:w-2/5">
        <AppLogo type="secondary" small className="flex self-center" />
        <Radio.Group
          value={selectedUserType}
          className="flex self-center"
          onChange={e => {
            setSelectedUserType(e.target.value);
            dispatch(setUserType(e.target.value));
          }}>
          <Radio.Button value={UserType.Company}>{t('employer')}</Radio.Button>
          <Radio.Button value={UserType.JobSeeker}>
            {t('jobseeker')}
          </Radio.Button>
        </Radio.Group>
        <CustomInput
          label={t('emailPhone')}
          placeholder="example@gmail.com"
          required
          name="email"
          onChange={handleChange}
          onBlur={handleBlur}
          value={values.email}
          error={errors.email && touched.email ? errors.email : null}
        />
        <PasswordInput
          label={t('password')}
          placeholder="********"
          required
          name="password"
          onChange={handleChange}
          onBlur={handleBlur}
          value={values.password}
          error={errors.password && touched.password ? errors.password : null}
        />
        <span>
          <Title type="caption1">{t('forgotPassword')}</Title>
          <CustomButton
            type="link"
            title={t('clickHere')}
            className="pl-1 py-0 pr-0"
            onClick={() => navigate('/forgot-password')}
          />
        </span>
        <CustomButton
          title={t('signIn')}
          onClick={() => handleSubmit()}
          loading={isLoading}
          className="mt-6"
        />
        <div className="flex flex-wrap gap-4 justify-center items-center my-4">
          <FacebookLoginButton />
          <GoogleLoginButton />
        </div>
        <div className="flex flex-wrap items-center justify-center">
          <Title type="body2">{t('dontHaveAccount')}</Title>
          <CustomButton
            type="link"
            size="small"
            title={t('signUp')}
            onClick={() => navigate('/signup')}
          />
        </div>
      </div>
    </AuthWrapper>
  );
};

export default Login;
