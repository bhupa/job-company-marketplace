import React, { useState } from 'react';

import { Pagination, Spin } from 'antd';
import { useTranslation } from 'react-i18next';

// Components
import { DashboardWrapper } from '@templates';
import { ReportUserModal } from '@components/home';
import { ChatListItem, DeleteChatModal } from '@components/chat';
import { CustomInput, EmptyListMessage, Title } from '@components/common';

// Hooks
import useDebounce from '@customHooks/useDebounce';
import useChatList from '@customHooks/useChatList';

// Others
import { Search } from '@assets/icons';

const ChatList = () => {
  const { t } = useTranslation(['chat']);

  const [deleteChatId, setDeleteChatId] = useState<string | null>(null);
  const [reportChatId, setReportChatId] = useState<string | null>(null);
  const [searchText, setSearchText] = useState<string>('');
  const debouncedSearchValue = useDebounce(searchText, 1000);

  const { data, isLoading, isEmpty, setPage, totalData } =
    useChatList(debouncedSearchValue);

  return (
    <DashboardWrapper>
      <div className="w-full">
        <div className="sticky h-[50px] top-0 z-50 pl-3 bg-white flex items-center justify-center w-full mb-1">
          <Title type="heading2" className="text-BLUE_25396F" bold>
            {t('chat')}
          </Title>
        </div>
        <div className="gap-2 flex flex-col">
          <CustomInput
            placeholder={t('search')}
            onChange={e => setSearchText(e.target.value.trim())}
            prefix={
              <span>
                <Search width={12} height={12} className="text-GRAY_A6A6A6" />
              </span>
            }
          />
          <Title type="body2" bold className={'px-3 py-1'}>
            {t('messages')}
          </Title>
          {isLoading ? (
            <Spin className="flex justify-center" />
          ) : isEmpty ? (
            <EmptyListMessage message={t('noChatAvailable')} />
          ) : (
            <div className="w-full">
              {data?.map(item => {
                return (
                  <ChatListItem
                    key={item.id}
                    item={item}
                    onDeletePressed={() => setDeleteChatId(item.id)}
                    onReportPressed={() => setReportChatId(item.id)}
                  />
                );
              })}
              <Pagination
                defaultCurrent={1}
                total={totalData}
                className="mt-4"
                onChange={page => setPage(page)}
              />
            </div>
          )}
        </div>
      </div>
      {deleteChatId && (
        <DeleteChatModal
          closeModal={() => setDeleteChatId(null)}
          chatRoomId={deleteChatId}
        />
      )}
      {reportChatId && (
        <ReportUserModal
          closeModal={() => setReportChatId(null)}
          userId={reportChatId}
          type="chat"
        />
      )}
    </DashboardWrapper>
  );
};

export default ChatList;
