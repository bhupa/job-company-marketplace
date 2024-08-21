import React, { useEffect, useRef, useState } from 'react';

import { Grid } from 'antd';
import OtpInput from 'react-otp-input';
import { useTranslation } from 'react-i18next';
import { useNavigate } from 'react-router-dom';
import { RecaptchaVerifier, signInWithPhoneNumber } from 'firebase/auth';

// Components
import { CustomButton, Timer, Title } from '@components/common';

// Hooks
import { useShowMessage } from '@customHooks/useShowMessage';

// Redux
import { useOtpCountMutation } from '@redux/services/authApi';

// Others
import { auth } from '@pages/index';
import { CELL_COUNT } from '@utils/constants';

interface Props {
  type: 'signup' | 'forgot';
  confirmation: any;
  phoneNumber: string;
}

const { useBreakpoint } = Grid;

const OTPScreen = ({ type, confirmation, phoneNumber }: Props) => {
  const { t } = useTranslation(['auth', 'messages']);
  const { showError, showSuccess } = useShowMessage();

  const { md } = useBreakpoint();

  const navigate = useNavigate();

  const [otpValue, setOtpValue] = useState<string>('');
  const [isLoading, setLoading] = useState<boolean>(false);
  const [isOtpResent, setOtpResent] = useState<boolean>(false);
  const [isResendOtpLoading, setResendOtpLoading] = useState<boolean>(false);
  const [confirmationObj, setConfirmationObject] = useState<any>(confirmation);

  const recaptchaVerifierRef = useRef<any>(null);

  const [countOtp, { isSuccess, isError }] = useOtpCountMutation();

  useEffect(() => {
    // Initialize reCAPTCHA verifier when component mounts
    recaptchaVerifierRef.current = new RecaptchaVerifier(
      auth,
      'otp-recaptcha-container',
      {
        size: 'invisible',
        callback: (response: any) => {
          console.log('Recaptcha verified:', response);
        },
        'expired-callback': () => {
          console.log('Recaptcha expired');
        },
      },
    );
  }, []);

  useEffect(() => {
    if (isSuccess) {
      handleResendOtp();
    }
  }, [isSuccess]);

  useEffect(() => {
    if (isError) {
      setResendOtpLoading(false);
    }
  }, [isError]);

  const handleVerifyOtp = async () => {
    try {
      setLoading(true);
      const userCredential = await confirmationObj.confirm(otpValue);
      const user = userCredential.user;
      if (user) {
        if (type === 'signup') {
          return navigate('/enter-email', {
            // Passed isAllowed params so that user can't go to that screen directly by entering url in browser
            state: { params: { isAllowed: true } },
          });
        }
        navigate('/reset-password', { state: { params: { isAllowed: true } } });
      }
    } catch (e: any) {
      if (e.code === 'auth/invalid-verification-code') {
        return showError(t('otp.invalidCode', { ns: 'messages' }));
      }
      showError(t('somethingWrong', { ns: 'messages' }));
      console.log('Firebase OTP verification failed!', e);
    } finally {
      setLoading(false);
    }
  };

  const handleResendOtp = async () => {
    try {
      const resendConfirmation = await signInWithPhoneNumber(
        auth,
        phoneNumber,
        recaptchaVerifierRef.current,
      );
      setConfirmationObject(resendConfirmation);
      showSuccess(t('otp.firebaseResendSuccess', { ns: 'messages' }));
      setOtpResent(true);
    } catch (e) {
      console.log('Firebase OTP resend failed!', e);
    } finally {
      setResendOtpLoading(false);
    }
  };

  /**
   * First check for OTP count
   * If it exceeds max allowed OTP, then show error
   * Otherwise resend otp
   */
  const handleCheckOtpCount = () => {
    setResendOtpLoading(true);
    countOtp();
  };

  return (
    <>
      <Timer resetTimer={isOtpResent} setTimerReset={setOtpResent} />
      <Title
        type="body1"
        className={'text-BLACK_000000B2 text-center leading-6 my-6'}>
        {t('typeVerificationCode')}
      </Title>
      <OtpInput
        value={otpValue}
        onChange={setOtpValue}
        numInputs={CELL_COUNT}
        containerStyle={{
          gap: md ? 20 : 12,
          display: 'flex',
          justifyContent: 'center',
        }}
        inputStyle={{
          width: md ? 40 : 32,
          height: md ? 40 : 32,
          padding: 8,
          border: '1px solid #ACACAC',
          borderRadius: 8,
          color: '#004D80',
          fontWeight: '700',
          fontSize: md ? 24 : 20,
        }}
        renderInput={props => <input {...props} />}
      />
      <CustomButton
        title={t('next', { ns: 'common' })}
        disabled={otpValue.length !== CELL_COUNT}
        onClick={() => handleVerifyOtp()}
        className="mt-6"
        loading={isLoading}
      />
      <div id="otp-recaptcha-container" />
      <CustomButton
        title={t('sendAgain')}
        type="link"
        size="small"
        onClick={() => handleCheckOtpCount()}
        loading={isResendOtpLoading}
      />
    </>
  );
};

export default OTPScreen;
