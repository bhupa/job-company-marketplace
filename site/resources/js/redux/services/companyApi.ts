import { IJobData } from './jobsApi';
import { IUser, authApi } from './authApi';
import { IUserImage } from './jobSeekerApi';
import { ApiResponse, API_Methods } from '../helpers/types';

export const companyApi = authApi.injectEndpoints({
  endpoints: builder => ({
    createCompanyProfile: builder.mutation<ApiResponse<any>, FormData>({
      query: params => {
        return {
          url: '/company/store',
          method: API_Methods.POST,
          body: params,
        };
      },
    }),
    updateCompany: builder.mutation<ApiResponse<any>, IUpdateParams>({
      query: ({ formData, id }) => {
        return {
          url: `/company/${id}`,
          method: API_Methods.POST,
          body: formData,
        };
      },
      invalidatesTags: ['Company'],
    }),
    getCompany: builder.query<ApiResponse<ICompany>, void>({
      query: () => {
        return {
          url: '/company-details',
          method: API_Methods.GET,
        };
      },
      providesTags: ['Company'],
    }),
    getSingleCompany: builder.query<ApiResponse<ICompany>, string>({
      query: id => {
        return {
          url: `/company/${id}`,
          method: API_Methods.GET,
        };
      },
    }),
    checkCompanyExists: builder.mutation<ApiResponse<any>, string>({
      query: name => {
        return {
          url: '/check-company-exists',
          method: API_Methods.POST,
          body: {
            company_name: name,
          },
        };
      },
    }),
  }),
});

export const {
  useCreateCompanyProfileMutation,
  useUpdateCompanyMutation,
  useGetCompanyQuery,
  useCheckCompanyExistsMutation,
  useGetSingleCompanyQuery,
} = companyApi;

export interface ICompany {
  aboutCompany: string;
  aboutCompanyJa: string;
  address: string;
  companyName: string;
  id: string;
  image: IUserImage[];
  logo: string;
  status: string;
  user: IUser;
  percentage: number;
  jobs: IJobData[];
}

interface IUpdateParams {
  formData: FormData;
  id: string;
}
