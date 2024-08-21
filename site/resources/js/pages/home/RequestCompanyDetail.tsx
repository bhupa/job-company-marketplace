import React, { useState } from 'react';

import { Divider, Spin } from 'antd';
import { useTranslation } from 'react-i18next';
import { useLocation, useNavigate } from 'react-router-dom';

// Components
import { DashboardWrapper } from '@templates';
import { JobListItem } from '@components/jobs';
import { ReportUserModal } from '@components/home';
import { HeaderWithBackButton, Title } from '@components/common';

// Redux
import { ICompany, useGetSingleCompanyQuery } from '@redux/services/companyApi';

// Others
import { AppLogo } from '@assets/images';
import { Flag, Verified } from '@assets/icons';
import { VerificationStatus } from '@redux/services/authApi';

const RequestCompanyDetail = () => {
  const navigate = useNavigate();
  const { t, i18n } = useTranslation(['home']);
  const isJapanese = i18n.language === 'ja';

  const route = useLocation();
  const paramsData = (route?.state as any)?.data as ICompany;

  const [isReportModalVisible, setReportModalVisible] =
    useState<boolean>(false);

  const { isLoading, data: companyData } = useGetSingleCompanyQuery(
    paramsData?.id,
    {
      skip: !paramsData.id,
    },
  );

  if (!paramsData) {
    return null;
  }

  const data = companyData?.data;
  const companyLogo = data?.logo;
  const isVerified =
    data?.user?.verificationStatus === VerificationStatus.APPROVED;
  const matchedJobId = (route?.state as any)?.matchedJobId as string;

  return (
    <DashboardWrapper className="bg-WHITE_F6F6F6">
      <div className="flex flex-col gap-4 mb-9 w-full">
        <div className="flex flex-col gap-4 p-4 rounded-xl bg-white shadow-md">
          <HeaderWithBackButton
            title={t('detail')}
            onBackPressed={() => navigate(-1)}
            rightBtn={<Flag className={'text-black'} />}
            onRightButtonPress={() => setReportModalVisible(true)}
          />
          {isLoading ? (
            <Spin />
          ) : !data ? null : (
            <>
              <div className="flex gap-4 justify-between relative break-words items-center">
                <div className="flex flex-col gap-[6px]">
                  <div className="flex gap-2 items-center">
                    <Title type="body2" bold className={'text-BLUE_25396F'}>
                      {data.companyName}
                    </Title>
                    {isVerified ? <Verified width={16} height={16} /> : null}
                  </div>
                  <Title type="caption1" className={'text-GRAY_77838F'}>
                    {data.address}
                  </Title>
                </div>
                <div>
                  <img
                    src={companyLogo ? companyLogo : AppLogo}
                    alt="company-logo"
                    className={
                      'w-12 h-12 rounded-full self-end border border-white object-cover'
                    }
                  />
                </div>
              </div>
              {/* About Company */}
              <div className="flex flex-col gap-2 p-4 rounded-xl bg-white shadow-md">
                <Title type="body1" className={'text-BLUE_25396F mb-1'} bold>
                  {t('aboutCompany')}
                </Title>
                <Title
                  type="caption1"
                  className={
                    'text-GRAY_77838F text-justify leading-6 whitespace-break-spaces'
                  }>
                  {isJapanese
                    ? data.aboutCompanyJa || data.aboutCompany
                    : data.aboutCompany}
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
              {data.jobs?.length > 0 && (
                <div className="flex flex-col gap-4 py-4 rounded-xl items-start bg-white">
                  <div className="w-full">
                    <Title
                      type="body1"
                      bold
                      className={'text-BLUE_25396F px-4 mb-1'}>
                      {t('jobs', { ns: 'jobs' })}
                    </Title>
                    <Divider className="m-0" />
                  </div>
                  <div className="w-full gap-4 flex flex-col">
                    {data.jobs.map(jobItem => {
                      return (
                        <div key={jobItem.id}>
                          <JobListItem
                            job={jobItem}
                            hideMoreBtn
                            isMatched={jobItem.id === matchedJobId}
                          />
                        </div>
                      );
                    })}
                  </div>
                </div>
              )}
            </>
          )}
        </div>
      </div>
      {isReportModalVisible && (
        <ReportUserModal
          closeModal={() => setReportModalVisible(false)}
          userId={paramsData.user.id}
        />
      )}
    </DashboardWrapper>
  );
};

export default RequestCompanyDetail;
