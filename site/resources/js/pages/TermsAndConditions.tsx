import React, { useState } from 'react';

import { Card, Checkbox, Spin } from 'antd';
import { useTranslation } from 'react-i18next';
import { useNavigate } from 'react-router-dom';

// Components
import { AuthProfileWrapper, AuthWrapper, DashboardWrapper } from '@templates';
import { CustomButton, Title } from '@components/common';

// Hooks
import useUserProfile from '@customHooks/useUserProfile';

// Redux
import { useGetContentDataQuery } from '@redux/services/dataApi';

const TermsAndConditions = () => {
  const navigate = useNavigate();
  const { t, i18n } = useTranslation(['auth', 'messages']);

  const { isLoggedIn, accessToken, isProfileComplete } = useUserProfile();

  const [isChecked, setChecked] = useState<boolean>(false);

  const { data: termsData, isLoading: isTermsLoading } = useGetContentDataQuery(
    { type: 'terms_of_service' },
  );

  const title = termsData?.data?.[i18n.language]?.title;
  const link = termsData?.data?.[i18n.language]?.link;
  const description = termsData?.data?.[i18n.language]?.description;
  const formattedText = (description || '')
    .replace(/\t/g, '    ')
    .replace(/\\n/g, '\n');

  const handleOpenLink = () => {
    try {
      if (link) {
        window.open(link, '_blank');
      }
    } catch (e) {
      console.log('Link open error', e);
    }
  };

  const Wrapper = isLoggedIn
    ? DashboardWrapper
    : accessToken
      ? AuthProfileWrapper
      : AuthWrapper;

  return (
    <Wrapper>
      {isTermsLoading ? (
        <Spin />
      ) : (
        <Card className="w-full">
          {title && (
            <Title type="heading2" className="text-center" tagName="p">
              {title}
            </Title>
          )}
          <br />
          <Title
            type="caption1"
            className="text-justify whitespace-break-spaces"
            tagName="p">
            {formattedText}
          </Title>
          {link && (
            <CustomButton
              title={link}
              type="link"
              className="pl-1 py-0 pr-0"
              onClick={() => handleOpenLink()}
            />
          )}
          {(!isLoggedIn || !isProfileComplete) && accessToken && (
            <>
              <div className="mt-2">
                <Checkbox
                  checked={isChecked}
                  onChange={e => setChecked(e.target.checked)}>
                  <div className="flex items-center">
                    <Title type="caption2">
                      {t('readAndUnderstood', { ns: 'auth' })}&nbsp;
                    </Title>
                    <Title type="caption2" className={'text.RED_FF4D4D'}>
                      *
                    </Title>
                  </div>
                </Checkbox>
              </div>
              <CustomButton
                title={t('continue', { ns: 'common' })}
                className="w-full mt-4"
                disabled={!isChecked}
                onClick={() => navigate('/profile/detail')}
              />
            </>
          )}
        </Card>
      )}
    </Wrapper>
  );
};

export default TermsAndConditions;
