import React from 'react';

// Components
import Title from './Title';

interface InputLabelProps {
  label: string;
  required?: boolean;
}

const InputLabel = (props: InputLabelProps) => {
  const { label, required } = props;
  return (
    <>
      {label && (
        <span>
          <Title type="caption1" className="text-GRAY_545454">
            {label}
          </Title>
          {required && (
            <Title type="caption1" className="text-RED_FF4D4D">
              *
            </Title>
          )}
        </span>
      )}
    </>
  );
};

export default InputLabel;
