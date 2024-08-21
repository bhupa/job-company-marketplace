import { useEffect, useState } from 'react';

import { useTranslation } from 'react-i18next';

// Hooks
import { useShowMessage } from './useShowMessage';

// Redux
import {
  IJobData,
  useDeleteJobMutation,
  useGetJobsListQuery,
} from '@redux/services/jobsApi';

const useCompanyJobs = () => {
  const { t } = useTranslation(['messages']);
  const { showSuccess } = useShowMessage();
  const [page, setPage] = useState<number>(1);
  const [jobs, setJobs] = useState<IJobData[]>([]);
  const [isLoading, setLoading] = useState<boolean>(true);

  const [
    deleteJob,
    {
      isSuccess: isDeleteSuccess,
      originalArgs: deleteJobId,
      isError: isDeleteError,
    },
  ] = useDeleteJobMutation();
  const { data, isError } = useGetJobsListQuery({
    page,
  });

  useEffect(() => {
    if (isDeleteSuccess) {
      const filteredJobs = jobs.filter(job => job.id !== deleteJobId);
      setJobs(filteredJobs);
      showSuccess(t('job.deleteSuccess'));
    }
  }, [isDeleteSuccess, isDeleteError]);

  useEffect(() => {
    if (data) {
      setJobs(data.data?.data);
      setLoading(false);
    }
  }, [data]);

  useEffect(() => {
    if (isError) {
      setLoading(false);
    }
  }, [isError]);

  const handleDeleteJob = (jobId: string) => {
    deleteJob(jobId);
  };

  return {
    jobs,
    isLoading,
    isEmpty: !isLoading && (!jobs || jobs.length === 0),
    totalData: data?.data?.pagination.total,
    setPage,
    handleDeleteJob,
  };
};

export default useCompanyJobs;
