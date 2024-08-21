import React, { ButtonHTMLAttributes } from 'react';

// Components
import Title from './Title';

// Others
import { CheckMark } from '@assets/icons';

interface RadioButtonProps extends ButtonHTMLAttributes<HTMLButtonElement> {
  title: string;
  isSelected?: boolean;
}

const RadioButton = (props: RadioButtonProps) => {
  const { title, isSelected, ...rest } = props;

  return (
    <button
      className={`flex justify-between rounded-md p-5 border ${isSelected ? 'bg-BLUE_004D80 border-BLUE_004D80' : 'bg-white border-WHITE_E8E6EA'}`}
      {...rest}>
      <Title
        type="body1"
        className={`${isSelected ? 'text-white' : 'text-black'}`}>
        {title}
      </Title>
      {isSelected && (
        <CheckMark height={20} width={20} className={'text-white'} />
      )}
    </button>
  );
};

export default RadioButton;
