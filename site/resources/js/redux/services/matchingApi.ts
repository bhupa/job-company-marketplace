import { IUser, authApi } from './authApi';
import {
  ApiResponse,
  API_Methods,
  PaginatedData,
  IPaginationParams,
} from '../helpers/types';
import { IJobData } from './jobsApi';
import { ICompany } from './companyApi';
import { IJobSeekerProfile } from './jobSeekerApi';

export const matchingApi = authApi.injectEndpoints({
  endpoints: builder => ({
    swipeRequestMatching: builder.mutation<
      ApiResponse<ISwipeMatchingResponse>,
      FormData
    >({
      query: formData => {
        return {
          url: '/matching/request',
          method: API_Methods.POST,
          body: formData,
        };
      },
    }),
    favoriteMatching: builder.mutation<
      ApiResponse<IFavoriteMatchingResponse>,
      IFavoriteMatchingParams
    >({
      query: params => {
        return {
          url: '/matching/favourite',
          method: API_Methods.POST,
          body: params,
        };
      },
    }),
    getMatchingRequests: builder.query<
      ApiResponse<IMatchingRequestResponse>,
      IMatchingRequestsQueryParams
    >({
      query: ({ per_page = 10, page = 1, ...rest }) => ({
        url: '/matching',
        method: API_Methods.GET,
        params: {
          page,
          per_page,
          ...rest,
        },
      }),
      forceRefetch: () => true,
      providesTags: ['MatchingRequest'],
    }),
    confirmMatchingRequest: builder.mutation<
      ApiResponse<any>,
      IConfirmMatchingRequestParams
    >({
      query: params => {
        const { requestId, type } = params;
        return {
          url: `/matching/accept/${requestId}`,
          method: API_Methods.POST,
          body: {
            type,
          },
        };
      },
      invalidatesTags: ['ChatList'],
    }),
    // Send chat request to user
    sendChatRequest: builder.mutation<
      ApiResponse<IChatRequestResponse>,
      IChatRequestParams
    >({
      query: params => {
        return {
          url: '/matching/chat-request/',
          method: API_Methods.POST,
          body: params,
        };
      },
    }),
    // Rewind left swiped cards
    rewind: builder.mutation<ApiResponse<any>, IRewindParams>({
      query: params => {
        return {
          url: '/matching/rewind',
          method: API_Methods.POST,
          body: params,
        };
      },
    }),
  }),
});

export const {
  useSwipeRequestMatchingMutation,
  useFavoriteMatchingMutation,
  useGetMatchingRequestsQuery,
  useConfirmMatchingRequestMutation,
  useSendChatRequestMutation,
  useRewindMutation,
} = matchingApi;

// 0 = swipe left and 1 = swipe right
export type SWIPE_TYPE = 0 | 1;
export type MATCHING_TYPE = 'accept' | 'refuse';

export interface IRequestMatchingParams {
  job_id?: string;
  job_seeker_id?: string;
  type: SWIPE_TYPE;
}

export interface IFavoriteMatchingParams {
  job_id?: string;
  job_seeker_id?: string;
  favourite: SWIPE_TYPE; // 0 = false, 1 = true
}

export interface IMatchingRequestsQueryParams extends IPaginationParams {
  type: 'sent' | 'received' | 'match' | 'favourite';
}

export interface IConfirmMatchingRequestParams {
  type: MATCHING_TYPE;
  requestId: string;
}

export interface IFavoriteRequestParams {
  favoriteId: string;
}

interface IMatchingRequestResponse {
  matched: number;
  unmatched: number;
  items: PaginatedData<IRequestResponse>;
}

export interface IRequestResponse {
  id: string;
  company: ICompany;
  createdBy: IUser;
  job: IJobData[];
  jobseeker: IJobSeekerProfile;
  matched: string | null;
  unmatched: string | null;
  isPaid?: boolean;
  isRequestSent?: boolean;
  room?: string;
  requestFavouriteId: string;
}

export interface IFavoriteMatchingResponse {
  items: {
    id: string;
    company: ICompany;
    createdBy: IUser;
    job: IJobData;
    jobseeker: IJobSeekerProfile;
    matched: string | null;
    unmatched: string | null;
    isPaid: boolean;
    requestFavouriteId: string;
  };
  dailyCount: number;
  favouriteCount: number;
}

export interface ISwipeMatchingResponse {
  dailyCount: number;
  favouriteCount: number;
  items: IRequestResponse[];
  matchedData: IRequestResponse[];
}

export interface IChatRequestParams {
  job_id?: string;
  job_seeker_id?: string;
  message?: string;
}

export interface IChatRequestResponse {
  items: {
    id: string;
    company: ICompany;
    createdBy: IUser;
    job: IJobData;
    jobseeker: IJobSeekerProfile;
    matched: string | null;
    unmatched: string | null;
    isPaid: boolean;
    requestFavouriteId: string;
  };
  chatRequest: number;
}

export interface IRewindParams {
  job_id?: string;
  job_seeker_id?: string;
}
