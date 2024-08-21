import { useEffect, useState } from 'react';

import { useTranslation } from 'react-i18next';
// import { PaymentSheetError, useStripe } from '@stripe/stripe-react-native';

// Components

// Others
import {
  IPaymentIntentParams,
  useGetStripePaymentIntentMutation,
  useLazyGetSubscribedPlanQuery,
} from '@redux/services/subscriptionApi';
import { useStripe } from '@stripe/react-stripe-js';
import { useShowMessage } from './useShowMessage';

const useStripePayment = (successCallback?: () => void) => {
  const { t } = useTranslation(['messages']);
  const { showSuccess, showError } = useShowMessage();

  const [isLoading, setLoading] = useState<boolean>(false);

  // const { initPaymentSheet, presentPaymentSheet } = useStripe();
  const [refetch] = useLazyGetSubscribedPlanQuery();

  const [getPaymentIntent, { isSuccess, data, isError }] =
    useGetStripePaymentIntentMutation();

  useEffect(() => {
    if (isError) {
      setLoading(false);
    }
  }, [isError]);

  useEffect(() => {
    if (isSuccess) {
      initializePaymentSheet();
    }
  }, [isSuccess]);

  const setUpPaymentSheet = (paymentIntentParams: IPaymentIntentParams) => {
    setLoading(true);
    getPaymentIntent(paymentIntentParams);
  };

  const initializePaymentSheet = async () => {
    if (!data?.data) {
      return;
    }
    const { paymentIntent, ephemeralKey, customer } = data.data;

    // const { error } = await initPaymentSheet({
    //   merchantDisplayName: 'Job Matchy',
    //   customerId: customer,
    //   customerEphemeralKeySecret: ephemeralKey,
    //   paymentIntentClientSecret: paymentIntent,
    //   // Set `allowsDelayedPaymentMethods` to true if your business can handle payment
    //   //methods that complete payment after a delay, like SEPA Debit and Sofort.
    //   allowsDelayedPaymentMethods: false,
    //   returnURL: 'job-matchy://stripe-redirect',
    //   // applePay: {
    //   //   merchantCountryCode: 'US',
    //   // },
    //   // googlePay: {
    //   //   merchantCountryCode: 'US',
    //   //   testEnv: true,
    //   // },
    // });
    if (!error) {
      openPaymentSheet();
    } else {
      setLoading(false);
      console.log('Error on init payment sheet', error);
      showError(t('payment.initiatePaymentFailed'));
    }
  };

  const openPaymentSheet = async () => {
    try {
      // const { error } = await presentPaymentSheet();
      // if (error) {
      //   showError(t(getStripeError(error.code)));
      // } else {
      //   successCallback && successCallback();
      //   showSuccess(t('payment.paymentSuccess'));
      //   setTimeout(() => refetch(), 1000);
      // }
    } catch (e) {
      console.log('catched', e);
    } finally {
      setLoading(false);
    }
  };

  const getStripeError = (errorCode: string) => {
    switch (errorCode) {
      // case PaymentSheetError.Canceled:
      //   return 'payment.paymentCanceled';
      // case PaymentSheetError.Failed:
      //   return 'payment.paymentFailed';
      // case PaymentSheetError.Timeout:
      //   return 'payment.couldnotConnectServer';
      default:
        return 'somethingWrong';
    }
  };

  return {
    isLoading,
    setUpPaymentSheet,
  };
};

export default useStripePayment;
