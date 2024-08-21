import { createApi } from '@reduxjs/toolkit/query/react';

import { ICompany } from './companyApi';
import { UserType } from '@redux/reducers/auth';
import { baseQuery } from '../helpers/baseQuery';
import { IJobSeekerProfile } from './jobSeekerApi';
import { ApiResponse, API_Methods } from '../helpers/types';

export const authApi = createApi({
  reducerPath: 'authApi',
  baseQuery: baseQuery,
  tagTypes: [
    'Jobs',
    'Company',
    'JobSeeker',
    'MatchingJobs',
    'MatchingJobSeeker',
    'MatchingRequest',
    'ChatList',
    'AccountVerification',
    'SubscribedPlan',
  ],
  endpoints: builder => ({
    checkPhone: builder.mutation<ApiResponse<any>, IPhoneParams>({
      query: params => ({
        url: '/check-phone',
        method: API_Methods.POST,
        body: params,
      }),
    }),
    registerUser: builder.mutation<
      ApiResponse<IRegisterResponse>,
      IRegisterParams
    >({
      query: params => ({
        url: '/register',
        method: API_Methods.POST,
        body: params,
      }),
    }),
    login: builder.mutation<ApiResponse<IRegisterResponse>, ILoginParams>({
      query: params => ({
        url: '/login',
        method: API_Methods.POST,
        body: params,
      }),
    }),
    socialAuth: builder.mutation<
      ApiResponse<IRegisterResponse>,
      ISocialAuthParams
    >({
      query: params => ({
        url: '/social-login',
        method: API_Methods.POST,
        body: params,
      }),
    }),
    forgotPassword: builder.mutation<
      ApiResponse<IForgotResponse>,
      IForgotParams
    >({
      query: params => ({
        url: '/forgot-password',
        method: API_Methods.POST,
        body: params,
      }),
    }),
    verifyOtp: builder.mutation<ApiResponse<IForgotResponse>, string>({
      query: otpCode => ({
        url: `/verify-otp?otp=${otpCode}`,
        method: API_Methods.GET,
      }),
    }),
    resetPassword: builder.mutation<ApiResponse<[]>, IResetPasswordParams>({
      query: params => ({
        url: '/reset-password',
        method: API_Methods.POST,
        body: params,
      }),
    }),
    changePassword: builder.mutation<ApiResponse<[]>, IChangePasswordParams>({
      query: params => ({
        url: '/change-password',
        method: API_Methods.POST,
        body: params,
      }),
      invalidatesTags: ['JobSeeker', 'Company'],
    }),
    logout: builder.mutation<ApiResponse<any>, void>({
      query: () => ({
        url: '/logout',
        method: API_Methods.GET,
      }),
    }),
    changeAccountStatus: builder.mutation<ApiResponse<any>, AccountStatusType>({
      query: status => ({
        url: '/user/change-status',
        method: API_Methods.POST,
        body: {
          status,
        },
      }),
    }),
    deleteAccount: builder.mutation<ApiResponse<any>, string>({
      query: userId => ({
        url: `/user/${userId}`,
        method: API_Methods.DELETE,
      }),
    }),
    otpCount: builder.mutation<ApiResponse<any>, void>({
      query: () => ({
        url: '/otp-count',
        method: API_Methods.POST,
      }),
    }),
    refreshToken: builder.query<ApiResponse<any>, void>({
      query: () => ({
        url: '/refresh-token',
        method: API_Methods.GET,
      }),
    }),
    verifyAccount: builder.mutation<ApiResponse<IUser>, FormData>({
      query: params => ({
        url: '/verify-account',
        method: API_Methods.POST,
        body: params,
      }),
      invalidatesTags: ['AccountVerification', 'Company', 'JobSeeker'],
    }),
    getVerificationDetail: builder.query<ApiResponse<IUser>, void>({
      query: () => ({
        url: '/verify-account',
        method: API_Methods.GET,
      }),
      forceRefetch: () => true,
      providesTags: ['AccountVerification'],
    }),
    verifyEmail: builder.mutation<ApiResponse<null>, void>({
      query: () => ({
        url: '/email/verification-notification',
        method: API_Methods.POST,
      }),
      invalidatesTags: ['Company', 'JobSeeker'],
    }),
    updateEmail: builder.mutation<ApiResponse<null>, string>({
      query: email => ({
        url: '/update-email',
        method: API_Methods.POST,
        body: {
          email,
        },
      }),
      invalidatesTags: ['Company', 'JobSeeker'],
    }),
    changeLanguage: builder.mutation<ApiResponse<null>, string>({
      query: lang => ({
        url: '/lang-set',
        method: API_Methods.POST,
        body: {
          language: lang,
        },
      }),
      invalidatesTags: ['Company', 'JobSeeker'],
    }),
    deleteAccountForm: builder.mutation<
      ApiResponse<any>,
      IDeleteAccountFormParams
    >({
      query: params => ({
        url: '/reason-for-cancellation',
        method: API_Methods.POST,
        body: params,
      }),
    }),
  }),
});

