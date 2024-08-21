import { createSlice, PayloadAction } from '@reduxjs/toolkit';

import { IMatchingJobsQueryParams } from '@redux/services/jobsApi';
import { MAX_AGE, MAX_SALARY, MIN_AGE, MIN_SALARY } from '@utils/constants';
import { IMatchingJobSeekerQueryParams } from '@redux/services/jobSeekerApi';

const initialState = {
  jobSeekerFilter: {
    gender: '',
    age_from: MIN_AGE,
    age_to: MAX_AGE,
    occupation: '',
    experience: '',
    japanese_level: '',
    start_when: '',
    job_type: '',
  },
  companyFilter: {
    job_location: '',
    pay_type: '',
    salary_from: MIN_SALARY,
    salary_to: MAX_SALARY,
    job_type: '',
    from_when: '',
    occupation: '',
  },
};

const filterSlice = createSlice({
  name: 'filter',
  initialState,
  reducers: {
    setJobSeekerFilterValues(
      state,
      action: PayloadAction<IMatchingJobSeekerQueryParams>,
    ) {
      state.jobSeekerFilter = {
        ...state.jobSeekerFilter,
        ...action.payload,
      };
    },
    setCompanyFilterValues(
      state,
      action: PayloadAction<IMatchingJobsQueryParams>,
    ) {
      state.companyFilter = {
        ...state.companyFilter,
        ...action.payload,
      };
    },
  },
});

export const { setJobSeekerFilterValues, setCompanyFilterValues } =
  filterSlice.actions;
const filterReducer = filterSlice.reducer;

export default filterReducer;
