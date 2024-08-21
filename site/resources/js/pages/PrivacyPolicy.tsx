import React from 'react';

import { Card, Spin } from 'antd';
import { useTranslation } from 'react-i18next';

// Components
import { Title } from '@components/common';
import { AuthWrapper, DashboardWrapper } from '@templates';

// Hooks
import useUserProfile from '@customHooks/useUserProfile';

// Redux
import { useGetContentDataQuery } from '@redux/services/dataApi';

const PrivacyPolicy = () => {
  const { i18n } = useTranslation('profile');
  const { isProfileComplete } = useUserProfile();

  const { data: privacyData, isLoading: isPrivacyLoading } =
    useGetContentDataQuery({ type: 'privacy_policy' });

  const title = privacyData?.data?.[i18n.language]?.title;
  const description = privacyData?.data?.[i18n.language]?.description;

  const formattedText = (description || '')
    .replace(/\t/g, '    ')
    .replace(/\\n/g, '\n');

  const Wrapper = isProfileComplete ? DashboardWrapper : AuthWrapper;

  return (
    <Wrapper>
      {isPrivacyLoading ? (
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
        </Card>
      )}
    </Wrapper>
  );
};

export default PrivacyPolicy;
