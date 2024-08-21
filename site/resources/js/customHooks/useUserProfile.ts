import Cookies from 'js-cookie';
import { googleLogout } from '@react-oauth/google';

// Redux
import {
  SOCIALPROVIDER,
  authApi,
  useLogoutMutation,
} from '@redux/services/authApi';
import { useAppDispatch, useAppSelector } from '@redux/hook';
import { useGetSubscribedPlanQuery } from '@redux/services/subscriptionApi';
import { IAuthData, UserType, logout, saveUser } from '@redux/reducers/auth';

const useUserProfile = () => {
  const dispatch = useAppDispatch();
  const { userType, signupData, isLoggedIn, user, provider } = useAppSelector(
    state => state.auth,
  );

  const accessToken = Cookies.get('accessToken');

  const [logoutFromApi] = useLogoutMutation();
  const { data: subscribedPlanData } = useGetSubscribedPlanQuery(undefined, {
    skip: !isLoggedIn,
  });

  /**
   * Logout from social providers and app server according to login type
   * If is logging out due to account deletion, no need to logout from server as all data is already deleted.
   * @param shouldLogoutFromApi
   */
  const handleLogout = (shouldLogoutFromApi = true) => {
    try {
      if (provider === SOCIALPROVIDER.GOOGLE) {
        googleLogout();
      }
      if (shouldLogoutFromApi) {
        logoutFromApi();
      }
      dispatch(authApi.util.resetApiState());
      dispatch(logout());
    } catch (e) {
      console.log('logout err', e);
    }
  };

  const handleSetAuthData = (userData: IAuthData) => {
    dispatch(saveUser(userData));
  };

  return {
    user,
    userType,
    signupData,
    isProfileComplete: user?.isProfileComplete,
    isJobSeeker: userType === UserType.JobSeeker,
    provider,
    accessToken,
    isLoggedIn,
    isSubscribed: subscribedPlanData?.data?.isSubscribed ?? false,
    subscription: subscribedPlanData?.data?.subscription ?? null,
    handleLogout,
    handleSetAuthData,
  };
};

export default useUserProfile;
