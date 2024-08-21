import React, { useEffect } from 'react';

import { Grid, Modal, Spin } from 'antd';
import { useTranslation } from 'react-i18next';

// Components
import { Title } from '@components/common';

// Redux
import { useSuperchatEmailMutation } from '@redux/services/chatApi';

// Hooks
import useUserProfile from '@customHooks/useUserProfile';

// Others
import { EsewaLogo, StripeLogo } from '@assets/icons';

interface Props {
  closeModal: () => void;
  roomId: string;
  makeEsewaPayment: () => void;
}

const { useBreakpoint } = Grid;

const SuperChatMailModal = ({
  closeModal,
  roomId,
  makeEsewaPayment,
}: Props) => {
  const breakpoints = useBreakpoint();

  const { isJobSeeker } = useUserProfile();

  const { t } = useTranslation(['chat']);
  const [paySuperchatFromStripe, { isSuccess, isLoading, isError, data }] =
    useSuperchatEmailMutation();

  useEffect(() => {
    if (isSuccess) {
      window.open(data.data.url, '_self');
    }
    if (isError || isSuccess) {
      closeModal();
    }
  }, [isSuccess, isError]);

  useEffect(() => {
    if (!isJobSeeker) {
      paySuperchatFromStripe(roomId);
    }
  }, []);

  if (!isJobSeeker) {
    return <Spin fullscreen />;
  }

  return (
    <Modal
      centered
      open={true}
      closable={true}
      onCancel={() => closeModal()}
      footer={null}
      width={breakpoints.sm ? '44%' : '70%'}>
      {isLoading && <Spin fullscreen />}
      <Title type="body1" className="flex justify-center text-center" bold>
        {t('unrestrictedChat.title')}
      </Title>
      <Title type="body2" className="flex text-center sm:text-justify">
        {t('unrestrictedChat.payTitle')}
      </Title>
      <div className="flex flex-col sm:flex-row gap-4 justify-between items-center mt-4">
        <button
          onClick={() => {
            makeEsewaPayment();
            closeModal();
          }}
          className="card w-full sm:w-[1/2] shadow-md hover:shadow-lg flex justify-center items-center">
          <EsewaLogo className="flex w-full sm:max-w-32 h-20 sm:h-20" />
        </button>
        <button
          onClick={() => paySuperchatFromStripe(roomId)}
          className="card w-full sm:w-[1/2] shadow-md hover:shadow-lg flex justify-center items-center">
          <StripeLogo className="flex w-full sm:max-w-32 h-20 sm:h-20" />
        </button>
      </div>
    </Modal>
  );
};

export default SuperChatMailModal;
