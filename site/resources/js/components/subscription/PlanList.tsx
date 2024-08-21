import React, { useEffect, useState } from 'react';

import { Grid, Modal, Spin } from 'antd';
import { useTranslation } from 'react-i18next';
import { useLocation } from 'react-router-dom';

// Components
import PlanCard from './PlanCard';
import SubscribedCard from './SubscribedCard';
import { CustomButton, Title } from '@components/common';

// Hooks
import useUserProfile from '@customHooks/useUserProfile';
import useEsewaPayment from '@customHooks/useEsewaPayment';
import { useShowMessage } from '@customHooks/useShowMessage';

// Others
import {
  IStripePlan,
  useAddSubscriptionMutation,
  useGetPlanListQuery,
  useGetSubscribedPlanQuery,
} from '@redux/services/subscriptionApi';
import { EsewaLogo, StripeLogo } from '@assets/icons';

const { useBreakpoint } = Grid;

const PlanList = () => {
  const { t, i18n } = useTranslation(['profile', 'messages']);
  const { showWarning, showError } = useShowMessage();

  const location = useLocation();
  const breakpoints = useBreakpoint();

  const [selectedIndex, setSelectedIndex] = useState<number>(0);
  const [selectedPlan, setSelectedPlan] = useState<IStripePlan>();
  const [isPaymentOptionsModalVisible, setPaymentOptionsModalVisible] =
    useState<boolean>(false);

  const { user, isJobSeeker } = useUserProfile();

  const { data, isLoading } = useGetPlanListQuery();
  const { data: subscribedPlanData, isFetching: isSubscribedPlanLoading } =
    useGetSubscribedPlanQuery();
  const { makePayment } = useEsewaPayment();

  const [
    addSubscription,
    {
      isLoading: isCheckoutLoading,
      data: checkoutData,
      isError: isCheckoutError,
    },
  ] = useAddSubscriptionMutation();

  useEffect(() => {
    if (isCheckoutError) {
      return showError(t('payment.paymentFailed', { ns: 'messages' }));
    }
    if (checkoutData?.session_url) {
      window.open(checkoutData.session_url, '_self');
    }
  }, [checkoutData, isCheckoutError]);

  // Set first item as default selected plan
  useEffect(() => {
    if (data?.length) {
      setSelectedPlan(data?.[0]);
    }
  }, [data]);

  const handleScroll = (event: any) => {
    const { target } = event;
    const { scrollLeft, scrollWidth, clientWidth } = target;
    const percentScrolled = (scrollLeft / (scrollWidth - clientWidth)) * 100;
    const equityBasis = 100 / (data?.length ?? 1);
    const index = Math.ceil(percentScrolled / equityBasis);
    const updatedIndex = index > 0 ? index - 1 : 0;
    updatedIndex !== selectedIndex && setSelectedIndex(updatedIndex);
  };

  const handleEsewaPayment = () => {
    if (selectedPlan) {
      const priceValue = selectedPlan.price.npr?.price ?? 10;
      const priceId = selectedPlan.price.npr?.id;
      const { VITE_BASE_URL } = import.meta.env;
      const returnPath = location.pathname.includes('home')
        ? 'home'
        : 'profile';

      const dateValue = Date.now();
      makePayment({
        amount: priceValue,
        product_delivery_charge: '0',
        product_service_charge: '0',
        success_url: `${VITE_BASE_URL}esewa/epay?url=${returnPath}&platform=web&type=subscription&user_id=${user.id}&price_id=${priceId}&`,
        tax_amount: '0',
        total_amount: priceValue,
        transaction_uuid: `subscription-user-${user.id}-` + dateValue,
      });
    }
  };

  return (
    <>
      {isLoading || isSubscribedPlanLoading ? (
        <Spin />
      ) : (
        <>
          {subscribedPlanData?.data?.isSubscribed ? (
            <SubscribedCard data={subscribedPlanData.data} />
          ) : (
            <>
              {data && (
                <>
                  <div className="my-5 gap-2 justify-center items-center flex">
                    {data?.map((item, index) => {
                      return (
                        <div
                          key={item.id}
                          className={`w-2 h-2 rounded-full ${selectedIndex === index ? 'bg-BLUE_004D80' : 'bg-BLUE_D9E3FF '}`}
                        />
                      );
                    })}
                  </div>
                  {data?.length > 0 && (
                    <>
                      <div
                        onScroll={e => handleScroll(e)}
                        className="flex gap-6 overflow-scroll">
                        {data.map(item => {
                          return (
                            <PlanCard
                              key={item.id}
                              data={item}
                              isSelected={selectedPlan?.id === item.id}
                              onPress={() => setSelectedPlan(item)}
                            />
                          );
                        })}
                      </div>
                      <div className="flex gap-4 md:gap-6 justify-between items-center">
                        <CustomButton
                          title={t('subscribeNow')}
                          className="my-4 w-full"
                          loading={isCheckoutLoading}
                          onClick={() => {
                            if (!selectedPlan) {
                              showWarning(
                                t('validation.selectPlanFirst', {
                                  ns: 'messages',
                                }),
                              );
                            }
                            if (isJobSeeker) {
                              setPaymentOptionsModalVisible(true);
                            } else {
                              selectedPlan &&
                                addSubscription({
                                  lookup_key:
                                    selectedPlan.price?.[i18n.language]?.lookup,
                                });
                            }
                          }}
                        />
                      </div>
                    </>
                  )}
                </>
              )}
            </>
          )}
        </>
      )}
      {isPaymentOptionsModalVisible && (
        <Modal
          centered
          open={true}
          closable={true}
          onCancel={() => setPaymentOptionsModalVisible(false)}
          footer={null}
          width={breakpoints.sm ? '44%' : '70%'}>
          {isLoading && <Spin fullscreen />}
          <Title
            type="body1"
            className="flex justify-center text-center mr-4"
            bold>
            {t('subscribeNow')}
          </Title>
          <div className="flex flex-col gap-3 justify-between items-center mt-4">
            <EsewaLogo
              className="w-full h-8 border rounded-md shadow-md hover:shadow-lg cursor-pointer"
              onClick={() => {
                handleEsewaPayment();
              }}
            />
            <StripeLogo
              className="w-full h-8 border rounded-md shadow-md hover:shadow-lg cursor-pointer"
              onClick={() => {
                if (!selectedPlan) {
                  showWarning(
                    t('validation.selectPlanFirst', { ns: 'messages' }),
                  );
                }
                selectedPlan &&
                  addSubscription({
                    lookup_key: selectedPlan.price?.[i18n.language]?.lookup,
                  });
              }}
            />
          </div>
        </Modal>
      )}
    </>
  );
};

export default PlanList;
