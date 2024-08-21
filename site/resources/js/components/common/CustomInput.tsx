import React, { ReactElement } from 'react';
import { Input, InputProps } from 'antd';

// Components
import InputLabel from './InputLabel';
import ErrorText from './ErrorText';

const CustomInput = (
  props: InputProps & {
    label?: string;
    error?: string | null;
    rightBtnComponent?: ReactElement;
    onRightBtnPress?: () => void;
  },
) => {
  const {
    label,
    required,
    className,
    error,
    rightBtnComponent,
    onRightBtnPress,
    ...rest
  } = props;
  return (
    <div className="flex flex-col gap-1 w-full">
      <div className="flex items-center justify-between pr-2">
        {label && <InputLabel label={label} required={required} />}
        {rightBtnComponent && (
          <button onClick={() => onRightBtnPress && onRightBtnPress()}>
            {rightBtnComponent}
          </button>
        )}
      </div>
      <Input
        className={`text-GRAY_5E5E5E text-base md:text-sm ${className}`}
        {...rest}
      />
      {error && <ErrorText error={error} />}
    </div>
  );
};

export default CustomInput;
