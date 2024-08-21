import React, { useEffect, useState } from 'react';

import { Select } from 'antd';
import { useTranslation } from 'react-i18next';

// Components
import Title from './Title';
import ErrorText from './ErrorText';
import InputLabel from './InputLabel';

// Utils
import { getSectionSelectedItem } from '@utils/helpers';
import { ISectionDropdownItem } from '@constants/dropdownData';
import { DropdownWithLabelProps } from './SectionDropdownWithLabel';

const DropdownWithLabel = (props: DropdownWithLabelProps) => {
  const {
    label,
    error,
    containerClassName = '',
    placeholder,
    items,
    onSelectItem,
    value,
    loading,
    disabled,
    required,
    searchable = false,
  } = props;

  const { i18n, t } = useTranslation();
  const isEnglish = i18n.language === 'en';

  const [selectedValue, setSelectedValue] =
    useState<ISectionDropdownItem | null>(null);

  /**
   * Set selected value at start
   */
  useEffect(() => {
    const selectedItem = getSectionSelectedItem(value, items);
    selectedItem?.label && setSelectedValue(selectedItem);
  }, []);

  const handleSelectItem = (item: ISectionDropdownItem | null) => {
    onSelectItem &&
      onSelectItem(
        item ?? { label: '', value: '', label_ja: '', sections: [] },
      );
    setSelectedValue(item);
  };

  const selectedDropdownValue = isEnglish
    ? selectedValue?.label || ''
    : selectedValue?.label_ja || selectedValue?.label || '';

  return (
    <div className={`flex flex-col gap-1 ${containerClassName}`}>
      {label && <InputLabel label={label} required={required} />}
      <Select
        key={i18n.language}
        showSearch={searchable}
        placeholder={placeholder}
        value={selectedDropdownValue || undefined}
        className="w-full"
        loading={loading}
        disabled={disabled}
        allowClear
        onChange={(_, item) => handleSelectItem(item as ISectionDropdownItem)}
        fieldNames={{
          label: isEnglish ? 'label' : 'label_ja',
          value: isEnglish ? 'label' : 'label_ja',
        }}
        options={items}
        notFoundContent={
          <Title type="body2">{t('emptyData', { ns: 'messages' })}</Title>
        }
      />
      {error && <ErrorText error={error} />}
    </div>
  );
};

export default DropdownWithLabel;
