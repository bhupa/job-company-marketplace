import React, { useEffect } from 'react';

import { useTranslation } from 'react-i18next';
import AppleSignin from 'react-apple-signin-auth';

// Components
import { IconButton } from '@components/common';

// Hooks
import useUserProfile from '@customHooks/useUserProfile';
import { useShowMessage } from '@customHooks/useShowMessage';
import useJobSeekerProfileInput from '@customHooks/useJobSeekerProfileInput';

// Redux
import { SOCIALPROVIDER, useSocialAuthMutation } from '@redux/services/authApi';

// Others
import { AppleLogo } from '@assets/icons';

const { VITE_APPLE_REDIRECT_URL = '', VITE_APPLE_SERVICE_ID = '' } = import.meta
  .env;

const AppleLoginButton = () => {
  const { t } = useTranslation('messages');
  const { showError } = useShowMessage();
  const { userType, handleSetAuthData } = useUserProfile();
  const [appleLogin, { data, isLoading, isSuccess }] = useSocialAuthMutation();
  const { handleSetProfileData } = useJobSeekerProfileInput();

  // When Facebook login is success, set token and user data in redux state
  useEffect(() => {
    if (isSuccess && data) {
      handleSetAuthData({
        isLoggedIn: data.data.user.isProfileComplete,
        accessToken: data.data.token,
        user: data.data.user,
        provider: data.data.provider,
      });
      const userName = data.data.username ?? '';
      const words = userName.split(' ');
      const [firstName, ...lastName] = words;
      handleSetProfileData({ firstName, lastName: lastName.join(' ') });
    }
  }, [isSuccess]);

  return (
    <AppleSignin
      /** Auth options passed to AppleID.auth.init() */
      authOptions={{
        clientId: VITE_APPLE_SERVICE_ID,
        /** Requested scopes, seperated by spaces - eg: 'email name' */
        scope: 'email name',
        /** Apple's redirectURI */
        redirectURI: VITE_APPLE_REDIRECT_URL,
        /** State string that is returned with the apple response */
        state: 'state',
        /** Nonce */
        nonce: 'nonce',
        /** Uses popup auth instead of redirection */
        usePopup: true,
      }}
      uiType="light"
      /** Removes default style tag */
      noDefaultStyle={false}
      /** Called upon signin success in case authOptions.usePopup = true -- which means auth is handled client side */
      onSuccess={(response: any) => {
        console.log(response);
        const token = response.authorization?.id_token;
        if (token) {
          appleLogin({
            provider: SOCIALPROVIDER.APPLE,
            user_type: userType,
            token,
          });
        }
      }}
      /** Called upon signin error */
      onError={(error: any) => {
        console.log('apple login error', error);
        showError(t('somethingWrong'));
      }}
      /** Skips loading the apple script if true */
      skipScript={false}
      render={({ onClick }: any) => (
        <IconButton onClick={onClick} disabled={isLoading}>
          <AppleLogo width={32} height={32} />
        </IconButton>
      )}
    />
  );
};

export default AppleLoginButton;
