import { IUser, authApi } from './authApi';
import { ApiResponse, API_Methods } from '../helpers/types';

export const notificationApi = authApi.injectEndpoints({
  endpoints: builder => ({
    registerDeviceToken: builder.mutation<
      ApiResponse<IDeviceTokenResponse>,
      string
    >({
      query: token => ({
        url: '/device-token',
        method: API_Methods.POST,
        body: { device_token: token },
      }),
    }),
  }),
});

export const { useRegisterDeviceTokenMutation } = notificationApi;

interface IDeviceTokenResponse {
  user: IUser;
}
