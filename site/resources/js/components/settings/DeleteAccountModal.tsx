import React, { useEffect, useState } from 'react';

import { Modal, Spin } from 'antd';
import { useTranslation } from 'react-i18next';

// Components
import { CustomButton, CustomInput, Title } from '@components/common';

// Hooks
import useUserProfile from '@customHooks/useUserProfile';
import { useShowMessage } from '@customHooks/useShowMessage';

// Redux
import { useDeleteAccountMutation } from '@redux/services/authApi';

interface Props {
  isVisible: boolean;
  setModalVisible?: (isVisible: boolean) => void;
}

const DeleteAccountModal = ({
  isVisible,
  setModalVisible = () => {},
}: Props) => {
  const { t } = useTranslation('profile, common');

  const { showSuccess } = useShowMessage();
  const { user, handleLogout } = useUserProfile();
  const [deleteAccount, { isLoading, isSuccess }] = useDeleteAccountMutation();

  const [deleteText, setDeleteText] = useState<string>();
  const [error, setError] = useState<string | null>(null);

  const handleChange = (event: React.ChangeEvent<HTMLInputElement>) => {
    const value = event.target.value;
    setDeleteText(value);
  };

  useEffect(() => {
    if (isSuccess) {
      handleLogout(false);
      showSuccess(t('account.deleted', { ns: 'messages' }));
    }
  }, [isSuccess]);

  const handleDeleteClicked = () => {
    setError(null);
    if (deleteText !== 'delete') {
      return setError(t('deleteInputError', { ns: 'messages' }));
    }
    deleteAccount(user.id);
  };

  if (isLoading) {
    return <Spin fullscreen />;
  }

  return (
    <Modal
      centered
      open={isVisible}
      closable={false}
      title={
        <Title
          type="heading2"
          className="flex items-center justify-center w-full">
          {t('deleteAccount.title', {
            ns: 'profile',
          })}
        </Title>
      }
      footer={
        <div className="flex items-center justify-around w-full border-t-2 border-WHITE_F6F6F6">
          <CustomButton
            type="text"
            className="mt-2 text-GRAY_77838F"
            title={t('cancel', { ns: 'common' })}
            onClick={() => setModalVisible(false)}
          />
          <CustomButton
            type="text"
            className="mt-2 text-RED_FF4D4D"
            title={t('confirm', { ns: 'common' })}
            onClick={handleDeleteClicked}
          />
        </div>
      }>
      <div className="flex flex-col gap-4">
        <div className="flex flex-col flex-wrap w-full items-center justify-center gap-1">
          <Title type="body2">
            {t('deleteAccount.thisActionCannotBeUndone', { ns: 'profile' })}
          </Title>
          <Title type="body2">
            {t('deleteAccount.type', { ns: 'profile' })}
            <Title type="body2" className="text-RED_FF4D4D">
              &nbsp;delete
            </Title>
            &nbsp;
            {t('deleteAccount.toConfirm', { ns: 'profile' })}
          </Title>
        </div>
        <CustomInput
          name="confirm"
          value={deleteText}
          onChange={handleChange}
          error={error ? error : null}
        />
      </div>
    </Modal>
  );
};

export default DeleteAccountModal;
