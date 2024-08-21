import React, { Suspense, lazy, useEffect, useState } from 'react';

import { Spin } from 'antd';
import { useTranslation } from 'react-i18next';

// Components
import { DashboardWrapper } from '@templates';
import { CustomButton, Title } from '@components/common';
import { SubscribeModal } from '@components/subscription';
import { SummaryCard, SwiperCard } from '@components/home';

const FilterCompany = lazy(() => import('./FilterCompany'));
const FilterJobSeeker = lazy(() => import('./FilterJobSeeker'));
const MatchModal = lazy(() => import('@components/home/MatchModal'));
const DirectChatModal = lazy(() => import('@components/home/DirectChatModal'));

// Hooks
import useCompany from '@customHooks/useCompany';
import useJobSeeker from '@customHooks/useJobSeeker';
import useUserProfile from '@customHooks/useUserProfile';
import useMatchingJobs from '@customHooks/useMatchingJobs';
import { useShowMessage } from '@customHooks/useShowMessage';
import useSwipeMatching from '@customHooks/useSwipeMatching';
import useMatchingJobSeekers from '@customHooks/useMatchingJobSeekers';

// Redux
import {
  addLeftSwipedItem,
  filterHomeData,
  undoLeftSwipedItem,
} from '@redux/reducers/home';
import {
  IChatRequestParams,
  IFavoriteMatchingParams,
  IRequestMatchingParams,
  IRewindParams,
  useFavoriteMatchingMutation,
  useRewindMutation,
} from '@redux/services/matchingApi';
import { IJobData } from '@redux/services/jobsApi';
import { useAppDispatch, useAppSelector } from '@redux/hook';
import { setMatchingLimit } from '@redux/reducers/subscription';
import { IJobSeekerProfile } from '@redux/services/jobSeekerApi';
import { setNeedsBookmarkRefresh } from '@redux/reducers/bookmark';

// Others
import {
  ArrowBack,
  BookmarkCircle,
  ChatRequest,
  CheckGreen,
  CloseCircle,
  Filter,
} from '@assets/icons';

