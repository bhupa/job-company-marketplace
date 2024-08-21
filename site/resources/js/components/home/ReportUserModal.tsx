import React, { useEffect, useState } from 'react';

import { Modal } from 'antd';
import { useNavigate } from 'react-router-dom';
import { useTranslation } from 'react-i18next';

// Components
import { CustomButton, TextAreaInput, Title } from '@components/common';

// Hooks
import { useShowMessage } from '@customHooks/useShowMessage';

// Redux
import {
  IReportUserParams,
  useReportUserMutation,
} from '@redux/services/userReportApi';
import { useAppDispatch } from '@redux/hook';
import { setNeedsHomeRefresh } from '@redux/reducers/home';

interface Props {
  closeModal: () => void;
  userId: string; // userId will be room id in case of chat
  type?: 'chat' | 'userId';
}

const ReportUserModal = ({ closeModal, userId, type = 'userId' }: Props) => {
  const dispatch = useAppDispatch();
  const navigate = useNavigate();
  const { showSuccess, showWarning } = useShowMessage();

  const { t, i18n } = useTranslation(['profile', 'messages']);

  const [reportUser, { isLoading, isSuccess, isError, error }] =
    useReportUserMutation();

  const [message, setMessage] = useState<string>();
  const [selectedMsg, setSelectedMsg] = useState<IReportUser>();

  useEffect(() => {
    if (isSuccess) {
      // It will trigger inside modal if kept without timeout and won't be visible as modal will be closed
      setTimeout(
        () => showSuccess(t('reportProfile.success', { ns: 'messages' })),
        500,
      );
      closeModal();
    }
  }, [isSuccess]);

  useEffect(() => {
    if (isError && (error as any)?.status === 422) {
      closeModal();
      navigate(-1);
      dispatch(setNeedsHomeRefresh(true));
    }
  }, [isError]);

  const handleSubmitReport = () => {
    const isOthersSelected = selectedMsg?.type === 'others';
    if (isOthersSelected && !message?.trim().length) {
      return showWarning(t('reportProfile.emptyMessage'));
    }
    const reportMessage = isOthersSelected
      ? message?.trim()
      : selectedMsg?.message.en;
    let reportId: IReportUserParams = { user_id: userId };
    if (type === 'chat') {
      reportId = { chat_room_id: userId };
    }
    reportUser({ message: reportMessage, ...reportId });
  };

  const selectedLanguageKey = i18n.language === 'en' ? 'en' : 'ja';

  return (
    <Modal
      centered
      title={
        <Title type="heading2" className="text-center" bold>
          {t(
            type === 'chat' ? 'reportProfile.chatTitle' : 'reportProfile.title',
          )}
        </Title>
      }
      open={true}
      onCancel={closeModal}
      maskClosable={false}
      footer={null}>
      <div className="flex flex-col bg-white p-6 rounded-t-lg gap-4 max-h-[70dvh] overflow-scroll">
        <Title type="caption1" className="text-center text-GRAY_807C83">
          {t('reportProfile.description')}
        </Title>
        <div className="flex flex-col">
          {REPORT_USER_MESSAGES.map(item => {
            return (
              <button
                key={item.id}
                className={`border-b text-left border-b-WHITE_E0E2E4 p-3 hover:bg-WHITE_F6F6F6 ${item.id === selectedMsg?.id ? 'bg-BLUE_004D801A' : ''}`}
                onClick={() => setSelectedMsg(item)}>
                <Title type="body2">
                  {item.message?.[selectedLanguageKey]}
                </Title>
              </button>
            );
          })}
        </div>
        {selectedMsg?.type === 'others' && (
          <TextAreaInput
            label={t('enterMessage')}
            placeholder={t('enterMessage')}
            onChange={e => setMessage(e.target.value)}
            autoSize={{ minRows: 1, maxRows: 6 }}
          />
        )}
        <CustomButton
          title={t('submit', { ns: 'common' })}
          loading={isLoading}
          disabled={!selectedMsg}
          onClick={() => handleSubmitReport()}
        />
      </div>
    </Modal>
  );
};

export default ReportUserModal;

const REPORT_USER_MESSAGES: IReportUser[] = [
  {
    id: '1',
    message: { en: 'Fake profile', ja: '偽のプロフィール' },
  },
  {
    id: '2',
    message: { en: 'Inappropriate content', ja: '不適切なコンテンツ' },
  },
  {
    id: '3',
    message: { en: 'Scam', ja: '詐欺' },
  },
  {
    id: '4',
    message: { en: 'Identity-based hate', ja: 'アイデンティティに基づく憎悪' },
  },
  {
    id: '5',
    message: {
      en: 'JobMatchy rules violation like sharing personal information',
      ja: '個人情報の共有などのJobMatchyルール違反',
    },
  },
  {
    id: '6',
    type: 'others',
    message: { en: 'Others', ja: 'その他' },
  },
];

interface IReportUser {
  id: string;
  type?: string;
  message: { en: string; ja: string };
}
