import { InferType, array, number, object, string } from 'yup';

const companyWorkInformationSchema = object().shape({
  jobTitle: string().trim().required('fieldRequired'),
  jobTitleJa: string().trim().nullable(),
  location: string().required('selectWorkLocation'),
  minSalary: number()
    .required('startingSalaryRequired')
    .min(1, 'startingSalaryRequired'),
  maxSalary: number().nullable(), //required('selectSalaryRange'),
  payType: string().required('selectPayType'),
  startDate: string().nullable(),
});

export interface CompanyWorkInformationValues
  extends InferType<typeof companyWorkInformationSchema> {}

const companySelectionPreferenceSchema = object().shape({
  // gender: string().required('selectGender'),
  // minAge: number().required('selectAgeRange'),
  // maxAge: number().required('selectAgeRange'),
  occupation: string().required('selectOccupation'),
  experience: string().nullable(),
  japaneseLevel: string().required('selectJapaneseLevel'),
});

export interface CompanySelectionPreferenceValues
  extends InferType<typeof companySelectionPreferenceSchema> {}

const companyRequiredSkillsSchema = object().shape({
  skills: string().trim().required('enterJobDescription'),
  skillsJa: string().trim().required('enterJobDescription'),
});

export interface CompanyRequiredSkillsValues
  extends InferType<typeof companyRequiredSkillsSchema> {}

const companyPreferenceOptionsSchema = object().shape({
  tags: array().of(string()).defined(),
  jobType: string().required('selectJobType'),
});

export interface CompanyPreferenceOptionsValues
  extends InferType<typeof companyPreferenceOptionsSchema> {}

export {
  companyWorkInformationSchema,
  companySelectionPreferenceSchema,
  companyRequiredSkillsSchema,
  companyPreferenceOptionsSchema,
};
