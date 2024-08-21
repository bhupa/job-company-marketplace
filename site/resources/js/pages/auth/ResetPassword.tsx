import React, { useEffect } from 'react';

import { useFormik } from 'formik';
import { useTranslation } from 'react-i18next';
import { useLocation, useNavigate } from 'react-router-dom';

// Components
import { AuthWrapper } from '@templates';
import { CustomButton, PasswordInput, Title } from '@components/common';

// Hooks
import { useShowMessage } from '@customHooks/useShowMessage';

// Redux
import { useAppSelector } from '@redux/hook';
import { useResetPasswordMutation } from '@redux/services/authApi';

// Others
import { EnterPasswordValues, enterPasswordSchema } from './schema';

const initialValues: EnterPasswordValues = {
  password: '',
  confirmPassword: '',
};

const ResetPassword = () => {
  const { showSuccess } = useShowMessage();
  const navigate = useNavigate();
  const { t } = useTranslation(['auth', 'messages']);
  const route = useLocation();
  const params = route.state?.params ?? {};

  const [resetPassword, { isSuccess, isLoading }] = useResetPasswordMutation();

  const { forgotData } = useAppSelector(state => state.auth);

  useEffect(() => {
    if (isSuccess) {
      showSuccess(t('password.resetSuccess', { ns: 'messages' }));
      navigate('/login');
    }
  }, [isSuccess]);

  const handleSubmitPassword = () => {
    const { password, confirmPassword } = values;
    resetPassword({
      user_id: forgotData.userId,
      password,
      password_confirmation: confirmPassword,
    });
  };

  const { handleBlur, handleChange, handleSubmit, values, touched, errors } =
    useFormik({
      initialValues: initialValues,
      validationSchema: enterPasswordSchema,
      onSubmit: handleSubmitPassword,
    });

  if (!params.isAllowed) {
    return null;
  }

  return (
    <AuthWrapper>
      <div className="flex flex-col gap-2 bg-white shadow-lg border-GRAY_ACACAC rounded-md p-4 sm:w-3/4 md:w-1/2 lg:w-2/5">
        <Title type="heading2" className="text-center" bold>
          {t('resetPassword')}
        </Title>
        <Title type="body2" className="text-center text-GRAY_5E5E5E">
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
          title={t('submit', { ns: 'common' })}
          className="my-4"
          onClick={() => handleSubmit()}
          loading={isLoading}
        />
      </div>
    </AuthWrapper>
  );
};

export default ResetPassword;
