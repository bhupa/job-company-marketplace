import React, { useRef, useState } from 'react';

import { Popover } from 'antd';
import { useNavigate } from 'react-router-dom';
import { useTranslation } from 'react-i18next';

// Components
import { TagLabel } from '@components/home';
import { PopperListItem, Title } from '@components/common';

// Hooks
import useCompanyProfileInput from '@customHooks/useCompanyProfileInput';

// Others
import { MAX_TAGS_COUNT } from '@utils/constants';
import { IJobData } from '@redux/services/jobsApi';
import { JOB_TYPES, SALARY_PAY_TYPE } from '@constants/dropdownData';
import {
  CheckmarkCircle,
  Delete,
  EditFilled,
  Location,
  MoreVertical,
} from '@assets/icons';

interface Props {
  job: IJobData;
  hideMoreBtn?: boolean;
  isPopperVisible?: boolean;
  togglePopper?: (isReset?: boolean) => void;
  onDeletePress?: () => void;
  isMatched?: boolean;
}

const JobListItem = ({
  job,
  isPopperVisible,
  togglePopper,
  hideMoreBtn,
  onDeletePress,
  isMatched,
}: Props) => {
  const navigate = useNavigate();
  const { t, i18n } = useTranslation(['common']);
  const isJapanese = i18n.language === 'ja';
  const languageKey = isJapanese ? 'label_ja' : 'label';

  const popoverRef = useRef<any>(null);
  const [isPopoverOpen, setPopoverOpen] = useState<boolean>(false);

  const {
    id,
    jobImage,
    jobTitle,
    jobTitleJa,
    salaryFrom,
    salaryTo,
    payType,
    jobLocation,
    jobType,
    occupation,
    experience,
    japaneseLevel,
    fromWhen,
    requiredSkills,
    requiredSkillsJa,
    tags,
  } = job;

  let location = '';
  let locationId = '';
  if (jobLocation?.[0]) {
    location = jobLocation[0]?.[languageKey] ?? '';
    locationId = jobLocation[0]?.value ?? '';
  }
  const postedJobType =
    JOB_TYPES.find(jobTypeItem => jobTypeItem.value === jobType)?.[
      languageKey
    ] ?? '';
  const payTypeValue = SALARY_PAY_TYPE.find(item => item.value === payType)?.[
    languageKey
  ];

  const { handleSetCompanyJobData, handleSetJobEditMode } =
    useCompanyProfileInput();

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
    togglePopper && togglePopper(true);
    navigate('/jobs/create');
  };

  let count = 0;

  return (
    <>
      <button
        className={'card flex flex-col p-4 gap-4 bg-white w-full'}
        onClick={(e: React.MouseEvent<HTMLButtonElement, MouseEvent>) => {
          if (
            popoverRef.current &&
            popoverRef.current.contains(e.target as Node)
          ) {
            return;
          }
          togglePopper && togglePopper(true);
          return navigate('/jobs/detail', { state: { data: job } });
        }}>
        <div className="flex gap-2 justify-between w-full">
          <div
            className={`flex items-start flex-col gap-2 ${!hideMoreBtn ? 'w-[90%]' : 'w-full'} ${isMatched ? 'w-[96%]' : ''}`}>
            <Title type="body1" bold className="line-clamp-3 text-left">
              {isJapanese ? jobTitleJa || jobTitle : jobTitle}
            </Title>
            <Title type="caption1" className="text-left">
              {`${t('startingFrom', {
                ns: 'jobs',
                SALARY_FROM: `¥${salaryFrom}`,
                PAY_TYPE: `${payTypeValue ?? ''}`,
              })}`}
            </Title>
          </div>
          {!hideMoreBtn ? (
            <div ref={popoverRef}>
              <Popover
                open={isPopoverOpen}
                onOpenChange={visible => setPopoverOpen(visible)}
                trigger="hover"
                placement="bottom"
                className="flex items-center cursor-pointer z-[999]"
                overlayInnerStyle={{
                  marginRight: 14,
                  padding: '8px 4px',
                }}
                content={
                  <div
                    className="flex flex-col"
                    onClick={e => e.stopPropagation()}>
                    <PopperListItem
                      onClick={() => {
                        handleEditJob();
                      }}
                      title={t('edit', { ns: 'common' })}
                      icon={<EditFilled />}
                    />
                    <PopperListItem
                      onClick={() => {
                        setPopoverOpen(false);
                        onDeletePress && onDeletePress();
                      }}
                      title={t('delete', { ns: 'common' })}
                      icon={<Delete />}
                      hideBorder
                      titleColor={'text-RED_FF4D4D'}
                    />
                  </div>
                }>
                <span>
                  <MoreVertical className={'text-GRAY_5E5E5E'} height={40} />
                </span>
              </Popover>
            </div>
          ) : isMatched ? (
            <CheckmarkCircle className={'text-BLUE_004D80'} />
          ) : null}
        </div>
        {location && (
          <div className="flex gap-2 items-center">
            <Location className={'text-BLUE_004D80'} />
            <Title type="caption1">{location}</Title>
          </div>
        )}
        <div className="flex flex-wrap gap-2">
          {postedJobType && <TagLabel title={postedJobType} />}
          {tags?.map(preferenceItem => {
            if (count < MAX_TAGS_COUNT) {
              count++;
              return (
                <TagLabel
                  key={preferenceItem.value}
                  title={preferenceItem?.[languageKey]}
                />
              );
            }
          })}
        </div>
      </button>
      {isPopperVisible && (
        <div className="absolute w-full h-full right-0 left-0 top-0 z-[999]">
          <button onClick={() => togglePopper && togglePopper()}>
            <div className="absolute right-4 border border-WHITE_E0E2E4 bg-white rounded-xl top-10 shadow-md">
              <PopperListItem
                onClick={() => {
                  handleEditJob();
                }}
                title={t('edit', { ns: 'common' })}
                icon={<EditFilled />}
              />
              <PopperListItem
                onClick={() => {
                  onDeletePress && onDeletePress();
                }}
                title={t('delete', { ns: 'common' })}
                icon={<Delete />}
                hideBorder
                titleColor={'text-RED_FF4D4D'}
              />
            </div>
          </button>
        </div>
      )}
    </>
  );
};

export default JobListItem;
