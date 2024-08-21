import React from 'react';

import { useTranslation } from 'react-i18next';

// Components
import Title from './Title';

interface ErrorTextProps {
  error: string;
  className?: string;
}

const ErrorText = ({ error, className = 'first-letter:' }: ErrorTextProps) => {
  const { t } = useTranslation(['messages']);
  return (
    <Title type="caption1" className={`mb-1 text-RED_FF4D4D ${className}`}>
      {t(`validation.${error}`)}
    </Title>
  );
};

export default ErrorText;
