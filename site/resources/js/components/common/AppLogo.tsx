import React from 'react';

import { AppLogo as AppLogoIcon, AppLogoWithName } from '@assets/images';

interface AppLogoProps {
  small?: boolean;
  className?: string;
  type?: 'primary' | 'secondary';
  onClick?: () => void;
}

const AppLogo = ({
  type = 'primary',
  small,
  className = '',
  onClick,
}: AppLogoProps) => {
  const isPrimary = type === 'primary';
  const size = small
    ? isPrimary
      ? 'w-[48px] h-[48px]'
      : 'w-[180px] h-[80px]'
    : isPrimary
      ? 'w-[80px] h-[80px]'
      : 'w-[320px] h-[140px]';
  const cursorType = onClick ? 'cursor-pointer' : '';
  return (
    <img
      className={`${size} object-contain ${cursorType} ${className}`}
      src={type === 'primary' ? AppLogoIcon : AppLogoWithName}
      loading="lazy"
      alt="app-logo"
      onClick={() => onClick && onClick()}
    />
  );
};

export default AppLogo;
