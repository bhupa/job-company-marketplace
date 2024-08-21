import React, { useEffect, useRef, useState } from 'react';

import { Spin } from 'antd';
import { useFormik } from 'formik';
import { useTranslation } from 'react-i18next';
import { useNavigate } from 'react-router-dom';

// Components
import {
  CustomButton,
  CustomInput,
  DateInput,
  DropdownWithLabel,
  ErrorText,
  HeaderWithBackButton,
  ImageUpload,
  InputLabel,
  SectionDropdownWithLabel,
  Title,
} from '@components/common';

// Hooks
import { useShowMessage } from '@customHooks/useShowMessage';
import useCompanyProfileInput from '@customHooks/useCompanyProfileInput';

// Redux
import {
  useGetJobLocationListQuery,
  useTranslateTextMutation,
} from '@redux/services/dataApi';

// Others
import {
  CompanyWorkInformationValues,
  companyWorkInformationSchema,
} from './schema';
import { validateSalary } from '@utils/helpers';
import { SALARY_PAY_TYPE } from '@constants/dropdownData';

interface Props {
  onNextPressed: () => void;
}

const initialValues: CompanyWorkInformationValues = {
  jobTitle: '',
  jobTitleJa: '',
  location: '',
  minSalary: 0,
  maxSalary: null,
  startDate: null,
  payType: '',
};

