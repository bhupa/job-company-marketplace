import React, { useEffect, useState } from 'react';

import { useFormik } from 'formik';
import { Modal, Slider, Spin } from 'antd';
import { useTranslation } from 'react-i18next';

// Components
import {
  CustomButton,
  DateInput,
  DropdownWithLabel,
  InputLabel,
} from '@components/common';
import FilterHeader from '@components/common/FilterHeader';

// Redux
import {
  resetLeftSwipedItems,
  setHomeData,
  setUndoAllowed,
} from '@redux/reducers/home';
import { useAppDispatch, useAppSelector } from '@redux/hook';
import { setJobSeekerFilterValues } from '@redux/reducers/filter';
import { useGetOccupationListQuery } from '@redux/services/dataApi';

// Others
import {
  ANY_GENDER,
  GENDER_DATA,
  JAPANESE_LEVEL,
} from '@constants/dropdownData';
import { MAX_AGE, MIN_AGE } from '@utils/constants';
import { JobSeekerFilterValues, jobSeekerFilterSchema } from './schema';

interface Props {
  setModalVisible: (isVisible: boolean) => void;
}

const initialValues: JobSeekerFilterValues = {
  gender: '',
  minAge: MIN_AGE,
  maxAge: MAX_AGE,
  occupation: '',
  japaneseLevel: '',
  startDate: null,
};

const FilterJobSeeker = ({ setModalVisible }: Props) => {
  const { t } = useTranslation(['jobseeker']);
  const dispatch = useAppDispatch();
  const { isLoading, data } = useGetOccupationListQuery();

  const { jobSeekerFilter } = useAppSelector(state => state.filter);

  const [filterKey, setFilterKey] = useState<string>(Date.now().toString());
  const [isDataLoading, setDataLoading] = useState<boolean>(true);

  useEffect(() => {
    if (jobSeekerFilter) {
      const {
        gender,
        age_from,
        age_to,
        japanese_level,
        occupation,
        start_when,
      } = jobSeekerFilter;
      setValues({
        gender,
        minAge: Number(age_from),
        maxAge: Number(age_to),
        japaneseLevel: japanese_level,
        occupation,
        startDate: start_when ? new Date(start_when) : null,
      });
      setDataLoading(false);
    } else {
      setDataLoading(false);
    }
  }, [jobSeekerFilter]);

  const handleCheckFilterChanged = () => {
    const {
      gender: fGender,
      age_from,
      age_to,
      japanese_level,
      occupation: fOccupation,
      start_when,
    } = jobSeekerFilter;
    const { gender, minAge, maxAge, japaneseLevel, occupation, startDate } =
      values;
    const fromWhen = start_when ? new Date(start_when).getTime() : null;
    const userStartDate = startDate ? new Date(startDate).getTime() : null;
    if (
      fGender !== gender ||
      Number(age_from) !== Number(minAge) ||
      Number(age_to) !== Number(maxAge) ||
      japanese_level !== japaneseLevel ||
      fOccupation !== occupation ||
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
    const { gender, minAge, maxAge, japaneseLevel, occupation, startDate } =
      values;
    dispatch(setUndoAllowed(false));
    dispatch(resetLeftSwipedItems());
    dispatch(
      setJobSeekerFilterValues({
        gender,
        age_from: minAge,
        age_to: maxAge,
        japanese_level: japaneseLevel,
        occupation,
        start_when: startDate?.toString() ?? '',
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
    validationSchema: jobSeekerFilterSchema,
    onSubmit: handleApplyFilter,
  });

  const { gender, minAge, maxAge, japaneseLevel, startDate, occupation } =
    values;
  const isFilterApplied =
    gender ||
    minAge !== MIN_AGE ||
    maxAge !== MAX_AGE ||
    japaneseLevel ||
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
      {isDataLoading || isLoading ? (
        <Spin />
      ) : (
        <div className="mt-2 flex flex-col gap-2">
          <DropdownWithLabel
            key={'gender' + filterKey}
            label={t('gender')}
            placeholder={t('select', { ns: 'common' })}
            value={values.gender || ''}
            items={[...GENDER_DATA, ...ANY_GENDER]}
            zIndex={10010}
            onSelectItem={async item => {
              setFieldValue('gender', item.value);
            }}
            error={errors.gender && touched.gender ? errors.gender : null}
          />
          <div className="flex flex-col gap-1">
            <InputLabel label={t('ageGroup')} />
            <Slider
              range
              min={MIN_AGE}
              max={MAX_AGE}
              defaultValue={[MIN_AGE, MAX_AGE]}
              value={[values.minAge || MIN_AGE, values.maxAge || MAX_AGE]}
              onChange={value => {
                setFieldValue('minAge', value[0]);
                setFieldValue('maxAge', value[1]);
              }}
            />
          </div>
          <DropdownWithLabel
            key={'japaneseLevel' + filterKey}
            label={t('japaneseLevel')}
            placeholder={t('select', { ns: 'common' })}
            value={values.japaneseLevel || ''}
            items={JAPANESE_LEVEL}
            onSelectItem={async item => {
              setFieldValue('japaneseLevel', item.value);
            }}
            error={
              errors.japaneseLevel && touched.japaneseLevel
                ? errors.japaneseLevel
                : null
            }
            zIndex={10009}
          />
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
            zIndex={10003}
          />
          <DateInput
            hasCloseButton
            label={t('workStartDate')}
            placeholder={'YYYY-MM-DD'}
            date={values.startDate}
            setDate={startDateValue => {
              setTouched({ startDate: true });
              setFieldValue('startDate', startDateValue);
            }}
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

export default FilterJobSeeker;
