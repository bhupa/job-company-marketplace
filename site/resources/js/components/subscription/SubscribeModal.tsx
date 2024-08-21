import React from 'react';

import { Grid, Modal } from 'antd';
import { useTranslation } from 'react-i18next';

// Components
import PlanList from './PlanList';
import { Title } from '@components/common';

const { useBreakpoint } = Grid;

interface Props {
  closeModal: () => void;
}

const SubscribeModal = ({ closeModal }: Props) => {
  const { t } = useTranslation(['profile']);
  const breakpoints = useBreakpoint();

  return (
    <Modal
      centered
      title={
        <Title type="heading2" className="text-center" bold>
          {t('subscribeNow')}
        </Title>
      }
      width={breakpoints.md ? '60%' : '90%'}
      open={true}
      onCancel={closeModal}
      maskClosable={false}
      footer={null}>
      <div className="flex flex-col gap-4 max-h-[70dvh] overflow-scroll">
        <Title type="heading1" className="text-center">
          {t('subscription.getUnlimitedSwipes')}
        </Title>
        <div className="flex flex-col  justify-center">
          <PlanList />
        </div>
      </div>
    </Modal>
  );
};

export default SubscribeModal;
