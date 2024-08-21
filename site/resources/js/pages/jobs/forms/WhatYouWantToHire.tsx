import React, { useEffect, useState } from 'react';

import { Spin } from 'antd';
import { useFormik } from 'formik';
import { useTranslation } from 'react-i18next';
import { useNavigate } from 'react-router-dom';

// Components
import {
  CustomButton,
  HeaderWithBackButton,
  TextAreaInput,
  Title,
} from '@components/common';

// Hooks
import useUserProfile from '@customHooks/useUserProfile';
import { useShowMessage } from '@customHooks/useShowMessage';
import useCompanyProfileInput from '@customHooks/useCompanyProfileInput';

// Redux
import { useAppDispatch } from '@redux/hook';
import { setLoggedIn } from '@redux/reducers/auth';
import { useTranslateTextMutation } from '@redux/services/dataApi';

// Others
import {
  CompanyRequiredSkillsValues,
  companyRequiredSkillsSchema,
} from './schema';
interface Props {
  onBackPressed: () => void;
}

const initialValues: CompanyRequiredSkillsValues = {
  skills: '',
  skillsJa: '',
};

const WhatYouWantToHire = ({ onBackPressed }: Props) => {
  const { showSuccess } = useShowMessage();
  const navigate = useNavigate();
  const { t } = useTranslation(['jobs', 'messages']);
  const dispatch = useAppDispatch();
  const {
    handleCreateJob,
    handleUpdateJob,
    handleCreateProfile,
    handleSetCompanyJobData,
    handleSetCompanyProfileInputData,
    isLoading,
    isSuccess,
    data,
    profile,
    isJobSuccess,
    jobData,
    isEditMode,
    jobInput,
  } = useCompanyProfileInput();
  const { isLoggedIn, user } = useUserProfile();

  const [inputKey, setInputKey] = useState<string>(Date.now().toString());
  const [japaneseInputKey, setJapaneseInputKey] = useState<string>(
    Date.now().toString(),
  );
  const [isUsingTemplate, setIsUsingTemplate] = useState<boolean>(false);
  const [isUsingJapaneseTemplate, setIsUsingJapaneseTemplate] =
    useState<boolean>(false);

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
      setFieldValue('skills', translationData.data);
      setInputKey(Date.now().toString());
    }
  }, [isTranslationSuccess]);

  useEffect(() => {
    const { required_skills, required_skills_ja } = jobInput;
    setValues({
      skills: required_skills,
      skillsJa: required_skills_ja,
    });
  }, []);

  /**
   * When company profile and job create is success
   */
  useEffect(() => {
    if (isSuccess && data) {
      showSuccess(t('profile.success', { ns: 'messages' }));
      dispatch(setLoggedIn(true));
    }
  }, [isSuccess, data]);

  /**
   * When job create is success
   */
  useEffect(() => {
    if (isJobSuccess && jobData) {
      showSuccess(
        t(!isEditMode ? 'job.success' : 'job.updateSuccess', {
          ns: 'messages',
        }),
      );
      navigate('/jobs');
    }
  }, [isJobSuccess, jobData]);

  useEffect(() => {
    if (profile?.isCompleted) {
      /**
       * When user is logged in, create job only
       */
      if (isLoggedIn && user?.isProfileComplete) {
        if (isEditMode) {
          handleUpdateJob();
          return;
        }
        handleCreateJob();
        return;
      }
      handleSetCompanyProfileInputData({
        isCompleted: false,
      });
      /**
       * When user is not logged in, create company profile as well as first job
       */
      handleCreateProfile({ skip: false });
    }
  }, [profile]);

  /**
   * Save job and company
   */
  const handleSaveRequiredSkills = () => {
    const { skills, skillsJa } = values;
    handleSetCompanyJobData({
      required_skills: skills,
      required_skills_ja: skillsJa || '',
    });
    handleSetCompanyProfileInputData({
      isCompleted: true,
    });
  };

  const {
    handleBlur,
    handleChange,
    handleSubmit,
    setValues,
    setFieldValue,
    values,
    touched,
    errors,
  } = useFormik({
    initialValues: initialValues,
    validationSchema: companyRequiredSkillsSchema,
    onSubmit: handleSaveRequiredSkills,
  });

  return (
    <>
      {isLoading ? (
        <Spin />
      ) : (
        <div className="flex flex-col gap-3 h-full overflow-scroll">
          <HeaderWithBackButton
            onBackPressed={onBackPressed}
            title={t('whatYouWantToHire')}
          />
          <TextAreaInput
            key={japaneseInputKey + 'ja'}
            label={t('aboutJob') + ' (日本語)'}
            placeholder={t('aboutJob') + ' (日本語)'}
            onChange={handleChange('skillsJa')}
            onBlur={handleBlur('skillsJa')}
            value={values.skillsJa || ''}
            error={errors.skillsJa && touched.skillsJa ? errors.skillsJa : null}
            required
            autoCapitalize="sentences"
            rows={6}
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
                setFieldValue('skillsJa', '');
              } else {
                setFieldValue('skillsJa', t('aboutJobPlaceholderJa'));
              }
            }}
          />
          <CustomButton
            size="small"
            title={t('translate', { ns: 'common' })}
            loading={isTranslationLoading}
            disabled={
              isTranslationLoading ||
              !values.skillsJa ||
              values.skillsJa.trim().length === 0
            }
            onClick={() =>
              values.skillsJa &&
              translateText({ text: values.skillsJa, from: 'ja', to: 'en' })
            }
            className="bg-GREEN_4EBE59 w-fit self-end rounded-full"
          />
          <TextAreaInput
            key={inputKey}
            label={t('aboutJob') + ` (${t('english', { ns: 'common' })})`}
            placeholder={t('aboutJob') + ` (${t('english', { ns: 'common' })})`}
            onChange={handleChange('skills')}
            onBlur={handleBlur('skills')}
            value={values.skills}
            error={errors.skills && touched.skills ? errors.skills : null}
            autoCapitalize="sentences"
            required
            rows={6}
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
                setFieldValue('skills', '');
              } else {
                setFieldValue('skills', t('aboutJobPlaceholder'));
              }
            }}
          />
          <CustomButton
            title={
              isEditMode
                ? t('update', { ns: 'common' })
                : isLoggedIn
                  ? t('save', { ns: 'common' })
                  : t('continue', { ns: 'common' })
            }
            className="my-4"
            onClick={() => handleSubmit()}
          />
        </div>
      )}
    </>
  );
};

export default WhatYouWantToHire;
