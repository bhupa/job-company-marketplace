import { useEffect, useState } from 'react';

// Hooks
import useUserProfile from './useUserProfile';

// Redux
import {
  IRequestMatchingParams,
  useSwipeRequestMatchingMutation,
} from '@redux/services/matchingApi';
import { UserType } from '@redux/reducers/auth';
import { useAppDispatch, useAppSelector } from '@redux/hook';
import { setMatchingLimit } from '@redux/reducers/subscription';
import { setNeedsBookmarkRefresh } from '@redux/reducers/bookmark';
import { setMatchingModalData, setUndoAllowed } from '@redux/reducers/home';

const useSwipeMatching = () => {
  const [requestMatching, { data, isLoading, isSuccess }] =
    useSwipeRequestMatchingMutation();
  const { userType } = useUserProfile();

  const [swipedItems, setSwipedItems] = useState<IRequestMatchingParams[]>([]);

  const dispatch = useAppDispatch();
  const { dailyCount } = useAppSelector(state => state.subscription);
  const { isUndoAllowed } = useAppSelector(state => state.home);

  useEffect(() => {
    if (data?.data) {
      setSwipeCount(data.data?.dailyCount ?? 0);
      dispatch(setNeedsBookmarkRefresh(true));
      const matchedItem = data.data.matchedData;
      if (matchedItem?.length > 0) {
        dispatch(
          setMatchingModalData({
            isMatchingModalVisible: true,
            data: matchedItem,
          }),
        );
      }
    }
  }, [data]);

  /**
   * Call matching api when debounced for 500ms
   */
  useEffect(() => {
    const debounceTimer = setTimeout(() => {
      if (swipedItems.length > 0) {
        const formData: any = new FormData();
        swipedItems.forEach(item => {
          if (userType === UserType.JobSeeker) {
            formData.append('job_id[]', item.job_id?.toString());
          } else {
            formData.append('job_seeker_id[]', item.job_seeker_id?.toString());
          }
          formData.append('type[]', item.type);
        });
        requestMatching(formData);
        setSwipedItems([]);
      }
    }, 500);

    return () => {
      clearTimeout(debounceTimer);
    };
  }, [swipedItems]);

  /**
   * User can swipe continuously all cards without stopping. In this case, hitting api continuously won't be feasible.
   * So, add items to state queue and after debounce of 500ms hit api.
   * @param item
   */
  const addItemToQueue = (item: IRequestMatchingParams) => {
    if (item.type === 1) {
      setSwipeCount(dailyCount + 1);
    } else {
      handleSetUndoAllowed(true);
    }
    setSwipedItems(prevItems => [...prevItems, item]);
  };

  /**
   * Set daily swipe count in reducer
   * @param count
   */
  const setSwipeCount = (count: number) => {
    dispatch(setMatchingLimit({ dailyCount: count }));
  };

  const handleSetUndoAllowed = (allowed: boolean) => {
    dispatch(setUndoAllowed(allowed));
  };

  return {
    swipeCount: dailyCount,
    setSwipeCount,
    addItemToQueue,
    isUndoAllowed,
    setUndoAllowed: handleSetUndoAllowed,
    isSwipeSuccess: isSuccess,
    isSwipeLoading: isLoading,
  };
};

export default useSwipeMatching;
