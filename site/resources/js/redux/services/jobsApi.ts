// Others
import { ITags } from './dataApi';
import { ICompany } from './companyApi';
import { IUser, authApi } from './authApi';
import {
  ApiResponse,
  API_Methods,
  PaginatedData,
  IPaginationParams,
} from '../helpers/types';
import { ISectionDropdownItem } from '@constants/dropdownData';

export const jobsApi = authApi.injectEndpoints({
  endpoints: builder => ({
    /**
     * This api is for company to view their posted jobs
     */
    getJobsList: builder.query<
      ApiResponse<PaginatedData<IJobData>>,
      IPaginationParams
    >({
      query: ({ per_page = 15, page = 1 }) => ({
        url: '/job-lists',
        method: API_Methods.GET,
        params: {
          per_page,
          page,
        },
      }),
      providesTags: ['Jobs'],
    }),
    createJob: builder.mutation<ApiResponse<any>, FormData>({
      query: params => {
        return {
          url: '/job/store',
          method: API_Methods.POST,
          body: params,
        };
      },
      invalidatesTags: () => ['Jobs'],
    }),
    updateJob: builder.mutation<ApiResponse<any>, IUpdateJobParams>({
      query: ({ formData, id }) => {
        return {
          url: `/job/${id}`,
          method: API_Methods.POST,
          body: formData,
        };
      },
      invalidatesTags: () => ['Jobs'],
    }),
    deleteJob: builder.mutation<ApiResponse<any>, string>({
      query: id => {
        return {
          url: `/job/${id}`,
          method: API_Methods.DELETE,
        };
      },
    }),
    /**
     * Homescreen card jobs
     * This api is for jobseekers to view company posted jobs
     */
    getMatchingJobs: builder.query<
      ApiResponse<IJobDataResponse>,
      IMatchingJobsQueryRequiredParams
    >({
      query: ({ per_page = 10, page = 1, ...rest }) => ({
        url: '/job',
        method: API_Methods.GET,
        params: {
          page,
          per_page,
          ...rest,
        },
      }),
      forceRefetch: () => true,
      providesTags: ['MatchingJobs'],
    }),
  }),
});

export const {
  useGetJobsListQuery,
  useCreateJobMutation,
  useUpdateJobMutation,
  useDeleteJobMutation,
  useGetMatchingJobsQuery,
} = jobsApi;

interface IJobDataResponse {
  dailyCount: number;
  dailylimit: number;
  favouriteCount: number;
  favoriteLimit: number;
  chatRequestCount: number;
  chatRequestLimit: number;
  items: PaginatedData<IJobData>;
}

export interface IJobData {
  id: string;
  occupation: {
    value: string;
    label: string;
    label_ja: string;
  };
  jobImage: string;
  jobTitle: string;
  jobTitleJa: string;
  jobLocation: ISectionDropdownItem[];
  salaryFrom: number;
  salaryTo: number;
  payType: string;
  experience: string;
  japaneseLevel: string;
  requiredSkills: string;
  requiredSkillsJa: string;
  fromWhen: string;
  jobType: string;
  status: 0 | 1; // 0 means jobseeker is not hired, 1 means hired
  user: IUser;
  company: ICompany;
  tags: ITags[];
}

interface IUpdateJobParams {
  formData: FormData;
  id: string;
}

export interface IMatchingJobsQueryParams extends IPaginationParams {
  job_title?: string;
  user_id?: string;
  job_location?: string;
  salary_from?: number;
  salary_to?: number;
  working_hours?: string;
  age_from?: number;
  age_to?: number;
  gender?: string;
  occupation?: string;
  experience?: string;
  japanese_level?: string;
  published?: string;
  required_skills?: string;
  from_when?: string;
  job_type?: string;
  pay_type?: string;
}

export interface IMatchingJobsQueryRequiredParams extends IPaginationParams {
  job_location?: string;
  pay_type?: string;
  salary_from?: number;
  salary_to?: number;
  from_when?: string;
  job_type?: string;
  occupation?: string;
  type?: 'reset' | string;
  previousId?: number[]; // prevent these ids to be repeated
}
