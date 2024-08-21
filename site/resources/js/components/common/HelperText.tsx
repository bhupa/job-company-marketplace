import React from 'react';

import { useTranslation } from 'react-i18next';

// Components
import Title from './Title';

interface HelperTextProps {
  message: string;
  className?: string;
}

const HelperText = ({ message, className = '' }: HelperTextProps) => {
  const { t } = useTranslation('messages');
  return (
    <Title
      type="caption1"
      className={`text-GRAY_545454 mt-2 italic ${className}`}>
      {t(message)}
    </Title>
  );
};

export default HelperText;
