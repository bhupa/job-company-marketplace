import { createSlice, PayloadAction } from '@reduxjs/toolkit';

// Others
// import { MAX_SALARY, MIN_SALARY } from '@utils/constants';
// import { IMatchingJobsQueryParams } from '@redux/services/jobsApi';

export const initialJobSeekerProfileInput = {
  firstName: '',
  lastName: '',
  birthday: '', //new Date(),
  gender: '',
  country: '',
  currentCountry: '',
  occupation: '',
  experience: '',
  japaneseLevel: '',
  about: '',
  aboutJa: '',
  image: [] as File[] | null | undefined,
  profileImg: null as File | null | undefined,
  isLivingInJapan: false,
  jobType: '',
  startDate: '',
  tags: [] as string[],
  introVideo: null as File | null | undefined,
  isCompleted: false, // this field is to detect if profile input is complete and can call api when not skipped
};

const jobSeekerSlice = createSlice({
  name: 'jobSeeker',
  initialState: {
    profileInput: initialJobSeekerProfileInput,
  },
  reducers: {
    setJobSeekerProfileInputData(
      state,
      action: PayloadAction<IJobSeekerProfileInputData>,
    ) {
      state.profileInput = {
        ...state.profileInput,
        ...action.payload,
      };
    },
  },
});

export const { setJobSeekerProfileInputData } = jobSeekerSlice.actions;
const jobSeekerReducer = jobSeekerSlice.reducer;

export default jobSeekerReducer;

export interface IJobSeekerProfileInputData {
  firstName?: string;
  lastName?: string;
  birthday?: string;
  gender?: string;
  country?: string;
  currentCountry?: string;
  occupation?: string;
  experience?: string;
  japaneseLevel?: string;
  about?: string;
  aboutJa?: string;
  image?: File[] | null | undefined;
  profileImg?: File | null | undefined;
  isLivingInJapan?: boolean;
  jobType?: string;
  isCompleted?: boolean;
  startDate?: string;
  tags?: string[];
  introVideo?: File | null | undefined;
}
