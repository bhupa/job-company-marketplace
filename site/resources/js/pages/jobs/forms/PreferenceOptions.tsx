import React, { useEffect, useState } from 'react';

import { useFormik } from 'formik';
import { Checkbox, Spin } from 'antd';
import { useTranslation } from 'react-i18next';

// Components
import {
  CustomButton,
  DropdownWithLabel,
  HeaderWithBackButton,
  Title,
} from '@components/common';

// Hooks
import useCompanyProfileInput from '@customHooks/useCompanyProfileInput';

// Redux
import { useGetTagListQuery } from '@redux/services/dataApi';

// Others
import {
  CompanyPreferenceOptionsValues,
  companyPreferenceOptionsSchema,
} from './schema';
import { JOB_TYPES } from '@constants/dropdownData';

interface Props {
  onNextPressed: () => void;
  onBackPressed: () => void;
}

const initialValues: CompanyPreferenceOptionsValues = {
  tags: [],
  jobType: '',
};

const PreferenceOptions = ({ onNextPressed, onBackPressed }: Props) => {
  const { i18n, t } = useTranslation(['jobs']);
  const isJapanese = i18n.language === 'ja';

  const { isLoading: isTagsLoading, data: tagsData } = useGetTagListQuery({
    type: 'job',
  });
  const { handleSetCompanyJobData, jobInput } = useCompanyProfileInput();

  const [isDataLoading, setDataLoading] = useState<boolean>(true);

  useEffect(() => {
    const { job_type, tags } = jobInput;
    setValues({
      jobType: job_type,
      tags: tags ?? [],
    });
    setDataLoading(false);
  }, []);

  const handleAddWorkInformation = () => {
    const { tags, jobType } = values;
    handleSetCompanyJobData({
      tags: (tags || []) as string[],
      job_type: jobType,
    });
    onNextPressed();
  };

  const { handleSubmit, setFieldValue, setValues, values, touched, errors } =
    useFormik({
      initialValues: initialValues,
      validationSchema: companyPreferenceOptionsSchema,
      onSubmit: handleAddWorkInformation,
    });

  return (
    <>
      {isTagsLoading || isDataLoading ? (
        <Spin />
      ) : (
        <div className="flex flex-col gap-3 h-full overflow-scroll">
          <HeaderWithBackButton
            title={t('selectionPreference', { ns: 'jobs' })}
            onBackPressed={onBackPressed}
          />
          <DropdownWithLabel
            label={t('jobType', { ns: 'jobseeker' })}
            placeholder={t('select', { ns: 'common' })}
            value={values.jobType}
            items={JOB_TYPES}
            zIndex={10005}
            onSelectItem={async item => {
              setFieldValue('jobType', item.value);
            }}
            error={errors.jobType && touched.jobType ? errors.jobType : null}
            required
          />
          {tagsData?.data?.map(item => {
            const isChecked = values.tags?.includes(item.value) ?? false;
            return (
              <Checkbox
                key={item.value}
                checked={isChecked}
                onChange={e => {
                  const checked = e.target.checked;
                  if (!checked) {
                    const remainingTags = values.tags?.filter(
                      tag => tag !== item.value,
                    );
                    return setFieldValue('tags', remainingTags);
                  }
                  setFieldValue('tags', [...values.tags, item.value]);
                }}>
                <Title type="caption1" className={'text-GRAY_77838F'}>
                  {isJapanese ? item.label_ja : item.label}
                </Title>
              </Checkbox>
            );
          })}
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

export default PreferenceOptions;
