import { IUser, authApi } from './authApi';
import {
  ApiResponse,
  API_Methods,
  PaginatedData,
  IPaginationParams,
} from '../helpers/types';
import { ITags } from './dataApi';

export const jobSeekerApi = authApi.injectEndpoints({
  endpoints: builder => ({
    createJobSeekerProfile: builder.mutation<
      ApiResponse<IJobSeekerProfile>,
      FormData
    >({
      query: params => {
        return {
          url: '/jobseeker/store',
          method: API_Methods.POST,
          body: params,
        };
      },
    }),
    updateJobSeeker: builder.mutation<ApiResponse<any>, IUpdateParams>({
      query: ({ formData, id }) => {
        return {
          url: `/jobseeker/${id}`,
          method: API_Methods.POST,
          body: formData,
        };
      },
      invalidatesTags: ['JobSeeker'],
    }),
    getJobSeeker: builder.query<ApiResponse<IJobSeekerProfile>, void>({
      query: () => {
        return {
          url: '/jobseeker-details',
          method: API_Methods.GET,
        };
      },
      providesTags: ['JobSeeker'],
    }),
    // Homescreen jobseeker cards
    getMatchingJobSeekers: builder.query<
      ApiResponse<IJobseekerDataResponse>,
      IMatchingJobSeekerQueryRequiredParams
    >({
      query: ({ per_page = 10, page = 1, ...rest }) => ({
        url: '/jobseeker',
        method: API_Methods.GET,
        params: {
          page,
          per_page,
          ...rest,
        },
      }),
      forceRefetch: () => true,
      providesTags: ['MatchingJobSeeker'],
    }),
  }),
});

export const {
  useCreateJobSeekerProfileMutation,
  useGetJobSeekerQuery,
  useUpdateJobSeekerMutation,
  useGetMatchingJobSeekersQuery,
} = jobSeekerApi;

interface IJobseekerDataResponse {
  dailyCount: number;
  dailylimit: number;
  favouriteCount: number;
  favoriteLimit: number;
  chatRequestCount: number;
  chatRequestLimit: number;
  items: PaginatedData<IJobSeekerProfile>;
}

export interface IJobSeekerProfileParams {
  first_name?: string;
  last_name?: string;
  image?: any[];
  profile_img?: any | null;
  birthday?: string | null;
  gender?: string | null;
  country?: string | null;
  current_country?: string | null;
  occupation?: string | null;
  experience?: string | null;
  japanese_level?: string | null;
  about?: string | null;
  about_ja?: string | null;
  living_japan?: 0 | 1;
  ielts_six?: 0 | 1;
  longterm?: 0 | 1;
  visa?: 0 | 1;
  job_type?: string | null;
  start_when?: string;
  tags?: string[];
  intro_video?: File | null;
  isIntroVideoDeleted?: boolean;
}

export interface IJobSeekerProfile {
  id: string;
  user: IUser;
  firstName: string;
  lastName: string;
  image: IUserImage[];
  profileImg: string;
  birthday: string;
  gender: string;
  country: string;
  currentCountry: string;
  occupation: string;
  experience: string;
  japaneseLevel: string;
  about: string;
  aboutJa: string;
  isLivingInJapan: boolean;
  jobType: string;
  percentage: number; // Profile completion percentage
  startWhen: string;
  tags: ITags[];
}

export interface IUserImage {
  id: string;
  image: string;
  fileType: string;
}

interface IUpdateParams {
  formData: FormData;
  id: string;
}

export interface IMatchingJobSeekerQueryParams extends IPaginationParams {
  gender?: string;
  experience?: string;
  japanese_level?: string;
  age_from?: number;
  age_to?: number;
  occupation?: string;
  start_when?: string;
  job_type?: string;
}

export interface IMatchingJobSeekerQueryRequiredParams
  extends IPaginationParams {
  gender: string | null;
  experience?: string;
  japanese_level?: string;
  age_from?: number;
  age_to?: number;
  occupation?: string;
  start_when?: string;
  previousId?: number[]; // prevent these ids to be repeated
}
