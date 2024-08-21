import React, { useEffect, useState } from 'react';

import { TreeSelect } from 'antd';
import { useTranslation } from 'react-i18next';

// Components
import Title from './Title';
import ErrorText from './ErrorText';
import InputLabel from './InputLabel';

// Hooks
import useDebounce from '@customHooks/useDebounce';

// Utils
import { ISectionDropdownItem } from '@constants/dropdownData';
import { getSectionSelectedItem, searchSectionItems } from '@utils/helpers';

export interface DropdownWithLabelProps {
  label?: string;
  error?: string | null;
  containerClassName?: string;
  items: ISectionDropdownItem[];
  onSelectItem: (item: ISectionDropdownItem) => void;
  placeholder: string;
  value: string | undefined;
  zIndex?: number;
  loading?: boolean;
  disabled?: boolean;
  required?: boolean;
  searchable?: boolean;
}

type Props = DropdownWithLabelProps;

const SectionDropdownWithLabel = (props: Props) => {
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

  const [listItems, setListItems] = useState<ISectionDropdownItem[]>(items);
  const [selectedValue, setSelectedValue] =
    useState<ISectionDropdownItem | null>(null);
  const [searchText, setSearchText] = useState<string>('');

  const debouncedSearchValue = useDebounce(searchText, 1000);

  /**
   * Filter on search
   */
  useEffect(() => {
    const filteredItems = searchSectionItems(
      debouncedSearchValue,
      items,
      !isEnglish,
    );
    setListItems(filteredItems);
  }, [debouncedSearchValue]);

  /**
   * Set selected value at start
   */
  useEffect(() => {
    const selectedItem = getSectionSelectedItem(value, items);
    selectedItem?.label && setSelectedValue(selectedItem);
  }, []);

  const handleSelectItem = (value: string) => {
    let selectedItem = listItems.find(listItem => listItem.value === value);
    if (!selectedItem) {
      // If the value is not found in top-level items, search in sections
      selectedItem = listItems
        .flatMap(listItem => listItem.sections)
        .find(sectionItem => sectionItem?.value === value);
    }
    onSelectItem &&
      onSelectItem(
        selectedItem ?? { label: '', value: '', label_ja: '', sections: [] },
      );
    setSelectedValue(selectedItem ?? null);
    setSearchText('');
  };

  const selectedDropdownValue = isEnglish
    ? selectedValue?.label || ''
    : selectedValue?.label_ja || selectedValue?.label || '';

  return (
    <div className={`flex flex-col gap-1 ${containerClassName}`}>
      {label && <InputLabel label={label} required={required} />}
      <TreeSelect
        key={i18n.language}
        showSearch={searchable}
        value={selectedDropdownValue || undefined}
        placeholder={placeholder}
        className="w-full"
        loading={loading}
        disabled={disabled}
        allowClear
        onChange={value => handleSelectItem(value)}
        searchValue={searchText}
        onSearch={searchVal => setSearchText(searchVal)}
        fieldNames={{
          label: isEnglish ? 'label' : 'label_ja',
          value: 'value',
          children: 'sections',
        }}
        // filter key value = label
        treeNodeFilterProp={isEnglish ? 'label' : 'label_ja'}
        treeData={listItems}
        treeDefaultExpandAll
        notFoundContent={
          <Title type="body2" className="flex w-full px-4">
            {t('emptyData', { ns: 'messages' })}
          </Title>
        }
      />
      {error && <ErrorText error={error} />}
    </div>
  );
};

export default SectionDropdownWithLabel;
