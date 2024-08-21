import React from 'react';

// Components
import { Title } from '@components/common';
import { DashboardWrapper } from '@templates';
import { PlanList } from '@components/subscription';
import { CompleteProfileButton, ProfilePicker } from '@components/profile';

// Hooks
import useCompany from '@customHooks/useCompany';

// Redux
import { VerificationStatus } from '@redux/services/authApi';

// Others
import { Verified } from '@assets/icons';
import { PROFILE_COMPLETION_PERCENT } from '@utils/constants';

const CompanyProfile = () => {
  const {
    companyName = '',
    logo,
    verificationStatus,
    percentage,
    user,
  } = useCompany();
  const isVerified = verificationStatus === VerificationStatus.APPROVED;

  /**
   * If percentage < PROFILE_COMPLETION_PERCENT, it means profile is incomplete as 30% is for verified user.
   * If percentage >= PROFILE_COMPLETION_PERCENT and also verified, it means its 70% along with verification. So, it needs profile completion.
   */
  const needsCompletion =
    percentage < PROFILE_COMPLETION_PERCENT ||
    (percentage !== 100 && isVerified);

  return (
    <DashboardWrapper>
      <div className="flex flex-col gap-2 w-full">
        <ProfilePicker isEdit imageUrl={logo} progress={percentage} />
        <div className="flex flex-col">
          <div className="flex gap-3 items-center justify-center w-full">
            <Title type="heading2" className="text-GRAY_5E5E5E">
              {companyName}
            </Title>
            {isVerified ? <Verified width={20} height={20} /> : null}
          </div>
          <Title type="body1" className="text-GRAY_5E5E5E text-center">
            {user?.email ?? user?.phone ?? ''}
          </Title>
        </div>
        <div className="flex flex-col gap-2">
          {needsCompletion ? (
            <CompleteProfileButton needsCompletion={true} />
          ) : null}
          {!isVerified ? (
            <CompleteProfileButton
              needsCompletion={false}
              verificationStatus={verificationStatus}
            />
          ) : null}
        </div>
        <PlanList />
      </div>
    </DashboardWrapper>
  );
};

export default CompanyProfile;
