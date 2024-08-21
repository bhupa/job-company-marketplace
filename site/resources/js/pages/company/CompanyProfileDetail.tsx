import React, { useEffect, useState } from 'react';

import { Spin } from 'antd';
import { useFormik } from 'formik';
import { useTranslation } from 'react-i18next';
import { useLocation, useNavigate } from 'react-router-dom';

// Components
import {
  CustomButton,
  CustomInput,
  HeaderWithBackButton,
  HelperText,
  TextAreaInput,
  Title,
} from '@components/common';
import { AuthProfileWrapper, DashboardWrapper } from '@templates';
import { ProfilePicker, SelfIntroductionVideo } from '@components/profile';

// Hooks
import useCompany from '@customHooks/useCompany';
import useCompanyProfileInput from '@customHooks/useCompanyProfileInput';

// Redux
import { useAppDispatch } from '@redux/hook';
import { saveAccessToken } from '@redux/reducers/auth';
import { useRefreshTokenQuery } from '@redux/services/authApi';
import { useTranslateTextMutation } from '@redux/services/dataApi';
import { useCheckCompanyExistsMutation } from '@redux/services/companyApi';

// Utils
import {
  CompanyProfileDetailValues,
  companyProfileDetailSchema,
} from './schema';

const initialValues: CompanyProfileDetailValues = {
  name: '',
  address: '',
  about: '',
  aboutJa: '',
  introVideo: null,
};

