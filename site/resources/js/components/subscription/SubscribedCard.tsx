import React, { useEffect } from 'react';

import { Spin } from 'antd';
import { useTranslation } from 'react-i18next';

// Components
import { AppLogo, CustomButton, Title } from '@components/common';

// Hooks
import { useShowMessage } from '@customHooks/useShowMessage';

// Others
import { CheckMark } from '@assets/icons';
import { getTimeInHoursMinutes } from '@utils/dateUtils';
import {
  IInAppPlanData,
  IStripePlan,
  ISubscribedInAppPlan,
  ISubscribedStripePlan,
  useCancelStripeSubscriptionMutation,
} from '@redux/services/subscriptionApi';

interface Props {
  data: ISubscribedInAppPlan | ISubscribedStripePlan;
}

const SubscribedCard = ({ data }: Props) => {
  const { t, i18n } = useTranslation(['profile']);
  const { showError, showSuccess } = useShowMessage();

  const [cancelStripeSubscription, { isLoading, isSuccess }] =
    useCancelStripeSubscriptionMutation();

  useEffect(() => {
    if (isSuccess) {
      showSuccess(t('payment.subscriptionCanceled', { ns: 'messages' }));
    }
  }, [isSuccess]);

  const subscription = data.subscription;
  const plan = data.plan;

  if (!subscription) {
    return null;
  }
  const subscriptionType = subscription.subscriptionType;

  const handleCancelSubscription = () => {
    try {
      if (subscriptionType === 'stripe') {
        cancelStripeSubscription();
      }
    } catch (e) {
      console.log('Failed to open store', e);
      showError(t('somethingWrong', { ns: 'messages' }));
    }
  };

  const languageKey = i18n.language;
  const isJapanese = languageKey === 'ja';

  let features,
    featuresJa,
    planName = '';
  if (
    subscriptionType === 'stripe' ||
    (subscriptionType === 'esewa' && subscription?.paymentFrom === 'web')
  ) {
    const planData = plan as IStripePlan;
    features = planData?.features?.[languageKey];
    featuresJa = planData?.features?.[languageKey];
    planName = planData?.name?.[languageKey] ?? '';
  } else {
    const planData = plan as IInAppPlanData;
    features = planData?.features;
    featuresJa = planData?.featuresJa;
    planName = isJapanese ? planData?.nameJa : planData?.name ?? '';
  }
  const hasJapaneseFeatures = featuresJa?.length ? true : false;
  const featuresList = isJapanese
    ? hasJapaneseFeatures
      ? featuresJa
      : features
    : features;

  return (
    <div className="border-2 border-BLUE_004D80 shadow-md flex flex-col full-width card gap-1 mb-4 mt-9 py-4 px-6">
      {isLoading && <Spin fullscreen />}
      <Title type="heading2" className={'text-BLUE_004D80 text-center'}>
        {t('subscription.premiumUser')}
      </Title>
      <AppLogo type="secondary" small className="flex self-center" />
      <Title type="body1" className="text-center">
        {subscriptionType === 'trial'
          ? t('subscription.oneMonthCampaign')
          : `${planName} ${t('subscription.subscription')}`}
      </Title>
      {subscription.remainingDays > 0 ? (
        <Title type="heading2" className="text-center">
          {t('subscription.daysRemaining1')}
          <Title type="heading1" className="text-center" bold>
            {subscription.remainingDays}
          </Title>
          {t('subscription.daysRemaining2')}
        </Title>
      ) : (
        <Title type="heading2" className="text-center">
          {t('subscription.endsAt1')}
          {getTimeInHoursMinutes(subscription.endsAt)}
          {t('subscription.endsAt2')}
        </Title>
      )}
      {subscriptionType === 'stripe' &&
        !['cancel', 'expired', 'trial'].includes(
          subscription?.subscriptionStatus ?? '',
        ) && (
          <CustomButton
            type="link"
            className="text-center"
            title={t('recurringBill')}
            onClick={() => {
              handleCancelSubscription();
            }}
          />
        )}
      {featuresList && featuresList.length > 0 && (
        <>
          <div className={'bg-GRAY_ADAFBB my-4 h-[1px]'} />
          <Title type="body2">{t('subscription.youHaveAccessTo')}</Title>
          {featuresList.map(item => {
            return (
              <div key={item} className="flex gap-2 items-baseline">
                <CheckMark className={'text-GRAY_807C83'} />
                <Title type="caption1">{item}</Title>
              </div>
            );
          })}
        </>
      )}
    </div>
  );
};

export default SubscribedCard;
