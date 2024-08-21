import { createSlice, PayloadAction } from '@reduxjs/toolkit';

const initialState = {
  profileInput: {
    company_name: '',
    about_company: '',
    about_company_ja: '',
    address: '',
    logo: null as File | undefined | null,
    image: [] as File[] | undefined | null,
    isCompleted: false, // this field is to detect if profile input is complete and can call api when not skipped
    intro_video: null as File | undefined | null,
  },
  job: {
    job_image: { path: '', imageObj: null as File | undefined | null },
    job_title: '',
    job_title_ja: '',
    job_location: '',
    salary_from: '',
    salary_to: '',
    pay_type: '',
    working_hours: '',
    from_when: '',
    occupation: '',
    experience: '',
    japanese_level: '',
    job_type: '',
    required_skills: '',
    required_skills_ja: '',
    tags: [] as string[],
  },
  isEditMode: false,
  editJobId: '',
};

const companySlice = createSlice({
  name: 'company',
  initialState,
  reducers: {
    setCompanyProfileInputData(
      state,
      action: PayloadAction<ICompanyProfileInputData>,
    ) {
      state.profileInput = {
        ...state.profileInput,
        ...action.payload,
      };
    },
    setCompanyJobData(state, action: PayloadAction<IJobInputData>) {
      state.job = {
        ...state.job,
        ...action.payload,
      };
    },
    setIsJobEditMode(state, action: PayloadAction<IJobEditParams>) {
      state.isEditMode = action.payload.isEdit;
      state.editJobId = action.payload.id || '';
    },
    resetJobData(state) {
      state.job = {
        ...state.job,
        ...initialState.job,
      };
    },
  },
});

export const {
  setCompanyProfileInputData,
  setCompanyJobData,
  setIsJobEditMode,
  resetJobData,
} = companySlice.actions;
const companyReducer = companySlice.reducer;

export default companyReducer;

export interface IJobInputData {
  job_image?: { path: string; imageObj: any | null };
  job_title?: string;
  job_title_ja?: string;
  job_location?: string;
  salary_from?: string;
  salary_to?: string;
  pay_type?: string;
  working_hours?: string;
  from_when?: string;
  gender?: string;
  age_from?: string;
  age_to?: string;
  occupation?: string;
  experience?: string;
  japanese_level?: string;
  required_skills?: string;
  required_skills_ja?: string;
  job_type?: string;
  tags?: string[]; // array of id's of tags
}

export interface ICompanyProfileInputData {
  company_name?: string;
  about_company?: string;
  about_company_ja?: string;
  address?: string;
  logo?: File | null | undefined;
  image?: File[] | null | undefined;
  isCompleted?: boolean;
  intro_video?: File | null;
}

interface IJobEditParams {
  isEdit: boolean;
  id?: string | null;
}
