import { InferType, date, number, object, string } from 'yup';

const jobSeekerFilterSchema = object().shape({
  gender: string(),
  minAge: number(),
  maxAge: number(),
  occupation: string(),
  experience: string(),
  japaneseLevel: string(),
  startDate: date().nullable(),
  jobType: string(),
});

export interface JobSeekerFilterValues
  extends InferType<typeof jobSeekerFilterSchema> {}

const jobFilterSchema = object().shape({
  location: string(),
  minSalary: number(),
  maxSalary: number(),
  workHour: string(),
  japaneseLevel: string(),
  minAge: number(),
  maxAge: number(),
  experience: string(),
  gender: string(),
  jobType: string(),
  startDate: date().nullable(),
  occupation: string(),
  payType: string(),
});

export interface JobFilterValues extends InferType<typeof jobFilterSchema> {}

export { jobSeekerFilterSchema, jobFilterSchema };
