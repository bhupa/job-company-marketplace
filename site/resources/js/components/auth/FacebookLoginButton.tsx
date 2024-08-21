import React, { useEffect } from 'react';

import { useTranslation } from 'react-i18next';
import FacebookLogin from '@greatsumini/react-facebook-login';

// Components
import { IconButton } from '@components/common';

// Hooks
import useUserProfile from '@customHooks/useUserProfile';
import useJobSeekerProfileInput from '@customHooks/useJobSeekerProfileInput';

// Redux
import { SOCIALPROVIDER, useSocialAuthMutation } from '@redux/services/authApi';

// Others
import { FacebookLogo } from '@assets/icons';
import { useShowMessage } from '@customHooks/useShowMessage';

const { VITE_FACEBOOK_APP_ID = '' } = import.meta.env;

const FacebookLoginButton = () => {
  const { t } = useTranslation('messages');
  const { showError } = useShowMessage();
  const { userType, handleSetAuthData } = useUserProfile();
  const [facebookLogin, { data, isLoading, isSuccess }] =
    useSocialAuthMutation();
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

  const handleGetFbToken = (response: any) => {
    const token = response.accessToken;
    if (token) {
      facebookLogin({
        provider: SOCIALPROVIDER.FACEBOOK,
        user_type: userType,
        token,
      });
    } else {
      showError(t('facebook.loginFailed'));
    }
  };

  return (
    <FacebookLogin
      appId={VITE_FACEBOOK_APP_ID}
      autoLoad={false}
      fields="name,email"
      onSuccess={handleGetFbToken}
      onFail={(error: any) => {
        console.log('Login Failed!', error);
        showError(t('facebook.loginFailed'));
      }}
      render={renderProps => (
        <IconButton onClick={renderProps.onClick} disabled={isLoading}>
          <FacebookLogo width={32} height={32} />
        </IconButton>
      )}
    />
  );
};

export default FacebookLoginButton;
