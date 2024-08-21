import React, { useEffect, useState } from 'react';

import { Spin } from 'antd';
import { useFormik } from 'formik';
import { useTranslation } from 'react-i18next';

// Components
import { AuthProfileWrapper } from '@templates';
import {
  CustomButton,
  HeaderWithBackButton,
  TextAreaInput,
  Title,
} from '@components/common';
import { SelfIntroductionVideo } from '@components/profile';

// Hooks
import { useShowMessage } from '@customHooks/useShowMessage';
import useJobSeekerProfileInput from '@customHooks/useJobSeekerProfileInput';

// Redux
import { useAppDispatch } from '@redux/hook';
import { setLoggedIn } from '@redux/reducers/auth';
import { useTranslateTextMutation } from '@redux/services/dataApi';
import { initialJobSeekerProfileInput } from '@redux/reducers/jobSeeker';

// Others
import { UserBioValues, userBioSchema } from './schema';

const initialValues: UserBioValues = {
  about: '',
  aboutJa: '',
  introVideo: null,
};

const UserBio = () => {
  const dispatch = useAppDispatch();
  const { showSuccess } = useShowMessage();
  const { t } = useTranslation(['jobseeker', 'messages']);

  const [isUsingTemplate, setIsUsingTemplate] = useState<boolean>(false);
  const [inputKey, setInputKey] = useState<string>(Date.now().toString());
  const [japaneseInputKey, setJapaneseInputKey] = useState<string>(
    Date.now().toString(),
  );
  const [isUsingJapaneseTemplate, setIsUsingJapaneseTemplate] =
    useState<boolean>(false);

  const {
    handleSetProfileData,
    handleCreateProfile,
    isLoading,
    isSuccess,
    data,
    profileInput,
  } = useJobSeekerProfileInput();

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

  useEffect(() => {
    if (isSuccess && data) {
      showSuccess(t('profile.success', { ns: 'messages' }));
      handleSetProfileData(initialJobSeekerProfileInput);
      dispatch(setLoggedIn(true));
    }
  }, [isSuccess, data]);

  useEffect(() => {
    if (profileInput.isCompleted) {
      handleCreateProfile();
    }
  }, [profileInput]);

  const handleAddAboutInfo = () => {
    const { about, aboutJa, introVideo } = values;
    handleSetProfileData({
      about,
      aboutJa: aboutJa || '',
      introVideo: (introVideo ?? '') as File | null,
      isCompleted: true,
    });
  };

  const {
    handleBlur,
    handleChange,
    handleSubmit,
    setFieldValue,
    values,
    touched,
    errors,
  } = useFormik({
    initialValues: initialValues,
    validationSchema: userBioSchema,
    onSubmit: handleAddAboutInfo,
  });

  return (
    <AuthProfileWrapper>
      {isLoading && <Spin fullscreen />}
      <div className="flex flex-col gap-4 w-full max-w-md">
        <HeaderWithBackButton title={t('aboutYou')} hasBackButton={false} />
        <TextAreaInput
          key={inputKey}
          label={t('aboutMe') + ` (${t('english', { ns: 'common' })})`}
          placeholder={t('aboutMe') + ` (${t('english', { ns: 'common' })})`}
          onChange={handleChange('about')}
          onBlur={handleBlur('about')}
          value={values.about}
          error={errors.about && touched.about ? errors.about : null}
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
          label={t('aboutMe') + ' (日本語)'}
          placeholder={t('aboutMe') + ' (日本語)'}
          onChange={handleChange('aboutJa')}
          onBlur={handleBlur('aboutJa')}
          value={values.aboutJa || ''}
          rows={6}
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
          setVideoFile={file => {
            setFieldValue('introVideo', file);
          }}
        />
        <CustomButton
          title={t('finish', { ns: 'common' })}
          className={'my-6'}
          onClick={() => handleSubmit()}
        />
      </div>
    </AuthProfileWrapper>
  );
};

export default UserBio;
