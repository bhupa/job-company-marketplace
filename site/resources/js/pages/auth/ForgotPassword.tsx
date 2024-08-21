import React, { useEffect, useRef, useState } from 'react';

import {
  RecaptchaVerifier,
  signInWithPhoneNumber,
  signOut,
} from 'firebase/auth';
import { useFormik } from 'formik';
import { useTranslation } from 'react-i18next';
import { useNavigate } from 'react-router-dom';

// Components
import { OTPScreen } from './signup/forms';
import { AuthWrapper } from '@templates';
import { CustomButton, CustomInput, Title } from '@components/common';

// Hooks
import { useShowMessage } from '@customHooks/useShowMessage';

// Redux
import { useAppDispatch } from '@redux/hook';
import { setForgotData } from '@redux/reducers/auth';
import { useForgotPasswordMutation } from '@redux/services/authApi';

// Others
import { auth } from '@pages/index';
import { ForgotValues, forgotSchema } from './schema';

const initialValues: ForgotValues = {
  email: '',
};

const ForgotPassword = () => {
  const { showSuccess, showError } = useShowMessage();
  const dispatch = useAppDispatch();
  const navigate = useNavigate();
  const { t } = useTranslation(['auth', 'messages']);

  const [
    forgotPassword,
    { data, isSuccess, isError, isLoading: isForgotLoading },
  ] = useForgotPasswordMutation();

  const [isLoading, setLoading] = useState<boolean>(false);
  const [isPhoneNumber, setIsPhoneNumber] = useState<boolean>(false);
  const [isOtpScreen, setOtpScreen] = useState<boolean>(false);
  const [confirmationObj, setConfirmationObj] = useState<any>();
  const [phoneNumber, setPhoneNumber] = useState<string>('');

  const recaptchaVerifierRef = useRef<any>(null);

  useEffect(() => {
    // Initialize reCAPTCHA verifier when component mounts
    recaptchaVerifierRef.current = new RecaptchaVerifier(
      auth,
      'recaptcha-container',
      {
        size: 'invisible',
        callback: () => {},
        'expired-callback': () => {
          console.log('Recaptcha expired');
        },
      },
    );
  }, []);

  useEffect(() => {
    if (isSuccess && data) {
      if (auth) {
        signOut(auth).then(() => console.log('User signed out!'));
      }
      dispatch(setForgotData(data.data));
      if (isPhoneNumber) {
        handleGetOtpCode();
      } else {
        showSuccess(t('otp.sendSuccess', { ns: 'messages' }));
        navigate('/email-otp', { state: { params: { isAllowed: true } } });
      }
    }
  }, [isSuccess]);

  useEffect(() => {
    if (isError) {
      setLoading(false);
    }
  }, [isError]);

  const handleGetOtpCode = async () => {
    if (data) {
      const { countryCode, phone } = data.data;
      const phoneNumberValue = '+' + countryCode + phone;
      try {
        const confirmation = await signInWithPhoneNumber(
          auth,
          phoneNumberValue,
          recaptchaVerifierRef.current,
        );
        setConfirmationObj(confirmation);
        setPhoneNumber(phoneNumberValue);
        setOtpScreen(true);
      } catch (e) {
        showError(t('somethingWrong', { ns: 'messages' }));
        console.log('Firebase signup failed!', e);
      } finally {
        setLoading(false);
      }
    }
  };

  const handleForgotPassword = () => {
    const { email } = values;
    const phoneRegex = /^\d{10,14}$/;
    const isPhone = phoneRegex.test(email);
    isPhone && setIsPhoneNumber(isPhone);
    setLoading(true);
    forgotPassword(isPhone ? { phone: email } : { email });
  };

  const { handleBlur, handleChange, handleSubmit, values, touched, errors } =
    useFormik({
      initialValues: initialValues,
      validationSchema: forgotSchema,
      onSubmit: handleForgotPassword,
    });

  return (
    <AuthWrapper>
      <div className="flex flex-col gap-2 bg-white shadow-lg border-GRAY_ACACAC rounded-md p-4 sm:w-3/4 md:w-1/2 lg:w-2/5">
        {!isOtpScreen ? (
          <>
            <Title type="heading2" bold className="mb-6 text-center">
              {t('forgotPassword')}
            </Title>
            <Title
              type="body1"
              bold
              className={'text-GRAY_5E5E5E text-center leading-6'}>
              {t('forgotPasswordDescription')}
            </Title>
            <Title type="body2" className={'text-GRAY_A6A6A6 text-center my-4'}>
              {t('forgotPasswordHelperText')}
            </Title>
            <CustomInput
              label={t('emailPhone')}
              placeholder="example@gmail.com"
              onChange={handleChange('email')}
              onBlur={handleBlur('email')}
              value={values.email}
              error={errors.email && touched.email ? errors.email : null}
              required
            />
            <div id="recaptcha-container" />
            <CustomButton
              title={t('send', { ns: 'common' })}
              className="my-6"
              onClick={() => handleSubmit()}
              loading={isLoading || isForgotLoading}
            />
          </>
        ) : (
          <OTPScreen
            type="forgot"
            confirmation={confirmationObj}
            phoneNumber={phoneNumber}
          />
        )}
      </div>
    </AuthWrapper>
  );
};

export default ForgotPassword;
