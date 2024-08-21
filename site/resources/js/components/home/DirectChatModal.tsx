import React, { useEffect, useState } from 'react';

import { Modal } from 'antd';
import { useTranslation } from 'react-i18next';

// Components
import { CustomButton, TextAreaInput, Title } from '@components/common';

// Hooks
import useChatRequest from '@customHooks/useChatRequest';
import useUserProfile from '@customHooks/useUserProfile';

// Redux
import { useAppDispatch } from '@redux/hook';
import { filterHomeData } from '@redux/reducers/home';
import { setNeedsChatRefresh } from '@redux/reducers/chat';
import { IChatRequestParams } from '@redux/services/matchingApi';

interface Props {
  closeModal: () => void;
  setDirectChatSuccess: () => void;
  chatRequestData: IChatRequestParams;
}

const DirectChatModal = ({
  closeModal,
  chatRequestData,
  setDirectChatSuccess,
}: Props) => {
  const { t } = useTranslation(['home']);

  const dispatch = useAppDispatch();

  const { sendChatRequest, isLoading, isSuccess, isError, error } =
    useChatRequest();
  const { isJobSeeker } = useUserProfile();

  const [message, setMessage] = useState<string>();

  useEffect(() => {
    if (isSuccess || (isError && error?.status === 422)) {
      closeModal();
      setDirectChatSuccess();
      dispatch(setNeedsChatRefresh(true));
      const dataId = isJobSeeker
        ? chatRequestData?.job_id
        : chatRequestData?.job_seeker_id;
      dataId && dispatch(filterHomeData(dataId));
    }
  }, [isSuccess, isError]);

  const handleSendRequest = () => {
    sendChatRequest({
      message,
      ...chatRequestData,
    });
  };

  return (
    <Modal
      centered
      title={
        <Title type="heading2" className="text-center" bold>
          {t('directChat')}
        </Title>
      }
      open={true}
      onCancel={closeModal}
      maskClosable={false}
      footer={null}>
      <div className="flex flex-col gap-4 max-h-[70dvh] overflow-scroll">
        <Title type="caption1" className="text-center">
          {t('enterDirectChatMessage')}
        </Title>
        <TextAreaInput
          label={t('enterMessage', { ns: 'profile' })}
          placeholder={t('optional', { ns: 'common' })}
          onChange={e => setMessage(e.target.value)}
          autoSize={{ minRows: 1, maxRows: 6 }}
        />
        <CustomButton
          title={t('sendRequest')}
          loading={isLoading}
          onClick={() => handleSendRequest()}
          className="mt-4"
        />
      </div>
    </Modal>
  );
};

export default DirectChatModal;
