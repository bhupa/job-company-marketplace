import React from 'react';

import { Tooltip } from 'antd';
import { useTranslation } from 'react-i18next';
import { useLocation, useNavigate } from 'react-router-dom';

// Components
import { DashboardWrapper } from '@templates';
import { ReadMoreText, Title } from '@components/common';

// Hooks
import useUserProfile from '@customHooks/useUserProfile';
import useCompanyProfileInput from '@customHooks/useCompanyProfileInput';

// Others
import {
  JOB_TYPES,
  EXPERIENCE_DATA,
  JAPANESE_LEVEL,
  SALARY_PAY_TYPE,
} from '@constants/dropdownData';
import { ArrowLeft, Edit } from '@assets/icons';
import { IJobData } from '@redux/services/jobsApi';
import { getDateInYearMonthDateFormat } from '@utils/dateUtils';

const JobDetail = () => {
  const { t, i18n } = useTranslation(['jobs', 'common']);

  const isJapanese = i18n.language === 'ja';

  const route = useLocation();
  const navigate = useNavigate();

  const { isJobSeeker } = useUserProfile();
  const { handleSetCompanyJobData, handleSetJobEditMode } =
    useCompanyProfileInput();

  const {
    id,
    jobImage,
    jobTitle,
    jobTitleJa,
    occupation,
    jobLocation,
    salaryFrom,
    salaryTo,
    japaneseLevel,
    experience,
    requiredSkills,
    requiredSkillsJa,
    jobType,
    fromWhen,
    payType,
    tags,
  } = (route?.state?.data as IJobData) ?? {};
  const dropdownKey = isJapanese ? 'label_ja' : 'label';
  let location = '';
  let locationId = '';
  if (jobLocation?.[0]) {
    location = jobLocation[0]?.[dropdownKey] ?? '';
    locationId = jobLocation[0]?.value ?? '';
  }
  const experienceObj =
    EXPERIENCE_DATA.find(item => item.value === experience)?.[dropdownKey] ??
    EXPERIENCE_DATA[0][dropdownKey];
  const employmentStatusObj =
    JOB_TYPES.find(item => item.value === jobType)?.[dropdownKey] ?? '-';
  const japaneseLevelObj =
    JAPANESE_LEVEL.find(item => item.value === japaneseLevel)?.[dropdownKey] ??
    '-';
  const payTypeValue = SALARY_PAY_TYPE.find(item => item.value === payType)?.[
    dropdownKey
  ];

  const handleEditJob = () => {
    handleSetJobEditMode({ isEdit: true, id });
    handleSetCompanyJobData({
      // Screen 1
      job_image: { path: jobImage, imageObj: null },
      job_title: jobTitle,
      job_title_ja: jobTitleJa,
      job_location: locationId,
      salary_from: salaryFrom?.toString(),
      salary_to: salaryTo?.toString(),
      pay_type: payType,
      from_when: fromWhen,
      // Screen 2
      occupation: occupation?.value,
      experience,
      japanese_level: japaneseLevel,
      // Screen 3
      job_type: jobType,
      tags: tags?.map(item => item.value) || [],
      // Screen 4
      required_skills: requiredSkills,
      required_skills_ja: requiredSkillsJa,
    });
    navigate('/jobs/create');
  };

  const titleValueText = (label: string, value: string) => {
    return (
      <div className={'flex gap-3 items-center'}>
        <div className={'w-[10px] h-[10px] bg-BLUE_25396F rounded-full'} />
        <div className="flex gap-1">
          <Title type="body2" className={'text-BLACK_1E2022'}>
            {t(label)}:&nbsp;
          </Title>
          <Title type="body2" className={'text-GRAY_77838F'}>
            {value}
          </Title>
        </div>
      </div>
    );
  };

  return (
    <DashboardWrapper>
      <div className="flex flex-col gap-4 p-4 rounded-xl bg-white shadow-md w-full overflow-scroll">
        <div className="flex gap-4 items-baseline justify-center relative break-words">
          <button
            onClick={() => navigate(-1)}
            className="px-4 hover:bg-WHITE_F6F6F6">
            <ArrowLeft className="text-black" />
          </button>
          <Title
            type="heading2"
            bold
            className={
              'text-BLUE_25396F text-center mb-1 w-[70%] sm:w-[80%] mx-auto'
            }>
            {isJapanese ? jobTitleJa || jobTitle : jobTitle}
          </Title>
          {!isJobSeeker && (
            <Tooltip placement="bottom" title={t('edit', { ns: 'common' })}>
              <button
                onClick={() => handleEditJob()}
                className="px-4 hover:bg-BLUE_004D801A">
                <Edit className="text-BLUE_004D80" width={20} height={20} />
              </button>
            </Tooltip>
          )}
        </div>
        {jobImage && (
          <img
            src={jobImage}
            className={' object-contain w-full h-[240px]'}
            alt="job-image"
          />
        )}
        {location && (
          <div>
            <div className="flex gap-2 items-center mb-1">
              <Title type="body2" className={'text-BLACK_1E2022'}>
                {t('location', { ns: 'jobseeker' })}: {location}
              </Title>
            </div>
            {(jobLocation?.[0]?.sections?.length ?? 0) > 0 && (
              <Title type="caption1" className={'text-BLACK_1E2022 leading-6'}>
                (&nbsp;
                {jobLocation?.[0]?.sections?.map((item, index) =>
                  index === (jobLocation?.[0]?.sections?.length ?? 0) - 1
                    ? item[dropdownKey]
                    : `${item[dropdownKey]}, `,
                )}
                &nbsp;)
              </Title>
            )}
          </div>
        )}
        {occupation?.[dropdownKey] && (
          <Title type="body2" className={'text-BLACK_1E2022'}>
            {t('occupation', { ns: 'jobseeker' })}: {occupation?.[dropdownKey]}
          </Title>
        )}
        <div className="flex flex-col mt-2 gap-2">
          <Title type="body1" className={'text-BLUE_25396F mb-1'} bold>
            {t('aboutJob')}
          </Title>
          {titleValueText(
            'salary',
            `${t('startingFrom', {
              ns: 'jobs',
              SALARY_FROM: `¥${salaryFrom}${salaryTo ? '- ¥' + salaryTo : ''}`,
              PAY_TYPE: `${payTypeValue ?? ''}`,
            })}`,
          )}
          {titleValueText(
            'jobStartDate',
            fromWhen ? `${getDateInYearMonthDateFormat(fromWhen)}` : 'N/A',
          )}
          {titleValueText(
            t('experience', { ns: 'jobseeker' }),
            `${experienceObj}`,
          )}
          {titleValueText(
            t('japaneseLevel', { ns: 'jobseeker' }),
            `${japaneseLevelObj}`,
          )}
          {employmentStatusObj &&
            titleValueText(
              t('jobType', { ns: 'jobseeker' }),
              `${employmentStatusObj}`,
            )}
        </div>
        <div className="flex flex-col mt-2 gap-2">
          <Title type="body1" className={'text-BLUE_25396F mb-1'} bold>
            {t('description', { ns: 'home' })}
          </Title>
          <ReadMoreText
            text={
              isJapanese ? requiredSkillsJa || requiredSkills : requiredSkills
            }
            color={'text-GRAY_545454'}
          />
        </div>
        {(tags?.length ?? 0) > 0 && (
          <div className="flex flex-col mt-2 gap-2">
            <Title type="body1" className={'text-BLUE_25396F mb-1'} bold>
              {t('preferences')}
            </Title>
            <div className="flex flex-col gap-2">
              {tags?.map(item => {
                return (
                  <div key={item.id} className={'flex gap-3 items-center'}>
                    <div
                      className={
                        'w-[10px] h-[10px] bg-BLUE_25396F rounded-full'
                      }
                    />
                    <div className="flex gap-1">
                      <Title type="body2" className={'text-GRAY_77838F'}>
                        {isJapanese ? item.label_ja : item.label}
                      </Title>
                    </div>
                  </div>
                );
              })}
            </div>
          </div>
        )}
      </div>
    </DashboardWrapper>
  );
};

export default JobDetail;
