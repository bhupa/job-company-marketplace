import React, { useEffect, useState } from 'react';

import { Spin } from 'antd';
import { useTranslation } from 'react-i18next';
import { useNavigate } from 'react-router-dom';

// Components
import DirectChatModal from './DirectChatModal';
import { SubscribeModal } from '@components/subscription';

// Hooks
import useUserProfile from '@customHooks/useUserProfile';
import useSwipeMatching from '@customHooks/useSwipeMatching';
import { useShowMessage } from '@customHooks/useShowMessage';

// Redux
import {
  IChatRequestParams,
  IFavoriteMatchingParams,
  useFavoriteMatchingMutation,
} from '@redux/services/matchingApi';
import { useAppDispatch, useAppSelector } from '@redux/hook';
import { setMatchingLimit } from '@redux/reducers/subscription';
import { setNeedsBookmarkRefresh } from '@redux/reducers/bookmark';
import { addLeftSwipedItem, filterHomeData } from '@redux/reducers/home';

// Others
import {
  CheckGreen,
  CloseCircle,
  BookmarkCircle,
  ChatRequest,
} from '@assets/icons';

interface Props {
  id: string;
  hideBookMark?: boolean;
}

const ButtonGroup = ({ id, hideBookMark }: Props) => {
  const { t } = useTranslation(['messages']);
  const { showSuccess } = useShowMessage();
  const navigate = useNavigate();

  const dispatch = useAppDispatch();
  const {
    dailyCount,
    dailyLimit,
    favoriteLimit,
    chatRequestCount,
    chatRequestLimit,
  } = useAppSelector(state => state.subscription);

  const { isJobSeeker, isSubscribed } = useUserProfile();

  const [isSubscriptionModalVisible, setSubscriptionModalVisible] =
    useState<boolean>(false);
  const [isDirectChatModalVisible, setDirectChatModalVisible] =
    useState<boolean>(false);
  const [directChatModalData, setDirectChatModalData] =
    useState<IChatRequestParams>({});

  // Save swipe data to queue and fire api when user stops for 500ms
  const {
    swipeCount,
    setSwipeCount,
    addItemToQueue,
    isSwipeLoading,
    isSwipeSuccess,
  } = useSwipeMatching();

  const [
    matchFavorite,
    {
      isLoading: isFavoriteLoading,
      isSuccess: isFavoriteSuccess,
      data: favoriteData,
    },
  ] = useFavoriteMatchingMutation();

  // Remove from list after item is set to favorite
  useEffect(() => {
    if (isFavoriteSuccess) {
      const dailyFavCount = favoriteData?.data?.dailyCount ?? 0;
      setSwipeCount(dailyFavCount);
      dispatch(
        setMatchingLimit({
          dailyCount: dailyFavCount,
          favoriteCount: favoriteData?.data?.favouriteCount,
        }),
      );
      dispatch(setNeedsBookmarkRefresh(true));
      dispatch(filterHomeData(id));
      showSuccess(t('favorite.success', { ns: 'messages' }));
      navigate(-1);
    }
  }, [isFavoriteSuccess]);

  useEffect(() => {
    if (isSwipeSuccess) {
      navigate(-1);
    }
  }, [isSwipeSuccess]);

  /**
   * Swipe right
   * If user is not paid user and limit is crossed, show subscription modal.
   * @param userId
   * @returns
   */
  const handleSwipeRight = (jobId: string) => {
    if (!isSubscribed && dailyCount >= dailyLimit) {
      return setSubscriptionModalVisible(true);
    }
    addItemToQueue(
      isJobSeeker
        ? { type: 1, job_id: jobId }
        : { type: 1, job_seeker_id: jobId },
    );
    dispatch(filterHomeData(jobId));
  };

  const handleSwipeLeft = (jobId: string) => {
    addItemToQueue(
      isJobSeeker
        ? { type: 0, job_id: jobId }
        : { type: 0, job_seeker_id: jobId },
    );
    dispatch(addLeftSwipedItem(jobId));
    dispatch(filterHomeData(jobId));
  };

  const handleDirectChatSuccess = () => {
    dispatch(setNeedsBookmarkRefresh(true));
    dispatch(filterHomeData(id));
    navigate(-1);
  };

  return (
    <>
      {(isFavoriteLoading || isSwipeLoading) && <Spin fullscreen />}
      <div className="flex flex-wrap gap-x-7 gap-y-4 justify-center items-center pt-4">
        <button
          onClick={() => {
            handleSwipeLeft(id);
          }}>
          <CloseCircle className="w-9 h-9 sm:w-12 sm:h-12" />
        </button>
        {!hideBookMark && (
          <button
            onClick={() => {
              const favCount = favoriteData?.data?.favouriteCount ?? 0;
              if (
                !isSubscribed &&
                (swipeCount >= dailyLimit || favCount >= favoriteLimit)
              ) {
                return setSubscriptionModalVisible(true);
              }
              const params: IFavoriteMatchingParams = { favourite: 1 };
              if (isJobSeeker) {
                params.job_id = id;
              } else {
                params.job_seeker_id = id;
              }
              matchFavorite(params);
            }}>
            <BookmarkCircle className="w-9 h-9 sm:w-12 sm:h-12" />
          </button>
        )}
        <button
          onClick={() => {
            if (!isSubscribed && chatRequestCount >= chatRequestLimit) {
              return setSubscriptionModalVisible(true);
            }
            const requestParams: IChatRequestParams = {};
            if (isJobSeeker) {
              requestParams.job_id = id;
            } else {
              requestParams.job_seeker_id = id;
            }
            setDirectChatModalData(requestParams);
            setDirectChatModalVisible(true);
          }}>
          <ChatRequest className="w-9 h-9 sm:w-12 sm:h-12" />
        </button>
        <button
          onClick={() => {
            if (!isSubscribed && swipeCount >= dailyLimit) {
              return setSubscriptionModalVisible(true);
            }
            handleSwipeRight(id);
          }}>
          <CheckGreen className="w-9 h-9 sm:w-12 sm:h-12" />
        </button>
      </div>
      {isSubscriptionModalVisible && (
        <SubscribeModal closeModal={() => setSubscriptionModalVisible(false)} />
      )}
      {isDirectChatModalVisible && (
        <DirectChatModal
          closeModal={() => {
            setDirectChatModalData({});
            setDirectChatModalVisible(false);
          }}
          chatRequestData={directChatModalData}
          setDirectChatSuccess={() => handleDirectChatSuccess()}
        />
      )}
    </>
  );
};

export default ButtonGroup;
