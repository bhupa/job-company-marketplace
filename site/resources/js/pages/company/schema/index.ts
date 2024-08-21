import { InferType, mixed, object, string } from 'yup';

const name = string().trim().required('fieldRequired');

const companyProfileDetailSchema = object().shape({
  name: name,
  address: name,
  about: string().trim().required('writeAboutCompany'),
  aboutJa: string().trim().nullable(),
  introVideo: mixed().nullable(),
});

export interface CompanyProfileDetailValues
  extends InferType<typeof companyProfileDetailSchema> {}

export { companyProfileDetailSchema };
