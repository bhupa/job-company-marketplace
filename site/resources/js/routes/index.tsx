import React, { useEffect } from 'react';

import { ConfigProvider } from 'antd';
import {
  createBrowserRouter,
  Navigate,
  RouterProvider,
} from 'react-router-dom';

// Hooks
import useUserProfile from '@customHooks/useUserProfile';
import { useShowMessage } from '@customHooks/useShowMessage';
import useSocketService from '@customHooks/useSocketService';

// Redux
import { setErrorMessage } from '@redux/reducers/auth';
import { useAppDispatch, useAppSelector } from '@redux/hook';
import { useChangeLanguageMutation } from '@redux/services/authApi';

// Pages
import {
  HomeCompanyDetail,
  HomeJobSeekerDetail,
  HomeScreen,
  RequestCompanyDetail,
} from '@pages/home';
import {
  ContinueSkip,
  EmailOTPScreen,
  ForgotPassword,
  Login,
  ResetPassword,
} from '@pages/auth';
import {
  AboutYou,
  Country,
  Gender,
  JobSeekerPersonalSettings,
  ProfileDetail,
  UserBio,
} from '@pages/jobseeker';
import {
  AccountSettings,
  ChangePassword,
  VerifyAccount,
  VerifyEmail,
} from '@pages/profile';
import { Landing } from '@pages/landing';
import { Bookmark } from '@pages/bookmarks';
import PrivacyPolicy from '@pages/PrivacyPolicy';
import { CompanyProfileDetail } from '@pages/company';
import { CompanyProfile } from '@pages/profile/company';
import TermsAndConditions from '@pages/TermsAndConditions';
import { JobSeekerProfile } from '@pages/profile/jobSeeker';
import { ChatList, ChatPolicy, ChatScreen } from '@pages/chat';
import AccountDeletionGuide from '@pages/AccountDeletionGuide';
import { EnterEmail, EnterPassword, Signup } from '@pages/auth/signup';
import { CreateJob, JobCreationPolicy, JobDetail, JobList } from '@pages/jobs';

// Others
import i18n from '../lang';

const publicRoutes = [
  {
    path: '/',
    element: <Landing />,
  },
  {
    path: '/login',
    element: <Login />,
  },
  {
    path: '/signup',
    element: <Signup />,
  },
  {
    path: '/enter-email',
    element: <EnterEmail />,
  },
  {
    path: '/enter-password',
    element: <EnterPassword />,
  },
  {
    path: '/forgot-password',
    element: <ForgotPassword />,
  },
  {
    path: '/reset-password',
    element: <ResetPassword />,
  },
  {
    path: '/email-otp',
    element: <EmailOTPScreen />,
  },
  {
    path: '/privacy-policy',
    element: <PrivacyPolicy />,
  },
  {
    path: '/terms-conditions',
    element: <TermsAndConditions />,
  },
  {
    path: '/user-policy',
    element: <PrivacyPolicy />,
  },
  {
    path: '/account-deletion-guide',
    element: <AccountDeletionGuide />,
  },
  {
    path: '*',
    hasErrorBoundary: true,
    element: <Navigate to="/login" replace />,
    errorElement: <Login />,
  },
];

const commonPrivateRoutes = [
  {
    path: '/home',
    element: <HomeScreen />,
  },
  {
    path: '/bookmark',
    element: <Bookmark />,
  },
  {
    path: '/chat-policy',
    element: <ChatPolicy />,
  },
  {
    path: '/chat',
    element: <ChatList />,
  },
  {
    path: '/chat-screen/:id',
    element: <ChatScreen />,
  },
  {
    path: '/change-password',
    element: <ChangePassword />,
  },
  {
    path: '/account-settings',
    element: <AccountSettings />,
  },
  {
    path: '/email-settings',
    element: <VerifyEmail />,
  },
  {
    path: '/verify-account',
    element: <VerifyAccount />,
  },
  {
    path: '/privacy-policy',
    element: <PrivacyPolicy />,
  },
  {
    path: '/terms-conditions',
    element: <TermsAndConditions />,
  },
  {
    path: '/user-policy',
    element: <PrivacyPolicy />,
  },
  {
    path: '/account-deletion-guide',
    element: <AccountDeletionGuide />,
  },
  {
    path: '*',
    hasErrorBoundary: true,
    element: <Navigate to="/home" replace />,
  },
];

