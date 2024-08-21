import React, { useEffect, useState } from 'react';

import { useFormik } from 'formik';
import { Checkbox, Spin } from 'antd';
import { useTranslation } from 'react-i18next';
import { useNavigate } from 'react-router-dom';

// Components
import {
  CustomButton,
  DateInput,
  DropdownWithLabel,
  HeaderWithBackButton,
  Title,
} from '@components/common';
import { AuthProfileWrapper } from '@templates';

// Hooks
import useJobSeekerProfileInput from '@customHooks/useJobSeekerProfileInput';

// Redux
import {
  useGetOccupationListQuery,
  useGetTagListQuery,
} from '@redux/services/dataApi';

// Others
import {
  JOB_TYPES,
  EXPERIENCE_DATA,
  JAPANESE_LEVEL,
} from '@constants/dropdownData';
import { AboutYouValues, aboutYouSchema } from './schema';

const initialValues: AboutYouValues = {
  occupation: '',
  experience: '',
  japaneseLevel: '',
  jobType: '',
  startDate: null,
  tags: [],
};

const AboutYou = () => {
  const navigate = useNavigate();
  const { t, i18n } = useTranslation(['jobseeker']);
  const isJapanese = i18n.language === 'ja';

  const { isLoading: isOccupationLoading, data: occupationData } =
    useGetOccupationListQuery();

  const { isLoading: isTagsLoading, data: tagsData } = useGetTagListQuery({
    type: 'jobseeker',
  });

  const { handleSetProfileData, profileInput } = useJobSeekerProfileInput();

  const [isDataLoading, setDataLoading] = useState<boolean>(true);

  useEffect(() => {
    const { occupation, experience, japaneseLevel, jobType, startDate, tags } =
      profileInput;
    setValues({
      occupation,
      experience,
      japaneseLevel,
      jobType,
      startDate: startDate ? new Date(startDate) : null,
      tags: tags || [],
    });
    setDataLoading(false);
  }, []);

  const handleAddAboutInfo = () => {
    handleSetProfileData({
      ...values,
      tags: (values.tags || []) as string[],
      startDate: values.startDate?.toString() ?? '',
    });
    navigate('/user-bio');
  };

  const {
    handleSubmit,
    setFieldValue,
    setValues,
    setTouched,
    values,
    touched,
    errors,
  } = useFormik({
    initialValues: initialValues,
    validationSchema: aboutYouSchema,
    onSubmit: handleAddAboutInfo,
  });

  return (
    <AuthProfileWrapper>
      <div className="flex flex-col gap-4 w-full max-w-md">
        <HeaderWithBackButton title={t('aboutYou')} hasBackButton={false} />
        {isDataLoading || isOccupationLoading || isTagsLoading ? (
          <Spin />
        ) : (
          <div className="flex flex-col gap-4 w-full">
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
              loading={isOccupationLoading}
              items={occupationData?.data ?? []}
              onSelectItem={async item => {
                setFieldValue('occupation', item.value);
              }}
              error={
                errors.occupation && touched.occupation
                  ? errors.occupation
                  : null
              }
              required
            />
            <DropdownWithLabel
              label={t('experience')}
              placeholder={t('select', { ns: 'common' })}
              value={values.experience}
              items={EXPERIENCE_DATA}
              onSelectItem={async item => {
                setFieldValue('experience', item.value);
              }}
              error={
                errors.experience && touched.experience
                  ? errors.experience
                  : null
              }
              required
            />
            <DropdownWithLabel
              label={t('jobType')}
              placeholder={t('select', { ns: 'common' })}
              value={values.jobType}
              items={JOB_TYPES}
              onSelectItem={async item => {
                setFieldValue('jobType', item.value);
              }}
              zIndex={10007}
              error={errors.jobType && touched.jobType ? errors.jobType : null}
              required
            />
            <DateInput
              label={t('workStartDate')}
              placeholder={'YYYY-MM-DD'}
              date={values.startDate}
              setDate={startDate => {
                setTouched({ startDate: true });
                setFieldValue('startDate', startDate);
              }}
              minimumDate={new Date()}
              error={
                errors.startDate && touched.startDate
                  ? (errors.startDate as string)
                  : null
              }
            />
            {/* {tagsData?.data?.map(item => {
                const isChecked = values.tags?.includes(item.value) ?? false;
                return (
                  <CustomCheckbox
                    key={item.value}
                    isChecked={isChecked}
                    label={isJapanese ? item.label_ja : item.label}
                    setChecked={(checked: boolean) => {
                      if (!checked) {
                        const remainingTags = values.tags?.filter(
                          tag => tag !== item.value,
                        );
                        return setFieldValue('tags', remainingTags);
                      }
                      setFieldValue('tags', [...values.tags, item.value]);
                    }}
                    needsMargin
                  />
                );
              })} */}
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
              className="my-6"
              onClick={() => handleSubmit()}
            />
          </div>
        )}
      </div>
    </AuthProfileWrapper>
  );
};

export default AboutYou;
