import React, { ReactElement } from 'react';
import { Input } from 'antd';
import { TextAreaProps } from 'antd/es/input';

// Components
import ErrorText from './ErrorText';
import InputLabel from './InputLabel';

const TextAreaInput = (
  props: TextAreaProps & {
    label?: string;
    error?: string | null;
    rightBtnComponent?: ReactElement;
    onRightBtnPress?: () => void;
    resizable?: boolean;
  },
) => {
  const {
    label,
    required,
    className,
    error,
    rightBtnComponent,
    onRightBtnPress,
    resizable,
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
      <Input.TextArea
        className={`ant-textarea text-GRAY_5E5E5E text-base md:text-sm ${resizable ? '' : 'ant-textarea-resize-disabled'} ${className}`}
        {...rest}
      />
      {error && <ErrorText error={error} />}
    </div>
  );
};

export default TextAreaInput;
