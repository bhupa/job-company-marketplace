import React, { useEffect } from 'react';

import { useGoogleLogin } from '@react-oauth/google';

// Components
import { IconButton } from '@components/common';

// Hooks
import useUserProfile from '@customHooks/useUserProfile';
import useJobSeekerProfileInput from '@customHooks/useJobSeekerProfileInput';

// Redux
import { SOCIALPROVIDER, useSocialAuthMutation } from '@redux/services/authApi';

// Others
import { GoogleLogo } from '@assets/icons';

const GoogleLoginButton = () => {
  const { userType, handleSetAuthData } = useUserProfile();
  const [googleLogin, { data, isLoading, isSuccess }] = useSocialAuthMutation();

  const { handleSetProfileData } = useJobSeekerProfileInput();

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

  const login = useGoogleLogin({
    onSuccess: tokenResponse => {
      googleLogin({
        provider: SOCIALPROVIDER.GOOGLE,
        user_type: userType,
        token: tokenResponse.access_token,
      });
    },
  });

  return (
    <IconButton onClick={() => login()} disabled={isLoading}>
      <GoogleLogo width={32} height={32} />
    </IconButton>
  );
};

export default GoogleLoginButton;
