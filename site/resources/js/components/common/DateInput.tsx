import React from 'react';

import dayjs from 'dayjs';
import { DatePicker } from 'antd';
import { useTranslation } from 'react-i18next';

// Components
import Title from './Title';
import InputLabel from './InputLabel';

interface DateInputProps {
  error?: string | null;
  placeholder?: string;
  minimumDate?: Date | number | string;
  maximumDate?: Date | number | string;
  date: string | Date | undefined | null;
  setDate: (date: string | null) => void;
  label?: string;
  disabled?: boolean;
  required?: boolean;
  hasCloseButton?: boolean;
}

const DateInput = (props: DateInputProps) => {
  const {
    date,
    setDate,
    error,
    placeholder,
    minimumDate,
    maximumDate,
    label,
    required,
    disabled = false,
    hasCloseButton = false,
  } = props;

  const { t } = useTranslation('messages');

  return (
    <div className="flex flex-col gap-1">
      {label && <InputLabel label={label} required={required} />}
      <DatePicker
        disabled={disabled}
        placeholder={placeholder}
        allowClear={hasCloseButton}
        value={date ? dayjs(date) : undefined}
        onChange={(date, ds) => {
          setDate(date ? (ds as string) : null);
        }}
        minDate={minimumDate ? dayjs(minimumDate) : undefined}
        maxDate={maximumDate ? dayjs(maximumDate) : undefined}
      />
      {error && (
        <Title type="caption1" className={'text-RED_FF4D4D mt-1'}>
          {t(error)}
        </Title>
      )}
    </div>
  );
};

export default DateInput;
