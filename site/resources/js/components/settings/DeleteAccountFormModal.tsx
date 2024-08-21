import React, { useEffect, useState } from 'react';

import { Checkbox, Modal, Spin } from 'antd';
import { useTranslation } from 'react-i18next';

// Components
import { CustomButton, TextAreaInput, Title } from '@components/common';

// Hooks
import useUserProfile from '@customHooks/useUserProfile';
import { useShowMessage } from '@customHooks/useShowMessage';

// Redux
import { useDeleteAccountFormMutation } from '@redux/services/authApi';

interface Props {
  isVisible: boolean;
  setModalVisible?: (isVisible: boolean) => void;
}

const DeleteAccountFormModal = ({
  isVisible,
  setModalVisible = () => {},
}: Props) => {
  const { t, i18n } = useTranslation(['profile', 'common']);

  const languageKey = i18n.language;

  const { showError, showSuccess } = useShowMessage();
  const { handleLogout } = useUserProfile();
  const [submitDeleteAccountForm, { isLoading, isSuccess }] =
    useDeleteAccountFormMutation();

  const [comments, setComments] = useState<{ [key: string]: string }>({});
  const [reasons, setReasons] = useState<{
    [key: string]: IFormDataOption | null;
  }>({});
  const [subMenuReasons, setSubMenuReasons] = useState<{
    [key: string]: ISubMenuOptions;
  }>({});

  useEffect(() => {
    if (isSuccess) {
      handleLogout(false);
      showSuccess(t('account.deleted', { ns: 'messages' }));
    }
  }, [isSuccess]);

  const handleDeleteClicked = () => {
    const cancellationReason = reasons?.cancellationReasons ?? null;
    if (!cancellationReason) {
      return showError(
        t('accountDelete.selectReasonForCancellation', { ns: 'messages' }),
      );
    }
    const futurePlans = reasons?.futurePlans ?? null;
    if (!futurePlans) {
      return showError(
        t('accountDelete.selectFuturePlanOption', { ns: 'messages' }),
      );
    }

    // Sub-reasons
    const hasHighFeesOption = cancellationReason.name === 'highFees';
    if (hasHighFeesOption) {
      if (!subMenuReasons?.highFees) {
        return showError(
          t('accountDelete.selectHighFeesOption', { ns: 'messages' }),
        );
      }
    }

    let subReasonParams = {};
    if (hasHighFeesOption) {
      subReasonParams = {
        sub_reason: subMenuReasons?.highFees?.id,
      };
    }
    submitDeleteAccountForm({
      reason: cancellationReason.id,
      future_plan: futurePlans.id,
      comment: comments?.comments ?? '',
      ...subReasonParams,
    });
  };

  if (isLoading) {
    return <Spin fullscreen />;
  }

  return (
    <Modal
      centered
      open={isVisible}
      closable={false}
      title={
        <Title
          type="heading2"
          className="flex items-center justify-center w-full">
          {t('deleteAccount.title', {
            ns: 'profile',
          })}
        </Title>
      }
      footer={
        <div className="flex items-center justify-around w-full border-t-2 border-WHITE_F6F6F6">
          <CustomButton
            type="text"
            className="mt-2 text-GRAY_77838F"
            title={t('cancel', { ns: 'common' })}
            onClick={() => setModalVisible(false)}
          />
          <CustomButton
            type="text"
            className="mt-2 text-RED_FF4D4D"
            title={t('confirm', { ns: 'common' })}
            onClick={handleDeleteClicked}
          />
        </div>
      }>
      <div className="flex flex-col gap-4 max-h-[64vh] overflow-scroll">
        <div className="flex flex-col w-full items-center justify-center gap-2">
          {FORM_DATA.map(item => {
            const reasonsList = reasons?.[item.name];
            return (
              <div key={item.id} className="w-full">
                <Title type="body2">{item.title?.[languageKey]}</Title>
                {item.type === 'input' && (
                  <TextAreaInput
                    placeholder={t('enterMessage', { ns: 'profile' })}
                    onChange={e => setComments({ [item.name]: e.target.value })}
                    autoSize={{ minRows: 1, maxRows: 6 }}
                  />
                )}
                <div className="flex flex-col gap-1">
                  {item.options?.map(option => {
                    const isChecked = reasonsList?.id === option.id;
                    return (
                      <Checkbox
                        key={option.id}
                        checked={isChecked}
                        onChange={e => {
                          const checked = e.target.checked;
                          if (!checked) {
                            return setReasons({
                              ...reasons,
                              [item.name]: null,
                            });
                          }
                          setReasons({
                            ...reasons,
                            [item.name]: option,
                          });
                        }}>
                        <Title type="caption1" className={'text-GRAY_77838F'}>
                          {option.title?.[languageKey || 'en']}
                        </Title>
                      </Checkbox>
                    );
                  })}
                </div>
                {reasonsList?.hasSubMenu &&
                  reasonsList.subMenu?.map(subMenuItem => {
                    const subReasonsItem = subMenuReasons?.[subMenuItem.name];
                    return (
                      <div
                        key={'submenu-' + subMenuItem.id}
                        className="w-full mt-1">
                        <Title type="body2">
                          {subMenuItem.title?.[languageKey]}
                        </Title>
                        <div className="flex flex-col gap-1">
                          {subMenuItem.options?.map(subMenuOption => {
                            const isChecked =
                              subReasonsItem?.id === subMenuOption.id;
                            return (
                              <Checkbox
                                key={'submenuoption-' + subMenuOption.id}
                                checked={isChecked}
                                onChange={e => {
                                  const checked = e.target.checked;
                                  if (!checked) {
                                    const allSubReasons = JSON.parse(
                                      JSON.stringify(subMenuReasons),
                                    );
                                    delete allSubReasons[subMenuItem.name];
                                    return setSubMenuReasons(allSubReasons);
                                  }
                                  setSubMenuReasons({
                                    ...subMenuReasons,
                                    [subMenuItem.name]: subMenuOption,
                                  });
                                }}>
                                <Title
                                  type="caption1"
                                  className={'text-GRAY_77838F'}>
                                  {subMenuOption.title?.[languageKey || 'en']}
                                </Title>
                              </Checkbox>
                            );
                          })}
                        </div>
                      </div>
                    );
                  })}
              </div>
            );
          })}
        </div>
      </div>
    </Modal>
  );
};

