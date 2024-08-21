import React, { useEffect, useState } from 'react';

import { useFormik } from 'formik';
import { Modal, Spin } from 'antd';
import { useTranslation } from 'react-i18next';

// Components
import {
  CustomButton,
  CustomInput,
  DateInput,
  DropdownWithLabel,
  InputLabel,
  SectionDropdownWithLabel,
  Title,
} from '@components/common';
import FilterHeader from '@components/common/FilterHeader';

// Hooks
import { useShowMessage } from '@customHooks/useShowMessage';

// Redux
import {
  resetLeftSwipedItems,
  setHomeData,
  setUndoAllowed,
} from '@redux/reducers/home';
import {
  useGetJobLocationListQuery,
  useGetOccupationListQuery,
} from '@redux/services/dataApi';
import { useAppDispatch, useAppSelector } from '@redux/hook';
import { setCompanyFilterValues } from '@redux/reducers/filter';

// Others
import { validateSalary } from '@utils/helpers';
import { MAX_SALARY, MIN_SALARY } from '@utils/constants';
import { JobFilterValues, jobFilterSchema } from './schema';
import { JOB_TYPES, SALARY_PAY_TYPE } from '@constants/dropdownData';

interface Props {
  setModalVisible: (isVisible: boolean) => void;
}

const initialValues: JobFilterValues = {
  minSalary: MIN_SALARY,
  maxSalary: MAX_SALARY,
  location: '',
  workHour: '',
  jobType: '',
  startDate: null,
  occupation: '',
  payType: '',
};

