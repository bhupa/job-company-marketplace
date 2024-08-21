import { useEffect, useState } from 'react';

// Redux
import {
  IJobSeekerProfile,
  useGetMatchingJobSeekersQuery,
} from '@redux/services/jobSeekerApi';
import { useAppDispatch, useAppSelector } from '@redux/hook';
import { setMatchingLimit } from '@redux/reducers/subscription';
import { resetLeftSwipedItems, setHomeData } from '@redux/reducers/home';

// Others
import { filterDuplicates } from '@utils/helpers';
import { HOME_PER_PAGE_COUNT } from '@utils/constants';

const useMatchingJobSeekers = () => {
  const [page, setPage] = useState<number>(1);
  const [isReset, setIsReset] = useState<boolean>(false);
  const [isFetchingMore, setFetchingMore] = useState<boolean>(false);
  // Set these ids to prevent from fetching again
  const [dataIdList, setDataIdList] = useState<number[]>([]);
  const [preventedIdList, setPreventedIdList] = useState<number[]>([]);

  const dispatch = useAppDispatch();
  const {
    homeData: jobSeekers,
    needsHomeRefresh,
    isFetched,
  } = useAppSelector(state => state.home);
  const [isLoading, setLoading] = useState<boolean>(!isFetched);

  const { jobSeekerFilter } = useAppSelector(state => state.filter);
  const {
    age_from,
    age_to,
    experience,
    gender,
    japanese_level,
    occupation,
    start_when,
  } = jobSeekerFilter || {};

  let resetParams = {};

  if (isReset && page === 1) {
    resetParams = {
      type: 'reset',
    };
  }

  let previousIdParams = {};
  if (preventedIdList?.length) {
    previousIdParams = {
      previousId: JSON.stringify(preventedIdList),
    };
  }

  /**
   * Set page 1 always because when we swipe the card, the total number of items will be decreased
   * and data from page 2 will move to page 1 and those data will be lost. So, to fix it, pass previous ids
   * in array to prevent them from fetching again.
   */
  const { data, isFetching, isError, isSuccess, refetch, fulfilledTimeStamp } =
    useGetMatchingJobSeekersQuery({
      page: 1,
      per_page: HOME_PER_PAGE_COUNT,
      age_from,
      age_to,
      experience,
      gender: gender === '4' ? null : gender,
      japanese_level,
      occupation,
      start_when,
      ...previousIdParams, // prevent these ids to be repeated
      ...resetParams,
    });

  const hasMorePage = data?.data?.items?.pagination?.nextPageUrl ? true : false;

  /**
   * Reset data from filter page (FilterJobseeker) and fetch again when filter is changed
   */
  useEffect(() => {
    setLoading(true);
    setPage(1);
  }, [
    age_from,
    age_to,
    experience,
    gender,
    japanese_level,
    occupation,
    start_when,
  ]);

  useEffect(() => {
    if (isError) {
      setLoading(false);
    }
  }, [isError]);

  useEffect(() => {
    // If already fetched and we are coming from other pages then set loading false and don't update data
    if (isFetched && page === 1) {
      setLoading(false);
      return;
    }
    if (!isFetching) {
      if (isSuccess && fulfilledTimeStamp) {
        dispatch(
          setMatchingLimit({
            dailyCount: data?.data?.dailyCount,
            dailyLimit: data?.data?.dailylimit,
            favoriteCount: data?.data?.favouriteCount,
            favoriteLimit: data?.data?.favoriteLimit,
            chatRequestCount: data?.data?.chatRequestCount,
            chatRequestLimit: data?.data?.chatRequestLimit,
          }),
        );
      }
      if (data?.data?.items?.data) {
        if (fulfilledTimeStamp) {
          const reversedData = data.data.items.data;
          const jobSeekerData =
            page === 1
              ? reversedData
              : filterDuplicates([
                  ...(jobSeekers as IJobSeekerProfile[]),
                  ...reversedData,
                ]);
          const obtainedDataIdList = jobSeekerData.map(item => Number(item.id));
          setDataIdList(obtainedDataIdList);
          dispatch(setHomeData({ data: reversedData, reset: page === 1 }));
          setFetchingMore(false);
          setTimeout(() => setLoading(false), 300);
        }
      } else {
        // If data is empty set loading to false
        if (isSuccess) {
          setFetchingMore(false);
          setTimeout(() => setLoading(false), 300);
        }
      }
    }
  }, [data, fulfilledTimeStamp]);

  useEffect(() => {
    if (needsHomeRefresh) {
      setPage(1);
      setPreventedIdList([]);
      refetch();
    }
  }, [needsHomeRefresh]);

  const fetchMore = () => {
    if (!isFetchingMore && hasMorePage && !isFetching) {
      setFetchingMore(true);
      setIsReset(false);
      setPage(page + 1);
      setPreventedIdList(dataIdList);
      refetch();
    }
  };

  /**
   * Used to reset all left swiped items to display again in feed
   */
  const resetAndFetch = () => {
    setLoading(true);
    setPreventedIdList([]);
    dispatch(setHomeData({ data: [], reset: true }));
    dispatch(resetLeftSwipedItems());
    setIsReset(true);
    setPage(1);
    refetch();
  };

  return {
    isLoading,
    fetchMore,
    isFetchingMore,
    page,
    hasMorePage,
    perPage: HOME_PER_PAGE_COUNT,
    resetAndFetch,
  };
};

export default useMatchingJobSeekers;
