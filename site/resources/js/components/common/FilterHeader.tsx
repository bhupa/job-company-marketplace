import React from 'react';

import { useTranslation } from 'react-i18next';

// Components
import Title from './Title';

// Others
import { Close } from '@assets/icons';

interface FilterHeaderProps {
  hasBackButton?: boolean;
  title?: string;
  hasClearBtn?: boolean;
  clearDisabled?: boolean;
  onClearPressed?: () => void;
  onClosePressed: () => void;
}

const FilterHeader = (props: FilterHeaderProps) => {
  const {
    hasBackButton = true,
    title,
    clearDisabled,
    onClearPressed,
    hasClearBtn,
    onClosePressed,
  } = props;
  const { t } = useTranslation(['home']);

  return (
    <div className="relative flex items-center justify-center pb-2 border-b border-b-WHITE_E0E2E4">
      {hasBackButton && (
        <button onClick={() => onClosePressed()} className={'absolute left-2'}>
          <Close className={'text-GRAY_545454'} />
        </button>
      )}
      <Title type="heading2" bold className="text-center">
        {title ? t(title) : t('filter')}
      </Title>
      {hasClearBtn && (
        <button
          className="absolute right-4"
          disabled={clearDisabled}
          onClick={() => onClearPressed && onClearPressed()}>
          <Title
            type="body1"
            className={`${clearDisabled ? 'text-GRAY_ACACAC' : 'text-black'}`}>
            {t('clearAll')}
          </Title>
        </button>
      )}
    </div>
  );
};

export default FilterHeader;
