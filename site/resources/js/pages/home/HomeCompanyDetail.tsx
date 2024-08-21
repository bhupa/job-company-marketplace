import React, { useState } from 'react';

import { Tooltip } from 'antd';
import { useTranslation } from 'react-i18next';
import { useLocation, useNavigate } from 'react-router-dom';

// Components
import { DashboardWrapper } from '@templates';
import { ReadMoreText, Title } from '@components/common';
import { ButtonGroup, ReportUserModal, TagLabel } from '@components/home';

// Others
import {
  JOB_TYPES,
  EXPERIENCE_DATA,
  JAPANESE_LEVEL,
  SALARY_PAY_TYPE,
} from '@constants/dropdownData';
import { AppLogo } from '@assets/images';
import { IJobData } from '@redux/services/jobsApi';
import { VerificationStatus } from '@redux/services/authApi';
import { getDateInYearMonthDateFormat } from '@utils/dateUtils';
import { ArrowLeft, Flag, Location, Verified } from '@assets/icons';

const HomeCompanyDetail = () => {
  const { t, i18n } = useTranslation(['jobs', 'jobseeker', 'home', 'company']);
  const isJapanese = i18n.language === 'ja';
  const navigate = useNavigate();

  const [isReportModalVisible, setReportModalVisible] =
    useState<boolean>(false);

  const route = useLocation();
  const params = route.state;
  const data = (params as any)?.data as IJobData;
  const hideBookmarkButton = (params as any)?.hideBookmarkButton as boolean;

  if (!data || !data.company) {
    return null;
  }

  const handleReportProfile = () => {
    setReportModalVisible(true);
  };

  const {
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
    tags,
    id,
    payType,
  } = data;
  const key = isJapanese ? 'label_ja' : 'label';
  let location = '';
  const jobLocationData = jobLocation?.[0];
  if (jobLocationData) {
    location = jobLocationData?.[key] ?? '';
  }
  const experienceObj =
    EXPERIENCE_DATA.find(item => item.value === experience)?.[key] ??
    EXPERIENCE_DATA[0][key];
  const employmentStatusObj = JOB_TYPES.find(item => item.value === jobType)?.[
    key
  ];
  const japaneseLevelObj = JAPANESE_LEVEL.find(
    item => item.value === japaneseLevel,
  )?.[key];
  const payTypeValue = SALARY_PAY_TYPE.find(item => item.value === payType)?.[
    key
  ];

  const companyLogo = data.company.logo;
  const isVerified =
    data?.user?.verificationStatus === VerificationStatus.APPROVED;

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
    <DashboardWrapper className="bg-WHITE_F6F6F6">
      <div className="flex flex-col gap-4 w-full">
        <div className="flex flex-col gap-4 p-4 rounded-xl bg-white shadow-md">
          <div className="flex gap-4 items-baseline justify-between relative break-words">
            <button
              onClick={() => navigate(-1)}
              className="px-4 hover:bg-WHITE_F6F6F6">
              <ArrowLeft className="text-black" />
            </button>
            <Title
              type="heading1"
              bold
              className={'text-BLUE_25396F text-center mb-1 mx-auto'}>
              {isJapanese ? jobTitleJa || jobTitle : jobTitle}
            </Title>
            <Tooltip placement="bottom" title={t('report', { ns: 'common' })}>
              <button
                onClick={() => handleReportProfile()}
                className="px-4 hover:bg-WHITE_F6F6F6">
                <Flag className="text-black" />
              </button>
            </Tooltip>
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
                <Title
                  type="caption1"
                  className={'text-BLACK_1E2022 leading-6'}>
                  (&nbsp;
                  {jobLocation?.[0]?.sections?.map((item, index) =>
                    index === (jobLocation?.[0]?.sections?.length ?? 0) - 1
                      ? item[key]
                      : `${item[key]}, `,
                  )}
                  &nbsp;)
                </Title>
              )}
            </div>
          )}
          {occupation?.[key] && (
            <Title type="body2" className={'text-BLACK_1E2022'}>
              {t('occupation', { ns: 'jobseeker' })}: {occupation?.[key]}
            </Title>
          )}
          {(tags?.length ?? 0) > 0 && (
            <div className="flex flex-col mt-2 gap-2">
              <Title type="body1" className={'text-BLUE_25396F mb-1'} bold>
                {t('preferences')}
              </Title>
              <div className="flex flex-wrap gap-2">
                {tags?.map(item => {
                  return (
                    <TagLabel
                      key={item.value}
                      title={isJapanese ? item.label_ja : item.label}
                    />
                  );
                })}
              </div>
            </div>
          )}
          <div className="flex flex-col mt-2 gap-2">
            <Title type="body1" className={'text-BLUE_25396F mb-1'} bold>
              {t('aboutJob')}
            </Title>
            {titleValueText(
              'salary',
              `${t('startingFrom', {
                ns: 'jobs',
                SALARY_FROM: `¥${salaryFrom}${
                  salaryTo ? '- ¥' + salaryTo : ''
                }`,
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
        </div>

        {/* Company Detail */}
        <div className="flex flex-col gap-4 p-4 rounded-xl bg-white shadow-md">
          <div className="flex items-center justify-between gap-4">
            <div className="flex flex-col gap-2">
              <div className="flex gap-2 items-center break-all">
                <Title type="body1" bold className={'text-BLUE_25396F'}>
                  {data.company.companyName}
                </Title>
                {isVerified ? <Verified width={16} height={16} /> : null}
              </div>
              <div className="flex gap-2 items-center">
                <Location className={'text-BLUE_004D80'} />
                <Title type="caption1" className={'text-GRAY_77838F'}>
                  {data.company.address}
                </Title>
              </div>
            </div>
            <div className="flex justify-end min-w-12 max-w-12">
              <img
                src={companyLogo ? companyLogo : AppLogo}
                className={
                  'w-12 h-12 rounded-full self-end border border-white object-cover'
                }
                alt="company-logo"
              />
            </div>
          </div>
        </div>
        {/* About Company */}
        <div className="flex flex-col gap-2 p-4 rounded-xl bg-white shadow-md">
          <Title type="body1" className={'text-BLUE_25396F mb-1'} bold>
            {t('aboutCompany', { ns: 'home' })}
          </Title>
          <Title
            type="caption1"
            className={
              'text-GRAY_77838F text-justify leading-6 whitespace-break-spaces'
            }>
            {isJapanese
              ? data.company.aboutCompanyJa || data.company.aboutCompany
              : data.company.aboutCompany}
          </Title>
        </div>
        {/* Introduction Video */}
        {data.user?.introVideo && (
          <div className="flex flex-col gap-2 p-4 rounded-xl bg-white shadow-md">
            <Title type="body1" className={'text-BLUE_25396F mb-1'} bold>
              {t('introductionVideo', { ns: 'profile' })}
            </Title>
            <video
              src={data.user.introVideo}
              width={400}
              height={400}
              controls
              className="aspect-video w-full"
            />
          </div>
        )}
        <ButtonGroup id={id} hideBookMark={hideBookmarkButton} />
      </div>
      {isReportModalVisible && (
        <ReportUserModal
          closeModal={() => setReportModalVisible(false)}
          userId={data.user?.id}
        />
      )}
    </DashboardWrapper>
  );
};

export default HomeCompanyDetail;
