import { useEffect } from 'react';

// Hooks
import useUserProfile from './useUserProfile';

// Redux
import {
  addSingleMessage,
  setChatUnseenCountFromEvent,
  setLastSeenMessageTime,
  setNeedsChatMessageRefresh,
  setNeedsChatRefresh,
  updateLastMessage,
  updateUnseenCount,
} from '@redux/reducers/chat';
import { useAppDispatch } from '@redux/hook';
import { setNeedsSubscriptionRefresh } from '@redux/reducers/subscription';

// Services
import { connectSocket, getSocket } from '@services/SocketService';

const useSocketService = () => {
  const dispatch = useAppDispatch();
  const { user, accessToken } = useUserProfile();

  useEffect(() => {
    if (accessToken) {
      const userId = user.id;
      // Connect to socket
      connectSocket(accessToken, userId);
      // Get socket instance
      const socket = getSocket();

      // EmitEvent1: Emit event to get chat unseen count
      socket.emit('getChatUnseenCount');

      // Event1: Get message sent to room of roomId as userId and event name userId-chatMessage
      socket.on(`${userId}-chatMessage`, stringResponse => {
        const response = JSON.parse(stringResponse);
        if (response) {
          const chatRoomId = response.room;
          const pathname = window.location.pathname;
          // Update latest message to redux state.
          dispatch(
            updateLastMessage({ chat: response, chatRoomId: chatRoomId }),
          );
          if (
            pathname &&
            pathname.includes('/chat-screen') &&
            pathname.split('/').pop() === chatRoomId
          ) {
            // Add message to redux state when event for current room is received inside chat screen.
            dispatch(
              addSingleMessage({ message: response, roomId: chatRoomId }),
            );
            // Update last seen time of user to this msg created time.
            dispatch(
              setLastSeenMessageTime({
                lastSeenTime: response.createdAt,
                chatRoomId,
              }),
            );
          } else {
            // Add unread count when msg is received outside of chat screen.
            dispatch(updateUnseenCount({ chatRoomId, count: 1 }));
          }
        }
      });

      // Event2: Chat request / accept / reject or match success event
      socket.on(`${userId}-matched`, () => {
        dispatch(setNeedsChatRefresh(true));
      });

      // Event3: Chat unseen count event
      socket.on(`${userId}-chatUnseenCount`, res => {
        if (res) {
          dispatch(setChatUnseenCountFromEvent(Number(res.unseenCount || 0)));
        }
      });

      // Event4: Refresh chat screen when user is in same chat room which is subscribed
      socket.on(`${userId}-chatRefresh`, res => {
        const response = JSON.parse(res);
        if (response) {
          const chatRoomId = response.chatRoomId?.toString();
          const pathname = window.location.pathname;
          /**
           * If user is on chat screen having same chat room id, then refresh that chat room messages
           */
          if (
            pathname &&
            pathname.includes('/chat-screen') &&
            pathname.split('/').pop() === chatRoomId
          ) {
            dispatch(setNeedsChatMessageRefresh(true));
          }
        }
      });

      // Event5: Update last seen time of your messages when another user views your message
      socket.on(`${userId}-chatSeenEvent`, response => {
        if (response) {
          const chatRoomId = response.roomId?.toString();
          const lastSeenTime = response.lastSeenTime;
          dispatch(setLastSeenMessageTime({ lastSeenTime, chatRoomId }));
        }
      });

      // Event6: Refetch subscription data when subscription event is received
      socket.on(`${userId}-refreshSubscription`, response => {
        if (response) {
          dispatch(setNeedsSubscriptionRefresh(true));
        }
      });
    }

    return () => {
      const socket = getSocket();
      if (socket) {
        socket.disconnect();
      }
    };
  }, [accessToken]);
};

export default useSocketService;
