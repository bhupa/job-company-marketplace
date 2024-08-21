import React from 'react';

import { useTranslation } from 'react-i18next';

// Components
import Title from './Title';

const EmptyListMessage = ({ message }: { message?: string }) => {
  const { t } = useTranslation(['messages']);
  return (
    <div className={'flex flex-1 justify-center items-center p-6 pt-2'}>
      <Title type="body1" className="text-center">
        {message ? t(message) : t('emptyData')}
      </Title>
    </div>
  );
};

export default EmptyListMessage;
