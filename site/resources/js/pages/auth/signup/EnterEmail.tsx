import React, { useEffect } from 'react';

import { useFormik } from 'formik';
import { useTranslation } from 'react-i18next';
import { useLocation, useNavigate } from 'react-router-dom';

// Components
import { AuthWrapper } from '@templates';
import { CustomButton, CustomInput, Title } from '@components/common';

// Redux
import { useAppDispatch } from '@redux/hook';
import { setSignupData } from '@redux/reducers/auth';
import { useCheckPhoneMutation } from '@redux/services/authApi';

// Others
import { EnterEmailValues, enterEmailSchema } from '../schema';

const initialValues: EnterEmailValues = {
  email: '',
};

const EnterEmail = () => {
  const dispatch = useAppDispatch();
  const navigate = useNavigate();
  const { t } = useTranslation(['auth']);
  const route = useLocation();
  const params = route.state?.params ?? {};

  const [checkEmail, { isLoading, isSuccess }] = useCheckPhoneMutation();

  useEffect(() => {
    if (isSuccess) {
      handleSubmitEmail();
    }
  }, [isSuccess]);

  const handleCheckRegisteredEmail = () => {
    const { email } = values;
    checkEmail({
      email,
    });
  };

  const handleSubmitEmail = () => {
    const { email } = values;
    dispatch(setSignupData({ email }));
    navigate('/enter-password', { state: { params: { isAllowed: true } } });
  };

  const { handleBlur, handleChange, handleSubmit, values, touched, errors } =
    useFormik({
      initialValues: initialValues,
      validationSchema: enterEmailSchema,
      onSubmit: handleCheckRegisteredEmail,
    });

  // If user directly entered url on browser, then show nothing
  if (!params.isAllowed) {
    return null;
  }

  return (
    <AuthWrapper>
      <div className="flex flex-col gap-2 bg-white shadow-lg border-GRAY_ACACAC rounded-md p-4 sm:w-3/4 md:w-1/2 lg:w-2/5">
        <Title type="heading2" className="text-center" bold>
          {t('enterYourEmail')}
        </Title>
        <Title
          type="body2"
          className={'text-GRAY_5E5E5E text-center leading-6 my-2'}>
          {t('enterEmailDescription')}
        </Title>
        <CustomInput
          label={t('email')}
          placeholder="example@gmail.com"
          onChange={handleChange('email')}
          onBlur={handleBlur('email')}
          value={values.email}
          error={errors.email && touched.email ? errors.email : null}
          required
        />
        <CustomButton
          title={t('next', { ns: 'common' })}
          className={'mt-6'}
          onClick={() => handleSubmit()}
          loading={isLoading}
        />
      </div>
    </AuthWrapper>
  );
};

export default EnterEmail;
