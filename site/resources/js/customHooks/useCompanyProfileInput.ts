import { useEffect } from 'react';

// Redux
import { useAppDispatch, useAppSelector } from '@redux/hook';
import {
  useCreateJobMutation,
  useUpdateJobMutation,
} from '@redux/services/jobsApi';
import { useCreateCompanyProfileMutation } from '@redux/services/companyApi';
import {
  ICompanyProfileInputData,
  IJobInputData,
  resetJobData,
  setCompanyJobData,
  setCompanyProfileInputData,
  setIsJobEditMode,
} from '@redux/reducers/company';

const useCompanyProfileInput = () => {
  const dispatch = useAppDispatch();
  const [createProfile, { isLoading, isSuccess, data, isError }] =
    useCreateCompanyProfileMutation();
  const [
    createJob,
    {
      isLoading: isJobLoading,
      isSuccess: isJobSuccess,
      data: jobData,
      isError: isJobError,
    },
  ] = useCreateJobMutation();

  const [
    updateJob,
    {
      isLoading: isUpdateJobLoading,
      isSuccess: isUpdateJobSuccess,
      data: updatejobData,
      isError: isUpdateJobError,
    },
  ] = useUpdateJobMutation();

  const { profileInput, job, isEditMode, editJobId } = useAppSelector(
    state => state.company,
  );

  useEffect(() => {
    if (isError || isJobError || isUpdateJobError) {
      dispatch(
        setCompanyProfileInputData({
          isCompleted: false,
        }),
      );
    }
  }, [isError, isJobError, isUpdateJobError]);

  useEffect(() => {
    if (isUpdateJobSuccess) {
      handleSetJobEditMode({ isEdit: false, id: null });
    }
  }, [isUpdateJobSuccess]);

  // Save company input data to redux store
  const handleSetCompanyProfileInputData = (
    values: ICompanyProfileInputData,
  ) => {
    dispatch(
      setCompanyProfileInputData({
        ...values,
      }),
    );
  };

  // Save job input data to redux store
  const handleSetCompanyJobData = (values: IJobInputData) => {
    dispatch(
      setCompanyJobData({
        ...values,
      }),
    );
  };

  // Set edit mode on/off for job
  const handleSetJobEditMode = ({
    isEdit,
    id,
  }: {
    isEdit: boolean;
    id?: string | null;
  }) => {
    dispatch(setIsJobEditMode({ isEdit, id }));
  };

  const handleResetJobData = () => {
    dispatch(resetJobData());
  };

  /**
   * Create company profile & job when user first signs up
   */
  const handleCreateProfile = async ({ skip = false }) => {
    const formData = new FormData();
    const {
      company_name,
      address,
      about_company,
      logo,
      image,
      about_company_ja,
      intro_video,
    } = profileInput;
    formData.append('company_name', company_name);
    formData.append('about_company', about_company);
    formData.append('about_company_ja', about_company_ja);
    formData.append('address', address);
    if (intro_video) {
      formData.append('intro_video', intro_video);
    }
    if (logo) {
      formData.append('logo', logo);
    }
    image?.forEach(imageItem => {
      formData.append('image[]', imageItem);
    });

    if (!skip) {
      Object.keys(job).forEach(key => {
        const jobKey = key as keyof typeof job;
        if (jobKey === 'job_image' && job[jobKey]) {
          const imageObj = job[jobKey].imageObj;
          if (imageObj) {
            formData.append('job_image[]', imageObj);
          }
          return;
        }
        if (Array.isArray(job[jobKey])) {
          const arrayData = job[jobKey] as unknown as [];
          if (jobKey === 'tags') {
            arrayData?.forEach(keyValue => {
              formData.append(`${jobKey}[]`, keyValue);
            });
          } else {
            arrayData?.forEach(keyValue => {
              formData.append(`job[${jobKey}[]]`, keyValue);
            });
          }
          return;
        } else {
          formData.append(`job[${jobKey}]`, job[jobKey] as string);
        }
      });
    }

    createProfile(formData);
  };

  /**
   * Used when user is logged in and creates job
   */
  const handleCreateJob = async () => {
    const formData = new FormData();
    Object.keys(job).forEach(key => {
      const jobKey = key as keyof typeof job;
      if (jobKey === 'job_image' && job[jobKey]) {
        const imageObj = job[jobKey].imageObj;
        if (imageObj) {
          formData.append('job_image[]', imageObj);
        }
        return;
      }
      if (Array.isArray(job[jobKey])) {
        const arrayData = job[jobKey] as unknown as [];
        arrayData?.forEach(keyValue => {
          formData.append(`${jobKey}[]`, keyValue);
        });
        return;
      } else {
        formData.append(`${jobKey}`, job[jobKey] as string);
      }
    });
    createJob(formData);
  };

  /**
   * Used when user is logged in and updates job
   */
  const handleUpdateJob = async () => {
    const formData = new FormData();
    Object.keys(job).forEach(key => {
      const jobKey = key as keyof typeof job;
      if (Array.isArray(job[jobKey])) {
        const arrayData = job[jobKey] as unknown as [];
        if (arrayData.length > 0) {
          arrayData?.forEach(keyValue => {
            formData.append(`${jobKey}[]`, keyValue);
          });
        }
        return;
      } else {
        if (jobKey === 'job_image' && job[jobKey]) {
          const imageObj = job[jobKey].imageObj;
          if (imageObj) {
            formData.append('job_image[]', imageObj);
          }
          return;
        }
        formData.append(`${jobKey}`, job[jobKey] as string);
      }
    });
    updateJob({ formData, id: editJobId });
  };

  return {
    handleCreateJob,
    handleUpdateJob,
    handleResetJobData,
    handleCreateProfile,
    handleSetJobEditMode,
    handleSetCompanyJobData,
    handleSetCompanyProfileInputData,
    isLoading: isLoading || isJobLoading || isUpdateJobLoading,
    isSuccess,
    data,
    isJobSuccess: isJobSuccess || isUpdateJobSuccess,
    jobData: jobData || updatejobData,
    profile: profileInput,
    jobInput: job,
    isEditMode,
  };
};

export default useCompanyProfileInput;
