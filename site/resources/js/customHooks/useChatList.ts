import { useEffect, useState } from 'react';

// Redux
import { useAppDispatch, useAppSelector } from '@redux/hook';
import { useGetChatListQuery } from '@redux/services/chatApi';
import { setChatList, setNeedsChatRefresh } from '@redux/reducers/chat';

// Others
// import { filterDuplicates } from '@utils/helpers';

const PER_PAGE = 10;

const useChatList = (searchKey: string = '') => {
  const dispatch = useAppDispatch();
  const { chatList, needsRefresh } = useAppSelector(state => state.chat);

  const [page, setPage] = useState<number>(1);
  const [isLoading, setLoading] = useState<boolean>(false);
  // const [isFetchingMore, setFetchingMore] = useState<boolean>(false);
  // To show empty message when user removes items from chatList
  const [isEmpty, setEmpty] = useState<boolean>(false);
  const [isRefresh, setRefresh] = useState<boolean>(false);

  const {
    data,
    isLoading: isChatListLoading,
    isFetching,
    refetch,
    isError,
  } = useGetChatListQuery({
    page,
    per_page: PER_PAGE,
    name: searchKey,
  });

  /**
   * Function to refetch chat list when socket event is received for match or request
   */
  useEffect(() => {
    if (needsRefresh) {
      refreshChatList();
      dispatch(setNeedsChatRefresh(false));
    }
  }, [needsRefresh]);

  useEffect(() => {
    if (isError) {
      setLoading(false);
    }
  }, [isError]);

  useEffect(() => {
    if (isChatListLoading) {
      setLoading(true);
    }
  }, [isChatListLoading]);

  const totalPages = data?.data?.pagination?.lastPage || 0;
  const hasMorePage = totalPages > page;

  useEffect(() => {
    if (data?.data?.data) {
      const listData = data.data?.data;
      // const chatListData =
      //   page === 1 || searchKey.trim().length
      //     ? listData
      //     : filterDuplicates([...chatList, ...listData]);
      dispatch(setChatList(listData));
      // setFetchingMore(false);
      setLoading(false);
      isEmpty && setEmpty(false);
    } else {
      // setFetchingMore(false);
      setLoading(false);
    }
  }, [data]);

  useEffect(() => {
    if (page === 1 && isRefresh) {
      refetch();
      setRefresh(false);
    }
  }, [page, isRefresh]);

  /**
   * When searched, set page to 1
   */
  useEffect(() => {
    setPage(1);
  }, [searchKey]);

  // const fetchMore = () => {
  //   if (!isFetchingMore && hasMorePage && !isFetching) {
  //     setFetchingMore(true);
  //     setPage(page + 1);
  //   }
  // };

  const refreshChatList = () => {
    setPage(1);
    setRefresh(true);
  };

  return {
    data: chatList,
    isLoading: isChatListLoading || isLoading,
    // fetchMore,
    // isFetchingMore,
    page,
    hasMorePage,
    perPage: PER_PAGE,
    isRefreshing: !isLoading && isFetching,
    refreshChatList,
    isEmpty: isEmpty || data?.data?.pagination?.count === 0,
    totalData: data?.data?.pagination.total,
    setPage,
  };
};

export default useChatList;
