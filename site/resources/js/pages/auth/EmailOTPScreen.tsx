import React, { useEffect, useState } from 'react';

import { Grid } from 'antd';
import OTPInput from 'react-otp-input';
import { useTranslation } from 'react-i18next';
import { useLocation, useNavigate } from 'react-router-dom';

// Components
import { AuthWrapper } from '@templates';
import { CustomButton, Timer, Title } from '@components/common';

// Hooks
import { useShowMessage } from '@customHooks/useShowMessage';

// Redux
import {
  useForgotPasswordMutation,
  useVerifyOtpMutation,
} from '@redux/services/authApi';
import { useAppSelector } from '@redux/hook';

// Others
import { CELL_COUNT } from '@utils/constants';

const { useBreakpoint } = Grid;

const EmailOTPScreen = () => {
  const { t } = useTranslation(['auth', 'messages']);
  const navigate = useNavigate();
  const { showSuccess } = useShowMessage();
  const route = useLocation();
  const params = route.state?.params ?? {};

  const { md } = useBreakpoint();

  const [verifyOtp, { isSuccess, isLoading }] = useVerifyOtpMutation();
  const [
    forgotPassword,
    { isSuccess: isResentSuccess, isLoading: isResendOtpLoading },
  ] = useForgotPasswordMutation();

  const { forgotData } = useAppSelector(state => state.auth);

  const [otpValue, setOtpValue] = useState<string>('');
  const [isOtpResent, setOtpResent] = useState<boolean>(false);

  useEffect(() => {
    if (isSuccess) {
      navigate('/reset-password', { state: { params: { isAllowed: true } } });
    }
  }, [isSuccess]);

  useEffect(() => {
    if (isResentSuccess) {
      setOtpResent(true);
      showSuccess(t('otp.resendSuccess', { ns: 'messages' }));
    }
  }, [isResentSuccess]);

  const handleVerifyOtp = async () => {
    verifyOtp(otpValue);
  };

  const handleResendOtp = async () => {
    forgotPassword({ email: forgotData.email });
  };

  if (!params.isAllowed) {
    return null;
  }

  return (
    <AuthWrapper>
      <div className="flex flex-col gap-2 bg-white shadow-lg border-GRAY_ACACAC rounded-md p-4 sm:w-3/4 md:w-1/2 lg:w-2/5">
        <Timer resetTimer={isOtpResent} setTimerReset={setOtpResent} />
        <Title
          type="body1"
          className={'text-BLACK_000000B2 text-center leading-6 my-6'}>
          {t('typeVerificationCode')}
        </Title>
        <OTPInput
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
        <CustomButton
          title={t('sendAgain')}
          type="link"
          size="small"
          onClick={() => handleResendOtp()}
          className="text-center"
          loading={isResendOtpLoading}
        />
      </div>
    </AuthWrapper>
  );
};

export default EmailOTPScreen;
