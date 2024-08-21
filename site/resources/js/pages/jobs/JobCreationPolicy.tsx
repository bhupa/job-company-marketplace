import React, { useState } from 'react';

import { Card, Checkbox, Spin } from 'antd';
import { useTranslation } from 'react-i18next';
import { useNavigate } from 'react-router-dom';

// Components
import { CustomButton, Title } from '@components/common';
import { AuthProfileWrapper, DashboardWrapper } from '@templates';

// Hooks
import useUserProfile from '@customHooks/useUserProfile';

// Redux
import { useGetContentDataQuery } from '@redux/services/dataApi';

const JobCreationPolicy = () => {
  const { t, i18n } = useTranslation(['jobs']);

  const navigate = useNavigate();

  const { isProfileComplete } = useUserProfile();

  const [isChecked, setChecked] = useState<boolean>(false);

  const { data: policyData, isLoading: isTermsLoading } =
    useGetContentDataQuery({ type: 'job_policy' });

  const title = policyData?.data?.[i18n.language]?.title;
  const link = policyData?.data?.[i18n.language]?.link;
  const description = policyData?.data?.[i18n.language]?.description;
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

  const AppLayout = !isProfileComplete ? AuthProfileWrapper : DashboardWrapper;

  return (
    <AppLayout>
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
            className="mt-4"
            disabled={!isChecked}
            onClick={() =>
              navigate(
                isProfileComplete ? '/jobs/create' : '/profile/jobs/create',
              )
            }
          />
        </Card>
      )}
    </AppLayout>
  );
};

export default JobCreationPolicy;
