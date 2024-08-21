import React, { useState } from 'react';

import { Dropdown } from 'antd';
import { useTranslation } from 'react-i18next';

// Components
import { Person } from '@assets/icons';
import { Title } from '@components/common';

// Hooks
import useUserProfile from '@customHooks/useUserProfile';
import { useShowMessage } from '@customHooks/useShowMessage';

// Others
import { IChatItem } from '@redux/services/chatApi';
import { getDateInTimeMonthDayFormat } from '@utils/dateUtils';

interface Props {
  item: IChatItem;
  lastSeenTime?: string | null;
  isLastMessage?: boolean;
  isChatDisabled: boolean;
}

const ChatMessage = (props: Props) => {
  const { item, lastSeenTime, isLastMessage, isChatDisabled } = props;

  const { t } = useTranslation(['messages']);

  const { showSuccess } = useShowMessage();
  const { user } = useUserProfile();

  const { message, createdAt, send_by, seen } = item;
  const isSelfMsg = send_by?.userId === user.id;

  const [isTimeVisible, setTimeVisible] = useState<boolean>(false);

  const handleCopyText = () => {
    navigator.clipboard
      .writeText(message)
      .then(() => showSuccess(t('textCopied')))
      .catch(error => {
        console.log('Error copying text to clipboard:', error);
      });
  };

  let isSeen = false;
  if (isSelfMsg) {
    isSeen = seen
      ? true
      : lastSeenTime
        ? new Date(lastSeenTime) >= new Date(createdAt)
        : false;
  }

  return (
    <div className="flex flex-col">
      {isTimeVisible && (
        <Title
          type="caption2"
          className={'text-center text-GRAY_545454 px-2 my-1'}>
          {getDateInTimeMonthDayFormat(createdAt)}
        </Title>
      )}
      <div
        className={`flex gap-1 max-w-[80%] ${!isSelfMsg ? 'self-start' : 'self-end'}`}>
        {!isSelfMsg &&
          (!isChatDisabled && send_by.image ? (
            <img
              src={send_by.image}
              alt="sender-avatar"
              className="flex w-6 h-6 rounded-full self-end border border-white"
            />
          ) : (
            <Person className="flex min-w-6 max-w-6 min-h-6 max-h-6 rounded-full text-GRAY_ACACAC border border-white self-center" />
          ))}
        <Dropdown
          menu={{
            items: [
              { label: 'Copy', key: '1', onClick: () => handleCopyText() },
            ],
          }}
          trigger={['contextMenu']}>
          <button
            onClick={() => setTimeVisible(!isTimeVisible)}
            className={`flex flex-col gap-1 bg-BLUE_004D801A rounded-lg px-2 py-1 ${!isSelfMsg ? 'self-start' : ''}`}>
            <Title type="body2" className="text-left whitespace-break-spaces">
              {message}
            </Title>
          </button>
        </Dropdown>
      </div>
      {((isLastMessage && isSelfMsg) || isTimeVisible) && (
        <Title
          type="caption2"
          className={`text-GRAY_545454 px-2 mt-1 mb-0 ${!isSelfMsg ? 'ml-6 text-left' : 'text-right'}`}>
          {!isSelfMsg || isSeen
            ? t('seen', { ns: 'chat' })
            : t('sent', { ns: 'chat' })}
        </Title>
      )}
    </div>
  );
};

export default ChatMessage;
