import React, { useState } from 'react';

import { Space, Spin, Pagination } from 'antd';
import { useTranslation } from 'react-i18next';
import { useNavigate } from 'react-router-dom';

// Components
import { DashboardWrapper } from '@templates';
import { JobListItem } from '@components/jobs';
import { SubscribeModal } from '@components/subscription';
import {
  ConfirmationModal,
  CustomButton,
  EmptyListMessage,
  Title,
} from '@components/common';

// Hooks
import useUserProfile from '@customHooks/useUserProfile';
import useCompanyJobs from '@customHooks/useCompanyJobs';
import useCompanyProfileInput from '@customHooks/useCompanyProfileInput';

const JobList = () => {
  const { t } = useTranslation(['jobs']);

  const { isSubscribed } = useUserProfile();
  const navigate = useNavigate();

  const [isSubscriptionModalVisible, setSubscriptionModalVisible] =
    useState<boolean>(false);
  const [selectedPopperId, setSelectedPopperId] = useState<string | null>(null);
  const [deleteJobId, setDeleteJobId] = useState<string | null>(null);

  const { jobs, isLoading, isEmpty, totalData, setPage, handleDeleteJob } =
    useCompanyJobs();
  const { handleSetJobEditMode, handleResetJobData } = useCompanyProfileInput();

  const handleCreateJob = () => {
    if (!isSubscribed && !isEmpty) {
      return setSubscriptionModalVisible(true);
    }
    handleResetJobData();
    handleSetJobEditMode({ isEdit: false });
    // Go to job policy screen
    navigate('/jobs/policy');
  };

  return (
    <DashboardWrapper>
      <div className="w-full">
        <div className="sticky h-[50px] top-0 z-50 pl-3 bg-white flex items-center justify-between w-full mb-1">
          <Title type="heading2" className="text-BLUE_25396F">
            {t('jobs', { ns: 'jobs' })}
          </Title>
          <CustomButton
            type="link"
            title={t('createJob', { ns: 'jobs' })}
            onClick={() => handleCreateJob()}
          />
        </div>
        {isLoading ? (
          <Spin className="flex justify-center" />
        ) : isEmpty ? (
          <EmptyListMessage message="job.noJobs" />
        ) : (
          <Space direction="vertical" size={16} className="w-full">
            {jobs?.map(item => {
              return (
                <JobListItem
                  key={item.id}
                  job={item}
                  isPopperVisible={item.id === selectedPopperId}
                  togglePopper={isReset =>
                    setSelectedPopperId(
                      isReset || selectedPopperId === item.id ? null : item.id,
                    )
                  }
                  onDeletePress={() => {
                    setDeleteJobId(item.id);
                    setSelectedPopperId(null);
                  }}
                />
              );
            })}
            <Pagination
              defaultCurrent={1}
              total={totalData}
              onChange={page => setPage(page)}
              className="pb-2"
            />
          </Space>
        )}
      </div>
      {isSubscriptionModalVisible && (
        <SubscribeModal closeModal={() => setSubscriptionModalVisible(false)} />
      )}
      {deleteJobId && (
        <ConfirmationModal
          closeModal={() => setDeleteJobId(null)}
          title={t('areYouSureToDeleteJob')}
          isLoading={false}
          onOkPress={() => {
            handleDeleteJob(deleteJobId);
            setDeleteJobId(null);
          }}
        />
      )}
    </DashboardWrapper>
  );
};

export default JobList;
