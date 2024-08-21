import React, { ReactElement } from 'react';

// Components
import Title from './Title';

interface PopperListItemProps
  extends React.ButtonHTMLAttributes<HTMLButtonElement> {
  title?: string;
  icon?: ReactElement;
  hideBorder?: boolean;
  className?: string;
  titleColor?: string;
}

const PopperListItem = ({
  title,
  icon,
  hideBorder,
  className = '',
  titleColor,
  ...rest
}: PopperListItemProps) => {
  return (
    <button
      className={`flex items-center gap-3 px-5 py-3 min-h-10 ${hideBorder ? '' : 'border-b border-b-WHITE_E0E2E4'} ${icon ? '' : 'justify-center'} ${className} hover:bg-WHITE_F6F6F6`}
      {...rest}>
      {icon && icon}
      {title && (
        <Title type="body2" className={titleColor}>
          {title}
        </Title>
      )}
    </button>
  );
};

export default PopperListItem;
