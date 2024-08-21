import React, { useState } from 'react';

import { Card, Checkbox, Spin } from 'antd';
import { useTranslation } from 'react-i18next';
import { useNavigate } from 'react-router-dom';

// Components
import { DashboardWrapper } from '@templates';
import { CustomButton, HeaderWithBackButton, Title } from '@components/common';

// Redux
import { useAppDispatch } from '@redux/hook';
import { setIsChatPolicyAccepted } from '@redux/reducers/chat';
import { useGetContentDataQuery } from '@redux/services/dataApi';

const ChatPolicy = () => {
  const { t, i18n } = useTranslation(['chat', 'messages']);

  const navigate = useNavigate();
  const dispatch = useAppDispatch();

  const [isChecked, setChecked] = useState<boolean>(false);

  const { data: chatPolicyData, isLoading: isDataLoading } =
    useGetContentDataQuery({ type: 'chat_policy' });

  const title = chatPolicyData?.data?.[i18n.language]?.title;
  const link = chatPolicyData?.data?.[i18n.language]?.link;
  const description = chatPolicyData?.data?.[i18n.language]?.description;
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

  const handleAcceptPolicy = () => {
    dispatch(setIsChatPolicyAccepted(true));
    navigate('/chat', { replace: true });
  };

  return (
    <DashboardWrapper>
      {isDataLoading ? (
        <Spin />
      ) : (
        <Card className="w-full">
          <HeaderWithBackButton
            title={title}
            onBackPressed={() => navigate('/home')}
          />
          <Title
            type="caption1"
            className="text-justify my-4 leading-6"
            tagName="p">
            {formattedText}
          </Title>
          {link && (
            <CustomButton
              type="link"
              title={link}
              onClick={() => handleOpenLink()}
              className="flex px-0 py-0"
            />
          )}
          <div className="my-4">
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
            disabled={!isChecked}
            onClick={() => handleAcceptPolicy()}
          />
        </Card>
      )}
    </DashboardWrapper>
  );
};

export default ChatPolicy;
