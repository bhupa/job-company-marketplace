import React from 'react';

import { useTranslation } from 'react-i18next';
import { useNavigate } from 'react-router-dom';

// Components
import { Title } from '@components/common';

// Hooks
import useUserProfile from '@customHooks/useUserProfile';

// Others
import { VerificationStatus } from '@redux/services/authApi';

interface Props {
  needsCompletion: boolean;
  verificationStatus?: VerificationStatus;
}

const CompleteProfileButton = ({
  needsCompletion,
  verificationStatus,
}: Props) => {
  const { t } = useTranslation(['profile']);
  const navigate = useNavigate();

  const { isJobSeeker } = useUserProfile();

  const handleCompleteProfile = () => {
    if (!needsCompletion) {
      return navigate('/verify-account');
    }
    if (isJobSeeker) {
      return navigate('/profile/detail');
    }
    navigate('/profile/detail', { state: { params: { isEdit: true } } });
  };
  const isPending = verificationStatus === VerificationStatus.PENDING;
  const isRejected = verificationStatus === VerificationStatus.REJECTED;

  return (
    <button
      className={`flex self-center px-3 py-1 rounded-3xl ${isPending ? 'bg-ORANGE_EFC269' : isRejected ? 'bg-RED_FF4D4D' : 'bg-GRAY_A6A6A6'}`}
      onClick={() => handleCompleteProfile()}>
      <Title
        type="caption1"
        className={`${isPending || isRejected ? 'text-white' : 'text-black'} underline`}>
        {verificationStatus
          ? t(`userVerification.${verificationStatus.toLocaleLowerCase()}`)
          : t(needsCompletion ? 'completeYourProfile' : 'verifyYourAccount')}
      </Title>
    </button>
  );
};

export default CompleteProfileButton;
