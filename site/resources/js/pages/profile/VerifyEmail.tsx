import React, { useEffect, useState } from 'react';

import { Card, Spin } from 'antd';
import { useFormik } from 'formik';
import { useTranslation } from 'react-i18next';
import { useNavigate } from 'react-router-dom';

// Components
import {
  CustomButton,
  CustomInput,
  HeaderWithBackButton,
  Title,
} from '@components/common';
import { DashboardWrapper } from '@templates';

// Hooks
import useCompany from '@customHooks/useCompany';
import useJobSeeker from '@customHooks/useJobSeeker';
import useUserProfile from '@customHooks/useUserProfile';
import { useShowMessage } from '@customHooks/useShowMessage';

// Redux
import {
  useUpdateEmailMutation,
  useVerifyEmailMutation,
} from '@redux/services/authApi';

// Others
import { EnterEmailValues, enterEmailSchema } from '@pages/auth/schema';

const initialValues: EnterEmailValues = {
  email: '',
};

const VerifyEmail = () => {
  const { t } = useTranslation(['profile']);
  const { showSuccess } = useShowMessage();
  const navigate = useNavigate();

  const { isJobSeeker } = useUserProfile();
  const useProfileData = isJobSeeker ? useJobSeeker : useCompany;
  const { user } = useProfileData();
  const { isEmailVerified, email: userEmail } = user ?? {};

  const [verifyEmail, { isLoading, isSuccess }] = useVerifyEmailMutation();
  const [
    updateEmail,
    { isLoading: isUpdateEmailLoading, isSuccess: isUpdateEmailSuccess },
  ] = useUpdateEmailMutation();

  const [isChangeEmail, setChangeEmail] = useState<boolean>(false);

  useEffect(() => {
    if (isUpdateEmailSuccess) {
      showSuccess(t('emailChange.success', { ns: 'messages' }));
    }
  }, [isUpdateEmailSuccess]);

  const handleChangeEmail = () => {
    const { email } = values;
    updateEmail(email);
  };

  const { handleBlur, handleChange, handleSubmit, values, touched, errors } =
    useFormik({
      initialValues: initialValues,
      validationSchema: enterEmailSchema,
      onSubmit: handleChangeEmail,
    });

  return (
    <DashboardWrapper>
      <Card className="w-full">
        <HeaderWithBackButton
          title={t('emailSettings')}
          onBackPressed={() => navigate(-1)}
        />
        {(isLoading || isUpdateEmailLoading) && <Spin fullscreen />}
        <div className="flex flex-col gap-4 mt-4 max-w-lg mx-auto">
          {!isChangeEmail ? (
            isEmailVerified ? (
              <>
                <Title type="body1" className={'text-GREEN_4EBE59 text-center'}>
                  {t('verifyEmail.emailVerified1')}
                  <Title type="body1" className={'text-GREEN_4EBE59'} bold>
                    {userEmail}
                  </Title>
                  {t('verifyEmail.emailVerified2')}
                </Title>
                <Title type="body1" className="text-center">
                  {t('verifyEmail.verifiedUserChangeEmail')}
                </Title>
              </>
            ) : isSuccess ? (
              <>
                <Title type="body1" className="text-center">
                  {t('verifyEmail.emailSent1')}
                  <Title type="body1" bold>
                    {userEmail}
                  </Title>
                  <Title type="body1">{t('verifyEmail.emailSent2')}</Title>
                </Title>
              </>
            ) : (
              <>
                <Title type="body1" className="text-center">
                  {t('verifyEmail.notVerified1')}
                  <Title type="body1" bold>
                    {userEmail}
                  </Title>
                  <Title type="body1">{t('verifyEmail.notVerified2')}</Title>
                </Title>
              </>
            )
          ) : null}
          {isChangeEmail && (
            <>
              <Title type="body2">{t('verifyEmail.enterNewEmail')}</Title>
              <CustomInput
                label={t('email', { ns: 'auth' })}
                placeholder="example@gmail.com"
                onChange={handleChange('email')}
                onBlur={handleBlur('email')}
                value={values.email}
                error={errors.email && touched.email ? errors.email : null}
                required
              />
            </>
          )}
          {!isSuccess && (
            <>
              {!isChangeEmail && !isEmailVerified && (
                <CustomButton
                  title={t('verifyEmail.verifyNow')}
                  onClick={() => verifyEmail()}
                />
              )}
              <CustomButton
                title={t('verifyEmail.changeEmail')}
                type={isChangeEmail || isEmailVerified ? 'primary' : 'default'}
                onClick={() => {
                  !isChangeEmail ? setChangeEmail(true) : handleSubmit();
                }}
              />
              {isChangeEmail && (
                <div className="flex items-center self-center gap-1">
                  <Title type="caption1">
                    {t('verifyEmail.keepUsingCurrentEmail')}
                  </Title>
                  <CustomButton
                    type="link"
                    title={t('verifyEmail.goBack')}
                    onClick={() => setChangeEmail(false)}
                  />
                </div>
              )}
            </>
          )}
        </div>
      </Card>
    </DashboardWrapper>
  );
};

export default VerifyEmail;
