import React from 'react';

import { AppName as AppNameTransparent, AppNameWhite } from '@assets/images';

interface AppNameProps {
  className?: string;
  small?: boolean;
  type?: 'transparent' | 'filled';
  onClick?: () => void;
}

const AppName = ({
  type = 'transparent',
  small,
  className = '',
  onClick,
}: AppNameProps) => {
  const cursorType = onClick ? 'cursor-pointer' : '';
  const size = small ? 'w-[80px] sm:w-[140px] h-[48px]' : 'w-[180px] h-[80px]';
  return (
    <img
      className={`${size} object-contain ${cursorType} ${className} hidden xs:flex`}
      src={type === 'transparent' ? AppNameTransparent : AppNameWhite}
      loading="lazy"
      alt="app-name-icon"
      onClick={() => onClick && onClick()}
    />
  );
};

export default AppName;
