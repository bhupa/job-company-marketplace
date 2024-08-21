import React from 'react';

import { Pagination, Spin } from 'antd';
import { useTranslation } from 'react-i18next';

// Components
import { DashboardWrapper } from '@templates';
import { RequestCard } from '@components/bookmark';
import { EmptyListMessage, HeaderWithBackButton } from '@components/common';

// Hooks
import useFavorites from '@customHooks/useFavorites';

const Bookmark = () => {
  const { t } = useTranslation(['notification']);

  const {
    isLoading,
    data,
    isEmpty,
    isFetching,
    matchFavorite,
    totalData,
    page,
    setPage,
    perPage,
    isFavoriteMatchingLoading,
  } = useFavorites();

  return (
    <DashboardWrapper>
      {isFavoriteMatchingLoading && <Spin fullscreen />}
      <div className="flex flex-col gap-6 w-full">
        <HeaderWithBackButton title={t('bookmark')} hasBackButton={false} />
        {isLoading || isFetching ? (
          <Spin className="flex justify-center" />
        ) : isEmpty ? (
          <EmptyListMessage />
        ) : (
          <>
            <div className="grid grid-cols-1 xs:grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 justify-items-center">
              {data &&
                data?.map(item => {
                  return (
                    <RequestCard
                      key={item.id}
                      data={item}
                      isFavoritesTab
                      onRemoveFavorite={params => matchFavorite(params)}
                      isVisible={true} // For favorites, all cards will be visible
                    />
                  );
                })}
            </div>
            <Pagination
              defaultCurrent={1}
              current={page}
              total={totalData}
              pageSize={perPage}
              onChange={page => setPage(page)}
            />
          </>
        )}
      </div>
    </DashboardWrapper>
  );
};

export default Bookmark;
