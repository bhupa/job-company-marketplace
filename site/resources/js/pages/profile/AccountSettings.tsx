import React, { useEffect, useState } from 'react';

import { Card, Spin } from 'antd';
import { useTranslation } from 'react-i18next';

// Components
import {
  // DeleteAccountModal,
  DeactivateAccountModal,
  DeleteAccountFormModal,
} from '@components/settings';
import { DashboardWrapper } from '@templates';
import { ConfirmationModal, Title } from '@components/common';

// Hooks
import { useShowMessage } from '@customHooks/useShowMessage';

// Redux
import {
  ISubscribedStripePlan,
  useCancelStripeSubscriptionMutation,
  useGetSubscribedPlanQuery,
} from '@redux/services/subscriptionApi';
import { useAppDispatch, useAppSelector } from '@redux/hook';
import { setNeedsSubscriptionRefresh } from '@redux/reducers/subscription';

const AccountSettings = () => {
  const { t } = useTranslation('profile');

  const { showError, showSuccess } = useShowMessage();

  const dispatch = useAppDispatch();
  const { needsRefresh } = useAppSelector(state => state.subscription);
  const {
    data: subscribedPlanData,
    isFetching: isSubscribedPlanLoading,
    refetch,
  } = useGetSubscribedPlanQuery(undefined, { refetchOnFocus: true });
  const [cancelStripeSubscription, { isLoading, isSuccess }] =
    useCancelStripeSubscriptionMutation();

  const [isConfirmUnsubscribeModalVisible, setConfirmUnsubscribeModalVisible] =
    useState<boolean>(false);
  const [isDeleteModalVisible, setDeleteModalVisible] =
    useState<boolean>(false);
  const [isDeactivateModalVisible, setDeactivateModalVisible] =
    useState<boolean>(false);
  const [
    isUnsubscribeConfirmationModalVisible,
    setUnsubscribeConfirmationModalVisible,
  ] = useState<boolean>(false);

  useEffect(() => {
    if (isSuccess) {
      showSuccess(t('payment.subscriptionCanceled', { ns: 'messages' }));
    }
  }, [isSuccess]);

  // Refresh subscription on subscription event
  useEffect(() => {
    if (needsRefresh) {
      refetch();
      dispatch(setNeedsSubscriptionRefresh(false));
    }
  }, [needsRefresh]);

  const subscriptionType =
    subscribedPlanData?.data?.subscription?.subscriptionType;
  const needsDeletionFromMobileApp =
    subscriptionType === 'iap' &&
    !['cancel', 'expired', 'trial'].includes(
      subscribedPlanData?.data?.subscription?.subscriptionStatus ?? '',
    );
  const subscribedPlanId = (subscribedPlanData?.data as ISubscribedStripePlan)
    ?.subscription?.stripeId;
  const shouldCancel =
    subscriptionType === 'stripe' &&
    !['cancel', 'expired', 'trial'].includes(
      subscribedPlanData?.data?.subscription?.subscriptionStatus ?? '',
    ) &&
    subscribedPlanId;

  const handleCancelSubscription = () => {
    try {
      cancelStripeSubscription();
    } catch (e) {
      console.log('Failed to cancel', e);
      showError(t('somethingWrong', { ns: 'messages' }));
    }
  };

  return (
    <DashboardWrapper>
      {isLoading && <Spin fullscreen />}
      {isSubscribedPlanLoading ? (
        <Spin />
      ) : (
        <Card
          className="w-full"
          title={<h2 className="text-center">{t('accountSettings')}</h2>}>
          {shouldCancel && (
            <Card
              type="inner"
              hoverable={true}
              title={
                <Title type="body2" className="text-RED_FF4D4D border-0">
                  {t('subscription.cancelSubscription')}
                </Title>
              }
              onClick={() => setConfirmUnsubscribeModalVisible(true)}>
              <Title type="caption1">
                {t('subscription.cancelSubscriptionDescription')}
              </Title>
            </Card>
          )}
          <Card
            type="inner"
            hoverable={true}
            className="mt-4"
            title={
              <Title type="body2" className="text-RED_FF4D4D border-0">
                {t('deactivateAccount.title')}
              </Title>
            }
            onClick={() =>
              shouldCancel || needsDeletionFromMobileApp
                ? setUnsubscribeConfirmationModalVisible(true)
                : setDeactivateModalVisible(true)
            }>
            <Title type="caption1">{t('deactivateAccount.description')}</Title>
          </Card>
          <Card
            type="inner"
            hoverable={true}
            className="mt-4"
            title={
              <Title type="body2" className="text-RED_FF4D4D">
                {t('deleteAccount.title')}
              </Title>
            }
            onClick={() =>
              shouldCancel || needsDeletionFromMobileApp
                ? setUnsubscribeConfirmationModalVisible(true)
                : setDeleteModalVisible(true)
            }>
            <Title type="caption1">{t('deleteAccount.description')}</Title>
          </Card>
        </Card>
      )}
      {/* {isDeleteModalVisible && (
        <DeleteAccountModal
          isVisible={isDeleteModalVisible}
          setModalVisible={setDeleteModalVisible}
        />
      )} */}
      {isDeleteModalVisible && (
        <DeleteAccountFormModal
          isVisible={isDeleteModalVisible}
          setModalVisible={setDeleteModalVisible}
        />
      )}
      {isDeactivateModalVisible && (
        <DeactivateAccountModal
          isVisible={isDeactivateModalVisible}
          setModalVisible={setDeactivateModalVisible}
        />
      )}
      {isUnsubscribeConfirmationModalVisible && (
        <ConfirmationModal
          title={
            needsDeletionFromMobileApp
              ? t('subscription.cancelSubscriptionFromMobile')
              : t('subscription.unsubscribeInformation')
          }
          hideCancel={needsDeletionFromMobileApp}
          closeModal={() => setUnsubscribeConfirmationModalVisible(false)}
          onOkPress={() => {
            setUnsubscribeConfirmationModalVisible(false);
            !needsDeletionFromMobileApp && handleCancelSubscription();
          }}
        />
      )}
      {isConfirmUnsubscribeModalVisible && (
        <ConfirmationModal
          title={t('subscription.cancelSubscriptionConfirmation')}
          closeModal={() => setConfirmUnsubscribeModalVisible(false)}
          onOkPress={() => {
            setConfirmUnsubscribeModalVisible(false);
            handleCancelSubscription();
          }}
        />
      )}
      {isConfirmUnsubscribeModalVisible && (
        <ConfirmationModal
          title={t('subscription.cancelSubscriptionConfirmation')}
          closeModal={() => setConfirmUnsubscribeModalVisible(false)}
          onOkPress={() => {
            setConfirmUnsubscribeModalVisible(false);
            handleCancelSubscription();
          }}
        />
      )}
    </DashboardWrapper>
  );
};

export default AccountSettings;