export default DeleteAccountFormModal;

interface ISubMenuOptions {
  id: string;
  title: { [key: string]: string };
}

interface ISubMenu {
  id: string;
  name: string;
  title: { [key: string]: string };
  type?: 'input' | 'checkbox';
  options?: ISubMenuOptions[];
}

interface IFormDataOption {
  id: string;
  name?: string;
  title: { [key: string]: string };
  type?: 'input' | 'other';
  hasSubMenu?: boolean;
  subMenu?: ISubMenu[];
}

interface IFormData {
  id: string;
  name: string;
  title: { [key: string]: string };
  type?: 'input' | 'checkbox';
  options?: IFormDataOption[];
}
const FORM_DATA: IFormData[] = [
  {
    id: '1',
    name: 'cancellationReasons',
    title: {
      en: 'Please check the reasons for cancellation',
      ja: '退会理由にチェックを入れてください。',
    },
    options: [
      {
        id: '1',
        title: {
          en: 'Dissatisfaction with service quality',
          ja: 'サービスの品質に不満がある',
        },
      },
      {
        id: '2',
        name: 'highFees',
        title: {
          en: 'High fees',
          ja: '料金が高い',
        },
        hasSubMenu: true,
        subMenu: [
          {
            id: '1',
            name: 'highFees',
            title: {
              en: 'Would you consider continuing if the fees were lower?',
              ja: '利用料が安ければご継続を考えられますか？',
            },
            options: [
              {
                id: '1',
                title: {
                  en: 'Considering',
                  ja: '考える',
                },
              },
              {
                id: '2',
                title: {
                  en: 'Willing to consider depending on the price',
                  ja: '価格によって考えても良い',
                },
              },
              {
                id: '3',
                title: {
                  en: 'Not considering',
                  ja: '考えない',
                },
              },
            ],
          },
        ],
      },
      {
        id: '3',
        title: {
          en: 'Poor support',
          ja: 'サポートが悪い',
        },
      },
      {
        id: '4',
        title: {
          en: 'Poor email response',
          ja: 'メール対応が悪い',
        },
      },
      {
        id: '5',
        title: {
          en: 'Lack of contact from the other party',
          ja: '相手から連絡がない',
        },
      },
      {
        id: '6',
        title: {
          en: 'Poor technical support',
          ja: '技術サポートが悪い',
        },
      },
      {
        id: '7',
        title: {
          en: 'Complicated payment procedures',
          ja: '支払手続きが面倒',
        },
      },
      {
        id: '8',
        title: {
          en: 'Low frequency of use',
          ja: '使用頻度が低い',
        },
      },
      {
        id: '9',
        title: {
          en: 'Experienced troubles',
          ja: 'トラブルがあった',
        },
      },
      {
        id: '10',
        type: 'other',
        title: {
          en: 'Other',
          ja: 'その他',
        },
      },
    ],
  },
  {
    id: '2',
    name: 'futurePlans',
    title: {
      en: 'Please tell us about your future plans',
      ja: '今後の予定を教えてください。',
    },
    options: [
      {
        id: '1',
        title: {
          en: 'Use other service',
          ja: '他のサイトへの乗換',
        },
      },
      {
        id: '2',
        title: {
          en: 'Continue working in current company',
          ja: '自社で運用',
        },
      },
    ],
  },
  {
    id: '3',
    name: 'comments',
    title: {
      en: 'Please share any necessary suggestions or comments about this service',
      ja: 'お客さまにとって必要なサービスや本サービスへのコメントを自由にお聞かせ下さい',
    },
    type: 'input',
  },
];
