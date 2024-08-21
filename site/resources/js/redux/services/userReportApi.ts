import { authApi } from './authApi';
import { ApiResponse, API_Methods } from '../helpers/types';

export const userReportApi = authApi.injectEndpoints({
  endpoints: builder => ({
    reportUser: builder.mutation<ApiResponse<any>, IReportUserParams>({
      query: params => ({
        url: '/violation-report/store',
        method: API_Methods.POST,
        body: params,
      }),
    }),
  }),
});

export const { useReportUserMutation } = userReportApi;

export interface IReportUserParams {
  message?: string;
  user_id?: string;
  chat_room_id?: string; // for chat report
}
