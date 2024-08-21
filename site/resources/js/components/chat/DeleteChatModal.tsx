import React, { useEffect } from 'react';

import { useTranslation } from 'react-i18next';

// Components
import { ConfirmationModal } from '@components/common';

// Hooks
import { useShowMessage } from '@customHooks/useShowMessage';

// Redux
import { useAppDispatch } from '@redux/hook';
import { deleteMessage } from '@redux/reducers/chat';
import { useDeleteChatRoomMutation } from '@redux/services/chatApi';

interface Props {
  closeModal: () => void;
  chatRoomId: string;
}

const DeleteChatModal = (props: Props) => {
  const { showSuccess } = useShowMessage();
  const { t } = useTranslation(['chat', 'messages']);

  const { closeModal, chatRoomId } = props;

  const dispatch = useAppDispatch();
  const [deleteChat, { isLoading, isSuccess, isError }] =
    useDeleteChatRoomMutation();

  useEffect(() => {
    if (isSuccess) {
      dispatch(deleteMessage({ chatRoomId }));
      showSuccess(t('chat.deleted', { ns: 'messages' }));
    }
    if (isError || isSuccess) {
      closeModal();
    }
  }, [isSuccess, isError]);

  return (
    <ConfirmationModal
      isLoading={isLoading}
      title={t('deleteChat.title')}
      closeModal={() => closeModal()}
      onOkPress={() => {
        deleteChat(chatRoomId);
      }}
    />
  );
};

export default DeleteChatModal;