const jobseekerPrivateRoutes = [
  {
    path: '/home/jobs/details',
    element: <HomeCompanyDetail />,
  },
  {
    path: '/home/company/details',
    element: <RequestCompanyDetail />,
  },
  {
    path: '/jobs/detail',
    element: <JobDetail />,
  },
  {
    path: '/profile',
    element: <JobSeekerProfile />,
  },
  {
    path: '/profile/detail',
    element: <JobSeekerPersonalSettings />,
  },
  ...commonPrivateRoutes,
];

const companyPrivateRoutes = [
  {
    path: '/home/jobseeker/details',
    element: <HomeJobSeekerDetail />,
  },
  {
    path: '/profile',
    element: <CompanyProfile />,
  },
  {
    path: '/jobs',
    element: <JobList />,
  },
  {
    path: '/jobs/detail',
    element: <JobDetail />,
  },
  {
    path: '/jobs/policy',
    element: <JobCreationPolicy />,
  },
  {
    path: '/jobs/create',
    element: <CreateJob />,
  },
  {
    path: '/profile/detail',
    element: <CompanyProfileDetail />,
  },
  ...commonPrivateRoutes,
];

const jobseekerAuthRoutes = [
  {
    path: '/terms',
    element: <TermsAndConditions />,
  },
  {
    path: '/profile/detail',
    element: <ProfileDetail />,
  },
  {
    path: '/gender',
    element: <Gender />,
  },
  {
    path: '/continue',
    element: <ContinueSkip />,
  },
  {
    path: '/country',
    element: <Country />,
  },
  {
    path: '/about-you',
    element: <AboutYou />,
  },
  {
    path: '/user-bio',
    element: <UserBio />,
  },
  {
    path: '*',
    hasErrorBoundary: true,
    element: <Navigate to="/terms" replace />,
  },
];

const companyAuthRoutes = [
  {
    path: '/terms',
    element: <TermsAndConditions />,
  },
  {
    path: '/profile/detail',
    element: <CompanyProfileDetail />,
  },
  {
    path: '/continue',
    element: <ContinueSkip />,
  },
  {
    path: '/jobs/policy',
    element: <JobCreationPolicy />,
  },
  {
    path: '/profile/jobs/create',
    element: <CreateJob />,
  },
  {
    path: '*',
    hasErrorBoundary: true,
    element: <Navigate to="/terms" replace />,
  },
];

const AppRouter = () => {
  const { showError } = useShowMessage();
  const dispatch = useAppDispatch();
  const { errorMessage } = useAppSelector(state => state.auth);
  const { user, isJobSeeker, accessToken, isLoggedIn, isProfileComplete } =
    useUserProfile();
  const [changeLanguage] = useChangeLanguageMutation();

  // Use socket-io
  useSocketService();

  useEffect(() => {
    if (errorMessage) {
      showError(errorMessage);
      dispatch(setErrorMessage(''));
    }
  }, [errorMessage]);

  useEffect(() => {
    if (accessToken) {
      handleUpdateLanguage();
    }
  }, [accessToken]);

  const handleUpdateLanguage = () => {
    try {
      const userLanguage = user.language ?? null;
      if (!userLanguage) {
        changeLanguage(i18n.language);
      } else if (userLanguage !== i18n.language) {
        localStorage.setItem('selectedLanguage', userLanguage);
        i18n.changeLanguage(userLanguage);
      }
    } catch (e) {
      console.log('set auth data error', e);
    }
  };

  const router = createBrowserRouter(
    accessToken
      ? isLoggedIn && isProfileComplete
        ? // Profile is complete, go to dashboard
          isJobSeeker
          ? jobseekerPrivateRoutes
          : companyPrivateRoutes
        : // Not logged in or incomplete profile, then go to user / company register screens
          isJobSeeker
          ? jobseekerAuthRoutes
          : companyAuthRoutes
      : // No access token, then go to auth screens
        publicRoutes,
  );

  return (
    <ConfigProvider
      theme={{
        token: {
          colorPrimary: '#004D80',
        },
      }}>
      <RouterProvider router={router} />
    </ConfigProvider>
  );
};

export default AppRouter;
