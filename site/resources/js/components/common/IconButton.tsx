import React, { ButtonHTMLAttributes } from 'react';

interface ButtonProps extends ButtonHTMLAttributes<HTMLButtonElement> {
  children: React.ReactNode;
}

const IconButton: React.FC<ButtonProps> = props => {
  return (
    <button
      className={
        'flex border-2 border-WHITE_E8E6EA rounded-2xl bg-white h-16 w-16 justify-center items-center hover:shadow-md'
      }
      {...props}>
      {props.children}
    </button>
  );
};

export default IconButton;
