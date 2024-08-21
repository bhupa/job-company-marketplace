import { authApi } from './authApi';
import { ApiResponse, API_Methods } from '../helpers/types';
import { IDropdownItem, ISectionDropdownItem } from '@constants/dropdownData';

export const dataApi = authApi.injectEndpoints({
  endpoints: builder => ({
    getOccupationList: builder.query<ApiResponse<IDropdownItem[]>, void>({
      query: () => ({
        url: '/category',
        method: API_Methods.GET,
      }),
    }),
    getJobLocationList: builder.query<
      ApiResponse<ISectionDropdownItem[]>,
      void
    >({
      query: () => ({
        url: '/job-location',
        method: API_Methods.GET,
      }),
    }),
    getContentData: builder.query<
      ApiResponse<IContentDataResponse>,
      {
        type:
          | 'terms_of_service'
          | 'privacy_policy'
          | 'user_policy'
          | 'job_policy'
          | 'chat_policy';
      }
    >({
      query: ({ type }) => ({
        url: `/content?type=${type}`,
        method: API_Methods.GET,
      }),
    }),
    translateText: builder.mutation<
      ApiResponse<string>,
      { text: string; from: 'en' | 'ja'; to: 'en' | 'ja' }
    >({
      query: ({ text, from, to }) => {
        return {
          url: '/translate-en-jp',
          method: API_Methods.POST,
          body: {
            // Transform line breaks to \n character
            description: text?.replace(/\n/g, '\\n'),
            request_source: from,
            request_target: to,
          },
        };
      },
      transformResponse: (res: ApiResponse<string>) => {
        if (res.data) {
          // Transform \n character to line breaks
          const modifiedResponse = res.data.replace(/\\n/g, '\n');
          return { ...res, data: modifiedResponse };
        }
        return res;
      },
    }),
    getTagList: builder.query<
      ApiResponse<ITags[]>,
      { type: 'job' | 'jobseeker' }
    >({
      query: ({ type = 'job' }) => ({
        url: `/tags?type=${type}`,
        method: API_Methods.GET,
      }),
    }),
    getAccountDeletionGuide: builder.query<
      ApiResponse<IAccountDeletionGuideResponse>,
      void
    >({
      query: () => ({
        url: '/account-deletion-guide',
        method: API_Methods.GET,
      }),
    }),
  }),
});

export const {
  useGetOccupationListQuery,
  useGetJobLocationListQuery,
  useTranslateTextMutation,
  useGetTagListQuery,
  useGetContentDataQuery,
  useGetAccountDeletionGuideQuery,
} = dataApi;

export interface ITags {
  id: string;
  label: string;
  label_ja: string;
  type: string;
  value: string;
}

interface IContentDataResponse {
  [key: string]: {
    id: string;
    title: string;
    type: string;
    lang: string;
    shortDescription: string;
    description: string;
    link: string;
  };
}

interface IAccountDeletionGuideResponse {
  content: {
    [key: string]: string;
  };
}
