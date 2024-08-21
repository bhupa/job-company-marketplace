import React, { useEffect } from 'react';

import { useTranslation } from 'react-i18next';

// Components
import { ConfirmationModal } from '@components/common';

// Hooks
import { useShowMessage } from '@customHooks/useShowMessage';

// Redux
import { useAdminAssistMutation } from '@redux/services/chatApi';

interface Props {
  closeModal: () => void;
  roomId: string;
  setShowAdminAssist?: (isVisible: boolean) => void;
}

const AdminAssistModal = ({
  closeModal,
  roomId,
  setShowAdminAssist,
}: Props) => {
  const { showSuccess } = useShowMessage();
  const { t } = useTranslation(['chat']);

  const [askAdminAssist, { isSuccess, isLoading, isError }] =
    useAdminAssistMutation();

  useEffect(() => {
    if (isSuccess) {
      showSuccess(t('adminAssist.success'));
      setShowAdminAssist && setShowAdminAssist(false);
    }
    if (isError || isSuccess) {
      closeModal();
    }
  }, [isSuccess, isError]);

  return (
    <ConfirmationModal
      isLoading={isLoading}
      title={t('adminAssist.title')}
      description={t('adminAssist.description')}
      closeModal={() => closeModal()}
      onOkPress={() => {
        askAdminAssist(roomId);
      }}
    />
  );
};

export default AdminAssistModal;