export const {
  useRegisterUserMutation,
  useCheckPhoneMutation,
  useLoginMutation,
  useSocialAuthMutation,
  useForgotPasswordMutation,
  useVerifyOtpMutation,
  useResetPasswordMutation,
  useLogoutMutation,
  useChangePasswordMutation,
  useChangeAccountStatusMutation,
  useDeleteAccountMutation,
  useOtpCountMutation,
  useRefreshTokenQuery,
  useVerifyAccountMutation,
  useGetVerificationDetailQuery,
  useVerifyEmailMutation,
  useUpdateEmailMutation,
  useChangeLanguageMutation,
  useDeleteAccountFormMutation,
} = authApi;

export interface IRegisterParams {
  user_type: UserType;
  country_code: string;
  phone: string;
  email: string;
  password: string;
  password_confirmation: string;
}

export interface IRegisterResponse {
  provider: SOCIALPROVIDER;
  token: string;
  user: IUser;
  jobseeker?: IJobSeekerProfile;
  company?: ICompany;
  username?: string;
}

export interface IUser {
  id: string;
  email: string;
  countryCode: number;
  phone: string;
  userType: UserType;
  status: AccountStatusType;
  language: string;
  isProfileComplete: boolean;
  isPasswordSet: boolean;
  deviceToken: string;
  appleId: string;
  facebookId: string;
  googleId: string;
  isEmailVerified: boolean;
  isViolation: boolean;
  verificationStatus: VerificationStatus;
  verification: {
    documents: { id: string; image: string; fileType: string }[];
    comment: string;
  };
  introVideo: string;
}

export interface IPhoneParams {
  phone?: string;
  country_code?: string;
  email?: string;
}

export interface ILoginParams {
  email?: string;
  password: string;
  phone?: string;
}

export interface ISocialAuthParams {
  user_type: UserType;
  provider: SOCIALPROVIDER;
  token: string;
}

export enum SOCIALPROVIDER {
  GOOGLE = 'GOOGLE',
  FACEBOOK = 'FACEBOOK',
  APPLE = 'APPLE',
  DEFAULT = 'DEFAULT',
}

export interface IForgotParams {
  phone?: string;
  email?: string;
}

export interface IForgotResponse {
  userId: number;
  email: string;
  countryCode: number;
  phone: string;
}

export interface IResetPasswordParams {
  user_id: number;
  password: string;
  password_confirmation: string;
}

export interface IChangePasswordParams {
  old_password?: string;
  password: string;
  password_confirmation: string;
}

export enum AccountStatusType {
  ACTIVE = 1,
  DEACTIVATED = 2,
  RESTRICTED = 3,
  DELETED = 4,
}

export enum VerificationStatus {
  PENDING = 'PENDING',
  REJECTED = 'REJECTED',
  APPROVED = 'APPROVED',
}

export interface IDeleteAccountFormParams {
  reason: string;
  sub_reason?: string;
  future_plan: string;
  comment: string;
}
