import { IUser, authApi } from './authApi';
import {
  ApiResponse,
  API_Methods,
  IPaginationParams,
  PaginatedData,
} from '../helpers/types';
import { IJobData } from './jobsApi';
import { ICompany } from './companyApi';
import { UserType } from '@redux/reducers/auth';
import { IRequestResponse } from './matchingApi';
import { IJobSeekerProfile } from './jobSeekerApi';
import { IInAppPlanData } from './subscriptionApi';

export const chatApi = authApi.injectEndpoints({
  endpoints: builder => ({
    getChatList: builder.query<
      ApiResponse<PaginatedData<IChatListResponse>>,
      IGetChatListParams
    >({
      query: ({ per_page = 10, page = 1, name = '' }) => ({
        url: '/chat/user-lists',
        method: API_Methods.GET,
        params: {
          per_page,
          page,
          name,
        },
      }),
      forceRefetch: () => true,
      providesTags: ['ChatList'],
    }),
    getChatMessages: builder.query<
      ApiResponse<IChatMessageResponse>,
      IGetChatMessageParams
    >({
      query: ({ per_page = 10, page = 1, roomId }) => ({
        url: `/chat-room/${roomId}`,
        method: API_Methods.GET,
        params: {
          per_page,
          page,
        },
      }),
      forceRefetch: () => true,
    }),
    storeChat: builder.mutation<ApiResponse<IChatItem>, IStoreChatParams>({
      query: params => ({
        url: '/chat/store',
        method: API_Methods.POST,
        body: params,
      }),
      // invalidatesTags: ['ChatList'],
    }),
    getSuperChatPrice: builder.query<ApiResponse<IInAppPlanData[]>, void>({
      query: () => ({
        url: '/chat-price',
        method: API_Methods.GET,
      }),
    }),
    markAllMessagesRead: builder.mutation<
      ApiResponse<any>,
      IGetChatMessageParams
    >({
      query: ({ roomId }) => ({
        url: `/chat-room/${roomId}`,
        method: API_Methods.GET,
      }),
    }),
    adminAssist: builder.mutation<ApiResponse<any>, string>({
      query: roomId => ({
        url: `/chat/email/${roomId}`,
        method: API_Methods.POST,
      }),
    }),
    superchatEmail: builder.mutation<ApiResponse<any>, string>({
      query: roomId => ({
        url: `/chat-room/superchat/${roomId}?type=web`,
        method: API_Methods.GET,
      }),
    }),
    deleteChatRoom: builder.mutation<ApiResponse<any>, string>({
      query: roomId => ({
        url: `/chat-room/${roomId}`,
        method: API_Methods.DELETE,
      }),
    }),
  }),
});

export const {
  useGetChatListQuery,
  useGetChatMessagesQuery,
  useStoreChatMutation,
  useGetSuperChatPriceQuery,
  useLazyGetSuperChatPriceQuery,
  useMarkAllMessagesReadMutation,
  useAdminAssistMutation,
  useDeleteChatRoomMutation,
  useSuperchatEmailMutation,
} = chatApi;

export interface IChatListResponse {
  adminAssit: string;
  chats: IChatItem;
  createdBy: IDirectChatUser;
  id: string;
  image: string;
  match: IRequestResponse;
  name: string;
  status: number;
  type: 'match' | 'request';
  unseen: number;
  user: IDirectChatUser;
  payment_id: string;
  isAccepted: boolean;
  isDeleted: boolean;
  lastSeenId: string; // Used to show --------Unread Messages--------
}

interface IStoreChatParams {
  chat_room_id: string;
  message: string;
  type: string;
  payment_id?: string;
  admin_id?: string;
}

interface IGetChatListParams extends IPaginationParams {
  name?: string;
}

export interface IChatItem {
  admin_id: string | null;
  id: string;
  message: string;
  createdAt: string;
  room: string;
  seen: string | null;
  send_by: {
    id: string;
    image: string;
    name: string;
    userId: string;
    userType: UserType;
  };
}

interface IDirectChatUser {
  id: string;
  image: string;
  name: string;
  userId: string;
  userType: UserType;
}

interface IGetChatMessageParams extends IPaginationParams {
  roomId: string;
}

export interface IChatMessageResponse {
  adminAssit: string;
  chats: PaginatedData<IChatItem>;
  createdBy: IDirectChatUser;
  id: string;
  image: string;
  match: {
    id: string;
    company: ICompany;
    createdBy: IUser;
    job: IJobData;
    jobseeker: IJobSeekerProfile;
    matched: string | null;
    unmatched: string | null;
    isPaid?: boolean;
    isRequestSent?: boolean;
    room?: string;
    requestFavouriteId: string;
  };
  name: string; // chatroom name
  status: number;
  type: 'match' | 'request';
  unseen: number;
  user: IDirectChatUser;
  isAccepted: boolean;
  matchedUser: {
    name: string;
    companyId: string | null;
    image: string;
    jobseekerId: string | null;
    userId: string | null;
  };
  superChat: boolean;
  isDeleted: boolean;
  isChatViolation: boolean;
}
