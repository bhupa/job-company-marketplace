import React, { useState } from 'react';

import { Tooltip } from 'antd';
import { useTranslation } from 'react-i18next';
import { useLocation, useNavigate } from 'react-router-dom';

// Components
import { Title } from '@components/common';
import { DashboardWrapper } from '@templates';
import { ButtonGroup, ReportUserModal, TagLabel } from '@components/home';

// Redux
import { useGetOccupationListQuery } from '@redux/services/dataApi';

// Others
import {
  JOB_TYPES,
  EXPERIENCE_DATA,
  GENDER_DATA,
  JAPANESE_LEVEL,
} from '@constants/dropdownData';
import { AppLogo } from '@assets/images';
import { JOBSEEKER_TAGS } from '@utils/constants';
import { VerificationStatus } from '@redux/services/authApi';
import { getDateInYearMonthDateFormat } from '@utils/dateUtils';
import { IJobSeekerProfile } from '@redux/services/jobSeekerApi';
import { ArrowLeft, Calendar, Flag, Location, Verified } from '@assets/icons';

const HomeJobSeekerDetail = () => {
  const { t, i18n } = useTranslation(['jobseeker', 'home']);

  const [isReportModalVisible, setReportModalVisible] =
    useState<boolean>(false);

  const isJapanese = i18n.language === 'ja';

  const navigate = useNavigate();
  const location = useLocation();
  const params = location.state;
  const data = (params as any)?.data as IJobSeekerProfile;
  const hideBookmarkButton = (params as any)?.hideBookmarkButton as boolean;
  const hideActionButtons = (params as any)?.hideActionButtons as boolean;

  const { data: occupationList } = useGetOccupationListQuery();

  if (!data) {
    return null;
  }

  const {
    firstName,
    country,
    birthday,
    about,
    aboutJa,
    jobType,
    experience,
    gender,
    profileImg,
    japaneseLevel,
    occupation,
    id,
    user,
    tags,
  } = data;
  const isVerified = user?.verificationStatus === VerificationStatus.APPROVED;

  const handleReportProfile = () => {
    setReportModalVisible(true);
  };

  const titleValueText = (label: string, value: string | undefined) => {
    if (!value) {
      return null;
    }
    return (
      <div className="flex gap-1">
        <Title type="body2" className={'text-BLACK_1E2022'}>
          {t(label)}:&nbsp;
        </Title>
        <Title type="body2" className={'text-GRAY_77838F'}>
          {value}
        </Title>
      </div>
    );
  };

  const dropdownKey = isJapanese ? 'label_ja' : 'label';

  const jobSeekerJobType =
    JOB_TYPES.find(type => type.value === jobType)?.[dropdownKey] ?? '';

  return (
    <DashboardWrapper className="bg-WHITE_F6F6F6">
      <div className="flex flex-col gap-4 w-full">
        <div
          className={'flex flex-col gap-4 p-4 rounded-xl bg-white shadow-md'}>
          <div className="flex justify-between gap-4">
            <button
              onClick={() => navigate(-1)}
              className="pr-4 hover:bg-WHITE_F6F6F6">
              <ArrowLeft className="text-black" />
            </button>
            <Tooltip placement="bottom" title={t('report', { ns: 'common' })}>
              <button
                onClick={() => handleReportProfile()}
                className="pl-4 hover:bg-WHITE_F6F6F6">
                <Flag className="text-black" />
              </button>
            </Tooltip>
          </div>
          {/* USER INFO */}
          <div className={'flex items-center justify-between'}>
            <div className={'flex flex-col gap-2'}>
              <div className={'flex gap-2 items-center'}>
                <Title type="body1" bold className={'text-BLUE_25396F'}>
                  {firstName}
                </Title>
                {isVerified ? <Verified width={16} height={16} /> : null}
              </div>
              <div className={'flex gap-2 items-center'}>
                <Location className={'text-BLUE_004D80'} />
                <Title type="caption1" className={'text-GRAY_77838F'}>
                  {country}
                </Title>
              </div>
              <div className={'flex flex-wrap gap-2 items-center'}>
                <TagLabel
                  title={getDateInYearMonthDateFormat(birthday)}
                  icon={Calendar}
                />
                <TagLabel
                  title={
                    GENDER_DATA.find(item => item.value === gender)?.[
                      dropdownKey
                    ] ?? ''
                  }
                />
              </div>
            </div>
            <img
              src={profileImg ? profileImg : AppLogo}
              className={
                'w-12 h-12 rounded-full self-start border border-WHITE_EFEFEF object-cover'
              }
              alt="profile-image"
            />
          </div>
          {/* TAGS */}
          <div className="flex justify-between">
            <div className={'flex flex-wrap gap-2'}>
              {jobSeekerJobType && <TagLabel title={jobSeekerJobType} />}
              {JOBSEEKER_TAGS.map(tag => {
                if (!data?.[tag.key as keyof IJobSeekerProfile]) {
                  return null;
                }
                const tagTitle = t(tag.label, { ns: 'jobseeker' });
                if (!tagTitle) {
                  return null;
                }
                return <TagLabel key={tag.key} title={tagTitle} />;
              })}
              {tags?.map(tag => (
                <TagLabel key={tag.value} title={tag?.[dropdownKey]} />
              ))}
            </div>
          </div>
        </div>
        {/* USER INFO */}
        <div
          className={
            'flex flex-col items-start gap-4 p-4 rounded-xl bg-white shadow-md'
          }>
          {titleValueText(
            'jobType',
            JOB_TYPES.find(item => item.value === jobType)?.[dropdownKey],
          )}
          {titleValueText(
            'experience',
            EXPERIENCE_DATA.find(item => item.value === experience)?.[
              dropdownKey
            ],
          )}
          {titleValueText(
            'japaneseLevel',
            JAPANESE_LEVEL.find(item => item.value === japaneseLevel)?.[
              dropdownKey
            ],
          )}
          {titleValueText(
            'occupation',
            occupationList?.data?.find(item => item.value === occupation)?.[
              dropdownKey
            ],
          )}
        </div>
        {/* DESCRIPTION */}
        <div
          className={
            'flex flex-col items-start gap-4 p-4 rounded-xl bg-white shadow-md'
          }>
          <Title type="body1" bold className={'text-BLUE_25396F'}>
            {t('description', { ns: 'home' })}
          </Title>
          <Title
            type="caption1"
            className="text-justify text-GRAY_77838F leading-6 break-words whitespace-break-spaces">
            {isJapanese ? aboutJa || about : about || 'N/A'}
          </Title>
        </div>
        {/* Introduction Video */}
        {user?.introVideo && (
          <div className="flex flex-col gap-2 p-4 rounded-xl bg-white shadow-md">
            <Title type="body1" className={'text-BLUE_25396F mb-1'} bold>
              {t('introductionVideo', { ns: 'profile' })}
            </Title>
            <video
              src={user.introVideo}
              width={400}
              height={400}
              controls
              className="aspect-video w-full"
            />
          </div>
        )}
        {!hideActionButtons && (
          <ButtonGroup id={id} hideBookMark={hideBookmarkButton} />
        )}
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

export default HomeJobSeekerDetail;
