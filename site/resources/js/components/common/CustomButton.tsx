import React, { FC } from 'react';
import { Button, ButtonProps } from 'antd';

// Others
import { ButtonType } from 'antd/es/button';

const btnClass: { [key in ButtonType]: string } = {
  primary: 'btn-primary',
  dashed: 'text-BLACK_1E2022',
  default: 'text-BLACK_1E2022',
  link: 'text-primary',
  text: 'text-BLACK_1E2022',
};

const CustomButton: FC<ButtonProps> = props => {
  const { type = 'primary', title, className = '', ...rest } = props;
  const btnClassName = btnClass[type];
  return (
    <Button type={type} className={`${btnClassName} ${className}`} {...rest}>
      {title}
    </Button>
  );
};

export default CustomButton;
