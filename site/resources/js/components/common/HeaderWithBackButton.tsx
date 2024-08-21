import React, { ReactElement } from 'react';

// Components
import Title from './Title';

// Others
import { ArrowLeft } from '@assets/icons';

interface HeaderWithBackButtonProps {
  hasBackButton?: boolean;
  onBackPressed?: () => void;
  title?: string;
  hasBorder?: boolean;
  rightBtn?: ReactElement;
  onRightButtonPress?: () => void;
}

const HeaderWithBackButton = (props: HeaderWithBackButtonProps) => {
  const {
    hasBackButton = true,
    title,
    hasBorder,
    rightBtn,
    onBackPressed,
    onRightButtonPress,
  } = props;

  return (
    <div
      className={`flex relative justify-center items-center px-3 ${hasBorder ? 'border-b border-b-WHITE_EFF0F2' : ''}`}>
      {hasBackButton && (
        <button
          onClick={() => {
            onBackPressed && onBackPressed();
          }}
          className="absolute px-2 py-2 left-0">
          <ArrowLeft height={16} />
        </button>
      )}
      <Title
        type="heading2"
        bold
        className="text-center"
        textTypeClassName="text-sm sm:text-xl">
        {title}
      </Title>
      {rightBtn && (
        <button
          onClick={() => {
            onRightButtonPress && onRightButtonPress();
          }}
          className="absolute right-4">
          {rightBtn}
        </button>
      )}
    </div>
  );
};

export default HeaderWithBackButton;
