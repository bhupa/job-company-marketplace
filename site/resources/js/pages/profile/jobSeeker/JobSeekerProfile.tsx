import React from 'react';

// Components
import { DashboardWrapper } from '@templates';
import { PlanList } from '@components/subscription';
import { Title } from '@components/common';
import { CompleteProfileButton, ProfilePicker } from '@components/profile';

// Hooks
import useJobSeeker from '@customHooks/useJobSeeker';

// Redux
import { VerificationStatus } from '@redux/services/authApi';

// Others
import { Verified } from '@assets/icons';
import { PROFILE_COMPLETION_PERCENT } from '@utils/constants';

const JobSeekerProfile = () => {
  const {
    firstName = '',
    lastName = '',
    verificationStatus,
    profileImg,
    percentage,
    user,
  } = useJobSeeker();
  const isVerified = verificationStatus === VerificationStatus.APPROVED;

  const needsCompletion =
    percentage < PROFILE_COMPLETION_PERCENT ||
    (percentage !== 100 && isVerified);

  return (
    <DashboardWrapper>
      <div className="flex flex-col gap-2 w-full">
        <ProfilePicker isEdit imageUrl={profileImg} progress={percentage} />
        <div className="flex flex-col">
          <div className="flex gap-3 items-center justify-center  w-full">
            <Title type="heading2" className="text-GRAY_5E5E5E">
              {`${firstName} ${lastName}`}
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

export default JobSeekerProfile;
