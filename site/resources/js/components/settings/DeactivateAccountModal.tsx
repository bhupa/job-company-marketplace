import React, { useEffect } from 'react';

import { Modal, Spin } from 'antd';
import { useTranslation } from 'react-i18next';

// Components
import { CustomButton, Title } from '@components/common';

// Hooks
import useUserProfile from '@customHooks/useUserProfile';
import { useShowMessage } from '@customHooks/useShowMessage';

// Redux
import {
  AccountStatusType,
  useChangeAccountStatusMutation,
} from '@redux/services/authApi';

interface Props {
  isVisible: boolean;
  setModalVisible?: (isVisible: boolean) => void;
}

const DeactivateAccountModal = (props: Props) => {
  const { t } = useTranslation(['profile, common']);
  const { showSuccess } = useShowMessage();

  const { isVisible, setModalVisible = () => {} } = props;

  const { handleLogout } = useUserProfile();
  const [deactivateAccount, { isLoading, isSuccess }] =
    useChangeAccountStatusMutation();

  useEffect(() => {
    if (isSuccess) {
      handleLogout();
      showSuccess(t('account.deactivated', { ns: 'messages' }));
    }
  }, [isSuccess]);

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
          {t('deactivateAccount.deactivateAccountQuestion', {
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
            onClick={() => deactivateAccount(AccountStatusType.DEACTIVATED)}
          />
        </div>
      }>
      <div className="flex flex-row flex-wrap gap-1 p-2">
        <Title type="body2">
          {t('deactivateAccount.areYouSure', { ns: 'profile' })}
        </Title>
        <Title type="body2" className="text-RED_FF4D4D">
          {t('deactivateAccount.deactivate', { ns: 'profile' })}
        </Title>
        <Title type="body2">
          {t('deactivateAccount.yourAccount', { ns: 'profile' })}
        </Title>
      </div>
    </Modal>
  );
};

export default DeactivateAccountModal;
