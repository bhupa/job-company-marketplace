import React, { useEffect, useRef, useState } from 'react';

import PhoneInputWithCountrySelect, {
  parsePhoneNumber,
} from 'react-phone-number-input';
import { useFormik } from 'formik';
import { Radio, Space } from 'antd';
import 'react-phone-number-input/style.css';
import { useTranslation } from 'react-i18next';
import { RecaptchaVerifier, signInWithPhoneNumber } from 'firebase/auth';

// Components
import { ErrorText, InputLabel } from '@components/common';
import { AppLogo, CustomButton, Title } from '@components/common';

// Hooks
import { useShowMessage } from '@customHooks/useShowMessage';

// Redux
import { useAppDispatch } from '@redux/hook';
import { useCheckPhoneMutation } from '@redux/services/authApi';
import { UserType, setSignupData, setUserType } from '@redux/reducers/auth';

// Others
import { auth } from '@pages/index';
import { SignupValues, signupSchema } from '../../schema';

interface Props {
  setConfirmationObj: (obj: any, phone: string) => void;
  goToNextStep: () => void;
}

const initialValues: SignupValues = {
  phone: '',
};

const RegisterForm = ({ setConfirmationObj, goToNextStep }: Props) => {
  const { showError } = useShowMessage();

  const dispatch = useAppDispatch();
  const { t } = useTranslation(['auth', 'messages']);

  const [checkPhone, { data, isSuccess, isError }] = useCheckPhoneMutation();

  const [selectedUserType, setSelectedUserType] = useState<UserType>(
    UserType.Company,
  );
  const [isLoading, setLoading] = useState<boolean>(false);

  const recaptchaVerifierRef = useRef<any>(null);

  useEffect(() => {
    dispatch(setUserType(UserType.Company));
  }, []);

  useEffect(() => {
    if (isSuccess) {
      if (data?.success) {
        handleSignup();
      } else {
        showError(t('register.phoneExists', { ns: 'messages' }));
        setLoading(false);
      }
    }
  }, [isSuccess]);

  useEffect(() => {
    if (isError) {
      setLoading(false);
    }
  }, [isError]);

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

  const handleCheckRegisteredPhone = () => {
    setLoading(true);
    const { phone } = values;
    const countryCode = parsePhoneNumber(phone)?.countryCallingCode;
    const phoneNumber = parsePhoneNumber(phone)?.nationalNumber;
    checkPhone({
      phone: phoneNumber,
      country_code: countryCode,
    });
  };

  const handleSignup = async () => {
    setLoading(true);
    const { phone } = values;
    const countryCode = parsePhoneNumber(phone)?.countryCallingCode;
    const phoneNumber = parsePhoneNumber(phone)?.nationalNumber;
    try {
      const confirmation = await signInWithPhoneNumber(
        auth,
        phone,
        recaptchaVerifierRef.current,
      );
      setConfirmationObj(confirmation, phone);
      dispatch(setSignupData({ countryCode, phone: phoneNumber }));
      goToNextStep();
    } catch (e) {
      showError(t('somethingWrong', { ns: 'messages' }));
      console.log('Firebase signup failed!', e);
    } finally {
      setLoading(false);
    }
  };

  const { handleBlur, handleSubmit, setFieldValue, values, touched, errors } =
    useFormik({
      initialValues: initialValues,
      validationSchema: signupSchema,
      onSubmit: handleCheckRegisteredPhone,
    });

  const errorMsg = errors.phone && touched.phone ? errors.phone : null;

  return (
    <>
      <Title type="heading2" className="text-center" bold>
        {t('signUp')}
      </Title>
      <Title type="body2" className="text-center text-GRAY_5E5E5E">
        {t('signUpDescription')}
      </Title>
      <AppLogo type="secondary" small className="flex self-center" />
      <Radio.Group
        value={selectedUserType}
        className="flex self-center"
        onChange={e => {
          setSelectedUserType(e.target.value);
          dispatch(setUserType(e.target.value));
        }}>
        <Radio.Button value={UserType.Company}>{t('employer')}</Radio.Button>
        <Radio.Button value={UserType.JobSeeker}>{t('jobseeker')}</Radio.Button>
      </Radio.Group>
      <div className="flex flex-col gap-4">
        <Space direction="vertical">
          <InputLabel label={t('phoneNumber')} required />
          <PhoneInputWithCountrySelect
            name="phone"
            placeholder="5550000000"
            value={values.phone}
            onChange={value => setFieldValue('phone', value)}
            onBlur={handleBlur}
            className="border flex gap-3 rounded-md px-2 py-1"
          />
          {errorMsg && <ErrorText error={errorMsg} />}
        </Space>
        <div id="recaptcha-container" />
        <CustomButton
          title={t('continue', { ns: 'common' })}
          onClick={() => handleSubmit()}
          loading={isLoading}
        />
      </div>
    </>
  );
};

export default RegisterForm;