const WorkInformation = ({ onNextPressed }: Props) => {
  const { t } = useTranslation(['jobs']);
  const navigate = useNavigate();
  const { showWarning } = useShowMessage();

  const imageUploadRef = useRef();

  const { isLoading: isJobLocationLoading, data: locationData } =
    useGetJobLocationListQuery();
  const {
    handleSetCompanyJobData,
    handleSetCompanyProfileInputData,
    jobInput,
  } = useCompanyProfileInput();
  const [
    translateText,
    {
      isLoading: isTranslationLoading,
      data: translationData,
      isSuccess: isTranslationSuccess,
    },
  ] = useTranslateTextMutation();

  const [isLoading, setLoading] = useState<boolean>(true);
  const [selectedJobImage, setSelectedJobImage] = useState<string | null>(null);
  const [selectedJobImageFile, setSelectedJobImageFile] = useState<File | null>(
    null,
  );

  // If translation is success, set value in about job title ( Japanese )
  useEffect(() => {
    if (isTranslationSuccess && translationData) {
      setFieldValue('jobTitle', translationData.data);
    }
  }, [isTranslationSuccess]);

  useEffect(() => {
    const {
      job_title,
      job_location,
      salary_from,
      salary_to,
      from_when,
      job_title_ja,
      pay_type,
    } = jobInput;
    setValues({
      jobTitle: job_title,
      jobTitleJa: job_title_ja,
      location: job_location,
      minSalary: Number(salary_from || 0),
      maxSalary: Number(salary_to || 0),
      startDate: from_when ? from_when : null,
      payType: pay_type,
    });
    setLoading(false);
  }, []);

  const jobImageUrl = selectedJobImage
    ? selectedJobImage
    : jobInput.job_image?.path;

  const handleAddWorkInformation = () => {
    const {
      location,
      minSalary,
      maxSalary,
      startDate,
      jobTitle,
      jobTitleJa,
      payType,
    } = values;
    handleSetCompanyProfileInputData({ isCompleted: false });
    if (!jobImageUrl) {
      return showWarning(t('validation.jobImageRequired', { ns: 'messages' }));
    }
    if (maxSalary && Number(minSalary) >= Number(maxSalary)) {
      return showWarning(t('validation.maxSalarySmall', { ns: 'messages' }));
    }
    let imageParams: any = {};
    if (selectedJobImage) {
      imageParams = {
        job_image: {
          path: selectedJobImage,
          imageObj: selectedJobImageFile,
        },
      };
    }
    handleSetCompanyJobData({
      ...imageParams,
      job_title: jobTitle,
      job_title_ja: jobTitleJa,
      job_location: location,
      salary_from: minSalary.toString(),
      salary_to: maxSalary?.toString() ?? null,
      pay_type: payType,
      from_when: startDate?.toString() ?? '',
    });
    onNextPressed();
  };

  const {
    handleBlur,
    handleChange,
    handleSubmit,
    setFieldValue,
    setTouched,
    setValues,
    values,
    touched,
    errors,
  } = useFormik({
    initialValues: initialValues,
    validationSchema: companyWorkInformationSchema,
    onSubmit: handleAddWorkInformation,
  });

  const salaryError =
    errors.minSalary && touched.minSalary
      ? errors.minSalary
      : errors.maxSalary && touched.maxSalary
        ? errors.maxSalary
        : null;

  return (
    <>
      {isLoading || isJobLocationLoading ? (
        <Spin />
      ) : (
        <div className="flex flex-col gap-3 h-full overflow-scroll">
          <HeaderWithBackButton
            onBackPressed={() => navigate('/jobs')}
            title={t('workInformation')}
          />
          <div className="flex flex-col gap-1">
            <InputLabel label={t('jobImage')} required />
            <ImageUpload
              uploadRef={imageUploadRef}
              image={jobImageUrl}
              onImageSelect={(image, file) => {
                setSelectedJobImage(image);
                setSelectedJobImageFile(file);
              }}
            />
            {!jobImageUrl && <ErrorText error="jobImageRequired" />}
          </div>
          <CustomInput
            label={t('jobTitle') + ' (日本語)'}
            placeholder={t('jobTitle') + ' (日本語)'}
            onChange={handleChange('jobTitleJa')}
            onBlur={handleBlur('jobTitleJa')}
            value={values.jobTitleJa ?? ''}
            error={
              errors.jobTitleJa && touched.jobTitleJa ? errors.jobTitleJa : null
            }
            autoCapitalize="words"
          />
          <CustomInput
            label={t('jobTitle')}
            placeholder={t('jobTitle')}
            onChange={handleChange('jobTitle')}
            onBlur={handleBlur('jobTitle')}
            value={values.jobTitle}
            error={errors.jobTitle && touched.jobTitle ? errors.jobTitle : null}
            autoCapitalize="words"
            required
            rightBtnComponent={
              <Title
                type="caption1"
                className={
                  isTranslationLoading ? 'text-GRAY_ACACAC' : 'text-BLUE_004D80'
                }>
                {t('translate', { ns: 'common' })}
              </Title>
            }
            onRightBtnPress={() => {
              if (
                isTranslationLoading ||
                !values.jobTitleJa ||
                values.jobTitleJa.trim().length === 0
              ) {
                return;
              }
              values.jobTitleJa &&
                translateText({
                  text: values.jobTitleJa,
                  from: 'ja',
                  to: 'en',
                });
            }}
          />
          <SectionDropdownWithLabel
            searchable
            label={t('location', { ns: 'jobseeker' })}
            placeholder={t('select', { ns: 'common' })}
            value={values.location}
            items={locationData?.data ?? []}
            onSelectItem={async item => {
              setFieldValue('location', item.value);
            }}
            error={errors.location && touched.location ? errors.location : null}
            required
          />
          <DropdownWithLabel
            searchable
            label={t('payType')}
            placeholder={t('select', { ns: 'common' })}
            value={values.payType}
            items={SALARY_PAY_TYPE}
            onSelectItem={async item => {
              setFieldValue('payType', item.value);
            }}
            error={errors.payType && touched.payType ? errors.payType : null}
            required
          />
          <div>
            <InputLabel label={t('salaryRange')} />
            <div className="flex gap-6 items-center">
              <CustomInput
                placeholder={t('startingFrom', {
                  PAY_TYPE: '',
                  SALARY_FROM: '',
                })}
                onChange={e => {
                  const validatedSalary = validateSalary(e.target.value);
                  if (validatedSalary || validatedSalary === '') {
                    setFieldValue('minSalary', validatedSalary);
                  }
                }}
                value={values.minSalary?.toString() ?? ''}
                required
              />
              <Title type="body1">~</Title>
              <CustomInput
                placeholder={t('salaryTo')}
                onChange={e => {
                  const validatedSalary = validateSalary(e.target.value);
                  if (validatedSalary || validatedSalary === '') {
                    setFieldValue('maxSalary', validatedSalary);
                  }
                }}
                value={values.maxSalary?.toString() ?? ''}
              />
            </div>
          </div>
          {salaryError && <ErrorText error={salaryError} />}
          <DateInput
            hasCloseButton
            label={t('jobStartDate')}
            placeholder={t('jobStartDate')}
            date={values.startDate}
            setDate={(startDate: string | null) => {
              setTouched({ startDate: true });
              setFieldValue('startDate', startDate ? startDate : null);
            }}
            minimumDate={new Date()}
            error={
              errors.startDate && touched.startDate
                ? (errors.startDate as string)
                : null
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

export default WorkInformation;
