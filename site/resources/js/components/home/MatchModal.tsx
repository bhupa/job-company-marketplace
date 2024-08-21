import React from 'react';

import { Grid, Modal } from 'antd';
import { useTranslation } from 'react-i18next';
import { useNavigate } from 'react-router-dom';

// Components
import { CustomButton, Title } from '@components/common';

// Hooks
import useUserProfile from '@customHooks/useUserProfile';

// Redux
import { setMatchingModalData } from '@redux/reducers/home';
import { useAppDispatch, useAppSelector } from '@redux/hook';

const { useBreakpoint } = Grid;

const MatchModal = () => {
  const { t } = useTranslation(['home']);
  const breakpoints = useBreakpoint();

  const navigate = useNavigate();
  const dispatch = useAppDispatch();

  const { matchingModalData } = useAppSelector(state => state.home);

  const { isJobSeeker } = useUserProfile();

  const handleCloseModal = () => {
    dispatch(
      setMatchingModalData({ isMatchingModalVisible: false, data: null }),
    );
  };

  const data = matchingModalData?.[0] ?? {};

  const jobSeekerImage = data.jobseeker?.profileImg;
  const companyImage = data.company?.logo;
  const likerName = isJobSeeker
    ? data.company?.companyName
    : data.jobseeker?.firstName;

  return (
    <Modal
      centered
      open={true}
      maskClosable={false}
      width={breakpoints.md ? '60%' : '90%'}
      className="match-modal"
      footer={null}>
      <div className="flex flex-col gap-4 max-h-[70dvh] overflow-scroll">
        <Title
          type="heading1"
          className={'text-white text-center text-4xl'}
          bold>
          {t('itsMatch')}
        </Title>
        <Title type="heading1" className={'text-white text-center'} bold>
          {likerName}&nbsp;{t('likedYou')}
        </Title>
        <div className={'flex gap-6 justify-between my-6'}>
          <img
            src={isJobSeeker ? jobSeekerImage : companyImage}
            alt="matched-userA"
            className="object-contain w-[140px] md:w-[240px] h-[140px] md:h-[240px] rounded-full border border-white"
          />
          <img
            src={!isJobSeeker ? jobSeekerImage : companyImage}
            alt="matched-userB"
            className="object-contain w-[140px] md:w-[240px] h-[140px] md:h-[240px] rounded-full border border-white"
          />
        </div>
        <CustomButton
          type="text"
          title={t('sendMessage')}
          className={
            'text-white hover:shadow-sm hover:shadow-white border border-WHITE_F6F6F6'
          }
          onClick={() => {
            navigate(`/chat-screen/${data.room}`, {
              state: {
                params: { chatRoomId: data.room },
              },
            });
            handleCloseModal();
          }}
        />
        <CustomButton
          type="text"
          title={t('continueSwiping')}
          className={
            'text-white hover:shadow-sm hover:shadow-white border border-WHITE_F6F6F6'
          }
          onClick={() => handleCloseModal()}
        />
      </div>
    </Modal>
  );
};

export default MatchModal;
