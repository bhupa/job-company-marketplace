import { useEffect, useState } from 'react';

// Redux
import { useAppDispatch, useAppSelector } from '@redux/hook';
import { setMatchingLimit } from '@redux/reducers/subscription';
import { resetLeftSwipedItems, setHomeData } from '@redux/reducers/home';
import { IJobData, useGetMatchingJobsQuery } from '@redux/services/jobsApi';

// Others
import { filterDuplicates } from '@utils/helpers';
import { HOME_PER_PAGE_COUNT } from '@utils/constants';

const useMatchingJobs = () => {
  const [page, setPage] = useState<number>(1);
  const [isReset, setIsReset] = useState<boolean>(false);
  const [isFetchingMore, setFetchingMore] = useState<boolean>(false);
  const [dataIdList, setDataIdList] = useState<number[]>([]);
  const [preventedIdList, setPreventedIdList] = useState<number[]>([]);

  const dispatch = useAppDispatch();
  const {
    homeData: jobs,
    needsHomeRefresh,
    isFetched,
  } = useAppSelector(state => state.home);
  const [isJobLoading, setJobLoading] = useState<boolean>(!isFetched);

  const { companyFilter } = useAppSelector(state => state.filter);
  const {
    job_location,
    salary_from,
    salary_to,
    job_type,
    from_when,
    occupation,
    pay_type,
  } = companyFilter || {};

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

  const { data, isFetching, isError, isSuccess, refetch, fulfilledTimeStamp } =
    useGetMatchingJobsQuery({
      page: 1,
      per_page: HOME_PER_PAGE_COUNT,
      job_location,
      salary_from,
      salary_to,
      pay_type,
      job_type,
      from_when,
      occupation,
      ...previousIdParams, // prevent these ids to be repeated
      ...resetParams,
    });

  const hasMorePage = data?.data?.items?.pagination?.nextPageUrl ? true : false;

  /**
   * Reset data from filter page (FilterCompany) and fetch again when filter is changed
   */
  useEffect(() => {
    setJobLoading(true);
    setPage(1);
  }, [job_location, salary_from, salary_to, job_type, from_when, occupation]);

  useEffect(() => {
    if (isError) {
      setJobLoading(false);
    }
  }, [isError]);

  useEffect(() => {
    // If already fetched and we are coming from other pages then set loading false and don't update data
    if (isFetched && page === 1) {
      setJobLoading(false);
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
          const jobData =
            page === 1
              ? reversedData
              : filterDuplicates([...(jobs as IJobData[]), ...reversedData]);
          const obtainedDataIdList = jobData.map(item => Number(item.id));
          setDataIdList(obtainedDataIdList);
          dispatch(setHomeData({ data: reversedData, reset: page === 1 }));
          setFetchingMore(false);
          setTimeout(() => setJobLoading(false), 300);
        }
      } else {
        // If data is empty set loading to false
        if (isSuccess) {
          setFetchingMore(false);
          setTimeout(() => setJobLoading(false), 300);
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

  const resetAndFetch = () => {
    setJobLoading(true);
    setPreventedIdList([]);
    dispatch(setHomeData({ data: [], reset: true }));
    dispatch(resetLeftSwipedItems());
    setIsReset(true);
    setPage(1);
    refetch();
  };

  return {
    isLoading: isJobLoading,
    fetchMore,
    isFetchingMore,
    page,
    hasMorePage,
    perPage: HOME_PER_PAGE_COUNT,
    resetAndFetch,
  };
};

export default useMatchingJobs;
