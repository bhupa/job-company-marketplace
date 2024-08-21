import React from 'react';

import { Modal, Spin, Grid } from 'antd';
import { useTranslation } from 'react-i18next';

// Components
import Title from './Title';
import CustomButton from './CustomButton';

const { useBreakpoint } = Grid;

interface Props {
  onOkPress: () => void;
  closeModal: () => void;
  title: string;
  description?: string;
  isLoading?: boolean;
  hideCancel?: boolean;
}

const ConfirmationModal = (props: Props) => {
  const { t } = useTranslation(['chat', 'messages']);
  const breakpoints = useBreakpoint();

  const { closeModal, title, description, onOkPress, isLoading, hideCancel } =
    props;

  if (isLoading) {
    return <Spin fullscreen />;
  }

  return (
    <Modal
      centered
      open={true}
      width={breakpoints.md ? '40%' : '70%'}
      closable={false}
      footer={
        <div className="flex items-center justify-around w-full border-t-2 border-t-WHITE_E0E2E4">
          {!hideCancel && (
            <>
              <CustomButton
                type="text"
                className="text-GRAY_77838F w-full"
                title={t('cancel', { ns: 'common' })}
                onClick={() => closeModal()}
              />
              <div className="w-[2px] h-10 bg-WHITE_E0E2E4" />
            </>
          )}
          <CustomButton
            type="text"
            className="text-RED_FF4D4D w-full"
            title={t('confirm', { ns: 'common' })}
            onClick={() => onOkPress()}
          />
        </div>
      }>
      <Title type="body1" className="flex text-center" bold>
        {title}
      </Title>
      {description && (
        <Title type="body2" className="flex text-center" bold>
          {description}
        </Title>
      )}
    </Modal>
  );
};

export default ConfirmationModal;
