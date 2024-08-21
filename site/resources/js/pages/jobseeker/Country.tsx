import React, { useEffect } from 'react';

import { Checkbox } from 'antd';
import { useFormik } from 'formik';
import { useNavigate } from 'react-router-dom';
import { useTranslation } from 'react-i18next';

// Components
import {
  CountrySelect,
  CustomButton,
  HeaderWithBackButton,
  HelperText,
  Title,
} from '@components/common';
import { AuthProfileWrapper } from '@templates';

// Hooks
import useJobSeekerProfileInput from '@customHooks/useJobSeekerProfileInput';

// Others
import { CountryValues, countrySchema } from './schema';

const initialValues: CountryValues = {
  country: '',
  isLivingInJapan: false,
};

const Country = () => {
  const navigate = useNavigate();
  const { t } = useTranslation(['jobseeker']);
  const { handleSetProfileData, profileInput } = useJobSeekerProfileInput();

  useEffect(() => {
    setValues({
      country: profileInput.country,
      isLivingInJapan: profileInput.isLivingInJapan,
    });
  }, []);

  const handleAddCountry = () => {
    handleSetProfileData({
      ...values,
    });
    navigate('/about-you');
  };

  const { setFieldValue, handleSubmit, setValues, values, touched, errors } =
    useFormik({
      initialValues: initialValues,
      validationSchema: countrySchema,
      onSubmit: handleAddCountry,
    });

  return (
    <AuthProfileWrapper>
      <div className="flex flex-col gap-4 w-full max-w-md">
        <HeaderWithBackButton title={t('country')} hasBackButton={false} />
        <CountrySelect
          label={t('country')}
          placeholder={profileInput.country || ''}
          handleSetCountry={country => setFieldValue('country', country)}
          error={errors.country && touched.country ? errors.country : null}
          required
        />
        <HelperText message={'validation.changeCountry'} />
        <Checkbox
          checked={values.isLivingInJapan as boolean}
          onChange={e => setFieldValue('isLivingInJapan', e.target.checked)}>
          <Title type="caption2">{t('livingInJapan')}</Title>
        </Checkbox>
        <CustomButton
          title={t('next', { ns: 'common' })}
          onClick={() => handleSubmit()}
        />
      </div>
    </AuthProfileWrapper>
  );
};

export default Country;
