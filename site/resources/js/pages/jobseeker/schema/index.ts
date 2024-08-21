import { InferType, array, boolean, date, mixed, object, string } from 'yup';

const name = string().trim().required('fieldRequired');

const profileDetailSchema = object().shape({
  firstName: name,
  lastName: name,
  dob: date()
    .nullable()
    .max(
      new Date(Date.now() - 18 * 365 * 24 * 60 * 60 * 1000),
      'validation.mustBeEighteenYears',
    )
    .required('validation.dob'),
});

export interface ProfileDetailValues
  extends InferType<typeof profileDetailSchema> {}

const countrySchema = object().shape({
  country: string().required('selectCountry'),
  // isResidingCountrySame: boolean(),
  // residingCountry: string().when('isResidingCountrySame', {
  //   is: false,
  //   then: () =>
  //     string().required('Please select your current residing country!'),
  // }),
  isLivingInJapan: boolean(),
});

export interface CountryValues extends InferType<typeof countrySchema> {}

const aboutYouSchema = object().shape({
  occupation: string().required('selectOccupation'),
  experience: string().required('selectExperience'),
  japaneseLevel: string().required('selectJapaneseLevel'),
  jobType: string().required('selectJobType'),
  // about: string().trim().required('Please write something about yourself!'),
  startDate: date().nullable(),
  tags: array().of(string()).defined(),
});

export interface AboutYouValues extends InferType<typeof aboutYouSchema> {}

const userBioSchema = object().shape({
  about: string().trim().required('writeAboutYourself'),
  aboutJa: string().nullable(),
  introVideo: mixed().nullable(),
});

export interface UserBioValues extends InferType<typeof userBioSchema> {}

const personalSettingsSchema = object().shape({
  name: string().trim().required('enterYourName'),
  gender: string().nullable(),
  dob: string().nullable().required('validation.dob'),
  country: string().required('selectCountry'),
  currentCountry: string().nullable(),
  occupation: string().required('selectOccupation'),
  experience: string().required('selectExperience'),
  japaneseLevel: string().required('selectJapaneseLevel'),
  jobType: string().required('selectJobType'),
  about: string().required('fieldRequired'),
  aboutJa: string().nullable(),
  isLivingInJapan: boolean(),
  startDate: string().nullable(),
  tags: array().of(string()).defined(),
  introVideo: mixed().nullable(),
});

export interface PersonalSettingsValues
  extends InferType<typeof personalSettingsSchema> {}

export {
  profileDetailSchema,
  countrySchema,
  aboutYouSchema,
  userBioSchema,
  personalSettingsSchema,
};
