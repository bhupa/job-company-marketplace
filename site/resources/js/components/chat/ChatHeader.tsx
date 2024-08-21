import React, { useEffect, useState } from 'react';

import { Popover, Spin } from 'antd';
import { useNavigate } from 'react-router-dom';
import { useTranslation } from 'react-i18next';

// Components
import AdminAssistModal from './AdminAssistModal';
import { ReportUserModal } from '@components/home';
import SuperChatMailModal from './SuperChatMailModal';
import { PopperListItem, Title } from '@components/common';

// Hooks
import useUserProfile from '@customHooks/useUserProfile';
import useEsewaPayment from '@customHooks/useEsewaPayment';
import { useShowMessage } from '@customHooks/useShowMessage';

// Redux
import { useLazyGetSuperChatPriceQuery } from '@redux/services/chatApi';

// Others
import {
  AddCircle,
  ArrowLeft,
  ChatDetail,
  Flag,
  MoreVertical,
  Person,
  RestrictedChat,
} from '@assets/icons';
import { IJobData } from '@redux/services/jobsApi';

interface ChatHeaderProps {
  hasBackButton?: boolean;
  title?: string;
  hasBorder?: boolean;
  image?: string | null;
  roomId: string;
  isSubscribed: boolean;
  hideRightButton: boolean;
  openUserProfile: () => void;
  matchedJob: IJobData | null;
}

export type ChatSubscriptionType = 'subscription' | 'superChat';

const ChatHeader = (props: ChatHeaderProps) => {
  const {
    hasBackButton = true,
    title,
    hasBorder,
    image,
    roomId,
    isSubscribed,
    hideRightButton,
    openUserProfile,
    matchedJob,
  } = props;

  const { showWarning } = useShowMessage();
  const { t } = useTranslation(['chat']);

  const { isJobSeeker, user } = useUserProfile();
  const navigate = useNavigate();

  const { makePayment } = useEsewaPayment();
  const [
    getUnrestrictedChatPrice,
    {
      isLoading: isUnrestrictedChatPriceLoading,
      data: unrestrictedChatPriceData,
      isSuccess: isUnrestrictedChatPriceSuccess,
    },
  ] = useLazyGetSuperChatPriceQuery();

  const [subscriptionType, setSubscriptionType] =
    useState<ChatSubscriptionType>();
  const [isReportModalVisible, setReportModalVisible] =
    useState<boolean>(false);
  const [isAdminAssistModalVisible, setAdminAssistModalVisible] =
    useState<boolean>(false);

  useEffect(() => {
    if (isUnrestrictedChatPriceSuccess) {
      const priceValue =
        unrestrictedChatPriceData.data?.[0]?.price?.npr?.price ?? 0;
      const priceId = unrestrictedChatPriceData.data?.[0]?.price?.npr?.id;
      const { VITE_BASE_URL } = import.meta.env;
      const dateValue = Date.now();
      makePayment({
        amount: priceValue,
        product_delivery_charge: '0',
        product_service_charge: '0',
        success_url: `${VITE_BASE_URL}esewa/epay?url=chat&platform=web&type=unrestricted-chat&room_id=${roomId}&user_id=${user.id}&price_id=${priceId}&`,
        tax_amount: '0',
        total_amount: priceValue,
        transaction_uuid: `unrestrictedChat-${roomId}-${dateValue}`,
      });
    }
  }, [isUnrestrictedChatPriceSuccess]);

  return (
    <>
      {isUnrestrictedChatPriceLoading && <Spin fullscreen />}
      <div
        className={`relative flex w-full items-center py-3 gap-2 ${hasBorder ? 'border border-WHITE_EFF0F2' : ''}`}>
        {hasBackButton && (
          <button
            onClick={() => {
              navigate(-1);
            }}
            className="px-6 py-2 hover:shadow-sm rounded-full">
            <ArrowLeft height={16} />
          </button>
        )}
        <button
          onClick={() => openUserProfile()}
          className="flex items-center gap-2">
          {image ? (
            <img src={image} className="flex w-6 h-6 rounded-full" />
          ) : (
            <Person className="flex min-w-6 max-w-6 min-h-6 max-h-6 rounded-full text-GRAY_ACACAC border border-WHITE_EFEFEF self-center" />
          )}
          <Title type="body2" bold className="text-center line-clamp-1">
            {title}
          </Title>
        </button>
        {!hideRightButton && (
          <div className="absolute right-5 flex gap-4 items-center">
            {/* More */}
            <Popover
              trigger="hover"
              placement="bottom"
              className="flex items-center cursor-pointer z-[999]"
              overlayInnerStyle={{
                marginRight: 14,
                padding: '8px 4px',
              }}
              content={
                <div
                  className="flex flex-col"
                  onClick={e => e.stopPropagation()}>
                  {/* Super chat */}
                  {/* Show only when not subscribed */}
                  {!isSubscribed && (
                    <PopperListItem
                      onClick={() => {
                        if (!isSubscribed) {
                          setSubscriptionType('superChat');
                        } else {
                          showWarning(t('unrestrictedChatAccess'));
                        }
                      }}
                      title={
                        !isSubscribed
                          ? t('unrestrictedChat.title')
                          : t('restrictedChat')
                      }
                      icon={
                        <RestrictedChat
                          className={'text-black'}
                          width={20}
                          height={20}
                        />
                      }
                    />
                  )}
                  {/* Admin Assist */}
                  {!isJobSeeker && (
                    <PopperListItem
                      onClick={() => {
                        // if (isSubscribed) {
                        setAdminAssistModalVisible(true);
                        // } else {
                        //   setSubscriptionType('superChat');
                        // }
                      }}
                      icon={<AddCircle className={'text-black'} />}
                      title={t('adminAssist.title')}
                    />
                  )}
                  {/* Report */}
                  <PopperListItem
                    onClick={() => {
                      setReportModalVisible(true);
                    }}
                    icon={<Flag className={'text-black'} />}
                    title={t('report', { ns: 'common' })}
                    hideBorder={matchedJob ? false : true}
                  />
                  {/* Chat Detail */}
                  {matchedJob && (
                    <PopperListItem
                      onClick={() => {
                        navigate('/jobs/detail', {
                          state: { data: matchedJob },
                        });
                      }}
                      title={t('chatDetail')}
                      hideBorder
                      icon={
                        <ChatDetail
                          className={'text-black'}
                          width={20}
                          height={20}
                        />
                      }
                    />
                  )}
                </div>
              }>
              <span>
                <MoreVertical className={'text-GRAY_5E5E5E'} height={40} />
              </span>
            </Popover>
          </div>
        )}
      </div>
      {subscriptionType && (
        <SuperChatMailModal
          roomId={roomId}
          closeModal={() => setSubscriptionType(undefined)}
          makeEsewaPayment={() => getUnrestrictedChatPrice()}
        />
      )}
      {isAdminAssistModalVisible && (
        <AdminAssistModal
          roomId={roomId}
          closeModal={() => setAdminAssistModalVisible(false)}
        />
      )}
      {isReportModalVisible && (
        <ReportUserModal
          closeModal={() => setReportModalVisible(false)}
          userId={roomId}
          type="chat"
        />
      )}
    </>
  );
};

export default ChatHeader;
