import React from 'react';
import { Input, InputProps } from 'antd';

// Components
import InputLabel from './InputLabel';
import ErrorText from './ErrorText';

const PasswordInput = (
  props: InputProps & {
    label?: string;
    error?: string | null;
  },
) => {
  const { label, required, className, error, ...rest } = props;
  return (
    <div className="flex flex-col gap-1 w-full">
      {label && <InputLabel label={label} required={required} />}
      <Input.Password
        className={`text-base md:text-sm ${className}`}
        {...rest}
      />
      {error && <ErrorText error={error} />}
    </div>
  );
};

export default PasswordInput;
