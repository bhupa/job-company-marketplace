import { useEffect, useState } from 'react';

import { useDispatch } from 'react-redux';

// Redux
import { useAppSelector } from '@redux/hook';
import {
  setChatMessages,
  setNeedsChatMessageRefresh,
  updateLastMessage,
} from '@redux/reducers/chat';
import {
  IChatItem,
  IChatMessageResponse,
  useGetChatMessagesQuery,
} from '@redux/services/chatApi';

// Others
import { filterDuplicates } from '@utils/helpers';

const PER_PAGE = 20;

const useChatMessages = (roomId: string) => {
  const [page, setPage] = useState<number>(1);
  const [isLoading, setLoading] = useState<boolean>(false);
  const [isFetchingMore, setFetchingMore] = useState<boolean>(false);
  // To show empty message when user removes items from chatMessages
  const [isEmpty, setEmpty] = useState<boolean>(false);
  const [isRefresh, setRefresh] = useState<boolean>(false);

  const dispatch = useDispatch();
  const {
    chatMessages: chatMessagesList,
    needsRefreshChatMessages,
    lastSeenMessageTime,
  } = useAppSelector(state => state.chat);
  const chatMessages = chatMessagesList?.[roomId] ?? [];

  const {
    data,
    isLoading: isChatMessageLoading,
    isFetching,
    refetch,
    isError,
  } = useGetChatMessagesQuery({
    page,
    per_page: PER_PAGE,
    roomId,
  });

  useEffect(() => {
    if (isChatMessageLoading) {
      setLoading(true);
    }
  }, [isChatMessageLoading]);

  useEffect(() => {
    if (isError) {
      setLoading(false);
    }
  }, [isError]);

  /**
   * Used when user subscribes to super chat feature
   */
  useEffect(() => {
    if (needsRefreshChatMessages) {
      refreshChatMessages();
      dispatch(setNeedsChatMessageRefresh(false));
    }
  }, [needsRefreshChatMessages]);

  const totalPages = data?.data?.chats?.pagination?.lastPage || 0;
  const hasMorePage = totalPages > page;

  useEffect(() => {
    if (data?.data?.chats?.data) {
      const messageData = data.data.chats.data;
      const chatMessageData =
        page === 1
          ? messageData
          : filterDuplicates([...chatMessages, ...messageData]);
      dispatch(setChatMessages({ messages: chatMessageData, roomId }));
      setFetchingMore(false);
      setLoading(false);
      isEmpty && setEmpty(false);
    } else {
      setFetchingMore(false);
      setLoading(false);
    }
  }, [data]);

  useEffect(() => {
    if (page === 1 && isRefresh) {
      refetch();
      setRefresh(false);
    }
  }, [page, isRefresh]);

  const fetchMore = () => {
    if (!isFetchingMore && hasMorePage && !isFetching) {
      setFetchingMore(true);
      setPage(page + 1);
    }
  };

  const refreshChatMessages = () => {
    setPage(1);
    setRefresh(true);
  };

  const updateChatMessage = (messageData: IChatItem) => {
    dispatch(
      setChatMessages({ messages: [messageData, ...chatMessages], roomId }),
    );
    dispatch(updateLastMessage({ chat: messageData, chatRoomId: roomId }));
  };

  return {
    data: chatMessages,
    isLoading: isChatMessageLoading || isLoading,
    fetchMore,
    isFetchingMore,
    page,
    hasMorePage,
    perPage: PER_PAGE,
    isRefreshing: !isLoading && isFetching,
    refreshChatMessages,
    isEmpty:
      chatMessages?.length === 0 &&
      (isEmpty || data?.data?.chats?.pagination?.count === 0),
    updateChatMessage,
    chatData: data?.data ?? ({} as IChatMessageResponse),
    hasError: isError && chatMessages.length === 0,
    lastSeenMessageTime: lastSeenMessageTime?.[roomId] ?? null,
    matchedJob: data?.data?.match?.job ?? null,
  };
};

export default useChatMessages;