const CompanyProfileDetail = () => {
  const route = useLocation();
  const { isEdit } = route.state?.params ?? {};

  const navigate = useNavigate();
  const dispatch = useAppDispatch();
  const { t } = useTranslation(['company', 'messages']);

  const { handleSetCompanyProfileInputData, profile } =
    useCompanyProfileInput();
  const {
    companyName,
    aboutCompany,
    aboutCompanyJa,
    address: companyAddress,
    logo: companyLogo,
    handleUpdateCompany,
    isUpdating,
    isRefetchingCompanyDetail,
    user,
  } = useCompany();

  const [inputKey, setInputKey] = useState<string>(Date.now().toString());
  const [japaneseInputKey, setJapaneseInputKey] = useState<string>(
    Date.now().toString(),
  );
  const [isInitialLoading, setInitialLoading] = useState<boolean>(isEdit);
  const [isUsingTemplate, setIsUsingTemplate] = useState<boolean>(false);
  const [isVideoDeleted, setIsVideoDeleted] = useState<boolean>(false);
  const [isUsingJapaneseTemplate, setIsUsingJapaneseTemplate] =
    useState<boolean>(false);

  const [checkCompanyExists, { isLoading, isSuccess }] =
    useCheckCompanyExistsMutation();
  const { data: refreshData, isSuccess: isRefreshSuccess } =
    useRefreshTokenQuery(undefined, { skip: isEdit });
  const [
    translateText,
    {
      isLoading: isTranslationLoading,
      data: translationData,
      isSuccess: isTranslationSuccess,
    },
  ] = useTranslateTextMutation();

  // If translation is success, set value in about company ( Japanese )
  useEffect(() => {
    if (isTranslationSuccess && translationData) {
      setFieldValue('about', translationData.data);
      setInputKey(Date.now().toString());
    }
  }, [isTranslationSuccess]);

  useEffect(() => {
    if (isRefreshSuccess) {
      dispatch(saveAccessToken(refreshData.data.token));
    }
  }, [isRefreshSuccess]);

  useEffect(() => {
    if (profile.logo) {
      setFieldValue('profileImage', [profile.logo]);
    } else {
      setFieldValue('profileImage', '');
    }
  }, [profile]);

  useEffect(() => {
    if (isEdit) {
      setValues({
        name: companyName,
        about: aboutCompany,
        aboutJa: aboutCompanyJa,
        address: companyAddress,
      });
      setTimeout(() => setInitialLoading(false), 300);
    }
  }, [isEdit, isRefetchingCompanyDetail]);

  useEffect(() => {
    if (isSuccess) {
      handleAddProfileDetail();
    }
  }, [isSuccess]);

  /**
   * Add profile detail to redux state
   */
  const handleAddProfileDetail = () => {
    const { name, address, about, aboutJa, introVideo } = values;
    handleSetCompanyProfileInputData({
      company_name: name,
      address,
      about_company: about,
      about_company_ja: aboutJa || '',
      intro_video: introVideo as File | null,
    });
    navigate('/continue');
  };

  const handleUpdateGeneralInfo = () => {
    const { name, address, about, aboutJa, introVideo } = values;
    const changes: any = {};
    if (name !== companyName) {
      changes.company_name = name;
    }
    if (address !== companyAddress) {
      changes.address = address;
    }
    if (about !== aboutCompany) {
      changes.about_company = about;
    }
    if (aboutJa !== aboutCompanyJa) {
      changes.about_company_ja = aboutJa;
    }
    let params = {};
    if (!isVideoDeleted) {
      params = {
        intro_video: (introVideo ?? '') as File | null,
      };
    }
    handleUpdateCompany({
      company_name: name,
      address,
      about_company: about,
      about_company_ja: aboutJa || '',
      isIntroVideoDeleted: isVideoDeleted,
      ...params,
    });
  };

  const handleCheckCompanyName = () => {
    const { name } = values;
    checkCompanyExists(name);
  };

  const {
    handleBlur,
    handleChange,
    handleSubmit,
    setFieldValue,
    setValues,
    values,
    touched,
    errors,
  } = useFormik({
    initialValues: initialValues,
    validationSchema: companyProfileDetailSchema,
    onSubmit: isEdit ? handleUpdateGeneralInfo : handleCheckCompanyName,
  });

  const AppLayout = isEdit ? DashboardWrapper : AuthProfileWrapper;

  return (
    <AppLayout>
      <div className="flex flex-col gap-4 mb-4 w-full mx-auto">
        <HeaderWithBackButton
          title={isEdit ? t('generalInfo', { ns: 'jobs' }) : t('profileDetail')}
          hasBackButton={isEdit ? true : false}
          onBackPressed={() => navigate(-1)}
        />
        {isInitialLoading ? (
          <Spin />
        ) : (
          <div className="flex flex-col gap-4 h-full overflow-scroll">
            <ProfilePicker
              isEdit={isEdit}
              imageUrl={
                profile.logo ? URL.createObjectURL(profile.logo) : companyLogo
              }
            />
            <CustomInput
              label={t('companyName')}
              placeholder={t('companyName')}
              onChange={handleChange('name')}
              onBlur={handleBlur('name')}
              autoCapitalize="words"
              value={values.name || profile.company_name}
              error={errors.name && touched.name ? errors.name : null}
              readOnly={isEdit}
              disabled={isEdit}
              required
            />
            {!isEdit && (
              <HelperText
                message={'validation.changeCompanyName'}
                className="-mt-3"
              />
            )}
            <CustomInput
              label={t('address')}
              placeholder={t('address')}
              onChange={handleChange('address')}
              onBlur={handleBlur('address')}
              value={values.address}
              error={errors.address && touched.address ? errors.address : null}
              autoCapitalize="words"
              required
              maxLength={120}
            />
            <TextAreaInput
              key={japaneseInputKey + 'ja'}
              rows={6}
              label={t('aboutCompany') + ' (日本語)'}
              placeholder={t('aboutCompany') + ' (日本語)'}
              onChange={handleChange('aboutJa')}
              onBlur={handleBlur('aboutJa')}
              value={values.aboutJa || ''}
              error={errors.aboutJa && touched.aboutJa ? errors.aboutJa : null}
              autoCapitalize="sentences"
              rightBtnComponent={
                <Title type="caption1" className={'text-BLUE_004D80'}>
                  {isUsingJapaneseTemplate
                    ? t('reset', { ns: 'common' })
                    : t('useTemplate', { ns: 'common' })}
                </Title>
              }
              onRightBtnPress={() => {
                setIsUsingJapaneseTemplate(!isUsingJapaneseTemplate);
                setJapaneseInputKey(Date.now().toString());
                if (isUsingJapaneseTemplate) {
                  setFieldValue('aboutJa', '');
                } else {
                  setFieldValue('aboutJa', t('aboutCompanyPlaceholderJa'));
                }
              }}
            />
            <CustomButton
              size="small"
              title={t('translate', { ns: 'common' })}
              loading={isTranslationLoading}
              disabled={
                isTranslationLoading ||
                !values.aboutJa ||
                values.aboutJa.trim().length === 0
              }
              onClick={() =>
                values.aboutJa &&
                translateText({ text: values.aboutJa, from: 'ja', to: 'en' })
              }
              className="bg-GREEN_4EBE59 w-fit self-end rounded-full"
            />
            <TextAreaInput
              key={inputKey + 'en'}
              rows={6}
              label={t('aboutCompany') + ` (${t('english', { ns: 'common' })})`}
              placeholder={
                t('aboutCompany') + ` (${t('english', { ns: 'common' })})`
              }
              onChange={handleChange('about')}
              onBlur={handleBlur('about')}
              value={values.about}
              error={errors.about && touched.about ? errors.about : null}
              autoCapitalize="sentences"
              required
              rightBtnComponent={
                <Title type="caption1" className={'text-BLUE_004D80'}>
                  {isUsingTemplate
                    ? t('reset', { ns: 'common' })
                    : t('useTemplate', { ns: 'common' })}
                </Title>
              }
              onRightBtnPress={() => {
                setIsUsingTemplate(!isUsingTemplate);
                setInputKey(Date.now().toString());
                if (isUsingTemplate) {
                  setFieldValue('about', '');
                } else {
                  setFieldValue('about', t('aboutCompanyPlaceholder'));
                }
              }}
            />
            <SelfIntroductionVideo
              label={t('introductionVideoCompany', { ns: 'profile' })}
              videoUrl={user?.introVideo ?? null}
              setVideoFile={(file, _, isDeleted) => {
                setFieldValue('introVideo', file);
                setIsVideoDeleted(isDeleted);
              }}
            />
            <CustomButton
              title={
                isEdit
                  ? t('update', { ns: 'common' })
                  : t('next', { ns: 'common' })
              }
              className="mt-4"
              onClick={() => handleSubmit()}
              loading={isUpdating || isLoading}
            />
          </div>
        )}
      </div>
    </AppLayout>
  );
};

export default CompanyProfileDetail;
