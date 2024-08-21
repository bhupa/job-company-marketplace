import React from 'react';

import { Dropdown } from 'antd';
import { useTranslation } from 'react-i18next';
import { useNavigate } from 'react-router-dom';

// Components
import { Title } from '@components/common';

// Hooks
import useUserProfile from '@customHooks/useUserProfile';

// Redux
import {
  setChatUnseenCountFromEvent,
  updateUnseenCount,
} from '@redux/reducers/chat';
import { useAppDispatch, useAppSelector } from '@redux/hook';

// Others
import { Person } from '@assets/icons';
import { getFormatDateInYYYYMMDD } from '@utils/dateUtils';
import { IChatListResponse } from '@redux/services/chatApi';
import { AccountStatusType } from '@redux/services/authApi';

interface IChatItem {
  item: IChatListResponse;
  onDeletePressed: () => void;
  onReportPressed: () => void;
}

const ChatListItem = (props: IChatItem) => {
  const { item, onDeletePressed, onReportPressed } = props;

  const navigate = useNavigate();
  const dispatch = useAppDispatch();

  const { tabUnseenCount } = useAppSelector(state => state.chat);

  const { t } = useTranslation(['chat']);

  const {
    isJobSeeker,
    user: { id: myUserId },
  } = useUserProfile();

  let user: { name: string; image: string; isViolated: boolean } = {
    name: '',
    image: '',
    isViolated: false,
  };
  const matchData = item.match;
  if (item.isDeleted || !matchData) {
    user = {
      name: t('deletedAccount'),
      image: '',
      isViolated: false,
    };
  } else {
    if (matchData) {
      if (isJobSeeker) {
        const companyData = matchData.company;
        const isProfileViolated =
          companyData?.user?.isViolation ||
          companyData?.user?.status === AccountStatusType.RESTRICTED;
        const isDeactivated =
          companyData?.user?.status === AccountStatusType.DEACTIVATED;
        user = {
          name: isProfileViolated
            ? t('bannedAccount')
            : isDeactivated
              ? t('deactivatedAccount')
              : companyData?.companyName ?? t('deletedAccount'),
          image: isProfileViolated || isDeactivated ? '' : companyData?.logo,
          isViolated: isProfileViolated,
        };
      } else {
        const jobSeeker = matchData.jobseeker;
        const isProfileViolated =
          jobSeeker?.user?.isViolation ||
          jobSeeker?.user?.status === AccountStatusType.RESTRICTED;
        const isDeactivated =
          jobSeeker?.user?.status === AccountStatusType.DEACTIVATED;
        user = {
          name: isProfileViolated
            ? t('bannedAccount')
            : isDeactivated
              ? t('deactivatedAccount')
              : jobSeeker
                ? `${jobSeeker.firstName}`
                : t('deletedAccount'),
          image:
            isProfileViolated || isDeactivated
              ? ''
              : jobSeeker?.profileImg ?? '',
          isViolated: isProfileViolated,
        };
      }
    }
  }

  const isRequest = item.type === 'request' && !item.isAccepted;
  const isRequestedByMe = item.match?.createdBy?.id === myUserId;

  const handleOpenChat = () => {
    if (item.unseen > 0) {
      // When chat screen is opened, reset local unread count to 0.
      dispatch(updateUnseenCount({ chatRoomId: item.id }));
      dispatch(setChatUnseenCountFromEvent(tabUnseenCount - 1));
    }
    navigate(`/chat-screen/${item.id}`, {
      state: {
        params: {
          chatRoomId: item.id,
          unreadCount: item.unseen,
          lastSeenId: item.lastSeenId,
        },
      },
    });
  };

  return (
    <Dropdown
      menu={{
        items: [
          {
            label: t('delete', { ns: 'common' }),
            key: '1',
            onClick: () => onDeletePressed(),
          },
          {
            label: t('report', { ns: 'common' }),
            key: '2',
            onClick: () => onReportPressed(),
          },
        ],
      }}
      trigger={['contextMenu']}>
      <button
        className="flex gap-4 py-2 px-3 border-b border-b-GRAY_ACACAC w-full hover:bg-WHITE_F6F6F6"
        onClick={() => handleOpenChat()}>
        {user?.image ? (
          <img
            src={user.image}
            className="flex min-w-12 max-w-12 min-h-12 max-h-12 rounded-full border border-WHITE_EFEFEF self-center"
            alt="avatar"
          />
        ) : (
          <Person className="flex min-w-12 max-w-12 min-h-12 max-h-12 rounded-full text-GRAY_ACACAC border border-WHITE_EFEFEF self-center" />
        )}
        <div className="w-full flex flex-col items-start">
          <Title type="caption1" className={'text-BLACK_1E2022 text-left'}>
            {user?.name}
          </Title>
          <Title
            type="caption2"
            className={`${
              isRequest
                ? isRequestedByMe
                  ? 'text-ORANGE_EFC269'
                  : 'text-RED_FF4D4D'
                : 'text-GRAY_77838F'
            } line-clamp-2 text-left whitespace-break-spaces`}>
            {isRequest
              ? isRequestedByMe
                ? t('requestPending')
                : t('messageRequest')
              : item.chats?.message ?? t('startConversation')}
          </Title>
        </div>
        {item.chats?.createdAt && (
          <div className="flex flex-col items-end gap-2 w-full sm:w-[30%]">
            <Title type="caption2" className={'text-GRAY_77838F'}>
              {getFormatDateInYYYYMMDD(new Date(item.chats.createdAt))}
            </Title>
            {!isRequest && item.unseen > 0 && (
              <div className="flex flex-col w-7 py-1 rounded-md bg-BLUE_004D80 items-center">
                <Title type="caption2" className={'text-white'} bold>
                  {item.unseen}
                </Title>
              </div>
            )}
          </div>
        )}
      </button>
    </Dropdown>
  );
};

export default ChatListItem;