const HomeScreen = () => {
  const { t } = useTranslation(['home', 'messages']);
  const { showSuccess } = useShowMessage();
  const { isJobSeeker, isSubscribed } = useUserProfile();
  const {
    chatRequestCount,
    chatRequestLimit,
    dailyCount,
    dailyLimit,
    favoriteLimit,
  } = useAppSelector(state => state.subscription);
  const {
    homeData: data,
    isMatchingModalVisible,
    leftSwipedItems,
  } = useAppSelector(state => state.home);
  const dispatch = useAppDispatch();

  const [isFilterModalVisible, setFilterModalVisible] =
    useState<boolean>(false);
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
    isUndoAllowed,
    setUndoAllowed,
    isSwipeLoading,
  } = useSwipeMatching();

  const [
    matchFavorite,
    {
      isLoading: isFavoriteLoading,
      isSuccess: isFavoriteSuccess,
      data: favoriteData,
      originalArgs: favoriteOriginalArgs,
    },
  ] = useFavoriteMatchingMutation();

  const [rewindFeed, { isLoading: isRewindLoading }] = useRewindMutation();

  const useMatchingData = isJobSeeker ? useMatchingJobs : useMatchingJobSeekers;
  const useFetchUserData = isJobSeeker ? useJobSeeker : useCompany;
  useFetchUserData();

  const {
    isLoading,
    isFetchingMore,
    fetchMore,
    perPage,
    hasMorePage,
    resetAndFetch,
  } = useMatchingData();

  // Swipe card right after item is set to favorite
  useEffect(() => {
    if (isFavoriteSuccess) {
      const dailyCount = favoriteData?.data?.dailyCount ?? 0;
      setSwipeCount(dailyCount);
      dispatch(
        setMatchingLimit({
          dailyCount: dailyCount,
          favoriteCount: favoriteData?.data?.favouriteCount,
        }),
      );
      const dataId = isJobSeeker
        ? favoriteOriginalArgs?.job_id
        : favoriteOriginalArgs?.job_seeker_id;
      dataId && dispatch(filterHomeData(dataId));
      dispatch(setNeedsBookmarkRefresh(true));
      showSuccess(t('favorite.success', { ns: 'messages' }));
    }
  }, [isFavoriteSuccess]);

  // Fetch more when last 2 items remain
  useEffect(() => {
    if (hasMorePage && data.length === perPage - 3) {
      fetchMore();
    }
  }, [data.length]);

  // Function to be called on chat request success
  const handleDirectChatSuccess = () => {
    dispatch(setNeedsBookmarkRefresh(true));
  };

  /**
   * Reset all left swiped items from db and show them again
   */
  const handleResetList = () => {
    setUndoAllowed(false);
    resetAndFetch();
  };

  /**
   * Swipe right
   * If user is unsubscribed user and limit is crossed, swipe back the card and show subscription modal.
   * If user swipes right, send type 1 with
   * 1. job_id for jobseeker user
   * 2. job_seeker_id for company user
   * @param feedId id of current feed  item
   * @returns
   */
  const handleSwipeRight = (feedId: string) => {
    if (!isSubscribed && dailyCount >= dailyLimit) {
      return setSubscriptionModalVisible(true);
    }
    const params: IRequestMatchingParams = { type: 1 };
    if (isJobSeeker) {
      params.job_id = feedId;
    } else {
      params.job_seeker_id = feedId;
    }
    addItemToQueue(params);
    dispatch(filterHomeData(feedId));
  };

  /**
   * Swipe left
   * If user swipes left, send type 0 with
   * 1. job_id for jobseeker user
   * 2. job_seeker_id for company user
   * @param feedId
   */
  const handleSwipeLeft = (feedId: string) => {
    const params: IRequestMatchingParams = { type: 0 };
    if (isJobSeeker) {
      params.job_id = feedId;
    } else {
      params.job_seeker_id = feedId;
    }
    addItemToQueue(params);
    dispatch(addLeftSwipedItem(feedId));
    dispatch(filterHomeData(feedId));
  };

  // If currentIndex is less than data length, then there is data left to be showed.
  // Current index will always be 0 because we will remove the item from list once any matching function is done.
  const currentIndex = 0;
  const isDataRemaining = currentIndex < data.length;

  return (
    <DashboardWrapper>
      {isFavoriteLoading && <Spin fullscreen />}
      <div className="flex flex-col sm:flex-row gap-1 sm:gap-10 lg:gap-20 justify-center w-full max-h-[1080px]">
        {!isLoading && (
          <div className="flex sm:flex-col px-4 sm:px-0 sm:w-40 items-center justify-between">
            <button
              onClick={() => {
                const feedToBeRewind = leftSwipedItems?.[0]?.id;
                let params: IRewindParams = {};
                if (isJobSeeker) {
                  params = {
                    job_id: feedToBeRewind,
                  };
                } else {
                  params = {
                    job_seeker_id: feedToBeRewind,
                  };
                }
                rewindFeed(params);
                // Set last left swiped item to data list and remove from left swiped list.
                dispatch(undoLeftSwipedItem());
                // Set undo false if no items are left for undo
                if (leftSwipedItems.length <= 1) {
                  setUndoAllowed(false);
                }
              }}
              disabled={!isUndoAllowed || isRewindLoading}
              className={`${isUndoAllowed ? '' : 'opacity-50 cursor-not-allowed'} hover:bg-WHITE_F6F6F6`}>
              <ArrowBack
                width={36}
                height={36}
                className="w-6 h-6 sm:w-9 sm:h-9"
              />
            </button>
            {/* Left Swipe ( CROSS Button ) */}
            {isDataRemaining && (
              <div className="h-full w-full hidden sm:flex items-center justify-center">
                <button
                  className="hover:shadow-lg rounded-full"
                  onClick={() => {
                    const feedDataId = data?.[currentIndex]?.id;
                    if (feedDataId) {
                      handleSwipeLeft(feedDataId);
                    }
                  }}>
                  <CloseCircle width={60} height={60} />
                </button>
              </div>
            )}
            <button
              onClick={() => setFilterModalVisible(true)}
              className="flex sm:hidden hover:bg-WHITE_F6F6F6">
              <Filter width={20} height={20} />
            </button>
          </div>
        )}
        {isLoading ? (
          <Spin className="flex items-center" />
        ) : (
          <div
            className={`flex flex-col w-[280px] sm:w-[380px] h-full gap-3 ${isDataRemaining ? 'self-center' : 'justify-center'}`}>
            {isDataRemaining ? (
              <div
                className={`flex flex-col w-full h-full relative shadow-md rounded-2xl `}>
                <SwiperCard
                  key={currentIndex.toString()}
                  data={data?.[currentIndex] as IJobData | IJobSeekerProfile}
                />
                <SummaryCard
                  item={data?.[currentIndex] as IJobData | IJobSeekerProfile}
                />
              </div>
            ) : (
              <div className="flex flex-col gap-4 justify-center items-center p-6">
                {isFetchingMore ? (
                  <Spin />
                ) : (
                  <>
                    {/* View More */}
                    <Title
                      type="body1"
                      className="text-center leading-6 font-semibold">
                      {t('outOfUsers')}
                    </Title>
                    {!hasMorePage && (
                      <CustomButton
                        title={t('viewMore', { ns: 'common' })}
                        disabled={isSwipeLoading}
                        loading={isFetchingMore}
                        onClick={() => handleResetList()}
                      />
                    )}
                  </>
                )}
              </div>
            )}
            {isDataRemaining && (
              <div className="flex gap-6 items-center justify-center">
                {/* Left Swipe ( CROSS Button ) */}
                <button
                  className="sm:hidden hover:shadow-lg rounded-full"
                  onClick={() => {
                    const feedDataId = data?.[currentIndex]?.id;
                    if (feedDataId) {
                      handleSwipeLeft(feedDataId);
                    }
                  }}>
                  <CloseCircle className="w-9 h-9 sm:w-12 sm:h-12" />
                </button>
                {/* Favorite / Bookmark */}
                <button
                  className="hover:shadow-lg rounded-full"
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
                      params.job_id = (data?.[currentIndex] as IJobData)?.id;
                    } else {
                      params.job_seeker_id = (
                        data?.[currentIndex] as IJobSeekerProfile
                      )?.id;
                    }
                    matchFavorite(params);
                  }}>
                  <BookmarkCircle className="w-9 h-9 sm:w-12 sm:h-12" />
                </button>
                {/* Chat Request */}
                <button
                  className="hover:shadow-lg rounded-full"
                  onClick={() => {
                    if (!isSubscribed && chatRequestCount >= chatRequestLimit) {
                      return setSubscriptionModalVisible(true);
                    }
                    const requestParams: IChatRequestParams = {};
                    if (isJobSeeker) {
                      requestParams.job_id = (
                        data?.[currentIndex] as IJobData
                      )?.id;
                    } else {
                      requestParams.job_seeker_id = (
                        data?.[currentIndex] as IJobSeekerProfile
                      )?.id;
                    }
                    setDirectChatModalData(requestParams);
                    setDirectChatModalVisible(true);
                  }}>
                  <ChatRequest className="w-9 h-9 sm:w-12 sm:h-12" />
                </button>
                {/* Right Swipe ( CHECKMARK Button ) */}
                <button
                  className="sm:hidden hover:shadow-lg rounded-full"
                  onClick={() => {
                    if (!isSubscribed && swipeCount >= dailyLimit) {
                      return setSubscriptionModalVisible(true);
                    }
                    const feedDataId = data?.[currentIndex]?.id;
                    if (feedDataId) {
                      handleSwipeRight(feedDataId);
                    }
                  }}>
                  <CheckGreen className="w-9 h-9 sm:w-12 sm:h-12" />
                </button>
              </div>
            )}
          </div>
        )}
        {!isLoading && (
          <div className="hidden sm:flex flex-col w-40 items-center">
            <button
              onClick={() => setFilterModalVisible(true)}
              className="hover:bg-WHITE_F6F6F6">
              <Filter width={36} height={36} />
            </button>
            {/* Right Swipe ( CHECKMARK Button ) */}
            {isDataRemaining && (
              <div className="h-full w-full flex items-center justify-center">
                <button
                  className="hover:shadow-lg rounded-full"
                  onClick={() => {
                    if (!isSubscribed && swipeCount >= dailyLimit) {
                      return setSubscriptionModalVisible(true);
                    }
                    const feedDataId = data?.[currentIndex]?.id;
                    if (feedDataId) {
                      handleSwipeRight(feedDataId);
                    }
                  }}>
                  <CheckGreen width={60} height={60} />
                </button>
              </div>
            )}
          </div>
        )}
      </div>

      {/* Filter Modal */}
      {isFilterModalVisible &&
        (isJobSeeker ? (
          <Suspense fallback={<Spin />}>
            <FilterCompany setModalVisible={setFilterModalVisible} />
          </Suspense>
        ) : (
          <Suspense fallback={<Spin />}>
            <FilterJobSeeker setModalVisible={setFilterModalVisible} />
          </Suspense>
        ))}

      {/* Subscription Modal */}
      {isSubscriptionModalVisible && (
        <Suspense fallback={<Spin />}>
          <SubscribeModal
            closeModal={() => setSubscriptionModalVisible(false)}
          />
        </Suspense>
      )}

      {/* Direct Chat Request Modal */}
      {isDirectChatModalVisible && (
        <Suspense fallback={<Spin />}>
          <DirectChatModal
            closeModal={() => {
              setDirectChatModalData({});
              setDirectChatModalVisible(false);
            }}
            chatRequestData={directChatModalData}
            setDirectChatSuccess={() => handleDirectChatSuccess()}
          />
        </Suspense>
      )}

      {/* Matched Modal */}
      {isMatchingModalVisible && (
        <Suspense fallback={<Spin />}>
          <MatchModal />
        </Suspense>
      )}
    </DashboardWrapper>
  );
};

export default HomeScreen;
