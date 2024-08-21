import React, { useEffect, useRef, useState } from 'react';

import { Divider, Spin } from 'antd';
import { useTranslation } from 'react-i18next';
import { useLocation, useNavigate } from 'react-router-dom';

// Components
import {
  CustomButton,
  EmptyListMessage,
  TextAreaInput,
  Title,
} from '@components/common';
import { ChatWrapper } from '@templates';
import { ChatHeader, ChatMessage } from '@components/chat';

// Hooks
import useUserProfile from '@customHooks/useUserProfile';
import useChatRequest from '@customHooks/useChatRequest';
import useChatMessages from '@customHooks/useChatMessages';
import { useShowMessage } from '@customHooks/useShowMessage';

// Redux
import { useStoreChatMutation } from '@redux/services/chatApi';
import { useAppDispatch } from '@redux/hook';
import {
  setLastSeenMessageTime,
  updateUnseenCount,
} from '@redux/reducers/chat';

// Others
import { Send } from '@assets/icons';
import { getSocket } from '@services/SocketService';
import { AccountStatusType } from '@redux/services/authApi';

const ChatScreen = () => {
  const navigate = useNavigate();
  const dispatch = useAppDispatch();
  const { t } = useTranslation(['chat']);
  const { showSuccess, showError } = useShowMessage();

  const { isJobSeeker, user } = useUserProfile();
  const route = useLocation();
  const params = route.state?.params;

  const {
    chatRoomId,
    unreadCount = 0,
    lastSeenId,
  } = (params as {
    chatRoomId: string;
    unreadCount: number;
    lastSeenId: string;
  }) || {};
  const [isInitialLoading, setInitialLoading] = useState<boolean>(true);
  const [isChatRequest, setIsChatRequest] = useState<boolean>(false);
  const [chatMessage, setChatMessage] = useState<string>('');
  const [isReject, setIsReject] = useState<boolean>(false);
  const [showUnreadMark, setShowUnreadMark] = useState<boolean>(true);
  const chatViewRef = useRef<HTMLDivElement | any>();

  const {
    data,
    fetchMore,
    isEmpty,
    isFetchingMore,
    isLoading,
    isRefreshing,
    updateChatMessage,
    chatData,
    hasError,
    lastSeenMessageTime,
    matchedJob,
  } = useChatMessages(chatRoomId);
  const [storeChat, { isSuccess: isChatSendSuccess, data: sentChatData }] =
    useStoreChatMutation();
  const { handleMatchRequest, isMatchingLoading, isMatchingSuccess } =
    useChatRequest();

  const socket = getSocket();
  const handleEmitSocketEvent = (type: 'open' | 'close') => {
    if (socket) {
      const socket1 = getSocket();
      socket1.emit('connectChatRoomCheck', {
        userId: user.id,
        roomId: chatRoomId,
        type,
      });
    }
  };

  useEffect(() => {
    if (chatViewRef.current) {
      chatViewRef.current.scrollTop = chatViewRef.current.scrollHeight;
    }
  }, []);

  const isSocketConnected = socket?.connected ?? false;
  useEffect(() => {
    /**
     * EmitEvent: Emit event to set that user is in that chatroom
     * so that user will not receive push notification and unseen count event for that chatroom
     **/
    handleEmitSocketEvent('open');

    return () => {
      // Emit event to set user has left the chatroom
      handleEmitSocketEvent('close');
    };
  }, [socket, isSocketConnected]);

  useEffect(() => {
    const handleWindowClose = () => {
      handleEmitSocketEvent('close');
    };

    // Event listener for window closing
    window.addEventListener('beforeunload', handleWindowClose);

    return () => {
      window.removeEventListener('beforeunload', handleWindowClose);
    };
  }, [socket, isSocketConnected]);

  /**
   * Update last seen time when msg is sent and response has seen time.
   */
  useEffect(() => {
    if (isChatSendSuccess && sentChatData) {
      if (sentChatData.data?.seen) {
        dispatch(
          setLastSeenMessageTime({
            lastSeenTime: new Date().toISOString(),
            chatRoomId,
          }),
        );
      }
      if (chatViewRef.current) {
        chatViewRef.current.scrollTop = chatViewRef.current.scrollHeight;
      }
    }
  }, [isChatSendSuccess]);

  // Set all messages read when going back
  useEffect(() => {
    return () => {
      if (!isReject) {
        // Reset local unread count to 0.
        dispatch(updateUnseenCount({ chatRoomId }));
      }
    };
  }, [isReject]);

  /**
   * Hit reject api after isReject is set true
   */
  useEffect(() => {
    if (isReject) {
      handleMatchRequest(matchId, 'refuse');
    }
  }, [isReject]);

  /**
   * When request is accepted show input field
   * if rejected, go back
   * */
  useEffect(() => {
    if (isMatchingSuccess) {
      if (isReject) {
        navigate(-1);
      } else {
        handleEmitSocketEvent('open');
        setIsChatRequest(false);
        showSuccess(t('youCanStartConversation'));
      }
    }
  }, [isMatchingSuccess]);

  useEffect(() => {
    if (chatData) {
      const isRequest = chatData.type === 'request' && !chatData.isAccepted;
      setIsChatRequest(isRequest);
      setInitialLoading(false);
    }
  }, [chatData]);

  if (!params) {
    showError(t('roomUnavailable'));
    navigate(-1);
    return null;
  }

  let isUserDeletedOrDeactivated = false;

  let chatUser: { name: string; image: string; isViolated: boolean } = {
    name: '',
    image: '',
    isViolated: false,
  };
  const matchData = chatData.match;
  if (!isLoading) {
    if (chatData.isDeleted || !matchData) {
      chatUser = {
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
          chatUser = {
            name: isProfileViolated
              ? t('bannedAccount')
              : isDeactivated
                ? t('deactivatedAccount')
                : companyData?.companyName ?? t('deletedAccount'),
            image: isProfileViolated || isDeactivated ? '' : companyData?.logo,
            isViolated: isProfileViolated,
          };
          if (isDeactivated || !companyData) {
            isUserDeletedOrDeactivated = true;
          }
        } else {
          const jobSeeker = matchData.jobseeker;
          const isProfileViolated =
            jobSeeker?.user?.isViolation ||
            jobSeeker?.user?.status === AccountStatusType.RESTRICTED;
          const isDeactivated =
            jobSeeker?.user?.status === AccountStatusType.DEACTIVATED;
          chatUser = {
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
          if (isDeactivated || !jobSeeker) {
            isUserDeletedOrDeactivated = true;
          }
        }
      }
    }
  }

  const isMyProfileViolated = user.isViolation;
  const isRequestedByMe = matchData?.createdBy?.id === user.id;
  const matchId = matchData?.id;
  // const isAdminAssist = chatData.adminAssit ? true : false;
  const isSubscribed = chatData.superChat;
  const isDeletedUser = chatData.isDeleted || isUserDeletedOrDeactivated;
  const isChatViolated = chatData.isChatViolation || chatUser.isViolated;
  const userImage = isDeletedUser || isChatViolated ? null : chatUser?.image;

  const handleSendMessage = () => {
    if (!chatMessage || chatMessage.trim().length === 0) {
      return;
    }
    showUnreadMark && setShowUnreadMark(false);
    setChatMessage('');
    const dateString = new Date().toISOString();
    updateChatMessage({
      message: chatMessage.trim(),
      room: chatRoomId,
      id: Date.now().toString(),
      createdAt: dateString,
      admin_id: null,
      seen: null,
      send_by: {
        id: Date.now().toString(),
        image: '',
        name: '',
        userId: user.id,
        userType: user.userType,
      },
    });
    storeChat({
      chat_room_id: chatRoomId,
      message: chatMessage.trim(),
      type: 'text',
    });
  };

  const handleOpenProfile = () => {
    const userDetail = isJobSeeker
      ? chatData.match.company
      : chatData.match.jobseeker;
    if (
      !userDetail ||
      chatUser.isViolated ||
      isDeletedUser ||
      isMyProfileViolated
    ) {
      return;
    }
    navigate(
      isJobSeeker ? '/home/company/details' : '/home/jobseeker/details',
      {
        state: {
          data: userDetail,
          hideActionButtons: true,
          matchedJobId: matchedJob?.id,
        },
      },
    );
  };

  const handleScroll = () => {
    if (chatViewRef.current) {
      const { scrollTop, scrollHeight, clientHeight } = chatViewRef.current;
      if (scrollHeight - (Math.abs(scrollTop) + clientHeight) === 0) {
        !isRefreshing && !isFetchingMore && fetchMore();
      }
    }
  };

  const handleKeyPress = (e: React.KeyboardEvent<HTMLTextAreaElement>) => {
    // Check if Shift + Enter is pressed
    if (e.key === 'Enter' && e.shiftKey) {
      return;
    } else if (e.key === 'Enter') {
      e.preventDefault();
      handleSendMessage();
    }
  };

  return (
    <ChatWrapper>
      <div className="flex flex-col w-full relative">
        <ChatHeader
          title={hasError ? '' : chatUser?.name}
          image={userImage}
          roomId={chatRoomId}
          isSubscribed={isSubscribed}
          hasBorder
          hideRightButton={
            hasError ||
            isChatRequest ||
            isDeletedUser ||
            isChatViolated ||
            isLoading ||
            isMyProfileViolated
          }
          matchedJob={matchedJob}
          openUserProfile={() => handleOpenProfile()}
        />
        {isLoading || isMatchingLoading ? (
          <div className="bg-WHITE_EFEFEF flex flex-col flex-1 p-3">
            <Spin />
          </div>
        ) : (
          <>
            {isEmpty ? (
              <div className="bg-WHITE_EFEFEF flex flex-col flex-1 p-3">
                <EmptyListMessage
                  message={
                    isDeletedUser || isChatViolated
                      ? t('noChatAvailable', { ns: 'chat' })
                      : 'chat.startChatMessage'
                  }
                />
              </div>
            ) : (
              <div
                ref={chatViewRef}
                onScroll={handleScroll}
                className="bg-WHITE_EFEFEF relative flex flex-col-reverse p-3 gap-2 overflow-scroll h-full">
                {data?.map((item, index) => {
                  if (
                    showUnreadMark &&
                    unreadCount > 0 &&
                    lastSeenId === item.id
                  ) {
                    return (
                      <div key={item.id?.toString()}>
                        <div className="flex justify-center items-center gap-4 mb-2 mt-4">
                          <Divider plain className="flex flex-1">
                            <Title
                              type="caption1"
                              className="text-center text-GRAY_545454">
                              {t('unreadMessages')}
                            </Title>
                          </Divider>
                        </div>
                        <ChatMessage
                          item={item}
                          lastSeenTime={lastSeenMessageTime}
                          isLastMessage={index === 0}
                          isChatDisabled={isChatViolated || isDeletedUser}
                        />
                      </div>
                    );
                  }
                  return (
                    <ChatMessage
                      key={item.id?.toString()}
                      item={item}
                      lastSeenTime={lastSeenMessageTime}
                      isLastMessage={index === 0}
                      isChatDisabled={isChatViolated || isDeletedUser}
                    />
                  );
                })}
                {isFetchingMore && <Spin className="my-2" />}
              </div>
            )}

            {!hasError &&
              !isDeletedUser &&
              !isChatViolated &&
              !isMyProfileViolated &&
              !isInitialLoading && (
                <>
                  {!isChatRequest ? (
                    <div className="flex items-center border-t border-t-WHITE_E0E2E4">
                      <TextAreaInput
                        value={chatMessage}
                        autoSize={{ minRows: 1, maxRows: 4 }}
                        placeholder={t('typeYourMessage')}
                        onChange={e => setChatMessage(e.target.value)}
                        onKeyDown={handleKeyPress}
                      />
                      <button
                        onClick={() => handleSendMessage()}
                        className="px-2">
                        <Send className={'text-BLUE_004D80'} />
                      </button>
                    </div>
                  ) : isRequestedByMe ? (
                    <div className="flex flex-col p-6 gap-4">
                      <Title type="body1" className="text-center">
                        {t('yourRequestIsPending')}
                      </Title>
                      <Title type="caption1" className="text-center">
                        {t('requestPendingMessage1')}&nbsp;{chatUser?.name}
                        &nbsp;
                        {t('requestPendingMessage2')}
                      </Title>
                    </div>
                  ) : (
                    <div className="flex flex-col p-6 gap-4">
                      <Title type="body1" className="text-center">
                        {chatUser.name} {t('hasSentYouChatRequest')}
                      </Title>
                      <div className="flex gap-6">
                        <CustomButton
                          title={t('reject', { ns: 'common' })}
                          type="default"
                          className={
                            'text-RED_FF4D4D bg-white border-GRAY_ACACAC'
                          }
                          onClick={() => {
                            setIsReject(true);
                            // Reject api will be hit from useEffect hook after state is changed.
                          }}
                          disabled={isMatchingLoading}
                        />
                        <CustomButton
                          title={t('accept', { ns: 'common' })}
                          onClick={() => handleMatchRequest(matchId, 'accept')}
                          disabled={isMatchingLoading}
                        />
                      </div>
                    </div>
                  )}
                </>
              )}
            {(isDeletedUser || isChatViolated || isMyProfileViolated) && (
              <Title type="body2" className="text-center py-2">
                {t('cannotReplyToConversation')}
              </Title>
            )}
          </>
        )}
      </div>
    </ChatWrapper>
  );
};

export default ChatScreen;
