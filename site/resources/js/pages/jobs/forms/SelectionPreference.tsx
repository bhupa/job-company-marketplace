import React, { useEffect, useState } from 'react';

import { useFormik } from 'formik';
import { useTranslation } from 'react-i18next';

// Components

// Hooks
import useCompanyProfileInput from '@customHooks/useCompanyProfileInput';

// Redux
import { useGetOccupationListQuery } from '@redux/services/dataApi';

// Others
import {
  CompanySelectionPreferenceValues,
  companySelectionPreferenceSchema,
} from './schema';
import { EXPERIENCE_DATA, JAPANESE_LEVEL } from '@constants/dropdownData';
import { Spin } from 'antd';
import {
  CustomButton,
  DropdownWithLabel,
  HeaderWithBackButton,
} from '@components/common';

interface Props {
  onNextPressed: () => void;
  onBackPressed: () => void;
}

const initialValues: CompanySelectionPreferenceValues = {
  occupation: '',
  experience: '',
  japaneseLevel: '',
};

const SelectionPreference = ({ onNextPressed, onBackPressed }: Props) => {
  const { t } = useTranslation(['jobseeker', 'jobs']);
  const { isLoading, data } = useGetOccupationListQuery();

  const { handleSetCompanyJobData, jobInput } = useCompanyProfileInput();

  const [isDataLoading, setDataLoading] = useState<boolean>(true);

  useEffect(() => {
    const { occupation, experience, japanese_level } = jobInput;
    setValues({
      occupation,
      experience,
      japaneseLevel: japanese_level,
    });
    setDataLoading(false);
  }, []);

  const handleAddWorkInformation = () => {
    const { occupation, experience, japaneseLevel } = values;
    handleSetCompanyJobData({
      occupation,
      experience: experience || '',
      japanese_level: japaneseLevel,
    });
    onNextPressed();
  };

  const { handleSubmit, setFieldValue, setValues, values, touched, errors } =
    useFormik({
      initialValues: initialValues,
      validationSchema: companySelectionPreferenceSchema,
      onSubmit: handleAddWorkInformation,
    });

  return (
    <>
      {isLoading || isDataLoading ? (
        <Spin />
      ) : (
        <div className="flex flex-col gap-3 h-full overflow-scroll">
          <HeaderWithBackButton
            title={t('selectionPreference', { ns: 'jobs' })}
            onBackPressed={onBackPressed}
          />
          <DropdownWithLabel
            label={t('japaneseLevel')}
            placeholder={t('select', { ns: 'common' })}
            value={values.japaneseLevel}
            items={JAPANESE_LEVEL}
            onSelectItem={async item => {
              setFieldValue('japaneseLevel', item.value);
            }}
            error={
              errors.japaneseLevel && touched.japaneseLevel
                ? errors.japaneseLevel
                : null
            }
            required
          />
          <DropdownWithLabel
            searchable
            label={t('occupation')}
            placeholder={t('select', { ns: 'common' })}
            value={values.occupation}
            loading={isLoading}
            items={data?.data ?? []}
            onSelectItem={async item => {
              setFieldValue('occupation', item.value);
            }}
            error={
              errors.occupation && touched.occupation ? errors.occupation : null
            }
            required
          />
          <DropdownWithLabel
            label={t('experience')}
            placeholder={t('select', { ns: 'common' })}
            value={values.experience || ''}
            items={EXPERIENCE_DATA}
            onSelectItem={async item => {
              setFieldValue('experience', item.value);
            }}
            error={
              errors.experience && touched.experience ? errors.experience : null
            }
          />
          <CustomButton
            title={t('next', { ns: 'common' })}
            className="my-4"
            onClick={() => handleSubmit()}
          />
        </div>
      )}
    </>
  );
};

export default SelectionPreference;
