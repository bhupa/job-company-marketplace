import React from 'react';

import { Card, Spin } from 'antd';
import { useTranslation } from 'react-i18next';
import { useNavigate } from 'react-router-dom';

// Components
import { AuthWrapper, DashboardWrapper } from '@templates';
import { HeaderWithBackButton, Title } from '@components/common';

// Hooks
import useUserProfile from '@customHooks/useUserProfile';

// Redux
import { useGetAccountDeletionGuideQuery } from '@redux/services/dataApi';

const AccountDeletionGuide = () => {
  const { t, i18n } = useTranslation(['profile']);
  const { isProfileComplete } = useUserProfile();

  const navigate = useNavigate();
  const { data, isLoading } = useGetAccountDeletionGuideQuery();

  const description = data?.data?.content?.[i18n.language];

  const formattedText = (description || '')
    .replace(/\t/g, '    ')
    .replace(/\\n/g, '\n');

  const Wrapper = isProfileComplete ? DashboardWrapper : AuthWrapper;

  return (
    <Wrapper>
      {isLoading ? (
        <Spin />
      ) : (
        <Card className="w-full">
          <HeaderWithBackButton
            title={t('accountDeletionGuide', { ns: 'auth' })}
            onBackPressed={() => navigate(-1)}
            hasBackButton={isProfileComplete}
          />
          <br />
          <Title
            type="caption1"
            className="text-justify whitespace-break-spaces"
            tagName="p">
            {formattedText}
          </Title>
        </Card>
      )}
    </Wrapper>
  );
};

export default AccountDeletionGuide;
