import React from 'react';
import { useTranslation } from 'react-i18next';
import { TCountryCode, countries } from 'countries-list';

// Components
import DropdownWithLabel from './DropdownWithLabel';

// Interface
interface Props {
  label?: string;
  handleSetCountry: (code: string) => void;
  error?: string | null;
  placeholder?: string;
  disabled?: boolean;
  required?: boolean;
}

const CountrySelect = (props: Props) => {
  const { t } = useTranslation(['common']);
  const { handleSetCountry, label, error, placeholder, disabled, required } =
    props;

  return (
    <div>
      <DropdownWithLabel
        label={label}
        searchable
        disabled={disabled}
        placeholder={t('searchHere')}
        items={Object.keys(countries).map(countryKey => {
          return {
            label: countries[countryKey as TCountryCode].name,
            label_ja: countries[countryKey as TCountryCode].name,
            value: countries[countryKey as TCountryCode].name,
          };
        })}
        value={placeholder}
        onSelectItem={async item => {
          handleSetCountry(item.value);
        }}
        error={error}
        required={required}
      />
    </div>
  );
};

export default CountrySelect;
