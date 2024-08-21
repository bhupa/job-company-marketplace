import { IUser, authApi } from './authApi';
import { ApiResponse, API_Methods } from '../helpers/types';

export const subscriptionApi = authApi.injectEndpoints({
  endpoints: builder => ({
    getPlanList: builder.query<IStripePlan[], void>({
      query: () => ({
        url: '/plan-lists',
        method: API_Methods.GET,
      }),
      transformResponse(baseQueryReturnValue: ApiResponse<IStripePlan[]>) {
        const data: IStripePlan[] = baseQueryReturnValue.data;
        return data.sort((a, b) => a.order - b.order);
      },
    }),
    addSubscription: builder.mutation<
      IAddSubscriptionResponse,
      IAddSubscriptionParams
    >({
      query: params => {
        return {
          url: '/add-plan',
          method: API_Methods.POST,
          body: params,
        };
      },
      invalidatesTags: ['Company', 'JobSeeker'],
    }),
    getStripePaymentIntent: builder.mutation<
      ApiResponse<IStripePaymentIntentResponse>,
      IPaymentIntentParams
    >({
      query: params => {
        return {
          url: '/stripe-payment-intent',
          method: API_Methods.POST,
          body: params,
        };
      },
    }),
    getSubscribedPlan: builder.query<
      ApiResponse<ISubscribedInAppPlan | ISubscribedStripePlan>,
      void
    >({
      query: () => ({
        url: '/subscribed-plan',
        method: API_Methods.GET,
      }),
      providesTags: ['SubscribedPlan'],
    }),
    getInAppPurchaseSkus: builder.query<ApiResponse<IInAppPurchaseSkus>, void>({
      query: () => ({
        url: '/iap/skus',
        method: API_Methods.GET,
      }),
    }),
    validateProductPurchase: builder.mutation<
      ApiResponse<any>,
      IPurchaseProductValidationParams
    >({
      query: params => {
        return {
          url: '/in-app-purchase',
          method: API_Methods.POST,
          body: params,
        };
      },
      invalidatesTags: ['Company', 'JobSeeker', 'SubscribedPlan'],
    }),
    cancelStripeSubscription: builder.mutation<ApiResponse<any>, void>({
      query: () => ({
        url: '/stripe-stop-autorenew',
        method: API_Methods.GET,
      }),
      invalidatesTags: ['SubscribedPlan'],
    }),
  }),
});

export const {
  useGetPlanListQuery,
  useAddSubscriptionMutation,
  useGetStripePaymentIntentMutation,
  useGetSubscribedPlanQuery,
  useLazyGetSubscribedPlanQuery,
  useGetInAppPurchaseSkusQuery,
  useValidateProductPurchaseMutation,
  useCancelStripeSubscriptionMutation,
} = subscriptionApi;

export interface IPlanFeatures {
  name: string;
}

interface IPriceData {
  currency: string;
  id: string;
  price: string;
  symbol: string;
}

export interface IInAppPlanData {
  price: { [key: string]: IPriceData };
  duration: string;
  features: string[];
  featuresJa: string[];
  id: string;
  name: string;
  nameJa: string;
}

interface IAddSubscriptionParams {
  // stripeToken: string;
  // stripe_plan: string;
  lookup_key: string;
}

interface IAddSubscriptionResponse {
  // subscribedPlan: {
  //   amount: number;
  //   currency: string;
  //   default_price: string;
  //   description: string;
  //   endsAt: string;
  //   features: IPlanFeatures[];
  //   id: string;
  //   name: string;
  //   status: string;
  //   subscriptionId: number;
  // };
  sessionId: string;
  session_url: string;
}

export interface IPaymentIntentParams {
  price: string;
  type: 'chat' | 'subscription';
  roomId?: string;
}

interface IStripePaymentIntentResponse {
  customer: string;
  ephemeralKey: string;
  paymentIntent: string;
  publishableKey: string;
}

export interface ISubscribedPlan {
  isSubscribed: boolean;
  subscription?: {
    plan: IInAppPlanData;
    endsAt: string;
    remainingDays: number;
    stripeId: string;
  };
}

export interface ISkus {
  [key: string]: {
    name: string;
    nameJa: string;
    timePeriod: {
      [key: string]: string;
    };
    features: string[];
    featuresJa: string[];
    price: {
      [key: string]: string;
    };
  };
}

export interface IInAppPurchaseSkus {
  [key: string]: {
    products: ISkus;
    subscription: ISkus;
    superChat: ISkus;
  };
}

export interface IPurchaseProductValidationParams {
  item_id: string;
  purchase_token: string;
  payment_type: 'google' | 'apple';
  transaction_receipt?: string;
  chat_room_id?: string;
  uniqueIdentifier: string;
}

export interface ISubscribedInAppPlan {
  isSubscribed: boolean;
  plan: IInAppPlanData;
  subscription: {
    endsAt: string;
    itemId: string;
    paymentFor: string;
    paymentType: string;
    purchaseToken: string;
    remainingDays: number;
    subscriptionStatus: 'active' | 'pending' | 'cancel' | 'expired' | 'trial';
    subscriptionType: 'iap' | 'stripe' | 'trial' | 'admin_pay' | 'esewa';
    paymentFrom?: 'web' | 'android' | 'ios';
    transactionReceipt: any;
    trialEndsAt: string;
    user: IUser;
    currency: string;
    orderId: string;
    price: string;
    storeUserId: string;
  };
}

export interface IStripePrice {
  id: string;
  price: string;
  currency: string;
  symbol: string;
  lookup: string;
}

export interface IStripePlan {
  id: string;
  order: number;
  timePeriod: {
    [key: string]: string;
  };
  name: {
    [key: string]: string;
  };
  features: {
    [key: string]: string[];
  };
  price: {
    [key: string]: IStripePrice;
  };
}

export interface ISubscribedStripePlan {
  isSubscribed: boolean;
  plan: IStripePlan;
  subscription: {
    endsAt: string;
    stripeId: string;
    remainingDays: number;
    subscriptionStatus: 'active' | 'pending' | 'cancel' | 'expired' | 'trial';
    subscriptionType: 'iap' | 'stripe' | 'trial' | 'admin_pay' | 'esewa';
    paymentFrom?: 'web' | 'android' | 'ios';
    user: IUser;
  };
}
