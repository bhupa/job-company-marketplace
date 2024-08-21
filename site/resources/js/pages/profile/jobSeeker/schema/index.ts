import { InferType, array, boolean, date, object, string } from 'yup';

const personalSettingsSchema = object().shape({
  name: string().trim().required('enterYourName'),
  gender: string().nullable(), //.required('jobseeker.gender.empty'),
  dob: date()
    .nullable()
    .max(
      new Date(Date.now() - 18 * 365 * 24 * 60 * 60 * 1000),
      'validation.mustBeEighteenYears',
    )
    .required('validation.dob'),
  country: string().required('selectCountry'),
  currentCountry: string().nullable(),
  occupation: string().required('selectOccupation'),
  experience: string().required('selectExperience'),
  japaneseLevel: string().required('selectJapaneseLevel'),
  jobType: string().required('selectJobType'),
  about: string().required('fieldRequired'),
  aboutJa: string().nullable(),
  isLivingInJapan: boolean(),
  startDate: date().nullable(),
  tags: array().of(string()).defined(),
});

export interface PersonalSettingsValues
  extends InferType<typeof personalSettingsSchema> {}

export { personalSettingsSchema };
