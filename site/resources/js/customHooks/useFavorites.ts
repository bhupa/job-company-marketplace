import { useEffect, useState } from 'react';

import { useTranslation } from 'react-i18next';

// Hooks
import { useShowMessage } from './useShowMessage';

// Redux
import {
  IRequestResponse,
  useFavoriteMatchingMutation,
  useGetMatchingRequestsQuery,
} from '@redux/services/matchingApi';
import { useAppDispatch, useAppSelector } from '@redux/hook';
import { setNeedsBookmarkRefresh } from '@redux/reducers/bookmark';

const PER_PAGE = 10;

const useFavorites = () => {
  const { showSuccess } = useShowMessage();
  const { t } = useTranslation('messages');
  const [page, setPage] = useState<number>(1);
  const [favorites, setFavorites] = useState<IRequestResponse[]>([]);
  const [isLoading, setLoading] = useState<boolean>(false);
  // To show empty message when user removes items from favorites
  const [isEmpty, setEmpty] = useState<boolean>(false);
  const [isRefresh, setRefresh] = useState<boolean>(false);

  const dispatch = useAppDispatch();
  const { needsRefresh } = useAppSelector(state => state.bookmark);

  const {
    data,
    isLoading: isFavoritesLoading,
    isFetching,
    isError,
    refetch,
  } = useGetMatchingRequestsQuery({
    page,
    per_page: PER_PAGE,
    type: 'favourite',
  });

  const [
    matchFavorite,
    {
      isLoading: isFavoriteMatchingLoading,
      isSuccess: isFavoriteSuccess,
      data: favoriteRemoveData,
    },
  ] = useFavoriteMatchingMutation();

  useEffect(() => {
    if (isError) {
      setLoading(false);
      setEmpty(true);
    }
  }, [isError]);

  useEffect(() => {
    if (isFavoritesLoading) {
      setLoading(true);
    }
  }, [isFavoritesLoading]);

  useEffect(() => {
    if (data?.data?.items?.data) {
      const listData = data.data.items?.data;
      setFavorites(listData);
      setLoading(false);
      isEmpty && setEmpty(false);
    }
  }, [data]);

  useEffect(() => {
    if (isFavoriteSuccess && favoriteRemoveData?.data) {
      const updatedFavorites = favorites.filter(
        favorite => favorite.id !== favoriteRemoveData.data.items?.id,
      );
      updatedFavorites.length === 0 && setEmpty(true);
      setFavorites(updatedFavorites);
      showSuccess(t('favorite.removeSuccess'));
    }
  }, [isFavoriteSuccess]);

  useEffect(() => {
    if (page === 1 && isRefresh) {
      refetch();
      setRefresh(false);
    }
  }, [page, isRefresh]);

  /**
   * Function to refetch favorites list when user marks favorite from home screen
   */
  useEffect(() => {
    if (needsRefresh) {
      refreshFavorites();
      dispatch(setNeedsBookmarkRefresh(false));
    }
  }, [needsRefresh]);

  const refreshFavorites = () => {
    setPage(1);
    setRefresh(true);
  };

  return {
    data: favorites,
    isLoading: isFavoritesLoading || isLoading,
    page,
    setPage,
    perPage: PER_PAGE,
    isFetching: !isLoading && isFetching,
    isEmpty: isEmpty || data?.data?.items?.pagination?.count === 0,
    // Favorite update functions
    isFavoriteMatchingLoading,
    matchFavorite,
    totalData: data?.data?.items?.pagination?.total ?? 0,
  };
};

export default useFavorites;