const FilterCompany = ({ setModalVisible }: Props) => {
  const dispatch = useAppDispatch();
  const { t } = useTranslation(['jobseeker', 'jobs']);
  const { showWarning } = useShowMessage();

  const { isLoading, data } = useGetOccupationListQuery();
  const { isLoading: isJobLocationLoading, data: locationData } =
    useGetJobLocationListQuery();

  const { companyFilter } = useAppSelector(state => state.filter);

  const [filterKey, setFilterKey] = useState<string>(Date.now().toString());
  const [isDataLoading, setDataLoading] = useState<boolean>(true);

  useEffect(() => {
    if (companyFilter) {
      const {
        job_location,
        salary_from,
        salary_to,
        job_type,
        from_when,
        occupation,
        pay_type,
      } = companyFilter;
      setValues({
        location: job_location,
        minSalary: Number(salary_from),
        maxSalary: Number(salary_to),
        jobType: job_type,
        occupation,
        startDate: from_when ? new Date(from_when) : null,
        payType: pay_type,
      });
      setDataLoading(false);
    } else {
      setDataLoading(false);
    }
  }, [companyFilter]);

  const handleCheckFilterChanged = () => {
    const {
      job_location,
      salary_from,
      salary_to,
      job_type,
      from_when,
      occupation: fOccupation,
      pay_type,
    } = companyFilter;
    const {
      location,
      minSalary,
      maxSalary,
      jobType,
      occupation,
      startDate,
      payType,
    } = values;
    const fromWhen = from_when ? new Date(from_when).getTime() : null;
    const userStartDate = startDate ? new Date(startDate).getTime() : null;
    if (
      location !== job_location ||
      payType !== pay_type ||
      Number(salary_from) !== Number(minSalary) ||
      Number(maxSalary) !== Number(salary_to) ||
      job_type !== jobType ||
      occupation !== fOccupation ||
      fromWhen !== userStartDate
    ) {
      return true;
    }
    return false;
  };

  /**
   * Reset left swiped items, home data and also set resetFetched to true to update data with new fetched data
   */
  const handleApplyFilter = () => {
    const isChanged = handleCheckFilterChanged();
    if (!isChanged) {
      return setModalVisible(false);
    }
    const {
      location,
      minSalary,
      maxSalary,
      jobType,
      occupation,
      startDate,
      payType,
    } = values;
    if (maxSalary && Number(minSalary) >= Number(maxSalary)) {
      return showWarning(t('validation.maxSalarySmall', { ns: 'messages' }));
    }
    dispatch(resetLeftSwipedItems());
    dispatch(setUndoAllowed(false));
    dispatch(
      setCompanyFilterValues({
        job_location: location,
        pay_type: payType,
        salary_from: minSalary,
        salary_to: maxSalary,
        job_type: jobType,
        occupation,
        from_when: startDate?.toString() ?? '',
      }),
    );
    dispatch(setHomeData({ data: [], reset: true, resetFetched: true }));
    setModalVisible(false);
  };

  const handleClearFilter = () => {
    setFilterKey(Date.now().toString());
    setValues(initialValues);
  };

  const {
    handleSubmit,
    setFieldValue,
    setValues,
    setTouched,
    values,
    touched,
    errors,
  } = useFormik({
    initialValues: initialValues,
    validationSchema: jobFilterSchema,
    onSubmit: handleApplyFilter,
  });

  const {
    location,
    minSalary,
    maxSalary,
    jobType,
    startDate,
    occupation,
    payType,
  } = values;
  const isFilterApplied =
    location ||
    payType ||
    minSalary !== MIN_SALARY ||
    maxSalary !== MAX_SALARY ||
    jobType ||
    occupation ||
    startDate
      ? true
      : false;

  return (
    <Modal open={true} closable={false} footer={null}>
      <FilterHeader
        onClosePressed={() => setModalVisible(false)}
        hasClearBtn
        clearDisabled={!isFilterApplied}
        onClearPressed={() => handleClearFilter()}
      />
      {isJobLocationLoading || isDataLoading || isLoading ? (
        <Spin />
      ) : (
        <div className="mt-2 flex flex-col gap-2">
          <SectionDropdownWithLabel
            key={'location' + filterKey}
            searchable
            label={t('location')}
            placeholder={t('select', { ns: 'common' })}
            value={values.location || ''}
            items={locationData?.data ?? []}
            onSelectItem={async item => {
              setFieldValue('location', item.value);
            }}
            error={errors.location && touched.location ? errors.location : null}
          />
          <DropdownWithLabel
            searchable
            label={t('payType', { ns: 'jobs' })}
            placeholder={t('select', { ns: 'common' })}
            value={values.payType}
            items={SALARY_PAY_TYPE}
            onSelectItem={async item => {
              setFieldValue('payType', item.value);
            }}
            error={errors.payType && touched.payType ? errors.payType : null}
          />
          <div>
            <InputLabel label={t('salaryRange')} />
            <div className="flex gap-6 items-center">
              <CustomInput
                placeholder={t('startingFrom', {
                  PAY_TYPE: '',
                  SALARY_FROM: '',
                  ns: 'jobs',
                })}
                onChange={e => {
                  const validatedSalary = validateSalary(e.target.value);
                  if (validatedSalary || validatedSalary === '') {
                    setFieldValue('minSalary', validatedSalary);
                  }
                }}
                value={values.minSalary?.toString() ?? ''}
                required
              />
              <Title type="body1">~</Title>
              <CustomInput
                placeholder={t('salaryTo', {
                  ns: 'jobs',
                })}
                onChange={e => {
                  const validatedSalary = validateSalary(e.target.value);
                  if (validatedSalary || validatedSalary === '') {
                    setFieldValue('maxSalary', validatedSalary);
                  }
                }}
                value={values.maxSalary?.toString() ?? ''}
              />
            </div>
          </div>
          <DropdownWithLabel
            key={'occupation' + filterKey}
            searchable
            label={t('occupation')}
            placeholder={t('select', { ns: 'common' })}
            value={values.occupation || ''}
            loading={isLoading}
            items={data?.data ?? []}
            onSelectItem={async item => {
              setFieldValue('occupation', item.value);
            }}
            error={
              errors.occupation && touched.occupation ? errors.occupation : null
            }
          />
          <DropdownWithLabel
            key={'jobType' + filterKey}
            label={t('jobType', { ns: 'jobseeker' })}
            placeholder={t('select', { ns: 'common' })}
            value={values.jobType}
            items={JOB_TYPES}
            onSelectItem={async item => {
              setFieldValue('jobType', item.value);
            }}
          />
          <DateInput
            hasCloseButton
            label={t('jobStartDate', { ns: 'jobs' })}
            placeholder={'YYYY-MM-DD'}
            date={values.startDate}
            setDate={startDateValue => {
              setTouched({ startDate: true });
              setFieldValue('startDate', startDateValue);
            }}
            minimumDate={new Date()}
            error={
              errors.startDate && touched.startDate
                ? (errors.startDate as string)
                : null
            }
          />
          <CustomButton
            title={t('apply', { ns: 'common' })}
            onClick={() => handleSubmit()}
            className="w-full mt-6"
          />
        </div>
      )}
    </Modal>
  );
};

export default FilterCompany;
