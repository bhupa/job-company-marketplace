import React, { useEffect, useState } from 'react';

import { useFormik } from 'formik';
import { Checkbox, Spin } from 'antd';
import { useTranslation } from 'react-i18next';
import { useNavigate } from 'react-router-dom';

// Components
import {
  CountrySelect,
  CustomButton,
  CustomInput,
  DateInput,
  DropdownWithLabel,
  HeaderWithBackButton,
  TextAreaInput,
  Title,
} from '@components/common';
import { DashboardWrapper } from '@templates';
import { ProfilePicker, SelfIntroductionVideo } from '@components/profile';

// Hooks
import useJobSeeker from '@customHooks/useJobSeeker';
import { useShowMessage } from '@customHooks/useShowMessage';

// Redux
import {
  useGetOccupationListQuery,
  useGetTagListQuery,
  useTranslateTextMutation,
} from '@redux/services/dataApi';

// Others
import {
  JOB_TYPES,
  EXPERIENCE_DATA,
  GENDER_DATA,
  JAPANESE_LEVEL,
} from '@constants/dropdownData';
import { PersonalSettingsValues, personalSettingsSchema } from './schema';

const initialValues: PersonalSettingsValues = {
  occupation: '',
  experience: '',
  japaneseLevel: '',
  about: '',
  aboutJa: '',
  country: '',
  currentCountry: '',
  gender: '',
  name: '',
  dob: new Date().toString(),
  jobType: '',
  isLivingInJapan: false,
  startDate: null,
  tags: [],
  introVideo: null,
};

const JobSeekerPersonalSettings = () => {
  const { showError } = useShowMessage();
  const navigate = useNavigate();
  const { t, i18n } = useTranslation(['jobseeker', 'profile', 'messages']);
  const { isUpdating, isFetching, handleUpdateJobSeeker, profileImg, ...rest } =
    useJobSeeker();
  const { isLoading: isOccupationLoading, data: occupationData } =
    useGetOccupationListQuery();
  const { isLoading: isTagsLoading, data: tagsData } = useGetTagListQuery({
    type: 'jobseeker',
  });

  const isJapanese = i18n.language === 'ja';

  const [isLoading, setLoading] = useState<boolean>(true);
  const [inputKey, setInputKey] = useState<string>(Date.now().toString());
  const [isUsingTemplate, setIsUsingTemplate] = useState<boolean>(false);
  const [japaneseInputKey, setJapaneseInputKey] = useState<string>(
    Date.now().toString(),
  );
  const [isUsingJapaneseTemplate, setIsUsingJapaneseTemplate] =
    useState<boolean>(false);
  const [isVideoDeleted, setIsVideoDeleted] = useState<boolean>(false);

  const [
    translateText,
    {
      isLoading: isTranslationLoading,
      data: translationData,
      isSuccess: isTranslationSuccess,
    },
  ] = useTranslateTextMutation();

  // If translation is success, set value in about job ( Japanese )
  useEffect(() => {
    if (isTranslationSuccess && translationData) {
      setFieldValue('aboutJa', translationData.data);
      setJapaneseInputKey(Date.now().toString());
    }
  }, [isTranslationSuccess]);

  // Set initial data
  useEffect(() => {
    const {
      firstName,
      lastName,
      birthday,
      gender,
      country,
      currentCountry,
      occupation,
      experience,
      japaneseLevel,
      jobType,
      about,
      aboutJa,
      isLivingInJapan,
      startWhen,
      tags,
    } = rest;
    setValues({
      name: `${firstName} ${lastName}`,
      dob: birthday,
      gender,
      country,
      currentCountry,
      occupation,
      experience,
      japaneseLevel,
      jobType,
      about,
      aboutJa,
      isLivingInJapan,
      startDate: startWhen ? startWhen : null,
      tags: tags?.map(tag => tag.value) ?? [],
    });
    setTimeout(() => setLoading(false), 400);
  }, [isFetching]);

  const handleUpdateProfile = () => {
    const {
      country,
      currentCountry,
      occupation,
      experience,
      japaneseLevel,
      about,
      aboutJa,
      jobType,
      dob,
      gender,
      name,
      isLivingInJapan,
      startDate,
      tags,
      introVideo,
    } = values;
    const names = name.split(' ');
    if (!names?.[1]) {
      return showError(t('jobSeeker.name.lastName', { ns: 'messages' }));
    }
    handleUpdateJobSeeker({
      first_name: names[0],
      last_name: names[1],
      birthday: dob?.toString() ?? null,
      gender,
      country,
      current_country: currentCountry,
      occupation,
      experience,
      japanese_level: japaneseLevel,
      job_type: jobType,
      about,
      about_ja: aboutJa,
      living_japan: isLivingInJapan ? 1 : 0,
      start_when: startDate?.toString() ?? '',
      tags: tags as string[],
      intro_video: (introVideo ?? '') as File | null,
      isIntroVideoDeleted: isVideoDeleted,
    });
  };

  const {
    handleBlur,
    handleChange,
    handleSubmit,
    setFieldValue,
    setTouched,
    values,
    touched,
    errors,
    setValues,
  } = useFormik({
    initialValues: initialValues,
    validationSchema: personalSettingsSchema,
    onSubmit: handleUpdateProfile,
  });

  return (
    <DashboardWrapper>
      <div className="flex flex-col gap-4 mb-4 w-full max-w-sm lg:max-w-xl mx-auto">
        <HeaderWithBackButton
          title={t('personalSettings', { ns: 'profile' })}
          onBackPressed={() => navigate(-1)}
        />
        {isFetching || isLoading || isOccupationLoading || isTagsLoading ? (
          <Spin />
        ) : (
          <div className="flex flex-col gap-4 h-full overflow-scroll">
            <ProfilePicker isEdit imageUrl={profileImg} />
            <CustomInput
              label={t('fullName')}
              placeholder={t('fullName')}
              onChange={handleChange('name')}
              onBlur={handleBlur('name')}
              value={values.name}
              error={errors.name && touched.name ? errors.name : null}
              autoCapitalize={'words'}
              readOnly={true}
              disabled={true}
              required
            />
            <DateInput
              label={t('dateOfBirth')}
              placeholder={t('dateOfBirth')}
              date={values.dob}
              disabled={rest?.birthday ? true : false}
              setDate={dob => {
                setTouched({ dob: true });
                setFieldValue('dob', dob);
              }}
              maximumDate={new Date().setFullYear(
                new Date().getFullYear() - 18,
              )}
              error={errors.dob && touched.dob ? (errors.dob as string) : null}
              required
              hasCloseButton
            />
            <DropdownWithLabel
              label={t('gender')}
              placeholder={t('select', { ns: 'common' })}
              value={values.gender || ''}
              items={GENDER_DATA}
              onSelectItem={async item => {
                setFieldValue('gender', item.value);
              }}
              error={errors.gender && touched.gender ? errors.gender : null}
            />
            <CountrySelect
              label={t('country')}
              placeholder={values.country || ''}
              handleSetCountry={countryValue =>
                setFieldValue('country', countryValue)
              }
              error={errors.country && touched.country ? errors.country : null}
              disabled={rest.country ? true : false}
              required
            />
            <DropdownWithLabel
              label={t('japaneseLevel')}
              placeholder={t('select', { ns: 'common' })}
              value={values.japaneseLevel || ''}
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
            <CountrySelect
              label={t('currentCountry')}
              placeholder={values.currentCountry || ''}
              handleSetCountry={countryValue =>
                setFieldValue('currentCountry', countryValue)
              }
              error={
                errors.currentCountry && touched.currentCountry
                  ? errors.currentCountry
                  : null
              }
            />
            <DropdownWithLabel
              searchable
              label={t('occupation')}
              placeholder={t('select', { ns: 'common' })}
              value={values.occupation || ''}
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
              value={values.experience || ''}
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
              value={values.jobType || ''}
              items={JOB_TYPES}
              onSelectItem={async item => {
                setFieldValue('jobType', item.value);
              }}
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
            <TextAreaInput
              key={inputKey}
              rows={6}
              label={t('aboutMe') + ` (${t('english', { ns: 'common' })})`}
              placeholder={
                t('aboutMe') + ` (${t('english', { ns: 'common' })})`
              }
              onChange={handleChange('about')}
              onBlur={handleBlur('about')}
              value={values.about || ''}
              error={errors.about && touched.about ? errors.about : null}
              required
              rightBtnComponent={
                <Title type="caption1" className={'text-BLUE_004D80'}>
                  {isUsingTemplate
                    ? t('reset', { ns: 'common' })
                    : t('useTemplate', { ns: 'common' })}
                </Title>
              }
              onRightBtnPress={() => {
                setInputKey(Date.now().toString());
                setIsUsingTemplate(!isUsingTemplate);
                if (isUsingTemplate) {
                  setFieldValue('about', '');
                } else {
                  setFieldValue('about', t('aboutMePlaceholder'));
                }
              }}
            />
            <CustomButton
              title={t('translate', { ns: 'common' })}
              loading={isTranslationLoading}
              disabled={
                isTranslationLoading ||
                !values.about ||
                values.about.trim().length === 0
              }
              onClick={() =>
                values.about &&
                translateText({ text: values.about, from: 'en', to: 'ja' })
              }
              size="small"
              className="bg-GREEN_4EBE59 w-fit self-end rounded-full"
            />
            <TextAreaInput
              key={japaneseInputKey + 'ja'}
              rows={6}
              label={t('aboutMe') + ' (日本語)'}
              placeholder={t('aboutMe') + ' (日本語)'}
              onChange={handleChange('aboutJa')}
              onBlur={handleBlur('aboutJa')}
              value={values.aboutJa || ''}
              error={errors.aboutJa && touched.aboutJa ? errors.aboutJa : null}
              rightBtnComponent={
                <Title type="caption1" className={'text-BLUE_004D80'}>
                  {isUsingJapaneseTemplate
                    ? t('reset', { ns: 'common' })
                    : t('useTemplate', { ns: 'common' })}
                </Title>
              }
              onRightBtnPress={() => {
                setJapaneseInputKey(Date.now().toString());
                setIsUsingJapaneseTemplate(!isUsingJapaneseTemplate);
                if (isUsingJapaneseTemplate) {
                  setFieldValue('aboutJa', '');
                } else {
                  setFieldValue('aboutJa', t('aboutMePlaceholderJa'));
                }
              }}
            />
            <SelfIntroductionVideo
              label={t('introductionVideo', { ns: 'profile' })}
              videoUrl={rest.user?.introVideo ?? null}
              setVideoFile={(file, _, isDeleted) => {
                setFieldValue('introVideo', file);
                setIsVideoDeleted(isDeleted);
              }}
            />
            <Checkbox
              checked={values.isLivingInJapan as boolean}
              onChange={e => {
                const checked = e.target.checked;
                setFieldValue('isLivingInJapan', checked);
              }}>
              <Title type="caption1" className={'text-GRAY_77838F'}>
                {t('livingInJapan')}
              </Title>
            </Checkbox>
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
              title={t('save', { ns: 'common' })}
              onClick={() => handleSubmit()}
              className="mt-4"
              loading={isUpdating}
            />
          </div>
        )}
      </div>
    </DashboardWrapper>
  );
};

export default JobSeekerPersonalSettings;
